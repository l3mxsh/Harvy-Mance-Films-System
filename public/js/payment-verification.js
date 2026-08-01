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
    completed: 'bg-secondary',
    rejected: 'bg-danger',
    cancelled: 'bg-danger'
};

var viewPaymentBadges = {
    pending: 'bg-warning text-dark',
    verified: 'bg-success',
    rejected: 'bg-danger'
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
