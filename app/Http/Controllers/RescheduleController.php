<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\InventoryItem;
use App\Models\RescheduleRequest;
use App\Models\Setting;
use App\Models\Staff;
use App\Models\StaffSchedule;
use App\Models\Team;
use Illuminate\Http\Request;

class RescheduleController extends Controller
{
    public function store(Request $request)
    {
        $account = auth('client')->user();
        $booking = Booking::findOrFail($account->booking_id);

        $leadTime = (int) Setting::getValue('reschedule_lead_time_days', 5);

        // Validate constraints
        if (!in_array($booking->status, ['pending', 'approved', 'ongoing'])) {
            return back()->with('reschedule_error', 'Reschedule is no longer available for this booking.');
        }

        if (now()->diffInDays($booking->event_date, false) < $leadTime) {
            return back()->with('reschedule_error', "Reschedule must be requested at least {$leadTime} days before the event date.");
        }

        $validated = $request->validate([
            'requested_date' => 'required|date|after:today',
            'requested_time' => 'required|string',
        ]);

        // Check slot availability on new date (same logic as booking)
        $conflicts = Booking::where('event_date', $validated['requested_date'])
            ->whereIn('status', ['approved', 'ongoing'])
            ->where('id', '!=', $booking->id)
            ->count();

        if ($conflicts > 0) {
            return back()->with('reschedule_error', 'The selected date is not available. Please choose another date.');
        }

        // Check inventory availability on new date
        $unavailable = $this->checkInventoryConflict($booking, $validated['requested_date']);
        if (!empty($unavailable)) {
            $names = implode(', ', array_column($unavailable, 'name'));
            return back()->with('reschedule_error', "Some equipment is unavailable on that date: {$names}. Please choose another date.");
        }

        // Cancel any previous pending request
        RescheduleRequest::where('booking_id', $booking->id)->where('status', 'pending')->delete();

        RescheduleRequest::create([
            'booking_id' => $booking->id,
            'requested_date' => $validated['requested_date'],
            'requested_time' => $validated['requested_time'],
            'status' => 'pending',
        ]);

        return back()->with('success', 'Reschedule request submitted. Waiting for admin approval.');
    }

    public function approve(Request $request, RescheduleRequest $rescheduleRequest)
    {
        if ($rescheduleRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $request->validate(['team_id' => 'required|exists:teams,id']);

        $booking = $rescheduleRequest->booking;
        $team = Team::with('members')->findOrFail($request->team_id);
        $newDate = $rescheduleRequest->requested_date->format('Y-m-d');

        // Check team availability on new date
        foreach ($team->members as $member) {
            $conflict = StaffSchedule::where('staff_id', $member->id)
                ->where('event_date', $newDate)
                ->whereIn('status', ['assigned', 'confirmed'])
                ->where('booking_id', '!=', $booking->id)
                ->exists();

            if ($conflict) {
                return back()->with('error', "{$member->name} is not available on {$newDate}.");
            }
        }

        // Remove old staff schedules for this booking
        StaffSchedule::where('booking_id', $booking->id)->delete();

        // Assign new schedules
        foreach ($team->members as $member) {
            StaffSchedule::create([
                'staff_id' => $member->id,
                'booking_id' => $booking->id,
                'event_date' => $newDate,
                'event_time' => $rescheduleRequest->requested_time,
                'status' => 'assigned',
            ]);
        }

        $rescheduleRequest->update(['status' => 'approved', 'new_team_id' => $team->id]);

        $booking->update([
            'event_date' => $rescheduleRequest->requested_date,
            'event_time' => $rescheduleRequest->requested_time,
            'team_id' => $team->id,
            'reschedule_used' => false,
        ]);

        return back()->with('success', "Reschedule approved. Booking {$booking->booking_ref} moved to {$newDate}.");
    }

    public function reject(Request $request, RescheduleRequest $rescheduleRequest)
    {
        if ($rescheduleRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $request->validate(['rejection_reason' => 'required|string|max:1000']);

        $rescheduleRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Reschedule request rejected.');
    }

    private function checkInventoryConflict(Booking $booking, string $newDate): array
    {
        $unavailable = [];

        foreach ($booking->items as $bookingItem) {
            $item = InventoryItem::find($bookingItem->inventory_item_id);
            if (!$item) continue;

            $reservedOnDate = BookingItem::where('inventory_item_id', $item->id)
                ->where('status', 'reserved')
                ->whereHas('booking', function ($q) use ($newDate, $booking) {
                    $q->where('event_date', $newDate)
                      ->whereIn('status', ['approved', 'ongoing'])
                      ->where('id', '!=', $booking->id);
                })
                ->sum('quantity');

            if (($item->quantity - $reservedOnDate) < $bookingItem->quantity) {
                $unavailable[] = ['name' => $item->name];
            }
        }

        return $unavailable;
    }
}
