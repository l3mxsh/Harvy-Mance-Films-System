{{-- Performance tab: most booked packages + most selected add-ons --}}
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
<section class="surface-card">
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
