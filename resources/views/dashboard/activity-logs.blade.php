<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/activity-logs.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Activity Logs</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">

            {{-- ==================== FILTERS ==================== --}}
            <section class="surface-card mb-4">
                <div class="d-flex align-items-end flex-wrap gap-2">
                    <div class="flex-grow-1" style="min-width: 220px;">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" id="logSearchInput" class="form-control form-control-sm" placeholder="Search actor, action or description..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                    <div style="width: 200px;">
                        <label class="form-label small text-muted mb-1">Action</label>
                        <select id="logActionFilter" class="form-select form-select-sm">
                            <option value="">All Actions</option>
                            @foreach($distinctActions as $action)
                                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="width: 160px;">
                        <label class="form-label small text-muted mb-1">User Type</label>
                        <select id="logUserTypeFilter" class="form-select form-select-sm">
                            <option value="">All Users</option>
                            <option value="admin" {{ request('user_type') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="staff" {{ request('user_type') === 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="client" {{ request('user_type') === 'client' ? 'selected' : '' }}>Client</option>
                        </select>
                    </div>
                    <div style="width: 160px;">
                        <label class="form-label small text-muted mb-1">From</label>
                        <input type="date" id="logDateFrom" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div style="width: 160px;">
                        <label class="form-label small text-muted mb-1">To</label>
                        <input type="date" id="logDateTo" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                </div>
            </section>

            {{-- ==================== ACTIVITY LOG TABLE ==================== --}}
            <section class="surface-card">
                <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="section-title">Activity Entries</h2>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border" id="logTotalBadge">{{ $logs->total() }} total</span>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Actor</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Timestamp</th>
                                <th class="text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody id="logTableBody">
                            @include('dashboard.partials.activity-log-rows', compact('logs'))
                        </tbody>
                    </table>
                </div>
            </section>

            <div id="logPagination" class="border-top pt-3 mt-3 @if(!$logs->hasPages()) d-none @endif">
                @if($logs->hasPages())
                    {{ $logs->links('vendor.pagination.bootstrap-5') }}
                @endif
            </div>
        </div>
    </div>

    {{-- ==================== LOG DETAILS MODAL ==================== --}}
    <div class="modal fade" id="logDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Activity Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="detail-box mb-3">
                        <span class="detail-label">Action</span>
                        <span class="detail-value"><span class="badge bg-dark" id="logDetailAction"></span></span>
                    </div>
                    <div class="detail-box mb-3">
                        <span class="detail-label">Actor</span>
                        <span class="detail-value" id="logDetailActor"></span>
                    </div>
                    <div class="detail-box mb-3">
                        <span class="detail-label">User Type</span>
                        <span class="detail-value" id="logDetailType"></span>
                    </div>
                    <div class="detail-box mb-3">
                        <span class="detail-label">Timestamp</span>
                        <span class="detail-value" id="logDetailTime"></span>
                    </div>
                    <div class="detail-box mb-3">
                        <span class="detail-label">Description</span>
                        <span class="detail-value" id="logDetailDescription"></span>
                    </div>
                    <div class="detail-box mb-0" id="logDetailContextWrap" style="display:none;">
                        <span class="detail-label">Context</span>
                        <pre class="log-context mt-2 mb-0" id="logDetailContext"></pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/activity-logs.js') }}"></script>

    {{-- FLASH TOASTS --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="flashToasts" style="z-index: 1080;">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="toast align-items-center text-bg-danger border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body"><i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}</div>
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
