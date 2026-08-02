<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
            <span class="fw-semibold">User Management</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">

            {{-- ==================== ADMIN USERS ==================== --}}
            <section class="surface-card">
                <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="section-title"><i class="bi bi-shield-lock me-2"></i>Admin Accounts</h2>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border">{{ $users->total() }} total</span>
                        <button type="button" class="btn btn-dark rounded-pill" onclick="openCreateModal()">
                            <i class="bi bi-plus-circle me-1"></i> Add Admin
                        </button>
                    </div>
                </div>

                <form method="GET" action="{{ route('users.admin.index') }}" class="row g-2 align-items-end mb-3">
                    <div class="col-md-6">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search by name or email..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2 mb-1">
                        <button type="submit" class="btn btn-dark w-100 rounded-2">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('users.admin.index') }}" class="btn btn-outline-dark btn-sm rounded-pill" title="Clear filters">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td class="fw-semibold">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-primary ms-1">You</span>
                                        @endif
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
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="border-top pt-3 mt-3">{{ $users->links('vendor.pagination.bootstrap-5') }}</div>
                @endif
            </section>
        </div>
    </div>

    {{-- ==================== CREATE ADMIN MODAL ==================== --}}
    <div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('users.admin.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="admin@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="createPassword" class="form-control" required minlength="6" placeholder="Minimum 6 characters">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="createPassword" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="createPasswordConfirmation" class="form-control" required minlength="6" placeholder="Re-enter password">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="createPasswordConfirmation" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="createGeneratePwBtn">
                            Generate Password
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark rounded-pill">Create Admin</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== EDIT ADMIN MODAL ==================== --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                        <hr>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reset Password <span class="req">(optional)</span></label>
                            <div class="input-group mb-2">
                                <input type="password" name="new_password" id="editNewPassword" class="form-control" minlength="6" placeholder="Leave blank to keep current">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="editNewPassword" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="input-group">
                                <input type="password" name="new_password_confirmation" id="editNewPasswordConfirmation" class="form-control" minlength="6" placeholder="Re-enter new password">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="editNewPasswordConfirmation" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill mt-2" id="editGeneratePwBtn">
                                Generate Password
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark rounded-pill">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== TOGGLE STATUS MODAL ==================== --}}
    <div class="modal fade" id="toggleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="toggleModalHeader">
                    <h5 class="modal-title" id="toggleModalTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="toggleForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-0" id="toggleMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn rounded-pill" id="toggleSubmitBtn"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== DELETE ADMIN MODAL ==================== --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-0">Are you sure you want to permanently delete <strong id="deleteUserName"></strong> (<span id="deleteUserEmail"></span>)?</p>
                        <p class="text-danger small mt-2 mb-0">This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/users.js') }}"></script>

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
