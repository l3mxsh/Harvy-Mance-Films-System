// ---- Reports: AJAX tabs, date range filter, booking search/status + pagination ----
(function () {
    var tabs = document.getElementById('reportTabs');
    var content = document.getElementById('reportTabContent');
    var rangeForm = document.getElementById('reportRangeForm');
    var rangeLabel = document.getElementById('reportRangeLabel');
    var debounceTimer = null;

    if (!tabs || !content) return;

    function headers() {
        return {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        };
    }

    function currentTab() {
        return content.getAttribute('data-tab') || 'overview';
    }

    // Current filters (tab, range, from, to) minus the AJAX-only params.
    function baseParams() {
        var params = new URLSearchParams(window.location.search);
        params.delete('content');
        params.delete('page');
        params.delete('q');
        params.delete('status');
        if (!params.get('tab')) params.set('tab', currentTab());
        return params;
    }

    function setLoading(isLoading) {
        content.style.opacity = isLoading ? '0.55' : '1';
        content.style.pointerEvents = isLoading ? 'none' : '';
    }

    // The range form lives above the tab body, so resync it after every swap.
    function syncRangeForm(data) {
        if (!rangeForm || !data || data.range === undefined) return;

        var presets = rangeForm.querySelectorAll('.report-range-presets .nav-link');
        presets.forEach(function (btn) {
            btn.classList.toggle('active', btn.value === data.range);
        });

        var from = document.getElementById('reportFrom');
        var to = document.getElementById('reportTo');
        if (from && data.from) from.value = data.from;
        if (to && data.to) to.value = data.to;

        var tabField = document.getElementById('reportRangeTab');
        if (tabField) tabField.value = data.tab || currentTab();
    }

    // Replace the whole tab body (used for tab switches and range changes).
    function fetchContent(url) {
        setLoading(true);

        return fetch(url, { headers: headers() })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.html !== undefined) {
                    content.innerHTML = data.html;
                    content.setAttribute('data-tab', data.tab || currentTab());
                }
                if (data.rangeLabel && rangeLabel) rangeLabel.textContent = data.rangeLabel;
                syncRangeForm(data);
                if (data.url && window.history && window.history.replaceState) {
                    window.history.replaceState(null, '', data.url);
                }
            })
            .catch(function (err) { console.error('Reports content fetch error:', err); })
            .then(function () { setLoading(false); });
    }

    // Refresh just the booking rows (search / status / pagination).
    function fetchRows(url) {
        return fetch(url, { headers: headers() })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var tableBody = document.getElementById('reportBookingTableBody');
                var mobileBody = document.getElementById('reportBookingMobileBody');
                var paginationWrap = document.getElementById('reportBookingPagination');
                var totalBadge = document.getElementById('reportBookingTotalBadge');

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
            .catch(function (err) { console.error('Reports rows fetch error:', err); });
    }

    function buildRowsUrl(page) {
        var params = baseParams();
        params.set('tab', 'bookings');
        params.set('content', 'rows');

        var search = document.getElementById('reportBookingSearch');
        var status = document.getElementById('reportBookingStatus');

        if (search && search.value.trim()) params.set('q', search.value.trim());
        if (status && status.value) params.set('status', status.value);
        if (page) params.set('page', page);

        return '/admin/reports?' + params.toString();
    }

    // ---- Tab switching ----
    tabs.addEventListener('click', function (e) {
        var link = e.target.closest ? e.target.closest('a') : null;
        if (!link || !tabs.contains(link)) return;

        e.preventDefault();

        var active = tabs.querySelector('.nav-link.active');
        if (active === link) return;

        tabs.querySelectorAll('.nav-link').forEach(function (el) { el.classList.remove('active'); });
        link.classList.add('active');

        var sep = link.href.indexOf('?') > -1 ? '&' : '?';
        fetchContent(link.href + sep + 'content=tab');
    });

    // ---- Date range filter (presets + custom apply) ----
    if (rangeForm) {
        rangeForm.addEventListener('submit', function (e) {
            e.preventDefault();

            var formData = new FormData(e.target, e.submitter);
            var params = baseParams();
            params.delete('range');
            params.delete('from');
            params.delete('to');

            formData.forEach(function (value, key) {
                if (key === 'tab') return;
                params.set(key, value);
            });

            params.set('content', 'tab');
            fetchContent('/admin/reports?' + params.toString());
        });
    }

    // ---- Booking search / status filter / pagination ----
    content.addEventListener('input', function (e) {
        if (!e.target.matches || !e.target.matches('#reportBookingSearch')) return;
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(function () { fetchRows(buildRowsUrl()); }, 400);
    });

    content.addEventListener('change', function (e) {
        if (!e.target.matches || !e.target.matches('#reportBookingStatus')) return;
        fetchRows(buildRowsUrl());
    });

    content.addEventListener('click', function (e) {
        var link = e.target.closest ? e.target.closest('#reportBookingPagination a') : null;
        if (!link || !content.contains(link)) return;

        e.preventDefault();
        fetchRows(link.href);
    });
})();
