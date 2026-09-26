@forelse($upcomingDeletions as $item)
    <div class="mobile-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center mobile-card-head">
                <div class="list-avatar me-2">
                    {{ strtoupper(substr($item['booking']->client_name, 0, 1)) }}
                </div>
                <div class="fw-semibold text-truncate">{{ $item['booking']->client_name }}</div>
            </div>
            <div class="flex-shrink-0 ms-2">
                @if($item['days_remaining'] <= 3)
                    <span class="badge bg-danger">{{ $item['days_remaining'] }} day(s)</span>
                @elseif($item['days_remaining'] <= 7)
                    <span class="badge bg-warning text-dark">{{ $item['days_remaining'] }} day(s)</span>
                @else
                    <span class="badge bg-success">{{ $item['days_remaining'] }} day(s)</span>
                @endif
            </div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Booking Ref</div>
            <div class="mobile-card-value"><code>{{ $item['booking']->booking_ref }}</code></div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Email</div>
            <div class="mobile-card-value text-truncate">{{ $item['booking']->client_email }}</div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Delivered</div>
            <div class="mobile-card-value">{{ $item['delivered_at']->format('M d, Y') }}</div>
        </div>
        <div class="mb-3">
            <div class="mobile-card-label">Auto-Archive</div>
            <div class="mobile-card-value">{{ $item['delete_at']->format('M d, Y') }}</div>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No delivered bookings with active client accounts.
    </div>
@endforelse
