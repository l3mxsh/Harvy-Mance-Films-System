// ---- Cancellations: AJAX search/filter (no page reloads) ----
(function () {
    var input = document.getElementById('cancellationSearchInput');
    var statusSelect = document.getElementById('cancellationStatusFilter');
    var tableBody = document.getElementById('cancellationTableBody');
    var paginationWrap = document.getElementById('cancellationPagination');
    var totalBadge = document.getElementById('cancellationTotalBadge');
    var debounceTimer = null;

    if (!input || !statusSelect) return;

    function buildUrl() {
        var params = new URLSearchParams();
        var s = input.value.trim();
        var st = statusSelect.value;
        if (s) params.set('search', s);
        if (st) params.set('status', st);
        var qs = params.toString();
        return '/admin/cancellations' + (qs ? '?' + qs : '');
    }

    function fetchCancellations(url) {
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
        fetchCancellations(buildUrl());
    });

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { fetchCancellations(buildUrl()); }, 400);
    });

    document.addEventListener('click', function (e) {
        var link = e.target.closest ? e.target.closest('#cancellationPagination a') : null;
        if (!link) return;
        e.preventDefault();
        fetchCancellations(link.href);
    });
})();
