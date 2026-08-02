<?php

namespace App\Http\Controllers;

use App\Models\ClientAccount;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class ClientAccountController extends Controller
{
    public function showLogin()
    {
        if (auth('client')->check()) {
            return redirect()->route('client.dashboard');
        }
        return view('client.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'control_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $account = ClientAccount::where('control_number', $request->control_number)->first();

        if ($account && $account->archived_at) {
            return back()->withErrors([
                'control_number' => 'This account has expired and is no longer active. Please contact us to request access again.',
            ])->withInput($request->only('control_number'));
        }

        if (!$account || !Hash::check($request->password, $account->password)) {
            $maxAttempts = 5;
            $key = md5('client-login' . $request->ip());
            $remaining = RateLimiter::retriesLeft($key, $maxAttempts);

            return back()->withErrors([
                'control_number' => "Invalid credentials. You have {$remaining} attempts remaining.",
            ])->withInput($request->only('control_number'));
        }

        auth('client')->login($account);

        $account->update(['last_login_at' => now()]);

        if ($account->must_change_password) {
            return redirect()->route('client.change-password');
        }

        return redirect()->route('client.dashboard');
    }

    public function logout()
    {
        auth('client')->logout();
        return redirect()->route('client.login');
    }

    public function dashboard()
    {
        $account = auth('client')->user();

        if ($account->archived_at) {
            auth('client')->logout();
            return redirect()->route('client.login')
                ->withErrors(['control_number' => 'This account has expired and is no longer active. Please contact us to request access again.']);
        }

        $booking = Booking::with(['package.services', 'addons', 'team.members', 'team.outsourcedMembers', 'postProduction.tasks.staff'])
            ->findOrFail($account->booking_id);

        $latestDownpayment = $booking->latestDownpayment;
        $remainingBalance = $booking->total_price - $booking->downpayment_amount;
        $finalPayments = $booking->downpayments()->where('status', 'verified')->get();
        $totalPaid = $booking->downpayments()->where('status', 'verified')->sum('amount');
        $hasFinalPayment = $totalPaid >= $booking->total_price;

        $autoDeleteDays = null;
        $daysUntilDeletion = null;
        $deleteAt = null;
        if ($booking->deliverables_unlocked && $booking->delivered_at) {
            $autoDeleteDays = (int) Setting::getValue('client_auto_delete_days', '30');
            $deleteAt = $booking->delivered_at->copy()->addDays($autoDeleteDays);
            $daysUntilDeletion = max(0, (int) round(now()->diffInDays($deleteAt, false)));
        }

        return view('client.dashboard', compact('account', 'booking', 'latestDownpayment', 'remainingBalance', 'hasFinalPayment', 'daysUntilDeletion', 'deleteAt'));
    }

    public function showChangePassword()
    {
        $account = auth('client')->user();
        return view('client.change-password', compact('account'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $account = auth('client')->user();

        if (!Hash::check($request->current_password, $account->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $account->update([
            'password' => $request->new_password,
            'must_change_password' => false,
        ]);

        return redirect()->route('client.dashboard')
            ->with('success', 'Password changed successfully!');
    }

    public static function generateControlNumber(): string
    {
        do {
            $controlNumber = 'HMF-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
        } while (ClientAccount::where('control_number', $controlNumber)->exists());

        return $controlNumber;
    }

    public static function generateTempPassword(): string
    {
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $password = '';
        for ($i = 0; $i < 6; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
}
