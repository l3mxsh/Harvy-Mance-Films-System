let packageModal, viewModal, deletePackageModal;
let addonModal, viewAddonModal, deleteAddonModal;

let pkgInventoryItems = [];
let addonInventoryItems = [];

document.addEventListener('DOMContentLoaded', function () {
    packageModal = new bootstrap.Modal(document.getElementById('packageModal'));
    viewModal = new bootstrap.Modal(document.getElementById('viewPackageModal'));
    deletePackageModal = new bootstrap.Modal(document.getElementById('deletePackageModal'));
    addonModal = new bootstrap.Modal(document.getElementById('addonModal'));
    viewAddonModal = new bootstrap.Modal(document.getElementById('viewAddonModal'));
    deleteAddonModal = new bootstrap.Modal(document.getElementById('deleteAddonModal'));

    setupInventorySearch('pkgInventorySearch', 'pkgInventoryDropdown', 'pkg');
    setupInventorySearch('addonInventorySearch', 'addonInventoryDropdown', 'addon');
});

// ==================== INVENTORY SEARCH ====================

function setupInventorySearch(inputId, dropdownId, prefix) {
    const input = document.getElementById(inputId);
    const dropdown = document.getElementById(dropdownId);
    let debounceTimer;

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            const query = this.value.trim();
            if (query.length < 1) {
                dropdown.classList.remove('show');
                dropdown.innerHTML = '';
                return;
            }
            searchInventoryItems(query, dropdownId, prefix);
        }, 300);
    });

    input.addEventListener('focus', function () {
        if (dropdown.children.length > 0 && this.value.trim().length >= 1) {
            dropdown.classList.add('show');
        }
    });

    document.addEventListener('click', function (e) {
        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.remove('show');
        }
    });
}

async function searchInventoryItems(query, dropdownId, prefix) {
    const dropdown = document.getElementById(dropdownId);
    try {
        const res = await fetch(`/api/inventory/search?search=${encodeURIComponent(query)}`);
        const items = await res.json();

        const existing = prefix === 'pkg' ? pkgInventoryItems : addonInventoryItems;
        const existingIds = existing.map(i => i.id);

        if (items.length === 0) {
            dropdown.innerHTML = '<div class="dropdown-item text-muted">No items found</div>';
            dropdown.classList.add('show');
            return;
        }

        dropdown.innerHTML = items
            .filter(item => !existingIds.includes(item.id))
            .map(item => `
                <button type="button" class="dropdown-item d-flex justify-content-between align-items-center"
                        onclick="addInventoryItem('${prefix}', ${item.id}, '${escapeHtml(item.name)}', '${item.category}', ${item.quantity}, '${escapeHtml(item.unit)}')">
                    <span>
                        <i class="bi bi-${item.category === 'equipment' ? 'camera' : 'box'} me-2"></i>${escapeHtml(item.name)}
                        <small class="text-muted ms-1">(${item.category})</small>
                    </span>
                    <small class="text-muted">${item.quantity} ${escapeHtml(item.unit)} avail</small>
                </button>
            `).join('');

        dropdown.classList.add('show');
    } catch (err) {
        dropdown.innerHTML = '<div class="dropdown-item text-danger">Error searching items</div>';
        dropdown.classList.add('show');
    }
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML.replace(/'/g, '&#39;').replace(/"/g, '&quot;');
}

// ==================== PACKAGE INVENTORY ====================

function addInventoryItem(prefix, id, name, category, maxQty, unit) {
    const items = prefix === 'pkg' ? pkgInventoryItems : addonInventoryItems;

    if (items.find(i => i.id === id)) return;

    items.push({ id, name, category, maxQty, unit, quantity: 1 });
    renderInventoryTable(prefix);

    const inputId = prefix === 'pkg' ? 'pkgInventorySearch' : 'addonInventorySearch';
    const dropdownId = prefix === 'pkg' ? 'pkgInventoryDropdown' : 'addonInventoryDropdown';
    document.getElementById(inputId).value = '';
    document.getElementById(dropdownId).classList.remove('show');
    document.getElementById(dropdownId).innerHTML = '';
}

function removeInventoryItem(prefix, id) {
    const items = prefix === 'pkg' ? pkgInventoryItems : addonInventoryItems;
    const idx = items.findIndex(i => i.id === id);
    if (idx !== -1) items.splice(idx, 1);
    renderInventoryTable(prefix);
}

function updateInventoryQty(prefix, id, qty) {
    const items = prefix === 'pkg' ? pkgInventoryItems : addonInventoryItems;
    const item = items.find(i => i.id === id);
    if (item) item.quantity = Math.max(1, parseInt(qty) || 1);
}

function renderInventoryTable(prefix) {
    const items = prefix === 'pkg' ? pkgInventoryItems : addonInventoryItems;
    const tableId = prefix === 'pkg' ? 'pkgInventoryTable' : 'addonInventoryTable';
    const bodyId = prefix === 'pkg' ? 'pkgInventoryBody' : 'addonInventoryBody';
    const emptyId = prefix === 'pkg' ? 'pkgInventoryEmpty' : 'addonInventoryEmpty';

    const table = document.getElementById(tableId);
    const body = document.getElementById(bodyId);
    const empty = document.getElementById(emptyId);

    if (items.length === 0) {
        table.style.display = 'none';
        empty.style.display = 'block';
        return;
    }

    table.style.display = 'table';
    empty.style.display = 'none';

    body.innerHTML = items.map(item => `
        <tr>
            <td>
                <input type="hidden" name="inventory_items[]" value="${item.id}">
                <i class="bi bi-${item.category === 'equipment' ? 'camera' : 'box'} me-1 text-muted"></i>
                ${item.name}
                <small class="text-muted">(${item.category})</small>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm" name="inventory_quantities[]"
                       value="${item.quantity}" min="1" max="${item.maxQty}"
                       onchange="updateInventoryQty('${prefix}', ${item.id}, this.value)"
                       required>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeInventoryItem('${prefix}', ${item.id})">
                    <i class="bi bi-x-lg"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function setPackageInventory(inventory) {
    pkgInventoryItems = inventory.map(i => ({
        id: i.id,
        name: i.name,
        category: i.category,
        maxQty: i.quantity,
        unit: i.unit,
        quantity: i.pivot ? i.pivot.quantity : 1,
    }));
    renderInventoryTable('pkg');
}

function setAddonInventory(inventory) {
    addonInventoryItems = inventory.map(i => ({
        id: i.id,
        name: i.name,
        category: i.category,
        maxQty: i.quantity,
        unit: i.unit,
        quantity: i.pivot ? i.pivot.quantity : 1,
    }));
    renderInventoryTable('addon');
}

function resetPkgInventory() {
    pkgInventoryItems = [];
    renderInventoryTable('pkg');
}

function resetAddonInventory() {
    addonInventoryItems = [];
    renderInventoryTable('addon');
}

// ==================== PACKAGE FUNCTIONS ====================

function openAddModal() {
    document.getElementById('packageForm').reset();
    document.getElementById('packageForm').action = '/package';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('packageModalTitle').textContent = 'Add Package';
    document.getElementById('packageSubmitBtn').textContent = 'Save Package';
    resetServices();
    resetPkgInventory();
    packageModal.show();
}

function addService(value = '') {
    const container = document.getElementById('servicesContainer');
    const row = document.createElement('div');
    row.className = 'input-group mb-2 service-row';
    row.innerHTML = `
        <input type="text" class="form-control" name="services[]" placeholder="e.g. Pre-nuptial shoot" value="${value}" required>
        <button type="button" class="btn btn-outline-danger" onclick="removeService(this)"><i class="bi bi-x-lg"></i></button>
    `;
    container.appendChild(row);
}

function removeService(btn) {
    const rows = document.querySelectorAll('#servicesContainer .service-row');
    if (rows.length > 1) {
        btn.closest('.service-row').remove();
    }
}

function resetServices() {
    const container = document.getElementById('servicesContainer');
    container.innerHTML = `
        <div class="input-group mb-2 service-row">
            <input type="text" class="form-control" name="services[]" placeholder="e.g. Pre-nuptial shoot" required>
            <button type="button" class="btn btn-outline-danger" onclick="removeService(this)"><i class="bi bi-x-lg"></i></button>
        </div>
    `;
}

function setServices(services) {
    const container = document.getElementById('servicesContainer');
    container.innerHTML = '';
    services.forEach(s => addService(s.service_name));
    if (services.length === 0) addService();
}

async function viewPackage(id) {
    const res = await fetch(`/package/${id}`);
    const pkg = await res.json();

    document.getElementById('viewName').textContent = pkg.name;
    document.getElementById('viewPrice').textContent = '\u20B1' + parseFloat(pkg.price).toLocaleString('en', { minimumFractionDigits: 2 });
    document.getElementById('viewDescription').textContent = pkg.description || 'No description provided.';

    const statusEl = document.getElementById('viewStatus');
    statusEl.innerHTML = pkg.status === 'active'
        ? '<span class="badge bg-success">Active</span>'
        : '<span class="badge bg-secondary">Inactive</span>';

    const servicesList = document.getElementById('viewServices');
    servicesList.innerHTML = pkg.services.length
        ? pkg.services.map(s => `<li class="list-group-item"><i class="bi bi-check2-circle text-success me-2"></i>${s.service_name}</li>`).join('')
        : '<li class="list-group-item text-muted">No services included.</li>';

    const inventoryEl = document.getElementById('viewInventory');
    if (pkg.inventory && pkg.inventory.length > 0) {
        inventoryEl.innerHTML = `
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr><th>Item</th><th>Category</th><th class="text-center">Qty Needed</th><th>Unit</th></tr>
                </thead>
                <tbody>
                    ${pkg.inventory.map(i => `
                        <tr>
                            <td><i class="bi bi-${i.category === 'equipment' ? 'camera' : 'box'} me-1"></i>${i.name}</td>
                            <td><span class="badge bg-${i.category === 'equipment' ? 'primary' : 'purple'} bg-opacity-10 text-${i.category === 'equipment' ? 'primary' : 'purple'}">${i.category === 'equipment' ? 'Equipment' : 'Material'}</span></td>
                            <td class="text-center">${i.pivot.quantity}</td>
                            <td>${i.unit}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    } else {
        inventoryEl.innerHTML = '<div class="text-muted small">No equipment or materials assigned.</div>';
    }

    viewModal.show();
}

async function editPackage(id) {
    const res = await fetch(`/package/${id}`);
    const pkg = await res.json();

    document.getElementById('packageName').value = pkg.name;
    document.getElementById('packagePrice').value = pkg.price;
    document.getElementById('packageDescription').value = pkg.description || '';
    document.getElementById('packageStatus').value = pkg.status;
    setServices(pkg.services);
    setPackageInventory(pkg.inventory || []);

    document.getElementById('packageForm').action = `/package/${pkg.id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('packageModalTitle').textContent = 'Edit Package';
    document.getElementById('packageSubmitBtn').textContent = 'Update Package';
    packageModal.show();
}

function confirmDeletePackage(id, name) {
    document.getElementById('deletePackageName').textContent = name;
    document.getElementById('deletePackageForm').action = `/package/${id}`;
    deletePackageModal.show();
}

// ==================== ADD-ON FUNCTIONS ====================

function openAddAddonModal() {
    document.getElementById('addonForm').reset();
    document.getElementById('addonForm').action = '/addon';
    document.getElementById('addonFormMethod').value = 'POST';
    document.getElementById('addonModalTitle').textContent = 'Add Add-On';
    document.getElementById('addonSubmitBtn').textContent = 'Save Add-On';
    resetAddonInventory();
    addonModal.show();
}

async function viewAddon(id) {
    const res = await fetch(`/addon/${id}`);
    const addon = await res.json();

    document.getElementById('viewAddonName').textContent = addon.name;
    document.getElementById('viewAddonPrice').textContent = '\u20B1' + parseFloat(addon.price).toLocaleString('en', { minimumFractionDigits: 2 });
    document.getElementById('viewAddonDescription').textContent = addon.description || 'No description provided.';

    const statusEl = document.getElementById('viewAddonStatus');
    statusEl.innerHTML = addon.status === 'active'
        ? '<span class="badge bg-success">Active</span>'
        : '<span class="badge bg-secondary">Inactive</span>';

    const inventoryEl = document.getElementById('viewAddonInventory');
    if (addon.inventory && addon.inventory.length > 0) {
        inventoryEl.innerHTML = `
            <table class="table table-sm table-bordered mb-0">
                <thead class="table-light">
                    <tr><th>Item</th><th>Category</th><th class="text-center">Qty Needed</th><th>Unit</th></tr>
                </thead>
                <tbody>
                    ${addon.inventory.map(i => `
                        <tr>
                            <td><i class="bi bi-${i.category === 'equipment' ? 'camera' : 'box'} me-1"></i>${i.name}</td>
                            <td><span class="badge bg-${i.category === 'equipment' ? 'primary' : 'purple'} bg-opacity-10 text-${i.category === 'equipment' ? 'primary' : 'purple'}">${i.category === 'equipment' ? 'Equipment' : 'Material'}</span></td>
                            <td class="text-center">${i.pivot.quantity}</td>
                            <td>${i.unit}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
    } else {
        inventoryEl.innerHTML = '<div class="text-muted small">No equipment or materials assigned.</div>';
    }

    viewAddonModal.show();
}

async function editAddon(id) {
    const res = await fetch(`/addon/${id}`);
    const addon = await res.json();

    document.getElementById('addonName').value = addon.name;
    document.getElementById('addonPrice').value = addon.price;
    document.getElementById('addonDescription').value = addon.description || '';
    document.getElementById('addonStatus').value = addon.status;
    setAddonInventory(addon.inventory || []);

    document.getElementById('addonForm').action = `/addon/${addon.id}`;
    document.getElementById('addonFormMethod').value = 'PUT';
    document.getElementById('addonModalTitle').textContent = 'Edit Add-On';
    document.getElementById('addonSubmitBtn').textContent = 'Update Add-On';
    addonModal.show();
}

function confirmDeleteAddon(id, name) {
    document.getElementById('deleteAddonName').textContent = name;
    document.getElementById('deleteAddonForm').action = `/addon/${id}`;
    deleteAddonModal.show();
}
