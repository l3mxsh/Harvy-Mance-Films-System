<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\CancellationRequest;
use App\Models\ClientAccount;
use App\Models\Downpayment;
use App\Models\RescheduleRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        $summary = [
            'totalBookings'   => Booking::count(),
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'activeBookings'  => Booking::whereIn('status', ['approved', 'ongoing'])->count(),
            'completedBookings' => Booking::whereIn('status', ['completed', 'delivered'])->count(),
            'todayEvents'     => Booking::whereDate('event_date', $today)
                ->whereNotIn('status', ['cancelled', 'rejected'])->count(),
            'totalClients'    => ClientAccount::active()->count(),
            'pendingPayments' => Downpayment::where('status', 'pending')->count(),
            'monthlyRevenue'  => Downpayment::where('status', 'verified')
                ->whereBetween('verified_at', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
                ->sum('amount'),
        ];

        $upcomingEvents = Booking::with(['package', 'team'])
            ->whereDate('event_date', '>=', $today)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->limit(6)
            ->get();

        $recentBookings = Booking::with(['package'])
            ->latest()
            ->limit(8)
            ->get();

        $pendingPayments = Downpayment::with('booking')
            ->where('status', 'pending')
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        $pendingCancellations = CancellationRequest::with('booking')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        $pendingReschedules = RescheduleRequest::with('booking')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        $pendingCounts = [
            'payments'      => Downpayment::where('status', 'pending')->count(),
            'cancellations' => CancellationRequest::where('status', 'pending')->count(),
            'reschedules'   => RescheduleRequest::where('status', 'pending')->count(),
            'bookings'      => Booking::where('status', 'pending')->count(),
        ];

        $recentActivity = ActivityLog::latest()->limit(8)->get();

        return view('dashboard.dashboard', compact(
            'summary',
            'upcomingEvents',
            'recentBookings',
            'pendingPayments',
            'pendingCancellations',
            'pendingReschedules',
            'pendingCounts',
            'recentActivity'
        ));
    }
}
