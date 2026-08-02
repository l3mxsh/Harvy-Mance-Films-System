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
                <div class="col-lg-3 col-md-4 col-6">
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
                <div class="col-lg-3 col-md-4 col-6">
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
                <div class="col-lg-3 col-md-4 col-6">
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
                <div class="col-lg-3 col-md-4 col-6">
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
            <ul class="nav nav-pills mb-4" id="packageTabs" role="tablist">
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
                    <section class="surface-card">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title">Packages</h2>
                            <button class="btn btn-dark rounded-pill" onclick="openAddModal()">
                                <i class="bi bi-plus-lg me-1"></i> Add Package
                            </button>
                        </div>
                        <div class="table-responsive">
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
                                <tbody>
                                    @forelse($packages as $pkg)
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $pkg->name }}</div>
                                                @if($pkg->description)
                                                    <small class="text-muted">{{ Str::limit($pkg->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @foreach($pkg->services->take(3) as $service)
                                                    <span class="badge bg-light text-dark border me-1">{{ $service->service_name }}</span>
                                                @endforeach
                                                @if($pkg->services->count() > 3)
                                                    <span class="badge bg-secondary">+{{ $pkg->services->count() - 3 }} more</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($pkg->inventory->count() > 0)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        <i class="bi bi-tools me-1"></i>{{ $pkg->inventory->count() }} items
                                                    </span>
                                                @else
                                                    <span class="text-muted small">None</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-semibold">&#8369;{{ number_format($pkg->price, 2) }}</td>
                                            <td class="text-center">
                                                @include('partials.status-badge', ['status' => $pkg->status])
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a class="btn btn-sm btn-outline-secondary rounded-3" title="View & Edit" href="{{ route('package.edit', $pkg->id) }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete" onclick="confirmDeletePackage({{ $pkg->id }}, '{{ addslashes($pkg->name) }}')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                                No packages found. Click "Add Package" to create one.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                {{-- ==================== ADD-ONS TAB ==================== --}}
                <div class="tab-pane fade" id="addonsTab" role="tabpanel">
                    <section class="surface-card">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title">Add-Ons</h2>
                            <button class="btn btn-dark rounded-pill" onclick="openAddAddonModal()">
                                <i class="bi bi-plus-lg me-1"></i> Add Add-On
                            </button>
                        </div>
                        <div class="table-responsive">
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
                                <tbody>
                                    @forelse($addons as $addon)
                                        <tr>
                                            <td class="fw-semibold">{{ $addon->name }}</td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($addon->description, 60) }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if($addon->inventory->count() > 0)
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                                        <i class="bi bi-tools me-1"></i>{{ $addon->inventory->count() }} items
                                                    </span>
                                                @else
                                                    <span class="text-muted small">None</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-semibold">&#8369;{{ number_format($addon->price, 2) }}</td>
                                            <td class="text-center">
                                                @include('partials.status-badge', ['status' => $addon->status])
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a class="btn btn-sm btn-outline-secondary rounded-3" title="View & Edit" href="{{ route('addon.edit', $addon->id) }}">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger rounded-3" title="Delete" onclick="confirmDeleteAddon({{ $addon->id }}, '{{ addslashes($addon->name) }}')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-5 text-muted">
                                                <i class="bi bi-plus-circle fs-1 d-block mb-2"></i>
                                                No add-ons found. Click "Add Add-On" to create one.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
