/* payment-verification.js */
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
});

function previewProof(imageUrl) {
    document.getElementById('proofPreviewImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('proofPreviewModal')).show();
}

function formatPeso(amount) {
    return '\u20B1' + parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

var viewStatusBadges = {
    pending: 'bg-warning text-dark',
    approved: 'bg-success',
    ongoing: 'bg-primary',
    completed: 'bg-success',
    rejected: 'bg-danger',
    cancelled: 'bg-danger'
};

var viewPaymentBadges = {
    pending: 'bg-warning text-dark',
    verified: 'bg-success',
    rejected: 'bg-danger',
    paid: 'bg-success',
    fully_paid: 'bg-success',
    unpaid: 'bg-danger',
    balance_due: 'bg-danger'
};

function openViewModal(data) {
    document.getElementById('viewRef').textContent = data.booking_ref || '—';
    var sb = document.getElementById('viewStatusBadge');
    sb.className = 'badge fs-6 ' + (viewStatusBadges[data.status] || 'bg-secondary');
    sb.textContent = data.status ? (data.status.charAt(0).toUpperCase() + data.status.slice(1)) : '—';

    document.getElementById('viewClientName').textContent = data.client_name || '—';
    document.getElementById('viewClientEmail').textContent = data.client_email || '—';
    document.getElementById('viewClientPhone').textContent = data.client_phone || '—';

    document.getElementById('viewPackageName').textContent = data.package_name || '—';
    document.getElementById('viewPackagePrice').textContent = formatPeso(data.package_price);

    var servicesWrap = document.getElementById('viewServicesWrap');
    var servicesList = document.getElementById('viewServices');
    servicesList.innerHTML = '';
    if (data.services && data.services.length) {
        servicesWrap.style.display = 'block';
        data.services.forEach(function (s) {
            var b = document.createElement('span');
            b.className = 'badge bg-light text-dark border';
            b.textContent = s;
            servicesList.appendChild(b);
        });
    } else {
        servicesWrap.style.display = 'none';
    }

    var addonsSection = document.getElementById('viewAddonsSection');
    var addonsList = document.getElementById('viewAddonsList');
    addonsList.innerHTML = '';
    if (data.addons && data.addons.length) {
        addonsSection.style.display = 'block';
        data.addons.forEach(function (a) {
            var row = document.createElement('div');
            row.className = 'd-flex justify-content-between gap-2 py-1';
            var span = document.createElement('span');
            span.className = 'small';
            span.textContent = a.name;
            var price = document.createElement('span');
            price.className = 'small fw-medium';
            price.textContent = formatPeso(a.price);
            row.appendChild(span);
            row.appendChild(price);
            addonsList.appendChild(row);
        });
        document.getElementById('viewAddonsTotal').textContent = formatPeso(data.addons_total);
    } else {
        addonsSection.style.display = 'none';
    }

    document.getElementById('viewEventType').textContent = data.event_type || '—';
    document.getElementById('viewEventDate').textContent = data.event_date || '—';
    document.getElementById('viewEventTime').textContent = data.event_time || '—';
    document.getElementById('viewVenue').textContent = data.event_venue || '—';
    document.getElementById('viewAddress').textContent = data.event_address || '—';

    var descWrap = document.getElementById('viewEventDescWrap');
    if (data.event_description) {
        descWrap.style.display = 'block';
        document.getElementById('viewEventDesc').textContent = data.event_description;
    } else {
        descWrap.style.display = 'none';
    }

    document.getElementById('viewTotal').textContent = formatPeso(data.total_price);
    document.getElementById('viewDownpayment').textContent = formatPeso(data.downpayment);
    document.getElementById('viewBalance').textContent = formatPeso(data.balance);
    document.getElementById('viewTeam').textContent = data.team_name || '—';

    var ps = document.getElementById('viewPaymentStatus');
    if (data.payment_status) {
        var pc = viewPaymentBadges[data.payment_status] || 'bg-secondary';
        ps.innerHTML = '<span class="badge ' + pc + '">' + data.payment_status.charAt(0).toUpperCase() + data.payment_status.slice(1) + '</span>';
    } else {
        ps.innerHTML = '<span class="text-muted fst-italic">—</span>';
    }

    var notesWrap = document.getElementById('viewNotesWrap');
    if (data.notes) {
        notesWrap.style.display = 'block';
        document.getElementById('viewNotes').textContent = data.notes;
    } else {
        notesWrap.style.display = 'none';
    }

    document.getElementById('viewCreatedAt').textContent = data.created_at || '—';

    new bootstrap.Modal(document.getElementById('viewModal')).show();
}

function openVerifyModal(downpaymentId, bookingRef) {
    document.getElementById('verifyForm').action = '/admin/payment-verification/' + downpaymentId + '/verify';
    document.getElementById('verifyBookingRef').textContent = bookingRef;
    new bootstrap.Modal(document.getElementById('verifyModal')).show();
}

function openRejectModal(downpaymentId, bookingRef) {
    document.getElementById('rejectForm').action = '/admin/payment-verification/' + downpaymentId + '/reject';
    document.getElementById('rejectBookingRef').textContent = bookingRef;
    document.getElementById('rejection_reason').value = '';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}

document.getElementById('verifyForm').addEventListener('submit', function () {
    var submitBtn = document.getElementById('verifyPaymentBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Approving...';
});

document.getElementById('rejectForm').addEventListener('submit', function () {
    var submitBtn = document.getElementById('rejectPaymentBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Rejecting...';
});

// ==================== AJAX TAB SWITCHING (no page reload) ====================

(function () {
    var tabs = document.getElementById('paymentTabs');
    var tableBody = document.getElementById('paymentTableBody');
    var mobileBody = document.getElementById('paymentMobileBody');
    var paginationWrap = document.getElementById('paymentPagination');
    var totalBadge = document.getElementById('paymentTotalBadge');

    if (!tabs || !tableBody) return;

    function fetchData(url) {
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function (r) {
                if (!r.ok) throw new Error('Request failed');
                return r.json();
            })
            .then(function (data) {
                tableBody.innerHTML = data.rows;
                if (mobileBody) mobileBody.innerHTML = data.mobileRows;
                if (paginationWrap) {
                    paginationWrap.innerHTML = data.pagination;
                    if (data.pagination.trim() === '') {
                        paginationWrap.classList.add('d-none');
                    } else {
                        paginationWrap.classList.remove('d-none');
                    }
                }
                if (totalBadge) totalBadge.textContent = data.total + ' total';
            })
            .catch(function (err) {
                console.error('Payment tab fetch error:', err);
            });
    }

    tabs.addEventListener('click', function (e) {
        var link = e.target.closest ? e.target.closest('a') : null;
        if (!link || !tabs.contains(link)) return;
        e.preventDefault();

        tabs.querySelectorAll('.nav-link').forEach(function (el) {
            el.classList.remove('active');
        });
        link.classList.add('active');

        fetchData(link.href);
    });

    if (paginationWrap) {
        paginationWrap.addEventListener('click', function (e) {
            var link = e.target.closest ? e.target.closest('a') : null;
            if (!link) return;
            e.preventDefault();
            fetchData(link.href);
        });
    }
})();
