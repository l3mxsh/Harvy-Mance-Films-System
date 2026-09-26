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
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}?v=7">
    <link rel="stylesheet" href="{{ asset('css/clients.css') }}?v=2">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Clients</span>
            <span></span>
        </div>

        {{-- ==================== TABS ==================== --}}
        <ul class="nav nav-pills mb-0 px-4 pt-4 justify-content-start" id="clientTabs">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-active" type="button">
                    <i class="bi bi-people me-1"></i> Client Accounts
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-archived" type="button">
                    <i class="bi bi-archive me-1"></i> Archived Accounts
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-upcoming" type="button">
                    <i class="bi bi-clock-history me-1"></i> Upcoming Archiving
                </button>
            </li>
        </ul>

        <div class="container-fluid p-4">

            {{-- ==================== SUMMARY CARDS ==================== --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-4 col-12">
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
                <div class="col-lg-3 col-md-4 col-12">
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
                <div class="col-lg-3 col-md-4 col-12">
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
            </div>

            <div class="tab-content" id="clientTabsContent">
                {{-- Active Accounts Tab --}}
                <div class="tab-pane fade show active" id="tab-active" role="tabpanel">
                    <section class="surface-card mb-4">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title">Client Accounts</h2>
                            <span class="badge bg-light text-dark border" id="clientTotalBadge">{{ $accounts->total() }} total</span>
                        </div>
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-10">
                                <label class="form-label small text-muted mb-1">Search</label>
                                <input type="text" id="clientSearchInput" class="form-control"
                                    placeholder="Control number, name, email, phone or booking ref..." value="{{ request('search') }}" autocomplete="off">
                            </div>
                        </div>
                    </section>

                    <section class="surface-card">
                        {{-- Desktop table (hidden on mobile) --}}
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Control Number</th>
                                        <th>Client</th>
                                        <th>Booking Ref</th>
                                        <th>Contact</th>
                                        <th>Last Login</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="clientTableBody">
                                    @include('dashboard.partials.client-rows', compact('accounts'))
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile cards (hidden on desktop) --}}
                        <div class="d-md-none" id="clientMobileBody">
                            @include('dashboard.partials.client-mobile-rows', compact('accounts'))
                        </div>
                        <div id="clientPagination" class="@if(!$accounts->hasPages()) d-none @endif">
                            {{ $accounts->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </section>
                </div>

                {{-- Archived Accounts Tab --}}
                <div class="tab-pane fade" id="tab-archived" role="tabpanel">
                    <section class="surface-card mb-4">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title">Archived Accounts</h2>
                            <span class="badge bg-light text-dark border">{{ $archivedAccounts->count() }} total</span>
                        </div>
                    </section>

                    <section class="surface-card">
                        {{-- Desktop table (hidden on mobile) --}}
                        <div class="table-responsive d-none d-md-block">
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
                                    @forelse($archivedAccounts as $archived)
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
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-secondary rounded-pill"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#restoreModal"
                                                        data-id="{{ $archived->id }}"
                                                        data-name="{{ $archived->client_name }}"
                                                        data-control="{{ $archived->control_number }}">
                                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Restore
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger rounded-pill"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteModal"
                                                        data-id="{{ $archived->id }}"
                                                        data-name="{{ $archived->client_name }}"
                                                        data-control="{{ $archived->control_number }}">
                                                    <i class="bi bi-trash me-1"></i>Delete
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-5 text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                No archived client accounts.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile cards (hidden on desktop) --}}
                        <div class="d-md-none">
                            @include('dashboard.partials.client-archived-mobile-rows', compact('archivedAccounts'))
                        </div>
                    </section>
                </div>

                {{-- Upcoming Archiving Tab --}}
                <div class="tab-pane fade" id="tab-upcoming" role="tabpanel">
                    <section class="surface-card mb-4">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title">Upcoming Archiving</h2>
                            <span class="badge bg-light text-dark border">{{ $upcomingDeletions->count() }} total</span>
                        </div>
                    </section>

                    <section class="surface-card">
                        {{-- Desktop table (hidden on mobile) --}}
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Booking Ref</th>
                                        <th>Delivered</th>
                                        <th>Auto-Archive</th>
                                        <th class="text-end">Days Left</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($upcomingDeletions as $item)
                                        <tr>
                                            <td>
                                                <div>{{ $item['booking']->client_name }}</div>
                                                <small class="text-muted">{{ $item['booking']->client_email }}</small>
                                            </td>
                                            <td><code>{{ $item['booking']->booking_ref }}</code></td>
                                            <td>{{ $item['delivered_at']->format('M d, Y') }}</td>
                                            <td>{{ $item['delete_at']->format('M d, Y') }}</td>
                                            <td class="text-end">
                                                @if($item['days_remaining'] <= 3)
                                                    <span class="badge bg-danger">{{ $item['days_remaining'] }} day(s)</span>
                                                @elseif($item['days_remaining'] <= 7)
                                                    <span class="badge bg-warning text-dark">{{ $item['days_remaining'] }} day(s)</span>
                                                @else
                                                    <span class="badge bg-success">{{ $item['days_remaining'] }} day(s)</span>
                                                @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center py-5 text-muted">
                                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                    No delivered bookings with active client accounts.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                            </table>
                        </div>

                        {{-- Mobile cards (hidden on desktop) --}}
                        <div class="d-md-none">
                            @include('dashboard.partials.client-upcoming-mobile-rows', compact('upcomingDeletions'))
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== RESTORE CLIENT MODAL ==================== --}}
    <div class="modal fade" id="restoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Restore Client Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="restoreForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-0">Are you sure you want to restore <strong id="restoreClientName"></strong> (<code id="restoreClientControl"></code>)?</p>
                        <p class="text-muted small mt-2 mb-0">The client will be able to log in again with their control number and password.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark rounded-pill">Restore</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== ARCHIVE CLIENT MODAL ==================== --}}
    <div class="modal fade" id="archiveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Archive Client Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="archiveForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-0">Are you sure you want to archive <strong id="archiveClientName"></strong> (<code id="archiveClientControl"></code>)?</p>
                        <p class="text-muted small mt-2 mb-0">The client will no longer be able to log in. You can restore it later from the Archived tab.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning rounded-pill">Archive</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== DELETE CLIENT MODAL ==================== --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Client Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="mb-0">Are you sure you want to permanently delete <strong id="deleteClientName"></strong> (<code id="deleteClientControl"></code>)?</p>
                        <p class="text-danger small mt-2 mb-0">This action cannot be undone. The client will permanently lose access.</p>
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
