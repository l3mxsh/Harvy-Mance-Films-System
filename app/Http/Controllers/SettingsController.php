<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Booking;
use App\Models\CustomerAccount;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $autoDeleteDays = Setting::getValue('client_auto_delete_days', '30');

        $deliveredBookings = Booking::where('status', 'completed')
            ->where('deliverables_unlocked', true)
            ->whereNotNull('delivered_at')
            ->with('customerAccount')
            ->get()
            ->filter(fn($b) => $b->customerAccount);

        $upcomingDeletions = $deliveredBookings->map(function ($booking) use ($autoDeleteDays) {
            $deliveredAt = $booking->delivered_at;
            $deleteAt = $deliveredAt->copy()->addDays((int) $autoDeleteDays);
            $daysRemaining = max(0, $deleteAt->diffInDays(now(), false));

            return [
                'booking' => $booking,
                'delivered_at' => $deliveredAt,
                'delete_at' => $deleteAt,
                'days_remaining' => abs($daysRemaining),
            ];
        })->sortBy('delete_at')->values();

        return view('dashboard.settings', compact('autoDeleteDays', 'upcomingDeletions'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'client_auto_delete_days' => 'required|integer|in:7,14,30',
        ]);

        Setting::setValue('client_auto_delete_days', $validated['client_auto_delete_days']);

        return back()->with('success', 'Settings updated successfully.');
    }
}
