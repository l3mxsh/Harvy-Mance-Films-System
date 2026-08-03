// ---- Activity Logs: details modal + AJAX search/filter ----
function openLogModal(btn) {
    var d = btn.dataset;
    document.getElementById('logDetailAction').textContent = d.action;
    document.getElementById('logDetailActor').textContent = d.actor;
    document.getElementById('logDetailType').textContent = d.type.charAt(0).toUpperCase() + d.type.slice(1);
    document.getElementById('logDetailTime').textContent = d.time;
    document.getElementById('logDetailDescription').textContent = d.description;

    var contextWrap = document.getElementById('logDetailContextWrap');
    var contextPre = document.getElementById('logDetailContext');
    if (d.context && d.context.trim() !== 'null' && d.context.trim() !== '') {
        try {
            contextPre.textContent = JSON.stringify(JSON.parse(d.context), null, 2);
            contextWrap.style.display = '';
        } catch (e) {
            contextWrap.style.display = 'none';
        }
    } else {
        contextWrap.style.display = 'none';
    }

    new bootstrap.Modal(document.getElementById('logDetailsModal')).show();
}

(function () {
    var input = document.getElementById('logSearchInput');
    var actionSelect = document.getElementById('logActionFilter');
    var typeSelect = document.getElementById('logUserTypeFilter');
    var dateFrom = document.getElementById('logDateFrom');
    var dateTo = document.getElementById('logDateTo');
    var tableBody = document.getElementById('logTableBody');
    var paginationWrap = document.getElementById('logPagination');
    var totalBadge = document.getElementById('logTotalBadge');
    var debounceTimer = null;

    if (!input || !actionSelect) return;

    function buildUrl() {
        var params = new URLSearchParams();
        var s = input.value.trim();
        var a = actionSelect.value;
        var t = typeSelect.value;
        var df = dateFrom.value;
        var dt = dateTo.value;
        if (s) params.set('search', s);
        if (a) params.set('action', a);
        if (t) params.set('user_type', t);
        if (df) params.set('date_from', df);
        if (dt) params.set('date_to', dt);
        var qs = params.toString();
        return '/admin/activity-logs' + (qs ? '?' + qs : '');
    }

    function fetchLogs(url) {
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

    function refresh() {
        fetchLogs(buildUrl());
    }

    [actionSelect, typeSelect, dateFrom, dateTo].forEach(function (el) {
        if (el) el.addEventListener('change', refresh);
    });

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(refresh, 400);
    });

    document.addEventListener('click', function (e) {
        var link = e.target.closest ? e.target.closest('#logPagination a') : null;
        if (!link) return;
        e.preventDefault();
        fetchLogs(link.href);
    });
})();
