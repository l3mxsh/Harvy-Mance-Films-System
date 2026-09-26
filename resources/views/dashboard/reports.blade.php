<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}?v=8">
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}?v=1">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Reports</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">

            {{-- ==================== HEADER ==================== --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div>
                    <h1 class="section-title fs-3 mb-1">Business Reports</h1>
                    <p class="section-sub mb-0">
                        {{ $from->format('M d, Y') }} &ndash; {{ $to->format('M d, Y') }}
                        <span class="text-muted">({{ $trend['granularity'] === 'day' ? 'daily' : 'monthly' }} view)</span>
                    </p>
                </div>
                <button type="button" class="btn btn-outline-dark rounded-pill no-print" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print Report
                </button>
            </div>

            {{-- ==================== DATE RANGE FILTER ==================== --}}
            <section class="surface-card mb-4 no-print">
                <form method="GET" action="{{ route('reports.index') }}" class="report-range-form">
                    <div class="report-range-presets">
                        <button type="submit" name="range" value="today"
                            class="btn btn-outline-dark {{ $range === 'today' ? 'active' : '' }}">Today</button>
                        <button type="submit" name="range" value="week"
                            class="btn btn-outline-dark {{ $range === 'week' ? 'active' : '' }}">This Week</button>
                        <button type="submit" name="range" value="month"
                            class="btn btn-outline-dark {{ $range === 'month' ? 'active' : '' }}">This Month</button>
                        <button type="submit" name="range" value="quarter"
                            class="btn btn-outline-dark {{ $range === 'quarter' ? 'active' : '' }}">This Quarter</button>
                        <button type="submit" name="range" value="year"
                            class="btn btn-outline-dark {{ $range === 'year' ? 'active' : '' }}">This Year</button>
                    </div>

                    <div class="d-flex flex-wrap gap-2 align-items-end ms-auto">
                        <div>
                            <label class="form-label small text-muted mb-1" for="reportFrom">From</label>
                            <input type="date" class="form-control form-control-sm" id="reportFrom" name="from"
                                value="{{ request('from', $from->format('Y-m-d')) }}">
                        </div>
                        <div>
                            <label class="form-label small text-muted mb-1" for="reportTo">To</label>
                            <input type="date" class="form-control form-control-sm" id="reportTo" name="to"
                                value="{{ request('to', $to->format('Y-m-d')) }}">
                        </div>
                        <button type="submit" name="range" value="custom"
                            class="btn btn-dark btn-sm rounded-pill {{ $range === 'custom' ? 'active' : '' }}">Apply</button>
                    </div>
                </form>
                <p class="report-range-note">
                    Bookings are counted by <strong>event date</strong>. Revenue is counted by
                    <strong>verification date</strong> of verified payments, refunds by <strong>processed date</strong>.
                </p>
            </section>

            {{-- ==================== BOOKING SUMMARY ==================== --}}
            <h2 class="section-title mb-3"><i class="bi bi-journal-check me-2"></i>Bookings</h2>
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-journal-text"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Total Bookings</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['totalBookings'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Completed</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['completedBookings'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Cancelled</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['cancelledBookings'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Pending</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['pendingBookings'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-arrow-repeat"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Active (Approved / Ongoing)</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['activeBookings'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-secondary bg-opacity-10 text-secondary">
                                <i class="bi bi-slash-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Rejected</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['rejectedBookings'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-dark bg-opacity-10 text-dark">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Booked Value</h6>
                                <h4 class="mb-0 fw-bold">&#8369;{{ number_format($summary['bookedValue'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-secondary bg-opacity-10 text-secondary">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Outstanding Balance</h6>
                                <h4 class="mb-0 fw-bold">&#8369;{{ number_format($summary['outstandingBalance'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== FINANCIAL SUMMARY ==================== --}}
            <h2 class="section-title mb-3"><i class="bi bi-cash-coin me-2"></i>Payments &amp; Refunds</h2>
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-graph-up-arrow"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Total Revenue</h6>
                                <h4 class="mb-0 fw-bold">&#8369;{{ number_format($summary['revenue'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-credit-card"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Downpayments (Verified)</h6>
                                <h4 class="mb-0 fw-bold">&#8369;{{ number_format($summary['downpayments'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-dark bg-opacity-10 text-dark">
                                <i class="bi bi-patch-check"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Final Payments (Verified)</h6>
                                <h4 class="mb-0 fw-bold">&#8369;{{ number_format($summary['finalPayments'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Refunds ({{ $summary['refundCount'] }})</h6>
                                <h4 class="mb-0 fw-bold">&#8369;{{ number_format($summary['refunds'], 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-hourglass-bottom"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Payments Awaiting Review</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['pendingPayments'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== TREND ==================== --}}
            <section class="surface-card mb-4">
                <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="section-title"><i class="bi bi-bar-chart-line me-2"></i>Booking Trend</h2>
                    <span class="badge bg-light text-dark border">{{ $trend['granularity'] === 'day' ? 'Per Day' : 'Per Month' }}</span>
                </div>

                <div class="report-trend">
                    @foreach($trend['rows'] as $row)
                        @php
                            $barHeight = $row['bookings'] > 0
                                ? max(4, (int) round(($row['bookings'] / $trend['maxBookings']) * 150))
                                : 3;
                        @endphp
                        <div class="report-trend-col" title="{{ $row['label'] }}: {{ $row['bookings'] }} booking(s), &#8369;{{ number_format($row['revenue'], 2) }}">
                            <span class="report-trend-value">{{ $row['bookings'] }}</span>
                            <div class="report-trend-bar {{ $row['bookings'] > 0 ? '' : 'is-empty' }}"
                                style="height: {{ $barHeight }}px;"></div>
                        </div>
                    @endforeach
                </div>
                <div class="report-trend-axis d-flex justify-content-between small text-muted">
                    <span>{{ $trend['firstLabel'] }}</span>
                    <span>{{ $trend['lastLabel'] }}</span>
                </div>

                <div class="table-responsive mt-3 d-none d-md-block">
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th class="text-center">Bookings</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trend['rows'] as $row)
                                <tr>
                                    <td>{{ $row['label'] }}</td>
                                    <td class="text-center">{{ $row['bookings'] }}</td>
                                    <td class="text-end">&#8369;{{ number_format($row['revenue'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No data for this range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-md-none mt-3">
                    @forelse($trend['rows'] as $row)
                        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                            <span class="small">{{ $row['label'] }}</span>
                            <span class="small">
                                <strong>{{ $row['bookings'] }}</strong>
                                <span class="text-muted">&middot; &#8369;{{ number_format($row['revenue'], 2) }}</span>
                            </span>
                        </div>
                    @empty
                        <div class="empty-state">No data for this range.</div>
                    @endforelse
                </div>
            </section>

            {{-- ==================== MOST BOOKED PACKAGES ==================== --}}
            <section class="surface-card mb-4">
                <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="section-title"><i class="bi bi-box-seam me-2"></i>Most Booked Packages</h2>
                    <span class="badge bg-light text-dark border">Top {{ $topPackages->count() }}</span>
                </div>

                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:48px;">#</th>
                                <th>Package</th>
                                <th class="text-center">Bookings</th>
                                <th class="text-end">Booked Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPackages as $index => $package)
                                <tr>
                                    <td><span class="report-rank {{ $index < 3 ? 'top' : '' }}">{{ $index + 1 }}</span></td>
                                    <td class="fw-semibold">{{ $package->name }}</td>
                                    <td class="text-center">{{ $package->bookings_count }}</td>
                                    <td class="text-end">&#8369;{{ number_format($package->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No package data for this range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-md-none">
                    @forelse($topPackages as $index => $package)
                        <div class="mobile-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold">{{ $package->name }}</div>
                                <span class="report-rank {{ $index < 3 ? 'top' : '' }}">{{ $index + 1 }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="mobile-card-label mb-0">Bookings</span>
                                <span class="mobile-card-value">{{ $package->bookings_count }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="mobile-card-label mb-0">Booked Value</span>
                                <span class="mobile-card-value">&#8369;{{ number_format($package->revenue, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No package data for this range.</div>
                    @endforelse
                </div>
            </section>

            {{-- ==================== MOST SELECTED ADD-ONS ==================== --}}
            <section class="surface-card mb-4">
                <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="section-title"><i class="bi bi-plus-square me-2"></i>Most Selected Add-Ons</h2>
                    <span class="badge bg-light text-dark border">Top {{ $topAddons->count() }}</span>
                </div>

                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:48px;">#</th>
                                <th>Add-On</th>
                                <th class="text-center">Times Booked</th>
                                <th class="text-end">Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topAddons as $index => $addon)
                                <tr>
                                    <td><span class="report-rank {{ $index < 3 ? 'top' : '' }}">{{ $index + 1 }}</span></td>
                                    <td class="fw-semibold">{{ $addon->name }}</td>
                                    <td class="text-center">{{ $addon->times_booked }}</td>
                                    <td class="text-end">&#8369;{{ number_format($addon->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No add-on data for this range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-md-none">
                    @forelse($topAddons as $index => $addon)
                        <div class="mobile-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold">{{ $addon->name }}</div>
                                <span class="report-rank {{ $index < 3 ? 'top' : '' }}">{{ $index + 1 }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="mobile-card-label mb-0">Times Booked</span>
                                <span class="mobile-card-value">{{ $addon->times_booked }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="mobile-card-label mb-0">Revenue</span>
                                <span class="mobile-card-value">&#8369;{{ number_format($addon->revenue, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No add-on data for this range.</div>
                    @endforelse
                </div>
            </section>

            {{-- ==================== BOOKINGS IN RANGE ==================== --}}
            <section class="surface-card">
                <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="section-title"><i class="bi bi-list-ul me-2"></i>Bookings in Range</h2>
                    <span class="badge bg-light text-dark border">{{ $bookings->total() }} total</span>
                </div>

                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Client</th>
                                <th>Event Date</th>
                                <th>Package</th>
                                <th>Status</th>
                                <th class="text-end">Total</th>
                                <th class="text-end">Paid</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                @php $balance = max(0, (float) $booking->total_price - (float) $booking->paid_amount); @endphp
                                <tr>
                                    <td><code>{{ $booking->booking_ref }}</code></td>
                                    <td class="fw-semibold">{{ $booking->client_name }}</td>
                                    <td class="text-nowrap">{{ $booking->event_date->format('M d, Y') }}</td>
                                    <td>{{ $booking->package->name ?? '—' }}</td>
                                    <td>@include('partials.status-badge', ['status' => $booking->status])</td>
                                    <td class="text-end">&#8369;{{ number_format($booking->total_price, 2) }}</td>
                                    <td class="text-end text-success">&#8369;{{ number_format($booking->paid_amount, 2) }}</td>
                                    <td class="text-end {{ $balance > 0 ? 'text-danger' : '' }}">&#8369;{{ number_format($balance, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No bookings in this date range.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-md-none">
                    @forelse($bookings as $booking)
                        @php $balance = max(0, (float) $booking->total_price - (float) $booking->paid_amount); @endphp
                        <div class="mobile-card">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="mobile-card-head">
                                    <div class="fw-semibold text-truncate">{{ $booking->client_name }}</div>
                                    <small class="text-muted"><code>{{ $booking->booking_ref }}</code></small>
                                </div>
                                <div class="flex-shrink-0 ms-2">
                                    @include('partials.status-badge', ['status' => $booking->status])
                                </div>
                            </div>
                            <div class="mb-2">
                                <div class="mobile-card-label">Event Date</div>
                                <div class="mobile-card-value">{{ $booking->event_date->format('M d, Y') }}</div>
                            </div>
                            <div class="mb-2">
                                <div class="mobile-card-label">Package</div>
                                <div class="mobile-card-value">{{ $booking->package->name ?? '—' }}</div>
                            </div>
                            <div class="d-flex justify-content-between border-top pt-2">
                                <span class="mobile-card-label mb-0">Total</span>
                                <span class="mobile-card-value">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="mobile-card-label mb-0">Paid</span>
                                <span class="mobile-card-value text-success">&#8369;{{ number_format($booking->paid_amount, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="mobile-card-label mb-0">Balance</span>
                                <span class="mobile-card-value {{ $balance > 0 ? 'text-danger' : '' }}">&#8369;{{ number_format($balance, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">No bookings in this date range.</div>
                    @endforelse
                </div>

                @if($bookings->hasPages())
                    <div class="mt-3">
                        {{ $bookings->links('vendor.pagination.bootstrap-5') }}
                    </div>
                @endif
            </section>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>

</html>
