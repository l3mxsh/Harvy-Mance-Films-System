<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Add-On - Admin</title>
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
            <span class="fw-semibold">Edit Add-On</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <a href="{{ route('package') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>Back to Add-Ons
                </a>
            </div>

            {{-- ==================== EDIT ADD-ON ==================== --}}
            <section class="surface-card">
                <div class="section-head">
                    <h2 class="section-title">Edit Add-On</h2>
                </div>
                <form id="addonForm" method="POST" action="{{ route('addon.update', $addon->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-4">
                        <div class="col-12">
                            <h6 class="section-title mb-3">Basic Information</h6>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Add-On Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ old('name', $addon->name) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Additional Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">&#8369;</span>
                                <input type="number" class="form-control" name="price" step="0.01" min="0" value="{{ old('price', $addon->price) }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Describe what this add-on includes...">{{ old('description', $addon->description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center bg-body-tertiary rounded-3 p-3">
                                <div>
                                    <div class="fw-semibold" id="addonStatusLabel">Active</div>
                                    <small class="text-muted">Active add-ons are available for clients to book.</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" id="addonStatusSwitch" role="switch"
                                           {{ old('status', $addon->status) === 'active' ? 'checked' : '' }}>
                                    <input type="hidden" name="status" id="addonStatus" value="{{ old('status', $addon->status) }}">
                                </div>
                            </div>
                        </div>

                        {{-- Inventory Assignment --}}
                        <div class="col-12">
                            <hr class="my-4">
                            <h6 class="section-title mb-3">Required Equipment &amp; Materials</h6>
                            <div class="dropdown">
                                <input type="text" class="form-control mb-2" id="addonInventorySearch"
                                       placeholder="Click here to browse or type to search inventory items..." autocomplete="off">
                                <div class="dropdown-menu w-100" id="addonInventoryDropdown" style="max-height: 240px; overflow-y: auto;"></div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0" id="addonInventoryTable" style="display: none;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center" style="width: 100px;">Qty</th>
                                            <th class="text-center" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="addonInventoryBody"></tbody>
                                </table>
                            </div>
                            <div id="addonInventoryEmpty" class="text-muted small mt-1">No items assigned yet.</div>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2">
                            <a href="{{ route('package') }}" class="btn btn-outline-dark rounded-pill">Cancel</a>
                            <button type="submit" class="btn btn-dark rounded-pill">
                                <i class="bi bi-check-lg me-1"></i> Update Add-On
                            </button>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/package.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var addon = @json($addon->only(['inventory']));
            setAddonInventory(addon.inventory || []);

            var statusSwitch = document.getElementById('addonStatusSwitch');
            var statusHidden = document.getElementById('addonStatus');
            var statusLabel = document.getElementById('addonStatusLabel');
            function syncStatus() {
                var active = statusSwitch.checked;
                statusHidden.value = active ? 'active' : 'inactive';
                statusLabel.textContent = active ? 'Active' : 'Inactive';
                statusLabel.className = 'fw-semibold ' + (active ? 'text-success' : 'text-muted');
            }
            statusSwitch.addEventListener('change', syncStatus);
            syncStatus();
        });
    </script>

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
