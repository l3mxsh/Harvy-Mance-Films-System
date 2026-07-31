var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

var currentEventDate = '';
var rrCurrentDate = '';
var currentBookingId = null;

function openRescheduleApproveModal(rrId, bookingRef, clientName, newDate, newTime) {
    document.getElementById('rescheduleApproveForm').action = '/admin/reschedule/' + rrId + '/approve';
    document.getElementById('rrApproveRef').textContent = bookingRef;
    document.getElementById('rrApproveClient').textContent = clientName;
    document.getElementById('rrApproveDate').textContent = newDate;
    document.getElementById('rrApproveTime').textContent = newTime;
    document.getElementById('rrApproveTeamSelect').value = '';
    document.getElementById('rrTeamMembersPreview').style.display = 'none';
    document.getElementById('rrAvailabilityResult').style.display = 'none';
    rrCurrentDate = newDate;
    new bootstrap.Modal(document.getElementById('rescheduleApproveModal')).show();
}

function checkRescheduleTeamAvailability() {
    var select = document.getElementById('rrApproveTeamSelect');
    var teamId = select.value;
    var membersPreview = document.getElementById('rrTeamMembersPreview');
    var membersList = document.getElementById('rrTeamMembersList');
    var resultDiv = document.getElementById('rrAvailabilityResult');

    if (!teamId) { membersPreview.style.display = 'none'; resultDiv.style.display = 'none'; return; }

    var memberNames = select.options[select.selectedIndex].getAttribute('data-member-names');
    membersList.innerHTML = memberNames.split(', ').map(function (n) {
        var isOS = n.endsWith(' (OS)');
        var label = isOS ? n.replace(' (OS)', '') : n;
        return '<span class="badge me-1 mb-1 ' + (isOS ? 'bg-warning text-dark' : 'bg-light text-dark border') + '">' + label + (isOS ? ' <small>(OS)</small>' : '') + '</span>';
    }).join('');
    membersPreview.style.display = 'block';

    fetch('/api/staff-schedule/check-availability', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ team_id: teamId, event_date: rrCurrentDate })
    })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            resultDiv.style.display = 'block';
            if (data.available) {
                resultDiv.innerHTML = '<div class="text-success fw-medium small mb-0 text-center"><i class="bi bi-check-circle me-1"></i>' + data.message + '</div>';
                document.getElementById('rrApproveBtn').disabled = false;
            } else {
                var names = data.unavailable_members.map(function (m) { return m.name + ' (has booking ' + m.booking_ref + ')'; }).join(', ');
                resultDiv.innerHTML = '<div class="alert alert-soft alert-soft-danger py-2 mb-0"><i class="bi bi-exclamation-triangle me-1"></i><strong>Conflict:</strong> ' + names + '</div>';
                document.getElementById('rrApproveBtn').disabled = true;
            }
        })
        .catch(function () {
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<div class="alert alert-soft alert-soft-warning py-2 mb-0"><i class="bi bi-info-circle me-1"></i>Could not check availability.</div>';
            document.getElementById('rrApproveBtn').disabled = false;
        });
}

function openRescheduleRejectModal(rrId, bookingRef) {
    document.getElementById('rescheduleRejectForm').action = '/admin/reschedule/' + rrId + '/reject';
    document.getElementById('rrRejectRef').textContent = bookingRef;
    document.querySelector('#rescheduleRejectForm textarea').value = '';
    new bootstrap.Modal(document.getElementById('rescheduleRejectModal')).show();
}

function openApproveModal(bookingId, bookingRef, clientName, eventDate) {
    document.getElementById('approveForm').action = '/admin/booking/' + bookingId + '/approve';
    document.getElementById('approveBookingRef').textContent = bookingRef;
    document.getElementById('approveClientName').textContent = clientName;
    document.getElementById('approveEventDate').textContent = eventDate;
    document.getElementById('approveTeamSelect').value = '';
    document.getElementById('teamMembersPreview').style.display = 'none';
    document.getElementById('availabilityResult').style.display = 'none';
    currentEventDate = eventDate;
    currentBookingId = bookingId;
    new bootstrap.Modal(document.getElementById('approveModal')).show();
}

function checkTeamAvailability() {
    var select = document.getElementById('approveTeamSelect');
    var teamId = select.value;
    var membersPreview = document.getElementById('teamMembersPreview');
    var membersList = document.getElementById('teamMembersList');
    var resultDiv = document.getElementById('availabilityResult');

    if (!teamId) {
        membersPreview.style.display = 'none';
        resultDiv.style.display = 'none';
        return;
    }

    var memberNames = select.options[select.selectedIndex].getAttribute('data-member-names');
    membersList.innerHTML = memberNames.split(', ').map(function (name) {
        var isOS = name.endsWith(' (OS)');
        var label = isOS ? name.replace(' (OS)', '') : name;
        return '<span class="badge me-1 mb-1 ' + (isOS ? 'bg-warning text-dark' : 'bg-light text-dark border') + '">' + label + (isOS ? ' <small>(OS)</small>' : '') + '</span>';
    }).join('');
    membersPreview.style.display = 'block';

    fetch('/api/staff-schedule/check-availability', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ team_id: teamId, event_date: currentEventDate })
    })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            resultDiv.style.display = 'block';
            if (data.available) {
                resultDiv.innerHTML = '<div class="text-success fw-medium small mb-0 text-center"><i class="bi bi-check-circle me-1"></i> ' + data.message + '</div>';
                document.getElementById('approveBtn').disabled = false;
            } else {
                var names = data.unavailable_members.map(function (m) { return m.name + ' (has booking ' + m.booking_ref + ')'; }).join(', ');
                resultDiv.innerHTML = '<div class="alert alert-soft alert-soft-danger py-2 mb-0"><i class="bi bi-exclamation-triangle me-1"></i> <strong>Conflict:</strong> ' + names + '</div>';
                document.getElementById('approveBtn').disabled = true;
            }
        })
        .catch(function () {
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<div class="alert alert-soft alert-soft-warning py-2 mb-0"><i class="bi bi-info-circle me-1"></i> Could not check availability. Proceed with caution.</div>';
            document.getElementById('approveBtn').disabled = false;
        });
}

function formatPeso(amount) {
    return '\u20B1' + parseFloat(amount).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function showApproveResultModal(message, isSuccess) {
    document.getElementById('approveResultMsg').textContent = message;
    var icon = document.getElementById('approveResultIcon');
    if (isSuccess) {
        icon.className = 'fs-1 mb-2 text-success';
        icon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
    } else {
        icon.className = 'fs-1 mb-2 text-danger';
        icon.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
    }
    new bootstrap.Modal(document.getElementById('approveResultModal')).show();
}

function refreshApprovedRow(bookingId, teamName) {
    var payload = window.viewPayloads && window.viewPayloads[bookingId];
    if (payload) {
        payload.status = 'approved';
        payload.team_name = teamName || payload.team_name;
    }

    var badge = document.getElementById('status-badge-' + bookingId);
    if (badge) {
        badge.className = 'badge bg-success';
        badge.textContent = 'Approved';
    }

    var payment = document.getElementById('payment-cell-' + bookingId);
    if (payment) {
        payment.innerHTML = '<span class="badge bg-info text-white"><i class="bi bi-clock me-1"></i>Awaiting</span>';
    }

    var actions = document.getElementById('actions-cell-' + bookingId);
    if (actions) {
        actions.innerHTML = '<div class="d-flex gap-2 justify-content-center flex-wrap">'
            + '<small class="text-success text-muted fst-italic align-self-center">Approved</small>'
            + '<button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" title="View Details" onclick="openViewModal(window.viewPayloads[' + bookingId + '])">'
            + '<i class="bi bi-eye"></i>'
            + '</button>'
            + '</div>';
    }
}

document.getElementById('approveForm').addEventListener('submit', function (e) {
    e.preventDefault();
    var form = this;
    var submitBtn = document.getElementById('approveBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Approving...';

    var teamSelect = document.getElementById('approveTeamSelect');
    var teamName = (teamSelect.selectedOptions && teamSelect.selectedOptions[0]) ? teamSelect.selectedOptions[0].textContent.trim() : '';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new FormData(form)
    })
        .then(function (r) {
            return r.json().then(function (data) {
                return { ok: r.ok, data: data };
            });
        })
        .then(function (res) {
            var approveModalEl = document.getElementById('approveModal');
            var approveModal = bootstrap.Modal.getInstance(approveModalEl) || new bootstrap.Modal(approveModalEl);
            approveModal.hide();

            if (res.ok && res.data.success) {
                refreshApprovedRow(currentBookingId, teamName);
                showApproveResultModal(res.data.success, true);
            } else {
                var msg = (res.data && (res.data.error || res.data.message)) || 'Could not approve the booking. Please try again.';
                showApproveResultModal(msg, false);
            }

            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Approve & Assign Team';
        })
        .catch(function () {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i> Approve & Assign Team';
            showApproveResultModal('Could not approve the booking. Please try again.', false);
        });
});

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
    document.getElementById('viewRef').textContent = data.booking_ref;
    var sb = document.getElementById('viewStatusBadge');
    sb.className = 'badge fs-6 ' + (viewStatusBadges[data.status] || 'bg-secondary');
    sb.textContent = (data.status.charAt(0).toUpperCase() + data.status.slice(1));

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

function openRejectModal(bookingId, bookingRef, clientName) {
    document.getElementById('rejectForm').action = '/admin/booking/' + bookingId + '/reject';
    document.getElementById('rejectBookingRef').textContent = bookingRef;
    document.getElementById('rejectClientName').textContent = clientName;
    document.getElementById('rejection_reason').value = '';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
