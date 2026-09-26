{{-- Bookings tab: quick money strip, search/status filters, list + pagination --}}
<div class="row g-3 mb-4">
    <div class="col-lg-4 col-md-4 col-12">
        <div class="summary-card">
            <div class="d-flex align-items-center">
                <div class="summary-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="ms-3">
                    <h6 class="text-muted mb-1 small">Revenue in Range</h6>
                    <h4 class="mb-0 fw-bold">&#8369;{{ number_format($summary['revenue'], 2) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-4 col-12">
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
    <div class="col-lg-4 col-md-4 col-12">
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

<section class="surface-card">
    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h2 class="section-title"><i class="bi bi-list-ul me-2"></i>Bookings in Range</h2>
        <span class="badge bg-light text-dark border" id="reportBookingTotalBadge">{{ $bookings->total() }} total</span>
    </div>

    <div class="row g-2 align-items-end mb-3">
        <div class="col-12 col-md-6">
            <label class="form-label small text-muted mb-1" for="reportBookingSearch">Search</label>
            <input type="text" id="reportBookingSearch" class="form-control" placeholder="Search ref, name or email..."
                value="{{ request('q') }}" autocomplete="off">
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label small text-muted mb-1" for="reportBookingStatus">Status</label>
            <select id="reportBookingStatus" class="form-select">
                <option value="">All Status</option>
                @foreach(['pending', 'approved', 'ongoing', 'completed', 'delivered', 'cancelled', 'rejected'] as $statusOption)
                    <option value="{{ $statusOption }}" {{ request('status') === $statusOption ? 'selected' : '' }}>
                        {{ ucfirst($statusOption) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Desktop table (hidden on mobile) --}}
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
            <tbody id="reportBookingTableBody">
                @include('dashboard.partials.report-booking-rows')
            </tbody>
        </table>
    </div>

    {{-- Mobile cards (hidden on desktop) --}}
    <div class="d-md-none" id="reportBookingMobileBody">
        @include('dashboard.partials.report-booking-mobile-rows')
    </div>

    <div id="reportBookingPagination" class="mt-3 @if(!$bookings->hasPages()) d-none @endif">
        @if($bookings->hasPages())
            {{ $bookings->links('vendor.pagination.bootstrap-5') }}
        @endif
    </div>
</section>
