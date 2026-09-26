@forelse($outsourcedStaff as $os)
    <tr>
        <td class="fw-semibold">{{ $os->name }}</td>
        <td>{{ $os->email ?? '—' }}</td>
        <td>{{ $os->contact_number ?? '—' }}</td>
        <td><span class="text-muted small">{{ $os->notes ?? '—' }}</span></td>
        <td>{{ $os->created_at->format('M d, Y') }}</td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-outline-secondary rounded-3" title="View Details"
                    onclick="openViewOutsourcedModal('{{ $os->id }}', '{{ addslashes($os->name) }}', '{{ addslashes($os->email ?? '') }}', '{{ $os->contact_number }}', '{{ addslashes($os->notes ?? '') }}', '{{ $os->created_at->format('M d, Y g:i A') }}')">
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-3" title="Edit"
                    onclick="openEditOutsourcedModal('{{ $os->id }}', '{{ addslashes($os->name) }}', '{{ addslashes($os->email ?? '') }}', '{{ $os->contact_number }}', '{{ addslashes($os->notes ?? '') }}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger rounded-3"
                    onclick="openDeleteOutsourcedModal('{{ $os->id }}', '{{ addslashes($os->name) }}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">
            <i class="bi bi-person-lines-fill fs-1 d-block mb-2"></i>
            No outsourced staff records found.
        </td>
    </tr>
@endforelse