<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/clients.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Clients</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">

            {{-- ==================== SUMMARY CARDS ==================== --}}
            <div class="row g-3 mb-4">
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-people"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Total Accounts</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['total'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-person-check"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Active</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['active'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-secondary bg-opacity-10 text-secondary">
                                <i class="bi bi-archive"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Archived</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['archived'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-key"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Pending Password Change</h6>
                                <h4 class="mb-0 fw-bold">{{ $summary['pendingChange'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== ACTIVE CLIENT ACCOUNTS ==================== --}}
            <section class="surface-card">
                <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="section-title">Client Accounts</h2>
                    <span class="badge bg-light text-dark border" id="clientTotalBadge">{{ $accounts->total() }} total</span>
                </div>

                <div class="d-flex align-items-end flex-wrap gap-2 mb-3">
                    <div class="flex-grow-1" style="min-width: 220px;">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" id="clientSearchInput" class="form-control form-control-sm" placeholder="Control number, name, email, phone or booking ref..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Control Number</th>
                                <th>Client</th>
                                <th>Booking Ref</th>
                                <th>Phone</th>
                                <th>Last Login</th>
                                <th>Created</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="clientTableBody">
                            @include('dashboard.partials.client-rows', compact('accounts'))
                        </tbody>
                    </table>
                </div>
            </section>

            <div id="clientPagination" class="@if(!$accounts->hasPages()) d-none @endif">
                {{ $accounts->links('vendor.pagination.bootstrap-5') }}
            </div>

            {{-- ==================== ARCHIVED CLIENT ACCOUNTS ==================== --}}
            <section class="surface-card mt-4">
                <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="section-title"><i class="bi bi-archive me-2"></i>Archived Client Accounts</h2>
                    <span class="badge bg-light text-dark border">{{ $archivedAccounts->count() }} total</span>
                </div>

                <div class="table-responsive">
                    @if($archivedAccounts->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No archived client accounts.
                        </div>
                    @else
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Control Number</th>
                                    <th>Client</th>
                                    <th>Booking Ref</th>
                                    <th>Delivered</th>
                                    <th>Archived</th>
                                    <th>Days Since Archive</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($archivedAccounts as $archived)
                                    <tr>
                                        <td><code>{{ $archived->control_number }}</code></td>
                                        <td>
                                            <div>{{ $archived->client_name }}</div>
                                            <small class="text-muted">{{ $archived->client_email }}</small>
                                        </td>
                                        <td>
                                            @if($archived->booking)
                                                <code>{{ $archived->booking->booking_ref }}</code>
                                            @else
                                                <span class="text-muted fst-italic">&mdash;</span>
                                            @endif
                                        </td>
                                        <td>{{ $archived->booking?->delivered_at?->format('M d, Y') ?? '—' }}</td>
                                        <td>{{ $archived->archived_at->format('M d, Y') }}</td>
                                        <td>
                                            @if((int) round($archived->archived_at->diffInDays(now())) === 0)
                                                <span class="badge bg-secondary">Today</span>
                                            @else
                                                <span class="badge bg-secondary">{{ (int) round($archived->archived_at->diffInDays(now())) }} day(s)</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('clients.admin.restore', $archived->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-dark rounded-pill" onclick="return confirm('Restore this client account?')">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/clients.js') }}"></script>

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
