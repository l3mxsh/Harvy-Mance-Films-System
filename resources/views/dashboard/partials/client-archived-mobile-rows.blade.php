@forelse($archivedAccounts as $archived)
    <div class="mobile-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center mobile-card-head">
                <div class="list-avatar me-2">
                    {{ strtoupper(substr($archived->client_name, 0, 1)) }}
                </div>
                <div class="fw-semibold text-truncate">{{ $archived->client_name }}</div>
            </div>
            <div class="flex-shrink-0 ms-2">
                <span class="badge bg-secondary">Archived</span>
            </div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Booking Ref</div>
            <div class="mobile-card-value">
                @if($archived->booking)
                    <code>{{ $archived->booking->booking_ref }}</code>
                @else
                    <span class="text-muted fst-italic">&mdash;</span>
                @endif
            </div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Email</div>
            <div class="mobile-card-value text-truncate">{{ $archived->client_email }}</div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Delivered</div>
            <div class="mobile-card-value">{{ $archived->booking?->delivered_at?->format('M d, Y') ?? '—' }}</div>
        </div>
        <div class="mb-3">
            <div class="mobile-card-label">Days Since Archive</div>
            <div>
                @php $daysArchived = (int) round($archived->archived_at->diffInDays(now())); @endphp
                @if($daysArchived === 0)
                    <span class="badge bg-secondary">Today</span>
                @else
                    <span class="badge bg-secondary">{{ $daysArchived }} day(s)</span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" title="Restore Account"
                data-bs-toggle="modal" data-bs-target="#restoreModal"
                data-id="{{ $archived->id }}" data-name="{{ $archived->client_name }}"
                data-control="{{ $archived->control_number }}">
                <i class="bi bi-arrow-counterclockwise"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-danger rounded-3" title="Delete Account"
                data-bs-toggle="modal" data-bs-target="#deleteModal"
                data-id="{{ $archived->id }}" data-name="{{ $archived->client_name }}"
                data-control="{{ $archived->control_number }}">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No archived client accounts.
    </div>
@endforelse
