@forelse($users as $user)
    <div class="mobile-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center mobile-card-head">
                <div class="list-avatar me-2">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="fw-semibold text-truncate">
                    {{ $user->name }}
                    @if($user->id === auth()->id())
                        <span class="badge bg-primary">You</span>
                    @endif
                </div>
            </div>
            <div class="flex-shrink-0 ms-2">
                @include('partials.status-badge', ['status' => $user->status])
            </div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Email</div>
            <div class="mobile-card-value text-truncate">{{ $user->email }}</div>
        </div>
        <div class="mb-3">
            <div class="mobile-card-label">Joined</div>
            <div class="mobile-card-value">{{ $user->created_at->format('M d, Y') }}</div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" title="Edit"
                onclick="openEditModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')">
                <i class="bi bi-pencil"></i>
            </button>
            @if($user->id !== auth()->id())
                <button type="button"
                    class="btn btn-sm btn-outline-{{ $user->status === 'active' ? 'warning' : 'success' }} rounded-3"
                    title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}"
                    onclick="openToggleModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ $user->status }}')">
                    <i class="bi bi-{{ $user->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-3" title="Delete"
                    onclick="openDeleteModal('{{ $user->id }}', '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')">
                    <i class="bi bi-trash"></i>
                </button>
            @endif
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-person-gear fs-1 d-block mb-2"></i>
        No admin accounts found.
    </div>
@endforelse
