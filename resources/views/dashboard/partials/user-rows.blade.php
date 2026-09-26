@forelse($users as $user)
    <tr>
        <td>
            <div class="d-flex align-items-center">
                <div class="list-avatar me-2">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="fw-semibold">
                    {{ $user->name }}
                    @if($user->id === auth()->id())
                        <span class="badge bg-primary ms-1">You</span>
                    @endif
                </div>
            </div>
        </td>
        <td>{{ $user->email }}</td>
        <td><span class="badge bg-dark">Admin</span></td>
        <td>
            @include('partials.status-badge', ['status' => $user->status])
        </td>
        <td>{{ $user->created_at->format('M d, Y') }}</td>
        <td class="text-center">
            @if($user->id === auth()->id())
                <button class="btn btn-sm btn-outline-secondary rounded-3"
                    onclick="openEditModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')">
                    <i class="bi bi-pencil"></i>
                </button>
            @else
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary rounded-3" title="Edit"
                        onclick="openEditModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-{{ $user->status === 'active' ? 'warning' : 'success' }} rounded-3"
                        title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}"
                        onclick="openToggleModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->status }}')">
                        <i class="bi bi-{{ $user->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete"
                        onclick="openDeleteModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">
            <i class="bi bi-person-gear fs-1 d-block mb-2"></i>
            No admin accounts found.
        </td>
    </tr>
@endforelse
