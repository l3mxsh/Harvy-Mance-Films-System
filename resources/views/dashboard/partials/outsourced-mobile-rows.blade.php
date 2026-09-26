@forelse($outsourcedStaff as $os)
    <div class="mobile-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center mobile-card-head">
                <div class="staff-avatar me-2">
                    {{ strtoupper(substr($os->name, 0, 1)) }}
                </div>
                <div class="fw-semibold text-truncate">{{ $os->name }}</div>
            </div>
            <div class="flex-shrink-0 ms-2">
                <span class="badge bg-warning text-dark">Record Only</span>
            </div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Email</div>
            <div class="mobile-card-value">{{ $os->email ?? '—' }}</div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Contact</div>
            <div class="mobile-card-value">{{ $os->contact_number ?? '—' }}</div>
        </div>
        <div class="mb-3">
            <div class="mobile-card-label">Notes</div>
            <div class="mobile-card-value">{{ $os->notes ?? '—' }}</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" title="View Details"
                onclick="openViewOutsourcedModal('{{ $os->id }}', '{{ addslashes($os->name) }}', '{{ addslashes($os->email ?? '') }}', '{{ $os->contact_number }}', '{{ addslashes($os->notes ?? '') }}', '{{ $os->created_at->format('M d, Y g:i A') }}')">
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" title="Edit"
                onclick="openEditOutsourcedModal('{{ $os->id }}', '{{ addslashes($os->name) }}', '{{ addslashes($os->email ?? '') }}', '{{ $os->contact_number }}', '{{ addslashes($os->notes ?? '') }}')">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete"
                onclick="openDeleteOutsourcedModal('{{ $os->id }}', '{{ addslashes($os->name) }}')">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-person-lines-fill fs-1 d-block mb-2"></i>
        No outsourced staff records found.
    </div>
@endforelse
