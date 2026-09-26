{{-- Overview tab: booking + finance summary cards, booking trend --}}
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
<section class="surface-card">
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
            <div class="report-trend-col"
                title="{{ $row['label'] }}: {{ $row['bookings'] }} booking(s), &#8369;{{ number_format($row['revenue'], 2) }}">
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
