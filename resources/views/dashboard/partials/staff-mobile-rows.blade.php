@forelse($staff as $member)
    <div class="mobile-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center mobile-card-head">
                <div class="staff-avatar me-2">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>
                <div class="fw-semibold text-truncate">{{ $member->name }}</div>
            </div>
            <div class="flex-shrink-0 ms-2">
                @include('partials.status-badge', ['status' => $member->status])
            </div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Email</div>
            <div class="mobile-card-value">{{ $member->email }}</div>
        </div>
        <div class="mb-3">
            <div class="mobile-card-label">Contact</div>
            <div class="mobile-card-value">{{ $member->contact_number ?? '—' }}</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" title="View Details"
                onclick="openViewModal('{{ $member->id }}', '{{ addslashes($member->name) }}', '{{ $member->email }}', '{{ $member->contact_number }}', '{{ $member->status }}', '{{ $member->created_at->format('M d, Y g:i A') }}', '{{ $member->last_login_at ? $member->last_login_at->format('M d, Y g:i A') : 'Never' }}')">
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" title="Edit Staff"
                onclick="openEditModal('{{ $member->id }}', '{{ addslashes($member->name) }}', '{{ $member->email }}', '{{ $member->contact_number }}', '{{ $member->status }}', {{ $member->is_outsourced ? 1 : 0 }})">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete Staff"
                onclick="openDeleteModal('{{ $member->id }}', '{{ addslashes($member->name) }}', '{{ $member->email }}')">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-people fs-1 d-block mb-2"></i>
        No staff members found.
    </div>
@endforelse
