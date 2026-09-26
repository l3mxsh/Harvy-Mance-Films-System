@forelse($teams as $team)
    @php
        $viewTeamArgs = json_encode([
            $team->id,
            $team->name,
            $team->description,
            $team->members
                ->map(fn ($m) => ['n' => $m->name, 'o' => false])
                ->concat($team->outsourcedMembers->map(fn ($o) => ['n' => $o->name, 'o' => true]))
                ->values(),
            $team->status,
            $team->created_at->format('M d, Y g:i A'),
        ], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP);
    @endphp
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
                <button class="btn btn-sm btn-outline-secondary rounded-3" title="View Details"
                    onclick='openViewTeamModal({!! $viewTeamArgs !!})'>
                    <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary rounded-3" title="Edit"
                    onclick="openEditTeamModal('{{ $team->id }}', '{{ addslashes($team->name) }}', '{{ addslashes($team->description ?? '') }}', {!! json_encode($team->members->pluck('id')->toArray()) !!}, {!! json_encode($team->outsourcedMembers->pluck('id')->toArray()) !!}, '{{ $team->status }}')">
                    <i class="bi bi-pencil"></i>
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
