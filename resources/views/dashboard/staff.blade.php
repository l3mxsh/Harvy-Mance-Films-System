<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <style>
        .summary-icon {
            width: 48px; height: 48px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; font-size: 1.3rem;
        }
        .summary-card .card-body { padding: 1rem 1.25rem; }
    </style>
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Staff Management</span>
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

            {{-- ==================== SUMMARY CARDS ==================== --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-4 col-md-4 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1">Total Staff</h6>
                                    <h4 class="mb-0 fw-bold">{{ $totalStaff }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1">Active</h6>
                                    <h4 class="mb-0 fw-bold">{{ $activeStaff }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-person-x"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1">Inactive</h6>
                                    <h4 class="mb-0 fw-bold">{{ $inactiveStaff }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== SEARCH + ADD ==================== --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('staff.admin.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small text-muted">Search</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}">
                            </div>
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
                                <i class="bi bi-plus-circle me-1"></i> Add Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ==================== STAFF TABLE ==================== --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>All Staff</h6>
                    <span class="badge bg-secondary">{{ $staff->total() }} total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staff as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:36px;height:36px;font-size:0.85rem;">
                                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $member->name }}</div>
                                                </div>
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
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary btn-sm" title="View Details"
                                                    onclick="openViewModal('{{ $member->id }}', '{{ addslashes($member->name) }}', '{{ $member->email }}', '{{ $member->contact_number }}', '{{ $member->status }}', '{{ $member->created_at->format('M d, Y g:i A') }}', '{{ $member->last_login_at ? $member->last_login_at->format('M d, Y g:i A') : 'Never' }}')">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm" title="Edit Staff"
                                                    onclick="openEditModal('{{ $member->id }}', '{{ addslashes($member->name) }}', '{{ $member->email }}', '{{ $member->contact_number }}')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-warning btn-sm" title="Reset Password"
                                                    onclick="openResetPasswordModal('{{ $member->id }}', '{{ addslashes($member->name) }}')">
                                                    <i class="bi bi-key"></i>
                                                </button>
                                                <button class="btn btn-outline-{{ $member->status === 'active' ? 'warning' : 'success' }} btn-sm"
                                                    title="{{ $member->status === 'active' ? 'Disable' : 'Enable' }} Account"
                                                    onclick="openToggleStatusModal('{{ $member->id }}', '{{ addslashes($member->name) }}', '{{ $member->status }}')">
                                                    <i class="bi bi-{{ $member->status === 'active' ? 'pause-circle' : 'play-circle' }}"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" title="Delete Staff"
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
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($staff->hasPages())
                    <div class="card-footer bg-white border-top">
                        {{ $staff->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ==================== VIEW STAFF MODAL ==================== --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-person-badge me-2"></i>Staff Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== CREATE STAFF MODAL ==================== --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add Staff</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Create Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== EDIT STAFF MODAL ==================== --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>Edit Staff</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-secondary"><i class="bi bi-check-lg me-1"></i> Update Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== RESET PASSWORD MODAL ==================== --}}
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="bi bi-key me-2"></i>Reset Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="resetPasswordForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">Resetting password for <strong id="resetPasswordName"></strong></p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Minimum 6 characters">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" required minlength="6">
                        </div>
                        <hr>
                        <p class="text-muted small mb-0">
                            Or generate a temporary password and send it to the staff's email:
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" formaction="" id="generatePasswordBtn" class="btn btn-warning">
                            <i class="bi bi-envelope me-1"></i> Generate & Email
                        </button>
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-key me-1"></i> Reset Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== TOGGLE STATUS MODAL ==================== --}}
    <div class="modal fade" id="toggleStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header" id="toggleStatusHeader">
                    <h5 class="modal-title" id="toggleStatusTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="toggleStatusForm">
                    @csrf
                    <div class="modal-body">
                        <p id="toggleStatusMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="toggleStatusBtn">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== DELETE STAFF MODAL ==================== --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Delete Staff</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
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
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Delete Permanently</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        function openViewModal(id, name, email, contact, status, joined, lastLogin) {
            document.getElementById('viewName').textContent = name;
            document.getElementById('viewEmail').textContent = email;
            document.getElementById('viewContact').textContent = contact || '—';
            document.getElementById('viewStatus').innerHTML = status === 'active'
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-danger">Inactive</span>';
            document.getElementById('viewJoined').textContent = joined;
            document.getElementById('viewLastLogin').textContent = lastLogin;
            new bootstrap.Modal(document.getElementById('viewModal')).show();
        }

        function openCreateModal() {
            var form = document.querySelector('#createModal form');
            form.reset();
            new bootstrap.Modal(document.getElementById('createModal')).show();
        }

        function openEditModal(id, name, email, contact) {
            document.getElementById('editForm').action = '/admin/staff/' + id;
            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editContact').value = contact || '';
            new bootstrap.Modal(document.getElementById('editModal')).show();
        }

        function openResetPasswordModal(id, name) {
            document.getElementById('resetPasswordForm').action = '/admin/staff/' + id + '/reset-password';
            document.getElementById('generatePasswordBtn').formAction = '/admin/staff/' + id + '/generate-password';
            document.getElementById('resetPasswordName').textContent = name;
            var form = document.getElementById('resetPasswordForm');
            form.querySelector('[name="new_password"]').value = '';
            form.querySelector('[name="new_password_confirmation"]').value = '';
            new bootstrap.Modal(document.getElementById('resetPasswordModal')).show();
        }

        function openToggleStatusModal(id, name, status) {
            var isDisabling = status === 'active';
            var header = document.getElementById('toggleStatusHeader');
            var title = document.getElementById('toggleStatusTitle');
            var message = document.getElementById('toggleStatusMessage');
            var btn = document.getElementById('toggleStatusBtn');

            header.className = 'modal-header ' + (isDisabling ? 'bg-warning' : 'bg-success');
            title.innerHTML = isDisabling
                ? '<i class="bi bi-pause-circle me-2"></i>Disable Account'
                : '<i class="bi bi-play-circle me-2"></i>Enable Account';
            message.innerHTML = isDisabling
                ? 'Are you sure you want to disable the account for <strong>' + name + '</strong>? They will not be able to log in until re-enabled.'
                : 'Are you sure you want to re-enable the account for <strong>' + name + '</strong>?';

            btn.className = 'btn ' + (isDisabling ? 'btn-warning' : 'btn-success');
            btn.textContent = isDisabling ? 'Disable Account' : 'Enable Account';
            document.getElementById('toggleStatusForm').action = '/admin/staff/' + id + '/toggle-status';

            new bootstrap.Modal(document.getElementById('toggleStatusModal')).show();
        }

        function openDeleteModal(id, name, email) {
            document.getElementById('deleteForm').action = '/admin/staff/' + id;
            document.getElementById('deleteStaffName').textContent = name;
            document.getElementById('deleteStaffEmail').textContent = email;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>

</html>
