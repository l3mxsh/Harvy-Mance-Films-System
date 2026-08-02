// ---- Staff Management: modal helpers ----
function openViewModal(id, name, email, contact, status, joined, lastLogin) {
    document.getElementById('viewName').textContent = name;
    document.getElementById('viewEmail').textContent = email;
    document.getElementById('viewContact').textContent = contact || '—';
    document.getElementById('viewStatus').innerHTML = status === 'active'
        ? '<span class="badge bg-success">Active</span>'
        : '<span class="badge bg-secondary">Inactive</span>';
    document.getElementById('viewJoined').textContent = joined;
    document.getElementById('viewLastLogin').textContent = lastLogin;
    new bootstrap.Modal(document.getElementById('viewModal')).show();
}

function openCreateModal() {
    document.querySelector('#createModal form').reset();
    new bootstrap.Modal(document.getElementById('createModal')).show();
}

function openEditModal(id, name, email, contact, status, isOutsourced) {
    document.getElementById('editForm').action = '/admin/staff/' + id;
    document.getElementById('editName').value = name;
    document.getElementById('editEmail').value = email;
    document.getElementById('editContact').value = contact || '';

    var isActive = status === 'active';
    document.getElementById('editStatusLabel').textContent = isActive ? 'Active' : 'Inactive';
    document.getElementById('editStatusSwitch').checked = isActive;
    document.getElementById('editToggleForm').action = '/admin/staff/' + id + '/toggle-status';

    document.getElementById('editGenerateForm').action = '/admin/staff/' + id + '/generate-password';

    document.getElementById('editGenerateSection').classList.toggle('d-none', !isOutsourced);
    document.getElementById('editPasswordSection').classList.toggle('d-none', !!isOutsourced);

    document.getElementById('editNotify').value = '0';
    document.getElementById('editNewPassword').value = '';
    document.getElementById('editNewPasswordConfirmation').value = '';

    new bootstrap.Modal(document.getElementById('editModal')).show();
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
    var btn = document.getElementById('editGeneratePwBtn');
    if (btn) {
        btn.addEventListener('click', function () {
            var password = generateRandomPassword();
            document.getElementById('editNewPassword').value = password;
            document.getElementById('editNewPasswordConfirmation').value = password;
        });
    }

    var emailBtn = document.getElementById('editEmailPwBtn');
    if (emailBtn) {
        emailBtn.addEventListener('click', function () {
            applyButtonSpinner(emailBtn);
            document.getElementById('editNotify').value = '1';
            document.getElementById('editForm').submit();
        });
    }

    var statusSwitch = document.getElementById('editStatusSwitch');
    if (statusSwitch) {
        statusSwitch.addEventListener('change', function () {
            var wrap = statusSwitch.closest('.form-check');
            if (wrap) {
                wrap.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }
            document.getElementById('editToggleForm').submit();
        });
    }
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

document.addEventListener('click', function (e) {
    var btn = e.target.closest('.toggle-password');
    if (!btn) return;
    var input = document.getElementById(btn.getAttribute('data-target'));
    if (!input) return;
    var show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.querySelector('i').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});

function openDeleteModal(id, name, email) {
    document.getElementById('deleteForm').action = '/admin/staff/' + id;
    document.getElementById('deleteStaffName').textContent = name;
    document.getElementById('deleteStaffEmail').textContent = email;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function openCreateOutsourcedModal() {
    document.querySelector('#createOutsourcedModal form').reset();
    new bootstrap.Modal(document.getElementById('createOutsourcedModal')).show();
}

function openEditOutsourcedModal(id, name, email, contact, notes) {
    document.getElementById('editOutsourcedForm').action = '/admin/outsourced-staff/' + id;
    document.getElementById('editOsName').value = name;
    document.getElementById('editOsEmail').value = email || '';
    document.getElementById('editOsContact').value = contact || '';
    document.getElementById('editOsNotes').value = notes || '';
    new bootstrap.Modal(document.getElementById('editOutsourcedModal')).show();
}

function openDeleteOutsourcedModal(id, name) {
    document.getElementById('deleteOutsourcedForm').action = '/admin/outsourced-staff/' + id;
    document.getElementById('deleteOsName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteOutsourcedModal')).show();
}

// ---- Teams: modal helpers ----
function openCreateTeamModal() {
    document.querySelector('#createTeamModal form').reset();
    new bootstrap.Modal(document.getElementById('createTeamModal')).show();
}

function openEditTeamModal(id, name, desc, memberIds, outsourcedIds) {
    document.getElementById('editTeamForm').action = '/admin/team/' + id;
    document.getElementById('editTeamName').value = name;
    document.getElementById('editTeamDesc').value = desc;
    document.querySelectorAll('.edit-member-check').forEach(cb => {
        cb.checked = memberIds.includes(parseInt(cb.value));
    });
    document.querySelectorAll('.edit-outsourced-check').forEach(cb => {
        cb.checked = outsourcedIds.includes(parseInt(cb.value));
    });
    new bootstrap.Modal(document.getElementById('editTeamModal')).show();
}

function openToggleTeamModal(id, name, status) {
    var isDeactivating = status === 'active';
    document.getElementById('toggleTeamHeader').className = 'modal-header';
    document.getElementById('toggleTeamTitle').innerHTML = isDeactivating
        ? '<i class="bi bi-pause-circle me-2"></i>Deactivate Team'
        : '<i class="bi bi-play-circle me-2"></i>Activate Team';
    document.getElementById('toggleTeamMessage').innerHTML = isDeactivating
        ? 'Deactivate <strong>' + name + '</strong>? It cannot be assigned to new bookings while inactive.'
        : 'Activate <strong>' + name + '</strong>? It will be available for booking assignments.';
    document.getElementById('toggleTeamBtn').className = 'btn rounded-pill ' + (isDeactivating ? 'btn-warning' : 'btn-success');
    document.getElementById('toggleTeamForm').action = '/admin/team/' + id + '/toggle-status';
    new bootstrap.Modal(document.getElementById('toggleTeamModal')).show();
}

function openDeleteTeamModal(id, name) {
    document.getElementById('deleteTeamForm').action = '/admin/team/' + id;
    document.getElementById('deleteTeamName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteTeamModal')).show();
}

// ---- Teams: auto-submit search/filter (no Filter button) ----
(function () {
    var form = document.getElementById('teamFilterForm');
    if (!form) return;

    var input = form.querySelector('input[name="search"]');
    var select = form.querySelector('select[name="status"]');
    var timer = null;

    input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { form.submit(); }, 500);
    });

    select.addEventListener('change', function () {
        form.submit();
    });
})();

// ---- Client-side search/filter (AJAX, no page reloads) ----
(function () {
    var input = document.getElementById('staffSearchInput');
    var statusSelect = document.getElementById('staffStatusFilter');
    var tableBody = document.getElementById('staffTableBody');
    var paginationWrap = document.getElementById('staffPagination');
    var totalBadge = document.getElementById('staffTotalBadge');
    var debounceTimer = null;

    if (!input || !statusSelect) return;

    function buildUrl() {
        var params = new URLSearchParams();
        var tab = new URLSearchParams(window.location.search).get('tab');
        var s = input.value.trim();
        var st = statusSelect.value;
        if (tab) params.set('tab', tab);
        if (s) params.set('search', s);
        if (st) params.set('status', st);
        var qs = params.toString();
        return '/admin/staff' + (qs ? '?' + qs : '');
    }

    function fetchStaff(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.rows !== undefined) tableBody.innerHTML = data.rows;
                if (data.pagination !== undefined) {
                    paginationWrap.innerHTML = data.pagination;
                    if (data.pagination.trim() === '') {
                        paginationWrap.classList.add('d-none');
                    } else {
                        paginationWrap.classList.remove('d-none');
                    }
                }
                if (data.total !== undefined && totalBadge) totalBadge.textContent = data.total + ' total';
            });
    }

    statusSelect.addEventListener('change', function () {
        fetchStaff(buildUrl());
    });

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { fetchStaff(buildUrl()); }, 400);
    });

    document.addEventListener('click', function (e) {
        var link = e.target.closest ? e.target.closest('#staffPagination a') : null;
        if (!link) return;
        e.preventDefault();
        fetchStaff(link.href);
    });
})();
