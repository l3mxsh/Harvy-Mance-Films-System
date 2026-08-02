// ---- User Management: modal helpers ----
function openCreateModal() {
    document.querySelector('#createModal form').reset();
    new bootstrap.Modal(document.getElementById('createModal')).show();
}

function openEditModal(id, name, email) {
    document.getElementById('editForm').action = '/admin/users/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editNewPassword').value = '';
    document.getElementById('editNewPasswordConfirmation').value = '';
    new bootstrap.Modal(document.getElementById('editModal')).show();
}

function openToggleModal(id, name, status) {
    var isDeactivating = status === 'active';
    document.getElementById('toggleModalHeader').className = 'modal-header';
    document.getElementById('toggleModalTitle').innerHTML = isDeactivating
        ? 'Deactivate Admin'
        : 'Activate Admin';
    document.getElementById('toggleMessage').innerHTML = isDeactivating
        ? 'Deactivate <strong>' + name + '</strong>? They will no longer be able to log in.'
        : 'Activate <strong>' + name + '</strong>? They will be able to log in again.';
    var btn = document.getElementById('toggleSubmitBtn');
    btn.className = 'btn rounded-pill ' + (isDeactivating ? 'btn-warning' : 'btn-success');
    btn.innerHTML = isDeactivating ? 'Deactivate' : 'Activate';
    document.getElementById('toggleForm').action = '/admin/users/' + id + '/toggle-status';
    new bootstrap.Modal(document.getElementById('toggleModal')).show();
}

function openDeleteModal(id, name, email) {
    document.getElementById('deleteForm').action = '/admin/users/' + id;
    document.getElementById('deleteUserName').textContent = name;
    document.getElementById('deleteUserEmail').textContent = email;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function generateRandomPassword() {
    var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789';
    var password = '';
    for (var i = 0; i < 10; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return password;
}

document.addEventListener('DOMContentLoaded', function () {
    [document.getElementById('editGeneratePwBtn'), document.getElementById('createGeneratePwBtn')].forEach(function (btn) {
        if (btn) {
            btn.addEventListener('click', function () {
                var password = generateRandomPassword();
                var isEdit = btn.id === 'editGeneratePwBtn';
                var passwordId = isEdit ? 'editNewPassword' : 'createPassword';
                var confirmationId = isEdit ? 'editNewPasswordConfirmation' : 'createPasswordConfirmation';
                document.getElementById(passwordId).value = password;
                document.getElementById(confirmationId).value = password;
            });
        }
    });
});

document.addEventListener('click', function (e) {
    var btn = e.target.closest('.toggle-password');
    if (!btn) return;
    var input = document.getElementById(btn.getAttribute('data-target'));
    if (!input) return;
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});

function applyButtonSpinner(btn) {
    if (!btn || btn.disabled) return;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Processing...';
}

document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || !form.closest('.modal')) return;
    var btn = e.submitter;
    if (!btn || btn.tagName !== 'BUTTON') return;
    applyButtonSpinner(btn);
});
