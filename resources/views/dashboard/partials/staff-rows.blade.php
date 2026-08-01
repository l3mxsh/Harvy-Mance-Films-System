@forelse($staff as $member)
    <tr>
        <td>
            <div class="d-flex align-items-center">
                <div class="staff-avatar me-2">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>
                <div class="fw-semibold">{{ $member->name }}</div>
            </div>
        </td>
        <td>{{ $member->email }}</td>
        <td>{{ $member->contact_number ?? '—' }}</td>
        <td>
            @if($member->status === 'active')
                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Active</span>
            @else
                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Inactive</span>
            @endif
        </td>
        <td>{{ $member->created_at->format('M d, Y') }}</td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-outline-secondary rounded-3" title="View Details"
                    onclick="openViewModal('{{ $member->id }}', '{{ addslashes($member->name) }}', '{{ $member->email }}', '{{ $member->contact_number }}', '{{ $member->status }}', '{{ $member->created_at->format('M d, Y g:i A') }}', '{{ $member->last_login_at ? $member->last_login_at->format('M d, Y g:i A') : 'Never' }}')">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-3" title="Edit Staff"
                    onclick="openEditModal('{{ $member->id }}', '{{ addslashes($member->name) }}', '{{ $member->email }}', '{{ $member->contact_number }}', '{{ $member->status }}', {{ $member->is_outsourced ? 1 : 0 }})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete Staff"
                    onclick="openDeleteModal('{{ $member->id }}', '{{ addslashes($member->name) }}', '{{ $member->email }}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">
            <i class="bi bi-people fs-1 d-block mb-2"></i>
            No staff members found.
        </td>
    </tr>
@endforelse
