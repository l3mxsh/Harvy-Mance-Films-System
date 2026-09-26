@forelse($accounts as $account)
    <div class="mobile-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center mobile-card-head">
                <div class="list-avatar me-2">
                    {{ strtoupper(substr($account->client_name, 0, 1)) }}
                </div>
                <div class="fw-semibold text-truncate">{{ $account->client_name }}</div>
            </div>
            <div class="flex-shrink-0 ms-2">
                @if($account->must_change_password)
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-key me-1"></i>First login pending
                    </span>
                @else
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Active
                    </span>
                @endif
            </div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Booking Ref</div>
            <div class="mobile-card-value">
                @if($account->booking)
                    <code>{{ $account->booking->booking_ref }}</code>
                @else
                    <span class="text-muted fst-italic">&mdash;</span>
                @endif
            </div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Email</div>
            <div class="mobile-card-value text-truncate">{{ $account->client_email }}</div>
        </div>
        <div class="mb-3">
            <div class="mobile-card-label">Contact</div>
            <div class="mobile-card-value">{{ $account->client_phone ?? '—' }}</div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" title="Archive Account"
                data-bs-toggle="modal" data-bs-target="#archiveModal"
                data-id="{{ $account->id }}" data-name="{{ $account->client_name }}"
                data-control="{{ $account->control_number }}">
                <i class="bi bi-archive"></i>
            </button>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No client accounts found.
    </div>
@endforelse
