// ---- Clients: AJAX search (no page reloads) ----
(function () {
    var input = document.getElementById('clientSearchInput');
    var tableBody = document.getElementById('clientTableBody');
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
