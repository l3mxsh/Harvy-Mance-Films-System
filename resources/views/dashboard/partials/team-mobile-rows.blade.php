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
    <div class="mobile-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="mobile-card-head">
                <div class="fw-semibold text-truncate">{{ $team->name }}</div>
                <small class="text-muted d-block text-truncate">{{ $team->description ?? '—' }}</small>
            </div>
            <div class="flex-shrink-0 ms-2">
                @include('partials.status-badge', ['status' => $team->status])
            </div>
        </div>
        <div class="mb-2">
            <div class="mobile-card-label">Members</div>
            <div class="d-flex flex-wrap gap-1">
                @forelse($team->members as $member)
                    <span class="member-chip">{{ $member->name }}</span>
                @empty
                @endforelse
                @foreach($team->outsourcedMembers as $os)
                    <span class="member-chip member-chip-os">{{ $os->name }} <small>(OS)</small></span>
                @endforeach
                @if($team->members->isEmpty() && $team->outsourcedMembers->isEmpty())
                    <span class="text-muted fst-italic small">No members</span>
                @endif
            </div>
        </div>
        <div class="mb-3">
            <div class="mobile-card-label">Created</div>
            <div class="mobile-card-value">{{ $team->created_at->format('M d, Y') }}</div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" title="View Details"
                onclick='openViewTeamModal({!! $viewTeamArgs !!})'>
                <i class="bi bi-eye"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary rounded-3 flex-fill" title="Edit Team"
                onclick="openEditTeamModal('{{ $team->id }}', '{{ addslashes($team->name) }}', '{{ addslashes($team->description ?? '') }}', {!! json_encode($team->members->pluck('id')->toArray()) !!}, {!! json_encode($team->outsourcedMembers->pluck('id')->toArray()) !!}, '{{ $team->status }}')">
                <i class="bi bi-pencil"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete Team"
                onclick="openDeleteTeamModal('{{ $team->id }}', '{{ addslashes($team->name) }}')">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-people-fill fs-1 d-block mb-2"></i>
        No teams found. Create one to get started.
    </div>
@endforelse
