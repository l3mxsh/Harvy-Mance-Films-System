<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/inventory.css') }}">
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Inventory Management</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- ==================== SUMMARY CARDS ==================== --}}
            <div class="row g-3 mb-4">
                <div class="col-lg col-md-4 col-6">
                    <div class="card border-0 shadow-sm summary-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1">Total Items</h6>
                                    <h4 class="mb-0 fw-bold">{{ $totalItems }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="card border-0 shadow-sm summary-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1">Available</h6>
                                    <h4 class="mb-0 fw-bold">{{ $availableItems }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="card border-0 shadow-sm summary-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-send"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1">In Use</h6>
                                    <h4 class="mb-0 fw-bold">{{ $inUseItems }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="card border-0 shadow-sm summary-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-tools"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1">Maintenance</h6>
                                    <h4 class="mb-0 fw-bold">{{ $maintenanceItems }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="card border-0 shadow-sm summary-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-exclamation-triangle"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1">Low Stock</h6>
                                    <h4 class="mb-0 fw-bold">{{ $lowStockItems }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== FILTERS & TABLE ==================== --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Inventory Items</h5>
                <button class="btn btn-dark btn-sm" onclick="openAddModal()">
                    <i class="bi bi-plus-lg"></i> Add Item
                </button>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('inventory') }}" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Search</label>
                            <input type="text" class="form-control form-control-sm" name="search" placeholder="Search items..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Category</label>
                            <select class="form-select form-select-sm" name="category">
                                <option value="">All Categories</option>
                                <option value="equipment" {{ request('category') === 'equipment' ? 'selected' : '' }}>Equipment</option>
                                <option value="material" {{ request('category') === 'material' ? 'selected' : '' }}>Material</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Availability</label>
                            <select class="form-select form-select-sm" name="availability_status">
                                <option value="">All Status</option>
                                <option value="available" {{ request('availability_status') === 'available' ? 'selected' : '' }}>Available</option>
                                <option value="in_use" {{ request('availability_status') === 'in_use' ? 'selected' : '' }}>In Use</option>
                                <option value="reserved" {{ request('availability_status') === 'reserved' ? 'selected' : '' }}>Reserved</option>
                                <option value="unavailable" {{ request('availability_status') === 'unavailable' ? 'selected' : '' }}>Unavailable</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted">Condition</label>
                            <select class="form-select form-select-sm" name="condition_status">
                                <option value="">All Conditions</option>
                                <option value="new" {{ request('condition_status') === 'new' ? 'selected' : '' }}>New</option>
                                <option value="good" {{ request('condition_status') === 'good' ? 'selected' : '' }}>Good</option>
                                <option value="maintenance" {{ request('condition_status') === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="damaged" {{ request('condition_status') === 'damaged' ? 'selected' : '' }}>Damaged</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-dark btn-sm w-100">
                                <i class="bi bi-funnel"></i>
                            </button>
                        </div>
                        <div class="col-md-2">
                            @if(request()->hasAny(['search', 'category', 'availability_status', 'condition_status']))
                                <a href="{{ route('inventory') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="bi bi-x-lg"></i> Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Item Name</th>
                                    <th class="text-center">Category</th>
                                    <th class="text-center">Qty</th>
                                    <th>Unit</th>
                                    <th class="text-center">Condition</th>
                                    <th class="text-center">Availability</th>
                                    <th class="text-center" style="width: 150px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td>
                                            <div class="fw-semibold">{{ $item->name }}</div>
                                            @if($item->description)
                                                <small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($item->category === 'equipment')
                                                <span class="badge bg-primary bg-opacity-10 text-primary">Equipment</span>
                                            @else
                                                <span class="badge bg-purple text-white">Material</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-semibold {{ $item->quantity <= 2 ? 'text-danger' : '' }}">{{ $item->quantity }}</span>
                                        </td>
                                        <td>{{ $item->unit }}</td>
                                        <td class="text-center">
                                            @include('partials.status-badge', ['status' => $item->condition_status])
                                        </td>
                                        <td class="text-center">
                                            @include('partials.status-badge', ['status' => $item->availability_status])
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-info" title="View" onclick="viewItem({{ $item->id }})">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" title="Edit" onclick="editItem({{ $item->id }})">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" title="Delete" onclick="confirmDeleteItem({{ $item->id }}, '{{ addslashes($item->name) }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                            No inventory items found. Click "Add Item" to create one.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($items->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $items->links() }}
                </div>
            @endif
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
</body>

</html>
