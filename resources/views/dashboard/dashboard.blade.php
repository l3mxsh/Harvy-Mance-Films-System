<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Dashboard</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">

            {{-- ==================== SUMMARY CARDS ==================== --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-dark bg-opacity-10 text-dark">
                                <i class="bi bi-journal-check"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Total Bookings</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['totalBookings'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-dark bg-opacity-10 text-dark">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Pending Bookings</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['pendingBookings'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-dark bg-opacity-10 text-dark">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Events Today</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['todayEvents'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-dark bg-opacity-10 text-dark">
                                <i class="bi bi-credit-card"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Pending Payments</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['pendingPayments'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-dark bg-opacity-10 text-dark">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Active Clients</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['totalClients'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-dark bg-opacity-10 text-dark">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Revenue This Month</h6>
                                <h4 class="mb-0 fw-bold">&#8369;{{ number_format($summary['monthlyRevenue'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== UPCOMING EVENTS + PENDING ACTIONS ==================== --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-7">
                    <section class="surface-card h-100 mb-0">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title"><i class="bi bi-calendar-week me-2"></i>Upcoming Events</h2>
                            <a href="{{ route('staff-schedule.index') }}" class="small text-decoration-none">View Schedule <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                        @forelse($upcomingEvents as $event)
                            <a href="{{ route('booking.admin.index', ['search' => $event->booking_ref]) }}" class="event-item">
                                <div class="event-date-block">
                                    <span class="day">{{ $event->event_date->format('d') }}</span>
                                    <span class="mon">{{ $event->event_date->format('M') }}</span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">{{ $event->client_name }}</div>
                                    <div class="event-meta">
                                        <i class="bi bi-film"></i> {{ $event->package->name ?? '—' }}
                                        <span class="mx-1">·</span>
                                        <i class="bi bi-clock"></i> {{ date('g:i A', strtotime($event->event_time)) }}
                                    </div>
                                </div>
                                <span class="text-muted small d-none d-md-inline">{{ $event->event_venue ?: '—' }}</span>
                                <span class="badge dash-badge">{{ ucwords(str_replace('_', ' ', $event->status)) }}</span>
                            </a>
                        @empty
                            <div class="empty-state">
                                <i class="bi bi-calendar-x"></i>
                                No upcoming events scheduled.
                            </div>
                        @endforelse
                    </section>
                </div>

                <div class="col-lg-5">
                    <section class="surface-card h-100 mb-0">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title"><i class="bi bi-bell me-2"></i>Pending Actions</h2>
                        </div>

                        <a href="{{ route('payment-verification.index', ['status' => 'pending']) }}" class="action-item">
                            <span class="action-icon bg-dark bg-opacity-10 text-dark"><i class="bi bi-credit-card"></i></span>
                            <span class="flex-grow-1">
                                <span class="d-block fw-semibold">Payment Verification</span>
                                <small class="text-muted">{{ $pendingCounts['payments'] }} payment{{ $pendingCounts['payments'] === 1 ? '' : 's' }} waiting</small>
                            </span>
                            <span class="badge bg-dark rounded-pill">{{ $pendingCounts['payments'] }}</span>
                        </a>

                        <a href="{{ route('booking.admin.index') }}" class="action-item">
                            <span class="action-icon bg-dark bg-opacity-10 text-dark"><i class="bi bi-hourglass-split"></i></span>
                            <span class="flex-grow-1">
                                <span class="d-block fw-semibold">New Booking Requests</span>
                                <small class="text-muted">{{ $pendingCounts['bookings'] }} booking{{ $pendingCounts['bookings'] === 1 ? '' : 's' }} to review</small>
                            </span>
                            <span class="badge bg-dark rounded-pill">{{ $pendingCounts['bookings'] }}</span>
                        </a>

                        <a href="{{ route('cancellation.admin.index', ['status' => 'pending']) }}" class="action-item">
                            <span class="action-icon bg-dark bg-opacity-10 text-dark"><i class="bi bi-arrow-counterclockwise"></i></span>
                            <span class="flex-grow-1">
                                <span class="d-block fw-semibold">Cancellation Requests</span>
                                <small class="text-muted">{{ $pendingCounts['cancellations'] }} cancellation{{ $pendingCounts['cancellations'] === 1 ? '' : 's' }} to review</small>
                            </span>
                            <span class="badge bg-dark rounded-pill">{{ $pendingCounts['cancellations'] }}</span>
                        </a>

                        <a href="{{ route('booking.admin.index') }}" class="action-item">
                            <span class="action-icon bg-dark bg-opacity-10 text-dark"><i class="bi bi-calendar-x"></i></span>
                            <span class="flex-grow-1">
                                <span class="d-block fw-semibold">Reschedule Requests</span>
                                <small class="text-muted">{{ $pendingCounts['reschedules'] }} reschedule{{ $pendingCounts['reschedules'] === 1 ? '' : 's' }} to review</small>
                            </span>
                            <span class="badge bg-dark rounded-pill">{{ $pendingCounts['reschedules'] }}</span>
                        </a>
                    </section>
                </div>
            </div>

            {{-- ==================== RECENT BOOKINGS + RECENT ACTIVITY ==================== --}}
            <div class="row g-3">
                <div class="col-lg-7">
                    <section class="surface-card h-100 mb-0">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title"><i class="bi bi-journal-text me-2"></i>Recent Bookings</h2>
                            <a href="{{ route('booking.admin.index') }}" class="small text-decoration-none">View All <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Ref</th>
                                        <th>Client</th>
                                        <th>Event Date</th>
                                        <th>Package</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentBookings as $booking)
                                        <tr>
                                            <td><code>{{ $booking->booking_ref }}</code></td>
                                            <td class="fw-semibold">{{ $booking->client_name }}</td>
                                            <td class="text-nowrap">{{ $booking->event_date->format('M d, Y') }}</td>
                                            <td>{{ $booking->package->name ?? '—' }}</td>
                                            <td><span class="badge dash-badge">{{ ucwords(str_replace('_', ' ', $booking->status)) }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No bookings yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <div class="col-lg-5">
                    <section class="surface-card h-100 mb-0">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title"><i class="bi bi-activity me-2"></i>Recent Activity</h2>
                            <a href="{{ route('activity-logs.index') }}" class="small text-decoration-none">View All <i class="bi bi-arrow-right ms-1"></i></a>
                        </div>
                        @forelse($recentActivity as $log)
                            <div class="activity-item">
                                <span class="activity-dot dot-{{ $log->user_type ?? 'system' }}"></span>
                                <div class="flex-grow-1">
                                    <div class="activity-text">{{ $log->description }}</div>
                                    <div class="activity-meta">{{ $log->actor_name ?? 'System' }} · {{ $log->created_at->format('M d, h:i A') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="bi bi-activity"></i>
                                No activity recorded yet.
                            </div>
                        @endforelse
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>

    {{-- FLASH TOASTS --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="flashToasts" style="z-index: 1080;">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="toast align-items-center text-bg-danger border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body"><i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</body>

</html>
