@forelse($teams as $team)
    <tr>
        <td class="fw-semibold">{{ $team->name }}</td>
        <td class="text-muted">{{ $team->description ?? '—' }}</td>
        <td>
            @forelse($team->members as $member)
                <span class="member-chip">{{ $member->name }}</span>
            @empty
            @endforelse
            @foreach($team->outsourcedMembers as $os)
                <span class="member-chip member-chip-os">{{ $os->name }} <small>(OS)</small></span>
            @endforeach
            @if($team->members->isEmpty() && $team->outsourcedMembers->isEmpty())
                <span class="text-muted fst-italic">No members</span>
            @endif
        </td>
        <td>
            @include('partials.status-badge', ['status' => $team->status])
        </td>
        <td>{{ $team->created_at->format('M d, Y') }}</td>
        <td class="text-center">
            <div class="d-flex justify-content-center gap-2">
                <button class="btn btn-sm btn-outline-secondary rounded-3" title="Edit"
                    onclick="openEditTeamModal('{{ $team->id }}', '{{ addslashes($team->name) }}', '{{ addslashes($team->description ?? '') }}', {!! json_encode($team->members->pluck('id')->toArray()) !!}, {!! json_encode($team->outsourcedMembers->pluck('id')->toArray()) !!})">
                    <i class="bi bi-pencil"></i>
                </button>
                <button
                    class="btn btn-sm btn-outline-{{ $team->status === 'active' ? 'warning' : 'success' }} rounded-3"
                    title="{{ $team->status === 'active' ? 'Deactivate' : 'Activate' }}"
                    onclick="openToggleTeamModal('{{ $team->id }}', '{{ addslashes($team->name) }}', '{{ $team->status }}')">
                    <i class="bi bi-{{ $team->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete"
                    onclick="openDeleteTeamModal('{{ $team->id }}', '{{ addslashes($team->name) }}')">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="text-center py-4 text-muted">
            <i class="bi bi-people-fill fs-1 d-block mb-2"></i>
            No teams found. Create one to get started.
        </td>
    </tr>
@endforelse
