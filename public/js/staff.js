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

    var teamStatusSwitch = document.getElementById('editTeamStatusSwitch');
    if (teamStatusSwitch) {
        teamStatusSwitch.addEventListener('change', function () {
            var wrap = teamStatusSwitch.closest('.form-check');
            if (wrap) {
                wrap.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            }
            document.getElementById('editTeamToggleForm').submit();
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

function openViewOutsourcedModal(id, name, email, contact, notes, added) {
    document.getElementById('viewOsName').textContent = name;
    document.getElementById('viewOsEmail').textContent = email || '—';
    document.getElementById('viewOsContact').textContent = contact || '—';
    document.getElementById('viewOsNotes').textContent = notes || '—';
    document.getElementById('viewOsAdded').textContent = added;
    new bootstrap.Modal(document.getElementById('viewOutsourcedModal')).show();
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
function escapeHtml(value) {
    var el = document.createElement('div');
    el.textContent = value == null ? '' : String(value);
    return el.innerHTML;
}

function openCreateTeamModal() {
    document.querySelector('#createTeamModal form').reset();
    new bootstrap.Modal(document.getElementById('createTeamModal')).show();
}

function openEditTeamModal(id, name, desc, memberIds, outsourcedIds, status) {
    document.getElementById('editTeamForm').action = '/admin/team/' + id;
    document.getElementById('editTeamName').value = name;
    document.getElementById('editTeamDesc').value = desc;
    document.querySelectorAll('.edit-member-check').forEach(cb => {
        cb.checked = memberIds.includes(parseInt(cb.value));
    });
    document.querySelectorAll('.edit-outsourced-check').forEach(cb => {
        cb.checked = outsourcedIds.includes(parseInt(cb.value));
    });

    var isActive = status === 'active';
    document.getElementById('editTeamStatusLabel').textContent = isActive ? 'Active' : 'Inactive';
    document.getElementById('editTeamStatusSwitch').checked = isActive;
    document.getElementById('editTeamToggleForm').action = '/admin/team/' + id + '/toggle-status';

    new bootstrap.Modal(document.getElementById('editTeamModal')).show();
}

function openViewTeamModal(args) {
    var name = args[1];
    var desc = args[2];
    var members = args[3];
    var status = args[4];
    var created = args[5];

    document.getElementById('viewTeamName').textContent = name;
    document.getElementById('viewTeamDesc').textContent = desc || '—';

    var wrap = document.getElementById('viewTeamMembers');
    if (!members || !members.length) {
        wrap.innerHTML = '<span class="text-muted fst-italic">No members</span>';
    } else {
        wrap.innerHTML = members.map(function (m) {
            var chip = '<span class="member-chip' + (m.o ? ' member-chip-os' : '') + '">' + escapeHtml(m.n);
            if (m.o) chip += ' <small>(OS)</small>';
            return chip + '</span>';
        }).join(' ');
    }

    document.getElementById('viewTeamStatus').innerHTML = status === 'active'
        ? '<span class="badge bg-success">Active</span>'
        : '<span class="badge bg-secondary">Inactive</span>';
    document.getElementById('viewTeamCreated').textContent = created;
    new bootstrap.Modal(document.getElementById('viewTeamModal')).show();
}

function openDeleteTeamModal(id, name) {
    document.getElementById('deleteTeamForm').action = '/admin/team/' + id;
    document.getElementById('deleteTeamName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteTeamModal')).show();
}

// ---- AJAX tab switching + search/filter + pagination (no page reloads) ----
(function () {
    var tabs = document.getElementById('staffTabs');
    var content = document.getElementById('staffTabContent');
    var debounceTimer = null;

    if (!tabs || !content) return;

    function currentTab() {
        return content.getAttribute('data-tab') || 'all';
    }

    function headers() {
        return {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        };
    }

    function buildRowsUrl() {
        var params = new URLSearchParams();
        params.set('tab', currentTab());
        var s = content.querySelector('#staffSearchInput') || content.querySelector('#teamSearchInput');
        var st = content.querySelector('#staffStatusFilter') || content.querySelector('#teamStatusFilter');
        if (s && s.value.trim()) params.set('search', s.value.trim());
        if (st && st.value) params.set('status', st.value);
        var qs = params.toString();
        return '/admin/staff' + (qs ? '?' + qs : '');
    }

    function fetchRows(url) {
        fetch(url, { headers: headers() })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var isTeams = currentTab() === 'teams';
                var tableBody = document.getElementById(isTeams ? 'teamTableBody' : 'staffTableBody');
                var mobileBody = document.getElementById(isTeams ? 'teamMobileBody' : 'staffMobileBody');
                var paginationWrap = document.getElementById(isTeams ? 'teamPagination' : 'staffPagination');
                var totalBadge = document.getElementById(isTeams ? 'teamTotalBadge' : 'staffTotalBadge');
                if (data.rows !== undefined && tableBody) tableBody.innerHTML = data.rows;
                if (data.mobileRows !== undefined && mobileBody) mobileBody.innerHTML = data.mobileRows;
                if (data.pagination !== undefined && paginationWrap) {
                    paginationWrap.innerHTML = data.pagination;
                    if (data.pagination.trim() === '') {
                        paginationWrap.classList.add('d-none');
                    } else {
                        paginationWrap.classList.remove('d-none');
                    }
                }
                if (data.total !== undefined && totalBadge) totalBadge.textContent = data.total + ' total';
            })
            .catch(function (err) { console.error('Staff rows fetch error:', err); });
    }

    function fetchTab(url) {
        var sep = url.indexOf('?') > -1 ? '&' : '?';
        fetch(url + sep + 'content=tab', { headers: headers() })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.html !== undefined) content.innerHTML = data.html;
            })
            .catch(function (err) { console.error('Staff tab fetch error:', err); });
    }

    tabs.addEventListener('click', function (e) {
        var link = e.target.closest ? e.target.closest('a') : null;
        if (!link || !tabs.contains(link)) return;
        e.preventDefault();
        tabs.querySelectorAll('.nav-link').forEach(function (el) { el.classList.remove('active'); });
        link.classList.add('active');
        fetchTab(link.href);
    });

    content.addEventListener('click', function (e) {
        var link = e.target.closest ? e.target.closest('.page-link') : null;
        if (!link || !content.contains(link)) return;
        e.preventDefault();
        fetchRows(link.href);
    });

    content.addEventListener('input', function (e) {
        if (!e.target.matches || !e.target.matches('#staffSearchInput, #teamSearchInput')) return;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { fetchRows(buildRowsUrl()); }, 400);
    });

    content.addEventListener('change', function (e) {
        if (!e.target.matches || !e.target.matches('#staffStatusFilter, #teamStatusFilter')) return;
        fetchRows(buildRowsUrl());
    });
})();
