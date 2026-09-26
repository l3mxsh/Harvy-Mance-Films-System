{{-- Bookings tab: mobile cards --}}
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
