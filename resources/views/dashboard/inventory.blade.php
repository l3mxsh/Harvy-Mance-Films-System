<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/inventory.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Manage Inventory</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">

            {{-- ==================== SUMMARY CARDS ==================== --}}
            <div class="row g-3 mb-4">
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Total Items</h6>
                                <h4 class="mb-0 fw-bold">{{ $totalItems }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Available</h6>
                                <h4 class="mb-0 fw-bold">{{ $availableItems }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-send"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">In Use</h6>
                                <h4 class="mb-0 fw-bold">{{ $inUseItems }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-tools"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Maintenance</h6>
                                <h4 class="mb-0 fw-bold">{{ $maintenanceItems }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== FILTERS ==================== --}}
            <section class="surface-card mb-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" id="inventorySearchInput" class="form-control form-control-sm" placeholder="Search items..." value="{{ request('search') }}" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Category</label>
                        <select id="inventoryCategoryFilter" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <option value="equipment" {{ request('category') === 'equipment' ? 'selected' : '' }}>Equipment</option>
                            <option value="material" {{ request('category') === 'material' ? 'selected' : '' }}>Material</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Availability</label>
                        <select id="inventoryAvailabilityFilter" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="available" {{ request('availability_status') === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="in_use" {{ request('availability_status') === 'in_use' ? 'selected' : '' }}>In Use</option>
                            <option value="reserved" {{ request('availability_status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                            <option value="unavailable" {{ request('availability_status') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1">Condition</label>
                        <select id="inventoryConditionFilter" class="form-select form-select-sm">
                            <option value="">All Conditions</option>
                            <option value="new" {{ request('condition_status') === 'new' ? 'selected' : '' }}>New</option>
                            <option value="good" {{ request('condition_status') === 'good' ? 'selected' : '' }}>Good</option>
                            <option value="maintenance" {{ request('condition_status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="damaged" {{ request('condition_status') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                        </select>
                    </div>
                </div>
            </section>

            {{-- ==================== INVENTORY ITEMS ==================== --}}
            <section class="surface-card">
                <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="section-title">Inventory Items</h2>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light text-dark border" id="inventoryTotalBadge">{{ $items->total() }} total</span>
                        <button class="btn btn-dark rounded-pill" onclick="openAddModal()">
                            <i class="bi bi-plus-lg me-1"></i> Add Item
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Item Name</th>
                                <th class="text-center">Category</th>
                                <th class="text-center">Qty</th>
                                <th>Unit</th>
                                <th class="text-center">Condition</th>
                                <th class="text-center">Availability</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            @include('dashboard.partials.inventory-rows', compact('items'))
                        </tbody>
                    </table>
                </div>
            </section>

            <div id="inventoryPagination" class="@if(!$items->hasPages()) d-none @endif">
                {{ $items->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>

    {{-- ==================== ADD / EDIT ITEM MODAL ==================== --}}
    <div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="itemForm" method="POST">
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="itemModalTitle">Add Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Item Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="itemName" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" name="category" id="itemCategory" required>
                                    <option value="equipment">Equipment</option>
                                    <option value="material">Material</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="itemDescription" rows="2"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity" id="itemQuantity" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Unit <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="unit" id="itemUnit" placeholder="e.g. pcs, sets" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Condition <span class="text-danger">*</span></label>
                                <select class="form-select" name="condition_status" id="itemCondition" required>
                                    <option value="new">New</option>
                                    <option value="good">Good</option>
                                    <option value="maintenance">Maintenance</option>
                                    <option value="damaged">Damaged</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Availability <span class="text-danger">*</span></label>
                                <select class="form-select" name="availability_status" id="itemAvailability" required>
                                    <option value="available">Available</option>
                                    <option value="in_use">In Use</option>
                                    <option value="reserved">Reserved</option>
                                    <option value="unavailable">Unavailable</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark rounded-pill" id="itemSubmitBtn">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== VIEW ITEM MODAL ==================== --}}
    <div class="modal fade" id="viewItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Item Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-muted small">Item Name</label>
                        <div class="fw-semibold fs-5" id="viewName"></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Category</label>
                            <div id="viewCategory"></div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Quantity</label>
                            <div class="fw-semibold" id="viewQuantity"></div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted small">Unit</label>
                            <div id="viewUnit"></div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small">Condition</label>
                            <div id="viewCondition"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted small">Availability</label>
                        <div id="viewAvailability"></div>
                    </div>
                    <div>
                        <label class="text-muted small">Description</label>
                        <div id="viewDescription" class="text-secondary"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== DELETE ITEM MODAL ==================== --}}
    <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="deleteItemForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                        <p class="mt-3 mb-0">Are you sure you want to delete <strong id="deleteItemName"></strong>?</p>
                        <small class="text-muted">This action cannot be undone.</small>
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
    <script src="{{ asset('js/inventory.js') }}"></script>

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
