<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package - Admin</title>
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
            <span class="fw-semibold">{{ $package->name }}</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            <div class="mb-4">
                <a href="{{ route('package') }}" class="text-decoration-none text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>Back to Packages
                </a>
            </div>

            {{-- ==================== EDIT PACKAGE ==================== --}}
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    {{-- Basic Information --}}
                    <form id="packageForm" method="POST" action="{{ route('package.update', $package->id) }}">
                        @csrf
                        @method('PUT')
                    <section class="surface-card">
                        <div class="section-head">
                            <h2 class="section-title">{{ $package->name }}</h2>
                        </div>
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Package Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $package->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">&#8369;</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" name="price" step="0.01" min="0" value="{{ old('price', $package->price) }}" required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="2" placeholder="Describe what this package includes...">{{ old('description', $package->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center bg-body-tertiary rounded-3 p-3 flex-wrap gap-2">
                                        <div>
                                            <div class="fw-semibold" id="packageStatusLabel">Active</div>
                                            <small class="text-muted">Active packages are available for clients to book.</small>
                                        </div>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" id="packageStatusSwitch" role="switch"
                                                   {{ old('status', $package->status) === 'active' ? 'checked' : '' }}>
                                            <input type="hidden" name="status" id="packageStatus" value="{{ old('status', $package->status) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </section>

                    {{-- Service Inclusions --}}
                    <section class="surface-card">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title">Service Inclusions</h2>
                            <button type="button" class="btn btn-sm btn-outline-dark rounded-2" onclick="addService()">
                                <i class="bi bi-plus-lg me-1"></i> Add Service
                            </button>
                        </div>
                        <div id="servicesContainer">
                            <div class="input-group mb-2 service-row">
                                <input type="text" class="form-control" name="services[]" placeholder="e.g. Pre-nuptial shoot" required>
                                <button type="button" class="btn btn-outline-danger border-0" onclick="removeService(this)" title="Remove service"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </div>
                    </section>

                    {{-- Inventory Assignment --}}
                    <section class="surface-card">
                        <div class="section-head">
                            <h2 class="section-title">Required Equipment &amp; Materials</h2>
                        </div>
                        <div class="dropdown">
                            <input type="text" class="form-control mb-2" id="pkgInventorySearch"
                                   placeholder="Click here to browse or type to search inventory items..." autocomplete="off">
                            <div class="dropdown-menu w-100" id="pkgInventoryDropdown" style="max-height: 240px; overflow-y: auto;"></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0" id="pkgInventoryTable" style="display: none;">
                                <thead class="table-light">
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center" style="width: 60px;">Qty</th>
                                        <th class="text-center" style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="pkgInventoryBody"></tbody>
                            </table>
                        </div>
                        <div id="pkgInventoryEmpty" class="text-muted small mt-1">No items assigned yet.</div>
                    </section>

                    <div class="d-flex justify-content-end gap-2 mb-4">
                        <a href="{{ route('package') }}" class="btn btn-outline-dark rounded-pill">Cancel</a>
                        <button type="submit" class="btn btn-dark rounded-pill">
                            <i class="bi bi-check-lg me-1"></i> Update Package
                        </button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/package.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var pkg = @json($package->only(['services', 'inventory']));
            setServices(pkg.services);
            setPackageInventory(pkg.inventory || []);

            var statusSwitch = document.getElementById('packageStatusSwitch');
            var statusHidden = document.getElementById('packageStatus');
            var statusLabel = document.getElementById('packageStatusLabel');
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
