<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/package.css') }}">
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Booking Management</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#packagesTab" type="button" role="tab">
                        <i class="bi bi-box-seam me-1"></i> Packages
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#addonsTab" type="button" role="tab">
                        <i class="bi bi-plus-circle me-1"></i> Add-Ons
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                {{-- ==================== PACKAGES TAB ==================== --}}
                <div class="tab-pane fade show active" id="packagesTab" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Packages</h5>
                        <button class="btn btn-dark btn-sm" onclick="openAddModal()">
                            <i class="bi bi-plus-lg"></i> Add Package
                        </button>
                    </div>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Package Name</th>
                                            <th>Services</th>
                                            <th class="text-center">Inventory</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="width: 150px;">Actions</th>
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
                                                    @if($pkg->status === 'active')
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-info" title="View" onclick="viewPackage({{ $pkg->id }})">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-primary" title="Edit" onclick="editPackage({{ $pkg->id }})">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" title="Delete" onclick="confirmDeletePackage({{ $pkg->id }}, '{{ addslashes($pkg->name) }}')">
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
                        </div>
                    </div>
                </div>

                {{-- ==================== ADD-ONS TAB ==================== --}}
                <div class="tab-pane fade" id="addonsTab" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Add-Ons</h5>
                        <button class="btn btn-dark btn-sm" onclick="openAddAddonModal()">
                            <i class="bi bi-plus-lg"></i> Add Add-On
                        </button>
                    </div>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Add-On Name</th>
                                            <th>Description</th>
                                            <th class="text-center">Inventory</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="width: 150px;">Actions</th>
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
                                                    @if($addon->status === 'active')
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-info" title="View" onclick="viewAddon({{ $addon->id }})">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-primary" title="Edit" onclick="editAddon({{ $addon->id }})">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger" title="Delete" onclick="confirmDeleteAddon({{ $addon->id }}, '{{ addslashes($addon->name) }}')">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== PACKAGE MODALS ==================== --}}

    <!-- Add / Edit Package Modal -->
    <div class="modal fade" id="packageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="packageForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="packageModalTitle">Add Package</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Package Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="packageName" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">&#8369;</span>
                                    <input type="number" class="form-control" name="price" id="packagePrice" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="packageDescription" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="packageStatus" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Service Inclusions <span class="text-danger">*</span></label>
                                <div id="servicesContainer">
                                    <div class="input-group mb-2 service-row">
                                        <input type="text" class="form-control" name="services[]" placeholder="e.g. Pre-nuptial shoot" required>
                                        <button type="button" class="btn btn-outline-danger" onclick="removeService(this)"><i class="bi bi-x-lg"></i></button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-dark" onclick="addService()">
                                    <i class="bi bi-plus"></i> Add Service
                                </button>
                            </div>

                            {{-- Inventory Assignment --}}
                            <div class="col-12">
                                <hr class="my-2">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-tools me-1"></i> Required Equipment & Materials
                                </label>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control form-control-sm" id="pkgInventorySearch"
                                           placeholder="Search inventory items..." autocomplete="off">
                                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="searchPkgInventory()">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <div class="dropdown-menu" id="pkgInventoryDropdown" style="width: 100%; max-height: 200px; overflow-y: auto;"></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0" id="pkgInventoryTable" style="display: none;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th style="width: 100px;">Qty</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="pkgInventoryBody"></tbody>
                                    </table>
                                </div>
                                <div id="pkgInventoryEmpty" class="text-muted small mt-1">No items assigned yet.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark" id="packageSubmitBtn">Save Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Package Modal -->
    <div class="modal fade" id="viewPackageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Package Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-muted small">Package Name</label>
                        <div class="fw-semibold fs-5" id="viewName"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Price</label>
                            <div class="fw-semibold text-success fs-5" id="viewPrice"></div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Status</label>
                            <div id="viewStatus"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Description</label>
                        <div id="viewDescription" class="text-secondary"></div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Service Inclusions</label>
                        <ul class="list-group list-group-flush" id="viewServices"></ul>
                    </div>
                    <div>
                        <label class="text-muted small">Required Equipment & Materials</label>
                        <div id="viewInventory"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Package Confirmation Modal -->
    <div class="modal fade" id="deletePackageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deletePackageForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                        <p class="mt-3 mb-0">Are you sure you want to delete <strong id="deletePackageName"></strong>?</p>
                        <small class="text-muted">This action cannot be undone.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== ADD-ON MODALS ==================== --}}

    <!-- Add / Edit Add-On Modal -->
    <div class="modal fade" id="addonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="addonForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="addonFormMethod" value="POST">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="addonModalTitle">Add Add-On</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Add-On Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="addonName" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Additional Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">&#8369;</span>
                                    <input type="number" class="form-control" name="price" id="addonPrice" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="addonDescription" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" name="status" id="addonStatus" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            {{-- Inventory Assignment --}}
                            <div class="col-12">
                                <hr class="my-2">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-tools me-1"></i> Required Equipment & Materials
                                </label>
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control form-control-sm" id="addonInventorySearch"
                                           placeholder="Search inventory items..." autocomplete="off">
                                    <button type="button" class="btn btn-sm btn-outline-dark" onclick="searchAddonInventory()">
                                        <i class="bi bi-search"></i>
                                    </button>
                                    <div class="dropdown-menu" id="addonInventoryDropdown" style="width: 100%; max-height: 200px; overflow-y: auto;"></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0" id="addonInventoryTable" style="display: none;">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th style="width: 100px;">Qty</th>
                                                <th style="width: 50px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="addonInventoryBody"></tbody>
                                    </table>
                                </div>
                                <div id="addonInventoryEmpty" class="text-muted small mt-1">No items assigned yet.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark" id="addonSubmitBtn">Save Add-On</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Add-On Modal -->
    <div class="modal fade" id="viewAddonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">Add-On Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-muted small">Add-On Name</label>
                        <div class="fw-semibold fs-5" id="viewAddonName"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Additional Price</label>
                            <div class="fw-semibold text-success fs-5" id="viewAddonPrice"></div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Status</label>
                            <div id="viewAddonStatus"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Description</label>
                        <div id="viewAddonDescription" class="text-secondary"></div>
                    </div>
                    <div>
                        <label class="text-muted small">Required Equipment & Materials</label>
                        <div id="viewAddonInventory"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Add-On Confirmation Modal -->
    <div class="modal fade" id="deleteAddonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deleteAddonForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                        <p class="mt-3 mb-0">Are you sure you want to delete <strong id="deleteAddonName"></strong>?</p>
                        <small class="text-muted">This action cannot be undone.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/package.js') }}"></script>
</body>

</html>
