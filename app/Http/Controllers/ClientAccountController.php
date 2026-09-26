<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ClientAccount;
use App\Models\Booking;
use App\Models\Setting;
use Illuminate\Support\Collection;
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

    public function adminIndex(Request $request)
    {
        $activeQuery = ClientAccount::query()
            ->with('booking')
            ->whereNull('archived_at');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $activeQuery->where(function ($q) use ($search) {
                $q->where('control_number', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('client_email', 'like', "%{$search}%")
                    ->orWhere('client_phone', 'like', "%{$search}%")
                    ->orWhereHas('booking', fn ($b) => $b->where('booking_ref', 'like', "%{$search}%"));
            });
        }

        $accounts = $activeQuery->orderByDesc('created_at')->paginate(15)->withQueryString();

        if ($request->ajax()) {
            $rowsHtml = view('dashboard.partials.client-rows', compact('accounts'))->render();
            $mobileRowsHtml = view('dashboard.partials.client-mobile-rows', compact('accounts'))->render();
            $paginationHtml = $accounts->hasPages() ? $accounts->links('vendor.pagination.bootstrap-5')->render() : '';

            return response()->json([
                'rows' => $rowsHtml,
                'mobileRows' => $mobileRowsHtml,
                'pagination' => $paginationHtml,
                'total' => $accounts->total(),
            ]);
        }

        $archivedAccounts = ClientAccount::with('booking')
            ->whereNotNull('archived_at')
            ->orderByDesc('archived_at')
            ->get();

        $autoDeleteDays = (int) Setting::getValue('client_auto_delete_days', '30');
        $upcomingDeletions = Booking::where('status', 'completed')
            ->where('deliverables_unlocked', true)
            ->whereNotNull('delivered_at')
            ->with(['clientAccount' => fn ($q) => $q->whereNull('archived_at')])
            ->get()
            ->filter(fn($b) => $b->clientAccount)
            ->map(function ($booking) use ($autoDeleteDays) {
                $deliveredAt = $booking->delivered_at;
                $deleteAt = $deliveredAt->copy()->addDays($autoDeleteDays);
                return [
                    'booking' => $booking,
                    'delivered_at' => $deliveredAt,
                    'delete_at' => $deleteAt,
                    'days_remaining' => max(0, (int) round(now()->diffInDays($deleteAt, false))),
                ];
            })->sortBy('delete_at')->values();

        $summary = [
            'total' => ClientAccount::count(),
            'active' => ClientAccount::whereNull('archived_at')->count(),
            'archived' => ClientAccount::whereNotNull('archived_at')->count(),
            'pendingChange' => ClientAccount::whereNull('archived_at')->where('must_change_password', true)->count(),
        ];

        return view('dashboard.clients', compact('accounts', 'archivedAccounts', 'upcomingDeletions', 'summary'));
    }

    public function restore(string $account)
    {
        $account = ClientAccount::findOrFail($account);

        if ($account->archived_at) {
            $account->update(['archived_at' => null]);
            ActivityLog::log('client.restored', "Restored client account {$account->control_number} ({$account->client_name}).", [
                'control_number' => $account->control_number,
                'client_name' => $account->client_name,
            ]);
        }

        return back()->with('success', "Account for {$account->client_name} has been restored.");
    }

    public function archive(string $account)
    {
        $account = ClientAccount::findOrFail($account);

        if (!$account->archived_at) {
            $account->update(['archived_at' => now()]);
            ActivityLog::log('client.archived', "Archived client account {$account->control_number} ({$account->client_name}).", [
                'control_number' => $account->control_number,
                'client_name' => $account->client_name,
            ]);
        }

        return back()->with('success', "Account for {$account->client_name} has been archived.");
    }

    public function destroy(string $account)
    {
        $account = ClientAccount::findOrFail($account);

        $name = $account->client_name;
        $controlNumber = $account->control_number;

        ActivityLog::log('client.deleted', "Permanently deleted client account {$controlNumber} ({$name}).", [
            'control_number' => $controlNumber,
            'client_name' => $name,
        ]);

        $account->delete();

        return back()->with('success', "Account for {$name} has been permanently deleted.");
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
