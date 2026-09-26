// ---- Clients: AJAX search (no page reloads) ----
(function () {
    var input = document.getElementById('clientSearchInput');
    var tableBody = document.getElementById('clientTableBody');
    var mobileBody = document.getElementById('clientMobileBody');
    var paginationWrap = document.getElementById('clientPagination');
    var totalBadge = document.getElementById('clientTotalBadge');
    var debounceTimer = null;

    if (!input || !tableBody) return;

    function buildUrl() {
        var params = new URLSearchParams();
        var s = input.value.trim();
        if (s) params.set('search', s);
        var qs = params.toString();
        return '/admin/clients' + (qs ? '?' + qs : '');
    }

    function fetchClients(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.rows !== undefined) tableBody.innerHTML = data.rows;
                if (data.mobileRows !== undefined && mobileBody) mobileBody.innerHTML = data.mobileRows;
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

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { fetchClients(buildUrl()); }, 400);
    });

    document.addEventListener('click', function (e) {
        var link = e.target.closest ? e.target.closest('#clientPagination a') : null;
        if (!link) return;
        e.preventDefault();
        fetchClients(link.href);
    });
})();

// ---- Clients: Restore confirmation modal ----
(function () {
    var restoreModal = document.getElementById('restoreModal');
    if (!restoreModal) return;

    restoreModal.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        if (!btn) return;
        var form = document.getElementById('restoreForm');
        var name = document.getElementById('restoreClientName');
        var control = document.getElementById('restoreClientControl');
        if (form) form.action = '/admin/clients/' + btn.dataset.id + '/restore';
        if (name) name.textContent = btn.dataset.name;
        if (control) control.textContent = btn.dataset.control;
    });
})();

// ---- Clients: Archive / Delete confirmation modals ----
(function () {
    function bindModal(modalId, formId, nameId, controlId, actionBuilder) {
        var modal = document.getElementById(modalId);
        if (!modal) return;
        modal.addEventListener('show.bs.modal', function (event) {
            var btn = event.relatedTarget;
            if (!btn) return;
            var form = document.getElementById(formId);
            var name = document.getElementById(nameId);
            var control = document.getElementById(controlId);
            if (form) form.action = actionBuilder(btn.dataset.id);
            if (name) name.textContent = btn.dataset.name;
            if (control) control.textContent = btn.dataset.control;
        });
    }

    bindModal('archiveModal', 'archiveForm', 'archiveClientName', 'archiveClientControl', function (id) {
        return '/admin/clients/' + id + '/archive';
    });
    bindModal('deleteModal', 'deleteForm', 'deleteClientName', 'deleteClientControl', function (id) {
        return '/admin/clients/' + id;
    });
})();
