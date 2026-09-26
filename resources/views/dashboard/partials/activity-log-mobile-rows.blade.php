@forelse($logs as $log)
    <div class="mobile-card">
        <div class="d-flex align-items-center mb-3">
            <div class="d-flex align-items-center mobile-card-head">
                <div class="list-avatar me-2 avatar-{{ $log->user_type ?? 'system' }}">
                    {{ strtoupper(substr($log->actor_name ?? 'System', 0, 1)) }}
                </div>
                <div>
                    <div class="fw-semibold text-truncate">{{ $log->actor_name ?? 'System' }}</div>
                    <small class="text-muted d-block text-truncate">{{ $log->friendly_action }}</small>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <div class="mobile-card-label">Timestamp</div>
            <div class="mobile-card-value">{{ $log->created_at->format('M d, Y · h:i A') }}</div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 w-100" title="View details"
                onclick="openLogModal(this)"
                data-id="{{ $log->id }}"
                data-actor="{{ $log->actor_name ?? 'System' }}"
                data-type="{{ $log->user_type ?? 'system' }}"
                data-action="{{ $log->friendly_action }}"
                data-time="{{ $log->created_at->format('M d, Y · h:i A') }}"
                data-description="{{ $log->description }}">
                <i class="bi bi-eye me-1"></i>View Details
            </button>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-activity fs-1 d-block mb-2"></i>
        No activity logs found.
    </div>
@endforelse
