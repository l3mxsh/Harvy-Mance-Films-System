<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Package;
use App\Models\Addon;
use App\Models\InventoryItem;
use App\Models\CustomerAccount;
use App\Models\StaffSchedule;
use App\Models\Team;
use App\Models\Otp;
use App\Mail\BookingCredentialsEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $packages = Package::with(['services', 'inventory'])
            ->where('status', 'active')
            ->get();

        $addons = Addon::with('inventory')
            ->where('status', 'active')
            ->get();

        $user = Auth::user();

        return view('booking', compact('packages', 'addons', 'user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'package_id' => 'required|exists:packages,id',
            'addon_ids' => 'nullable|array',
            'addon_ids.*' => 'exists:addons,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => ['required', 'regex:/^09\d{2}-\d{3}-\d{4}$/'],
            'event_type' => 'required|string|max:100',
            'event_date' => 'required|date|after:today',
            'event_time' => 'required|string',
            'event_venue' => 'required|string|max:255',
            'event_address' => 'nullable|string|max:500',
            'event_description' => 'nullable|string|max:1000',
            'total_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'otp_verified' => 'accepted',
            'terms_agreed' => 'accepted',
        ]);

        $otp = Otp::where('email', $validated['client_email'])
            ->where('purpose', 'booking_verification')
            ->where('verified', true)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->latest()
            ->first();

        if (!$otp) {
            return back()->withErrors(['otp' => 'Email verification is required. Please verify your email first.'])->withInput();
        }

        if ($this->emailHasActiveAccount($validated['client_email'])) {
            return back()->withErrors(['client_email' => 'This email already has an active account. Please log in instead of making a new booking.'])->withInput();
        }

        $package = Package::with('inventory')->findOrFail($validated['package_id']);
        $selectedAddons = Addon::with('inventory')
            ->whereIn('id', $validated['addon_ids'] ?? [])
            ->get();

        $totalPrice = (float) $package->price + $selectedAddons->sum('price');
        $downpayment = round($totalPrice * 0.30, 2);

        $booking = Booking::create([
            'booking_ref' => $this->generateBookingRef(),
            'package_id' => $package->id,
            'client_name' => $validated['client_name'],
            'client_email' => $validated['client_email'],
            'client_phone' => $validated['client_phone'],
            'event_type' => $validated['event_type'],
            'event_date' => $validated['event_date'],
            'event_time' => $validated['event_time'],
            'event_venue' => $validated['event_venue'],
            'event_address' => $validated['event_address'] ?? null,
            'event_description' => $validated['event_description'] ?? null,
            'total_price' => $totalPrice,
            'downpayment_amount' => $downpayment,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'terms_agreed' => true,
        ]);

        foreach ($package->inventory as $item) {
            $booking->items()->create([
                'inventory_item_id' => $item->id,
                'quantity' => $item->pivot->quantity,
                'status' => 'reserved',
            ]);
        }

        foreach ($selectedAddons as $addon) {
            $booking->addons()->attach($addon->id, ['price' => $addon->price]);

            foreach ($addon->inventory as $item) {
                $existing = $booking->items()
                    ->where('inventory_item_id', $item->id)
                    ->first();

                if ($existing) {
                    $existing->update(['quantity' => $existing->quantity + $item->pivot->quantity]);
                } else {
                    $booking->items()->create([
                        'inventory_item_id' => $item->id,
                        'quantity' => $item->pivot->quantity,
                        'status' => 'reserved',
                    ]);
                }
            }
        }

        return redirect()->route('booking.status', $booking->booking_ref)
            ->with('success', 'Booking submitted successfully! Your booking is now pending admin approval.');
    }

    public function status(string $bookingRef)
    {
        $booking = Booking::with(['package.services', 'addons', 'items.inventoryItem'])
            ->where('booking_ref', $bookingRef)
            ->firstOrFail();

        return view('booking-status', compact('booking'));
    }

    public function approve(Request $request, Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be approved.');
        }

        $request->validate([
            'team_id' => 'required|exists:teams,id',
        ]);

        $team = Team::with('members')->find($request->team_id);
        $eventDate = $booking->event_date->format('Y-m-d');

        $unavailableMembers = [];
        foreach ($team->members as $member) {
            $hasConflict = StaffSchedule::where('staff_id', $member->id)
                ->where('event_date', $eventDate)
                ->whereIn('status', ['assigned', 'confirmed'])
                ->whereHas('booking', fn($q) => $q->whereNotIn('status', ['cancelled', 'rejected']))
                ->exists();

            if ($hasConflict) {
                $unavailableMembers[] = $member->name;
            }
        }

        if (!empty($unavailableMembers)) {
            return back()->with('error', 'Cannot approve: ' . implode(', ', $unavailableMembers) . ' already have a booking on ' . $eventDate . '.');
        }

        foreach ($team->members as $member) {
            StaffSchedule::create([
                'staff_id' => $member->id,
                'booking_id' => $booking->id,
                'event_date' => $eventDate,
                'event_time' => $booking->event_time,
                'status' => 'assigned',
            ]);
        }

        $controlNumber = CustomerAccountController::generateControlNumber();
        $tempPassword = CustomerAccountController::generateTempPassword();

        $account = CustomerAccount::create([
            'control_number' => $controlNumber,
            'password' => $tempPassword,
            'client_name' => $booking->client_name,
            'client_email' => $booking->client_email,
            'client_phone' => $booking->client_phone,
            'booking_id' => $booking->id,
            'must_change_password' => true,
        ]);

        $booking->update([
            'status' => 'approved',
            'team_id' => $team->id,
        ]);

        try {
            Mail::to($booking->client_email)->send(
                new BookingCredentialsEmail(
                    $booking->client_name,
                    $controlNumber,
                    $tempPassword,
                    $booking->booking_ref
                )
            );
        } catch (\Exception $e) {
            // Mail may fail in log driver; continue anyway
        }

        return back()->with('success', "Booking {$booking->booking_ref} approved. Team {$team->name} assigned. Credentials sent to {$booking->client_email}.");
    }

    public function checkEmail(Request $request)
    {
        $email = trim((string) $request->input('email'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['exists' => false]);
        }

        $exists = $this->emailHasActiveAccount($email);

        return response()->json([
            'exists' => $exists,
            'message' => 'This email already has an active account. Please log in instead of making a new booking.',
        ]);
    }

    private function emailHasActiveAccount(string $email): bool
    {
        return CustomerAccount::whereRaw('LOWER(client_email) = ?', [strtolower($email)])->exists();
    }

    public function checkDate(Request $request)
    {
        $date = $request->input('date');

        if (!$date) {
            return response()->json(['available' => false, 'message' => 'No date provided.']);
        }

        $conflicts = Booking::where('event_date', $date)
            ->whereIn('status', ['approved', 'ongoing'])
            ->count();

        return response()->json([
            'available' => $conflicts === 0,
            'message' => $conflicts > 0
                ? "There are {$conflicts} approved booking(s) on this date. Availability may be limited."
                : 'This date is available.',
        ]);
    }

    public function checkInventory(Request $request)
    {
        $packageId = $request->input('package_id');
        $addonIds = $request->input('addon_ids', []);
        $eventDate = $request->input('event_date');

        if (!$packageId || !$eventDate) {
            return response()->json(['available' => false, 'message' => 'Missing required parameters.']);
        }

        $package = Package::with('inventory')->find($packageId);
        if (!$package) {
            return response()->json(['available' => false, 'message' => 'Package not found.']);
        }

        $selectedAddons = Addon::with('inventory')
            ->whereIn('id', $addonIds)
            ->get();

        $requiredItems = collect();

        foreach ($package->inventory as $item) {
            $existing = $requiredItems->firstWhere('id', $item->id);
            if ($existing) {
                $existing->pivot_qty += $item->pivot->quantity;
            } else {
                $requiredItems->push((object) [
                    'id' => $item->id,
                    'name' => $item->name,
                    'pivot_qty' => $item->pivot->quantity,
                ]);
            }
        }

        foreach ($selectedAddons as $addon) {
            foreach ($addon->inventory as $item) {
                $existing = $requiredItems->firstWhere('id', $item->id);
                if ($existing) {
                    $existing->pivot_qty += $item->pivot->quantity;
                } else {
                    $requiredItems->push((object) [
                        'id' => $item->id,
                        'name' => $item->name,
                        'pivot_qty' => $item->pivot->quantity,
                    ]);
                }
            }
        }

        $unavailable = [];
        foreach ($requiredItems as $req) {
            $item = InventoryItem::find($req->id);
            if (!$item) continue;

            $reservedOnDate = BookingItem::where('inventory_item_id', $item->id)
                ->where('status', 'reserved')
                ->whereHas('booking', function ($q) use ($eventDate) {
                    $q->where('event_date', $eventDate)
                      ->whereIn('status', ['approved', 'ongoing']);
                })
                ->sum('quantity');

            $availableQty = $item->quantity - $reservedOnDate;

            if ($availableQty < $req->pivot_qty) {
                $unavailable[] = [
                    'name' => $item->name,
                    'required' => $req->pivot_qty,
                    'available' => max(0, $availableQty),
                ];
            }
        }

        return response()->json([
            'available' => empty($unavailable),
            'message' => empty($unavailable)
                ? 'All required equipment and materials are available.'
                : 'Some items are not fully available for the selected date.',
            'unavailable_items' => $unavailable,
        ]);
    }

    public function adminIndex()
    {
        $bookings = Booking::with(['package.services', 'addons', 'team', 'latestDownpayment', 'postProduction'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $availableTeams = Team::with('members', 'outsourcedMembers')->where('status', 'active')->get();

        $rescheduleRequests = \App\Models\RescheduleRequest::with(['booking.team', 'newTeam'])
            ->whereHas('booking')
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingRescheduleCount = $rescheduleRequests->where('status', 'pending')->count();

        return view('dashboard.bookings', compact('bookings', 'availableTeams', 'rescheduleRequests', 'pendingRescheduleCount'));
    }

    public function reject(Request $request, Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return back()->with('error', 'Only pending bookings can be rejected.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $booking->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        $booking->items()->update(['status' => 'cancelled']);

        return back()->with('success', "Booking {$booking->booking_ref} has been rejected.");
    }

    private function generateBookingRef(): string
    {
        do {
            $ref = 'HMF-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (Booking::where('booking_ref', $ref)->exists());

        return $ref;
    }
}
