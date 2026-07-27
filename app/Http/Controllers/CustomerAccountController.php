<?php

namespace App\Http\Controllers;

use App\Models\CustomerAccount;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerAccountController extends Controller
{
    public function showLogin()
    {
        if (auth('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }
        return view('customer.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'control_number' => 'required|string',
            'password' => 'required|string',
        ]);

        $account = CustomerAccount::where('control_number', $request->control_number)->first();

        if (!$account || !Hash::check($request->password, $account->password)) {
            return back()->withErrors([
                'control_number' => 'Invalid control number or password.',
            ])->withInput($request->only('control_number'));
        }

        auth('customer')->login($account);

        $account->update(['last_login_at' => now()]);

        if ($account->must_change_password) {
            return redirect()->route('customer.change-password');
        }

        return redirect()->route('customer.dashboard');
    }

    public function logout()
    {
        auth('customer')->logout();
        return redirect()->route('customer.login');
    }

    public function dashboard()
    {
        $account = auth('customer')->user();
        $booking = Booking::with(['package.services', 'addons', 'items.inventoryItem', 'team.members', 'postProduction.tasks.staff'])
            ->findOrFail($account->booking_id);

        $latestDownpayment = $booking->latestDownpayment;
        $remainingBalance = $booking->total_price - $booking->downpayment_amount;
        $finalPayments = $booking->downpayments()->where('status', 'verified')->get();
        $totalPaid = $booking->downpayments()->where('status', 'verified')->sum('amount');
        $hasFinalPayment = $totalPaid >= $booking->total_price;

        $autoDeleteDays = null;
        $daysUntilDeletion = null;
        if ($booking->deliverables_unlocked && $booking->delivered_at) {
            $autoDeleteDays = (int) Setting::getValue('client_auto_delete_days', '30');
            $deleteAt = $booking->delivered_at->copy()->addDays($autoDeleteDays);
            $daysUntilDeletion = max(0, (int) now()->diffInDays($deleteAt, false));
        }

        return view('customer.dashboard', compact('account', 'booking', 'latestDownpayment', 'remainingBalance', 'hasFinalPayment', 'daysUntilDeletion'));
    }

    public function showChangePassword()
    {
        $account = auth('customer')->user();
        return view('customer.change-password', compact('account'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $account = auth('customer')->user();

        if (!Hash::check($request->current_password, $account->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $account->update([
            'password' => $request->new_password,
            'must_change_password' => false,
        ]);

        return redirect()->route('customer.dashboard')
            ->with('success', 'Password changed successfully!');
    }

    public static function generateControlNumber(): string
    {
        $year = date('Y');
        $last = CustomerAccount::where('control_number', 'like', "BKG-{$year}-%")
            ->orderByDesc('control_number')
            ->first();

        if ($last) {
            $lastNum = (int) substr($last->control_number, -5);
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return 'BKG-' . $year . '-' . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
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
