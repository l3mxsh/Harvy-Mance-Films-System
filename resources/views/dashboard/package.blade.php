<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/package.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Manage Packages</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">

            {{-- ==================== SUMMARY CARDS ==================== --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Total Packages</h6>
                                <h4 class="mb-0 fw-bold">{{ $packages->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Active Packages</h6>
                                <h4 class="mb-0 fw-bold">{{ $packages->where('status', 'active')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-plus-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Total Add-Ons</h6>
                                <h4 class="mb-0 fw-bold">{{ $addons->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-12">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Active Add-Ons</h6>
                                <h4 class="mb-0 fw-bold">{{ $addons->where('status', 'active')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== TABS ==================== --}}
            <ul class="nav nav-pills mb-4 justify-content-center justify-content-md-start" id="packageTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#packagesTab" type="button" role="tab">
                        <i class="bi bi-box-seam me-1"></i> Packages
                        <span class="badge bg-light text-dark border ms-1">{{ $packages->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#addonsTab" type="button" role="tab">
                        <i class="bi bi-plus-circle me-1"></i> Add-Ons
                        <span class="badge bg-light text-dark border ms-1">{{ $addons->count() }}</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                {{-- ==================== PACKAGES TAB ==================== --}}
                <div class="tab-pane fade show active" id="packagesTab" role="tabpanel">
                    <section class="surface-card mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-12">
                                <label class="form-label small text-muted mb-1">Search Packages</label>
                                <input type="text" id="packageSearchInput" class="form-control form-control-sm" placeholder="Search packages by name or description..." value="{{ request('search') }}" autocomplete="off">
                            </div>
                        </div>
                    </section>

                    <section class="surface-card">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title">Packages</h2>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border" id="packageTotalBadge">{{ $packages->count() }} total</span>
                                <button class="btn btn-dark rounded-pill" onclick="openAddModal()">
                                    <i class="bi bi-plus-lg me-1"></i> Add Package
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Package Name</th>
                                        <th>Services</th>
                                        <th class="text-center">Inventory</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="packageTableBody">
                                    @include('dashboard.partials.package-rows', compact('packages'))
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none" id="packageMobileBody">
                            @include('dashboard.partials.package-mobile-rows', compact('packages'))
                        </div>
                    </section>
                </div>

                {{-- ==================== ADD-ONS TAB ==================== --}}
                <div class="tab-pane fade" id="addonsTab" role="tabpanel">
                    <section class="surface-card mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-12">
                                <label class="form-label small text-muted mb-1">Search Add-Ons</label>
                                <input type="text" id="addonSearchInput" class="form-control form-control-sm" placeholder="Search add-ons by name or description..." autocomplete="off">
                            </div>
                        </div>
                    </section>

                    <section class="surface-card">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title">Add-Ons</h2>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border" id="addonTotalBadge">{{ $addons->count() }} total</span>
                                <button class="btn btn-dark rounded-pill" onclick="openAddAddonModal()">
                                    <i class="bi bi-plus-lg me-1"></i> Add Add-On
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Add-On Name</th>
                                        <th>Description</th>
                                        <th class="text-center">Inventory</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="addonTableBody">
                                    @include('dashboard.partials.addon-rows', compact('addons'))
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none" id="addonMobileBody">
                            @include('dashboard.partials.addon-mobile-rows', compact('addons'))
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.partials.package-modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/package.js') }}"></script>

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
