{{-- ==================== PACKAGE MODALS ==================== --}}

{{-- Add Package Modal --}}
<div class="modal fade" id="packageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="packageForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="packageModalTitle">Add Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill" id="packageSubmitBtn">Save Package</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Package Confirmation Modal --}}
<div class="modal fade" id="deletePackageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deletePackageForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    <p class="mt-3 mb-0">Are you sure you want to delete <strong id="deletePackageName"></strong>?</p>
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

{{-- ==================== ADD-ON MODALS ==================== --}}

{{-- Add Add-On Modal --}}
<div class="modal fade" id="addonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="addonForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="addonFormMethod" value="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="addonModalTitle">Add Add-On</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill" id="addonSubmitBtn">Save Add-On</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Add-On Confirmation Modal --}}
<div class="modal fade" id="deleteAddonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteAddonForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                    <p class="mt-3 mb-0">Are you sure you want to delete <strong id="deleteAddonName"></strong>?</p>
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
