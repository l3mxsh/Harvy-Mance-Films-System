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
                        <span class="badge bg-warning text-dark ms-1">Record Only</span>
                    </a>
                </li>
            </ul>

            {{-- ==================== SEARCH BAR ==================== --}}
            @if($tab !== 'outsourced')
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
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
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

            {{-- ==================== IN-HOUSE STAFF TABLE ==================== --}}
            @if($tab !== 'outsourced')
                <section class="surface-card">
                    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="section-title">
                            <i class="bi bi-people me-2"></i>{{ $tab === 'in-house' ? 'In-House Staff' : 'All Staff' }}
                        </h2>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border" id="staffTotalBadge">{{ $staff->total() }} total</span>
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

    {{-- ==================== CREATE OUTSOURCED MODAL ==================== --}}
    <div class="modal fade" id="createOutsourcedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-badge me-2 text-warning"></i>Add Outsourced Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('outsourced-staff.store') }}">
                    @csrf
                    <div class="modal-body">
                        <p class="text-muted small mb-3">Record only — no login account. Used for shoot-day assignments and post-production tracking.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. staff@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" placeholder="e.g. 09XXXXXXXXX">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" name="notes" class="form-control" placeholder="e.g. Photographer, Videographer...">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-pill"><i class="bi bi-check-lg me-1"></i>Add Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== EDIT OUTSOURCED MODAL ==================== --}}
    <div class="modal fade" id="editOutsourcedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2 text-secondary"></i>Edit Outsourced Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editOutsourcedForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editOsName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" id="editOsEmail" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact_number" id="editOsContact" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes</label>
                            <input type="text" name="notes" id="editOsNotes" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-dark rounded-pill"><i class="bi bi-check-lg me-1"></i>Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== DELETE OUTSOURCED MODAL ==================== --}}
    <div class="modal fade" id="deleteOutsourcedModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-trash me-2 text-danger"></i>Delete Outsourced Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="deleteOutsourcedForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Delete record for <strong id="deleteOsName"></strong>? This cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill"><i class="bi bi-trash me-1"></i>Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== VIEW STAFF MODAL ==================== --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-badge me-2 text-primary"></i>Staff Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless mb-0">
                        <tr><td class="text-muted" style="width:140px;">Name</td><td class="fw-semibold" id="viewName"></td></tr>
                        <tr><td class="text-muted">Email</td><td id="viewEmail"></td></tr>
                        <tr><td class="text-muted">Contact</td><td id="viewContact"></td></tr>
                        <tr><td class="text-muted">Status</td><td id="viewStatus"></td></tr>
                        <tr><td class="text-muted">Joined</td><td id="viewJoined"></td></tr>
                        <tr><td class="text-muted">Last Login</td><td id="viewLastLogin"></td></tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== CREATE STAFF MODAL ==================== --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2 text-primary"></i>Add Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('staff.admin.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="staff@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact_number" class="form-control" placeholder="e.g. 09XXXXXXXXX">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6" placeholder="Minimum 6 characters">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" class="form-control" required minlength="6" placeholder="Re-enter password">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-dark rounded-pill"><i class="bi bi-check-lg me-1"></i> Create Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== EDIT STAFF MODAL ==================== --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2 text-secondary"></i>Edit Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body pb-0">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="editEmail" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contact Number</label>
                            <input type="text" name="contact_number" id="editContact" class="form-control">
                        </div>
                    </div>
                </form>
                <div class="modal-body pt-0">
                    <hr>
                    <h6 class="mb-3"><i class="bi bi-person-check me-2 text-secondary"></i>Account Status</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold" id="editStatusLabel">Active</div>
                            <small class="text-muted">Enable or disable this staff account.</small>
                        </div>
                        <form method="POST" id="editToggleForm">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-warning rounded-pill" id="editToggleBtn">
                                <i class="bi bi-pause-circle me-1"></i> Disable Account
                            </button>
                        </form>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-key me-2 text-secondary"></i>Reset Password</h6>
                    <form method="POST" id="editPasswordForm">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" required minlength="6"
                                placeholder="Minimum 6 characters">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" required
                                minlength="6">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary-dark">
                            <i class="bi bi-key me-1"></i> Reset Password
                        </button>
                    </form>
                    <form method="POST" id="editGenerateForm" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning rounded-pill">
                            <i class="bi bi-envelope me-1"></i> Generate & Email Temporary Password
                        </button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="editForm" class="btn btn-primary-dark rounded-pill">
                        <i class="bi bi-check-lg me-1"></i> Update Staff
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== DELETE STAFF MODAL ==================== --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-trash me-2 text-danger"></i>Delete Staff</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to permanently delete this staff account?</p>
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            This action cannot be undone. All data for <strong id="deleteStaffName"></strong> (<span id="deleteStaffEmail"></span>) will be permanently removed.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill"><i class="bi bi-trash me-1"></i> Delete Permanently</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
