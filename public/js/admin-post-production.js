// ---- Post-Production: AJAX search (no page reloads) ----
(function () {
    var input = document.getElementById('ppSearchInput');
    var tableBody = document.getElementById('ppTableBody');
    var mobileBody = document.getElementById('ppMobileBody');
    var paginationWrap = document.getElementById('ppPagination');
    var totalBadge = document.getElementById('ppTotalBadge');
    var debounceTimer = null;

    if (!input || !tableBody) return;

    function buildUrl() {
        var params = new URLSearchParams();
        var s = input.value.trim();
        if (s) params.set('search', s);
        var qs = params.toString();
        return '/admin/post-production' + (qs ? '?' + qs : '');
    }

    function fetchRows(url) {
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
        debounceTimer = setTimeout(function () { fetchRows(buildUrl()); }, 400);
    });

    document.addEventListener('click', function (e) {
        var link = e.target.closest ? e.target.closest('#ppPagination a') : null;
        if (!link) return;
        e.preventDefault();
        fetchRows(link.href);
    });
})();
