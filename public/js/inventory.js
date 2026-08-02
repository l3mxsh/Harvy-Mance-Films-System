let itemModal, viewItemModal, deleteItemModal;

document.addEventListener('DOMContentLoaded', function () {
    itemModal = new bootstrap.Modal(document.getElementById('itemModal'));
    viewItemModal = new bootstrap.Modal(document.getElementById('viewItemModal'));
    deleteItemModal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
});

function openAddModal() {
    document.getElementById('itemForm').reset();
    document.getElementById('itemForm').action = '/inventory';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('itemModalTitle').textContent = 'Add Item';
    document.getElementById('itemSubmitBtn').textContent = 'Save Item';
    itemModal.show();
}

async function viewItem(id) {
    const res = await fetch(`/inventory/${id}`);
    const item = await res.json();

    document.getElementById('viewName').textContent = item.name;
    document.getElementById('viewDescription').textContent = item.description || 'No description provided.';
    document.getElementById('viewQuantity').textContent = item.quantity + ' ' + item.unit;

    const categoryEl = document.getElementById('viewCategory');
    categoryEl.innerHTML = item.category === 'equipment'
        ? '<span class="badge bg-primary bg-opacity-10 text-primary">Equipment</span>'
        : '<span class="badge bg-purple bg-opacity-10 text-purple">Material</span>';

    const conditionLabels = { new: 'New', good: 'Good', maintenance: 'Maintenance', damaged: 'Damaged' };
    const conditionColors = { new: 'success', good: 'info', maintenance: 'warning text-dark', damaged: 'danger' };
    const condEl = document.getElementById('viewCondition');
    condEl.innerHTML = `<span class="badge bg-${conditionColors[item.condition_status]}">${conditionLabels[item.condition_status]}</span>`;

    const availLabels = { available: 'Available', in_use: 'In Use', reserved: 'Reserved', unavailable: 'Unavailable' };
    const availColors = { available: 'success', in_use: 'info', reserved: 'warning text-dark', unavailable: 'secondary' };
    const availEl = document.getElementById('viewAvailability');
    availEl.innerHTML = `<span class="badge bg-${availColors[item.availability_status]}">${availLabels[item.availability_status]}</span>`;

    document.getElementById('viewUnit').textContent = item.unit;

    viewItemModal.show();
}

async function editItem(id) {
    const res = await fetch(`/inventory/${id}`);
    const item = await res.json();

    document.getElementById('itemName').value = item.name;
    document.getElementById('itemCategory').value = item.category;
    document.getElementById('itemDescription').value = item.description || '';
    document.getElementById('itemQuantity').value = item.quantity;
    document.getElementById('itemUnit').value = item.unit;
    document.getElementById('itemCondition').value = item.condition_status;
    document.getElementById('itemAvailability').value = item.availability_status;

    document.getElementById('itemForm').action = `/inventory/${item.id}`;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('itemModalTitle').textContent = 'Edit Item';
    document.getElementById('itemSubmitBtn').textContent = 'Update Item';
    itemModal.show();
}

function confirmDeleteItem(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteItemForm').action = `/inventory/${id}`;
    deleteItemModal.show();
}
