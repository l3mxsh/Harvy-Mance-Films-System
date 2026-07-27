<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <style>
        .member-chip {
            display: inline-block; padding: 2px 10px; border-radius: 20px;
            font-size: 0.78rem; margin: 2px 2px; background: #e9ecef; color: #495057;
        }
        .member-chip.selected { background: #1a1a2e; color: #fff; }
    </style>
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Team Management</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- SUMMARY --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-6 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.3rem;">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1">Total Teams</h6>
                                    <h4 class="mb-0 fw-bold">{{ $totalTeams }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:1.3rem;">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1">Active Teams</h6>
                                    <h4 class="mb-0 fw-bold">{{ $activeTeams }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SEARCH + ADD --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('team.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small text-muted">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search teams..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-dark w-100"><i class="bi bi-funnel me-1"></i> Filter</button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary w-100" onclick="openCreateModal()">
                                <i class="bi bi-plus-circle me-1"></i> Add Team
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- TEAMS TABLE --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>All Teams</h6>
                    <span class="badge bg-secondary">{{ $teams->total() }} total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Team Name</th>
                                    <th>Description</th>
                                    <th>Members</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($teams as $team)
                                    <tr>
                                        <td class="fw-semibold">{{ $team->name }}</td>
                                        <td class="text-muted">{{ $team->description ?? '—' }}</td>
                                        <td>
                                            @forelse($team->members as $member)
                                                <span class="member-chip">{{ $member->name }}</span>
                                            @empty
                                                <span class="text-muted fst-italic">No members</span>
                                            @endforelse
                                        </td>
                                        <td>
                                            @if($team->status === 'active')
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Active</span>
                                            @else
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $team->created_at->format('M d, Y') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-secondary btn-sm" title="Edit"
                                                    onclick="openEditModal('{{ $team->id }}', '{{ addslashes($team->name) }}', '{{ addslashes($team->description ?? '') }}', {!! json_encode($team->members->pluck('id')->toArray()) !!})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-{{ $team->status === 'active' ? 'warning' : 'success' }} btn-sm"
                                                    title="{{ $team->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                                    onclick="openToggleStatusModal('{{ $team->id }}', '{{ addslashes($team->name) }}', '{{ $team->status }}')">
                                                    <i class="bi bi-{{ $team->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" title="Delete"
                                                    onclick="openDeleteModal('{{ $team->id }}', '{{ addslashes($team->name) }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                                            No teams found. Create one to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($teams->hasPages())
                    <div class="card-footer bg-white border-top">{{ $teams->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- CREATE TEAM MODAL --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-people me-2"></i>Create Team</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('team.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Team Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Team Alpha">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief description of this team..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Members</label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                @forelse($allStaff as $s)
                                    <div class="form-check">
                                        <input class="form-check-input create-member-check" type="checkbox" name="member_ids[]" value="{{ $s->id }}" id="create_member_{{ $s->id }}">
                                        <label class="form-check-label" for="create_member_{{ $s->id }}">
                                            {{ $s->name }} <small class="text-muted">({{ $s->email }})</small>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No active staff found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Create Team</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- EDIT TEAM MODAL --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Team</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editTeamForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Team Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editTeamName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="editTeamDesc" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Members</label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="editMembersList">
                                @foreach($allStaff as $s)
                                    <div class="form-check">
                                        <input class="form-check-input edit-member-check" type="checkbox" name="member_ids[]" value="{{ $s->id }}" id="edit_member_{{ $s->id }}">
                                        <label class="form-check-label" for="edit_member_{{ $s->id }}">
                                            {{ $s->name }} <small class="text-muted">({{ $s->email }})</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-secondary"><i class="bi bi-check-lg me-1"></i> Update Team</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TOGGLE STATUS MODAL --}}
    <div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" id="toggleStatusHeader">
                    <h5 class="modal-title" id="toggleStatusTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="toggleStatusForm">
                    @csrf
                    <div class="modal-body"><p id="toggleStatusMessage"></p></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="toggleStatusBtn">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- DELETE TEAM MODAL --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Delete Team</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to permanently delete <strong id="deleteTeamName"></strong>?</p>
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            This action cannot be undone. Members will be unlinked but not deleted.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Delete Team</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        function openCreateModal() {
            document.querySelector('#createModal form').reset();
            new bootstrap.Modal(document.getElementById('createModal')).show();
        }

        function openEditModal(id, name, desc, memberIds) {
            document.getElementById('editTeamForm').action = '/admin/team/' + id;
            document.getElementById('editTeamName').value = name;
            document.getElementById('editTeamDesc').value = desc;
            document.querySelectorAll('.edit-member-check').forEach(cb => {
                cb.checked = memberIds.includes(parseInt(cb.value));
            });
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        function openToggleStatusModal(id, name, status) {
            var isDeactivating = status === 'active';
            document.getElementById('toggleStatusHeader').className = 'modal-header ' + (isDeactivating ? 'bg-warning' : 'bg-success');
            document.getElementById('toggleStatusTitle').innerHTML = isDeactivating
                ? '<i class="bi bi-pause-circle me-2"></i>Deactivate Team'
                : '<i class="bi bi-play-circle me-2"></i>Activate Team';
            document.getElementById('toggleStatusMessage').innerHTML = isDeactivating
                ? 'Deactivate <strong>' + name + '</strong>? It cannot be assigned to new bookings while inactive.'
                : 'Activate <strong>' + name + '</strong>? It will be available for booking assignments.';
            document.getElementById('toggleStatusBtn').className = 'btn ' + (isDeactivating ? 'btn-warning' : 'btn-success');
            document.getElementById('toggleStatusForm').action = '/admin/team/' + id + '/toggle-status';
            new bootstrap.Modal(document.getElementById('toggleStatusModal')).show();
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteForm').action = '/admin/team/' + id;
            document.getElementById('deleteTeamName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>

</html>
