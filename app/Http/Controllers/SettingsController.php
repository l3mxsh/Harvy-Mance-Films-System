<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Booking;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $autoDeleteDays = Setting::getValue('client_auto_delete_days', '30');
        $rescheduleLeadTime = Setting::getValue('reschedule_lead_time_days', '5');
        $refundPolicy = json_decode(Setting::getValue('refund_policy', json_encode([
            ['days' => 14, 'percent' => 100],
            ['days' => 7,  'percent' => 50],
            ['days' => 0,  'percent' => 0],
        ])), true);

        $deliveredBookings = Booking::where('status', 'completed')
            ->where('deliverables_unlocked', true)
            ->whereNotNull('delivered_at')
            ->with(['clientAccount' => fn ($q) => $q->whereNull('archived_at')])
            ->get()
            ->filter(fn($b) => $b->clientAccount);

        $upcomingDeletions = $deliveredBookings->map(function ($booking) use ($autoDeleteDays) {
            $deliveredAt = $booking->delivered_at;
            $deleteAt = $deliveredAt->copy()->addDays((int) $autoDeleteDays);
            $daysRemaining = max(0, (int) round(now()->diffInDays($deleteAt, false)));

            return [
                'booking' => $booking,
                'delivered_at' => $deliveredAt,
                'delete_at' => $deleteAt,
                'days_remaining' => $daysRemaining,
            ];
        })->sortBy('delete_at')->values();

        $user = auth()->user();
        $showSidebarBadges = Setting::getValue('admin_show_sidebar_badges', '1');

        return view('dashboard.settings', compact('autoDeleteDays', 'rescheduleLeadTime', 'upcomingDeletions', 'refundPolicy', 'user', 'showSidebarBadges'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'client_auto_delete_days'  => 'required|integer|in:7,14,30',
            'reschedule_lead_time_days' => 'required|integer|in:5,6,7',
            'refund_tiers'             => 'nullable|array',
            'refund_tiers.*.days'      => 'required|integer|min:0',
            'refund_tiers.*.percent'   => 'required|integer|min:0|max:100',
        ]);

        Setting::setValue('client_auto_delete_days', $validated['client_auto_delete_days']);
        Setting::setValue('reschedule_lead_time_days', $validated['reschedule_lead_time_days']);

        $policy = collect($validated['refund_tiers'] ?? [])
            ->map(fn($t) => ['days' => (int)$t['days'], 'percent' => (int)$t['percent']])
            ->sortByDesc('days')
            ->values()
            ->toArray();
        Setting::setValue('refund_policy', json_encode($policy));

        ActivityLog::log('settings.updated', 'System settings updated.', [
            'client_auto_delete_days' => $validated['client_auto_delete_days'],
            'reschedule_lead_time_days' => $validated['reschedule_lead_time_days'],
            'refund_tiers' => $policy,
        ]);

        return back()->with('success', 'Settings updated successfully.');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'show_sidebar_badges' => 'nullable|in:1,0',
        ]);

        $user = auth()->user();
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        Setting::setValue('admin_show_sidebar_badges', ($validated['show_sidebar_badges'] ?? null) ? '1' : '0');

        ActivityLog::log('admin.profile_updated', "Admin {$user->name} updated their profile.", [
            'email' => $validated['email'],
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->user()->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();
        $user->update(['password' => $validated['new_password']]);

        ActivityLog::log('admin.password_changed', "Admin {$user->name} changed their password.");

        return back()->with('success', 'Password updated successfully.');
    }
}
