<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Staff Management</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            {{-- ==================== SUMMARY CARDS ==================== --}}
            @if($tab === 'teams')
                <div class="row g-3 mb-4">
                    <div class="col-lg-6 col-md-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Total Teams</h6>
                                    <h4 class="mb-0 fw-bold">{{ $totalTeams }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Active Teams</h6>
                                    <h4 class="mb-0 fw-bold">{{ $activeTeams }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row g-3 mb-4">
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Total In-House</h6>
                                    <h4 class="mb-0 fw-bold">{{ $totalStaff }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Active</h6>
                                    <h4 class="mb-0 fw-bold">{{ $activeStaff }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-person-x"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Inactive</h6>
                                    <h4 class="mb-0 fw-bold">{{ $inactiveStaff }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Outsourced</h6>
                                    <h4 class="mb-0 fw-bold">{{ $outsourcedStaff->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ==================== TABS ==================== --}}
            <ul class="nav nav-pills mb-4" id="staffTabs">
                <li class="nav-item">
                    <a class="nav-link {{ in_array($tab, ['in-house', 'outsourced']) ? '' : 'active' }}"
                        href="{{ route('staff.admin.index') }}">
                        <i class="bi bi-people me-1"></i> All Staff
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'in-house' ? 'active' : '' }}"
                        href="{{ route('staff.admin.index', ['tab' => 'in-house']) }}">
                        <i class="bi bi-person-badge me-1"></i> In-House Staff
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'outsourced' ? 'active' : '' }}"
                        href="{{ route('staff.admin.index', ['tab' => 'outsourced']) }}">
                        <i class="bi bi-person-lines-fill me-1"></i> Outsourced Staff
                        <span class="badge bg-success text-light ms-1">Record Only</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'teams' ? 'active' : '' }}"
                        href="{{ route('staff.admin.index', ['tab' => 'teams']) }}">
                        <i class="bi bi-people-fill me-1"></i> Teams
                    </a>
                </li>
            </ul>

            {{-- ==================== SEARCH BAR ==================== --}}
            @if(!in_array($tab, ['outsourced', 'teams']))
                <section class="surface-card mb-4">
                    <div class="row g-2 align-items-center">
                        <div class="col-9 col-md-9 col-lg-10">
                            <input type="text" id="staffSearchInput" class="form-control"
                                placeholder="Search by name or email..." value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <div class="col-3 col-md-3 col-lg-2">
                            <select id="staffStatusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                </section>
            @endif

            {{-- ==================== OUTSOURCED STAFF TABLE ==================== --}}
            @if($tab === 'outsourced')
                <section class="surface-card">
                    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="section-title">
                            <i class="bi bi-person-lines-fill me-2"></i>Outsourced Staff
                            <span class="badge bg-warning text-dark ms-1">Record Only</span>
                        </h2>
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill"
                            onclick="openCreateOutsourcedModal()">
                            <i class="bi bi-plus-circle me-1"></i> Add Outsourced
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Notes</th>
                                    <th>Added</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outsourcedStaff as $os)
                                    <tr>
                                        <td class="fw-semibold">{{ $os->name }}</td>
                                        <td>{{ $os->email ?? '—' }}</td>
                                        <td>{{ $os->contact_number ?? '—' }}</td>
                                        <td><span class="text-muted small">{{ $os->notes ?? '—' }}</span></td>
                                        <td>{{ $os->created_at->format('M d, Y') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-outline-secondary rounded-3"
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
                                            No outsourced staff records yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            {{-- ==================== TEAMS TABLE ==================== --}}
            @if($tab === 'teams')
                <section class="surface-card mb-4">
                    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="section-title"><i class="bi bi-people-fill me-2"></i>All Teams</h2>
                        <span class="badge bg-light text-dark border">{{ $teams->total() }} total</span>
                    </div>
                    <form method="GET" action="{{ route('staff.admin.index') }}" id="teamFilterForm"
                        class="row g-2 align-items-end">
                        <input type="hidden" name="tab" value="teams">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search teams..."
                                value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-1">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-1">
                            <button type="button" class="btn btn-dark w-100 rounded-3" onclick="openCreateTeamModal()">Add
                                Team
                            </button>
                        </div>
                    </form>
                </section>

                <section class="surface-card">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
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
                                                    <i
                                                        class="bi bi-{{ $team->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
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
                            </tbody>
                        </table>
                    </div>
                    @if($teams->hasPages())
                        <div class="border-top pt-3 mt-3">{{ $teams->links() }}</div>
                    @endif
                </section>
            @endif

            {{-- ==================== IN-HOUSE STAFF TABLE ==================== --}}
            @if(!in_array($tab, ['outsourced', 'teams']))
                <section class="surface-card">
                    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="section-title">
                            <i class="bi bi-people me-2"></i>{{ $tab === 'in-house' ? 'In-House Staff' : 'All Staff' }}
                        </h2>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border" id="staffTotalBadge">{{ $staff->total() }}
                                total</span>
                            <button type="button" class="btn btn-dark rounded-pill" onclick="openCreateModal()">
                                <i class="bi bi-plus-circle me-1"></i> Add Staff
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="staffTableBody">
                                @include('dashboard.partials.staff-rows', compact('staff'))
                            </tbody>
                        </table>
                    </div>
                    <div id="staffPagination" class="border-top pt-3 mt-3 @if(!$staff->hasPages()) d-none @endif">
                        @if($staff->hasPages())
                            {{ $staff->links() }}
                        @endif
                    </div>
                </section>
            @endif
        </div>
    </div>

    @include('dashboard.partials.staff-modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/staff.js') }}"></script>

    {{-- FLASH TOASTS --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="flashToasts" style="z-index: 1080;">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="toast align-items-center text-bg-danger border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('#flashToasts .toast').forEach(function (el) {
                new bootstrap.Toast(el, { delay: 4000 }).show();
            });
        });
    </script>
</body>

</html>