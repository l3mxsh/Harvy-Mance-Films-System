/* ============================================================
   HarvyMance Films — Staff Schedule
   Calendar widget + schedule cards for the selected date
   ============================================================ */
(function () {
    'use strict';

    /* ---------------- Data & constants ---------------- */
    const DATA = window.SCHEDULE_DATA || {};
    const EVENTS = DATA.events || [];
    const STAFF = DATA.staff || [];

    const STATUS_META = {
        assigned: { label: 'Pending', color: '#f59e0b', badge: 'bg-warning text-dark' },
        confirmed: { label: 'Confirmed', color: '#0d6efd', badge: 'bg-primary' },
        completed: { label: 'Completed', color: '#198754', badge: 'bg-success' },
        cancelled: { label: 'Cancelled', color: '#dc3545', badge: 'bg-danger' },
    };

    const DAY_NAMES = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const DAY_NAMES_FULL = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];

    /* ---------------- State ---------------- */
    const today = new Date();
    const state = {
        anchor: { y: today.getFullYear(), m: today.getMonth(), d: today.getDate() },
        staff: new Set(STAFF.map((s) => String(s.id))), // empty set = show all
        type: 'all',
        status: 'all',
    };

    /* ---------------- Helpers ---------------- */
    const pad = (n) => String(n).padStart(2, '0');
    const fmtKey = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
    const parseKey = (key) => {
        const [y, m, d] = key.split('-').map(Number);
        return new Date(y, m - 1, d);
    };
    const addDays = (d, n) => { const x = new Date(d); x.setDate(x.getDate() + n); return x; };
    const startOfWeek = (d) => { const x = new Date(d); x.setDate(x.getDate() - x.getDay()); return x; };
    const isSameDay = (a, b) => fmtKey(a) === fmtKey(b);
    const daysInMonth = (d) => new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
    const esc = (s) => String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    const toMinutes = (t) => {
        const [h, m] = String(t).split(':').map(Number);
        return h * 60 + (m || 0);
    };

    const anchorDate = () => new Date(state.anchor.y, state.anchor.m, state.anchor.d);

    function filterEvents() {
        return EVENTS.filter((ev) => {
            if (state.staff.size > 0 && !state.staff.has(String(ev.staff_id))) return false;
            if (state.type !== 'all' && ev.event_type.toLowerCase() !== state.type) return false;
            if (state.status !== 'all' && ev.status !== state.status) return false;
            return true;
        });
    }

    function eventsByDate(events) {
        const map = {};
        events.forEach((ev) => {
            (map[ev.date] = map[ev.date] || []).push(ev);
        });
        Object.keys(map).forEach((k) => map[k].sort((a, b) => toMinutes(a.time) - toMinutes(b.time)));
        return map;
    }

    const visibleEvents = () => eventsByDate(filterEvents());
    const allEventsMap = () => eventsByDate(EVENTS);

    /* ---------------- Schedule card markup ---------------- */
    function groupByBooking(evs) {
        const map = {};
        evs.forEach((ev) => { (map[ev.booking_id] = map[ev.booking_id] || []).push(ev); });
        return Object.values(map);
    }

    function schedCardMarkup(group) {
        const ev = group[0];
        const st = STATUS_META[ev.status] || STATUS_META.assigned;
        const times = [...new Set(group.map((x) => x.time_display))].join(' · ');
        const names = [...new Set(group.map((x) => x.staff_name))].join(', ');
        return `
        <div class="sched-card" role="button" tabindex="0"
             data-id="${ev.id}" data-booking-id="${ev.booking_id}" data-date="${ev.date}"
             aria-label="${esc(st.label)} — ${esc(ev.package_name)} — ${esc(names)} at ${esc(times)}"
             title="${esc(ev.package_name)} — ${esc(names)} · ${esc(times)}">
          <div class="sched-time">
            <span>${esc(times)}</span>
            <span class="badge ${st.badge} sched-status">${st.label}</span>
          </div>
          <div class="sched-main">
            <div class="sched-title">${esc(ev.package_name)}</div>
            <div class="sched-staff"><i class="bi bi-person"></i>${esc(names)}</div>
            <div class="sched-venue"><i class="bi bi-geo-alt"></i>${esc(ev.venue)}</div>
          </div>
        </div>`;
    }

    /* ---------------- Schedules for the selected date ---------------- */
    function renderDateList() {
        const container = document.getElementById('dateSchedules');
        if (!container) return;
        const a = anchorDate();
        const key = fmtKey(a);
        const evs = visibleEvents()[key] || [];
        const groups = groupByBooking(evs);

        const dateEl = document.getElementById('dateTitle');
        if (dateEl) {
            dateEl.textContent = `${DAY_NAMES_FULL[a.getDay()]}, ${MONTH_NAMES[a.getMonth()]} ${a.getDate()}, ${a.getFullYear()}`;
        }
        const countEl = document.getElementById('eventCount');
        if (countEl) {
            countEl.textContent = `${groups.length} booking${groups.length === 1 ? '' : 's'}`;
        }

        container.innerHTML = groups.length
            ? groups.map(schedCardMarkup).join('')
            : `<div class="date-empty">
                 <i class="bi bi-calendar-x"></i>
                 <div>No schedules for this date.</div>
                 <small>Pick another day on the calendar.</small>
               </div>`;
    }

    /* ---------------- Calendar widget ---------------- */
    function renderCalendar() {
        const map = allEventsMap();
        const a = anchorDate();
        const first = new Date(a.getFullYear(), a.getMonth(), 1);
        const gridStart = startOfWeek(first);
        let html = `
        <div class="mini-nav">
          <button type="button" class="btn-icon" id="miniPrev" aria-label="Previous month"><i class="bi bi-chevron-left"></i></button>
          <span class="mini-month" id="miniMonth">${MONTH_NAMES[a.getMonth()]} ${a.getFullYear()}</span>
          <button type="button" class="btn-icon" id="miniNext" aria-label="Next month"><i class="bi bi-chevron-right"></i></button>
        </div>
        <div class="mini-cal-head">${DAY_NAMES.map((n) => `<span>${n}</span>`).join('')}</div>
        <div class="mini-cal-grid">`;

        for (let i = 0; i < 42; i++) {
            const d = addDays(gridStart, i);
            const key = fmtKey(d);
            const isOther = d.getMonth() !== a.getMonth();
            const isToday = isSameDay(d, today);
            const isSelected = isSameDay(d, anchorDate());
            const hasEvents = !!(map[key] && map[key].length);
            html += `
            <button type="button" class="mini-day ${isOther ? 'other-month' : ''} ${isToday ? 'today' : ''} ${isSelected ? 'selected' : ''}"
                    data-date="${key}" aria-label="${MONTH_NAMES[d.getMonth()]} ${d.getDate()}, ${hasEvents ? 'has schedules' : 'no schedules'}">
              ${d.getDate()}
              <span class="mini-dot ${hasEvents ? 'has-events' : ''}"></span>
            </button>`;
        }
        html += '</div>';
        document.getElementById('miniCalendar').innerHTML = html;
    }

    /* ---------------- Staff filter counts ---------------- */
    function renderStaffCounts() {
        const counts = {};
        EVENTS.forEach((ev) => { counts[ev.staff_id] = (counts[ev.staff_id] || 0) + 1; });
        document.querySelectorAll('.staff-filter-item').forEach((el) => {
            const id = el.dataset.staffId;
            const badge = el.querySelector('.filter-count');
            if (badge) badge.textContent = counts[id] || 0;
        });
    }

    /* ---------------- Detail modal ---------------- */
    function openEventModal(group) {
        const ev = group[0];
        if (!ev) return;
        const st = STATUS_META[ev.status] || STATUS_META.assigned;
        const staffNames = [...new Set(group.map((x) => x.staff_name))].join(', ');

        const field = (label, value, cls) => `
            <div class="${cls || 'col-sm-6'}">
                <div class="text-muted small">${label}</div>
                <div class="fw-medium">${esc(value)}</div>
            </div>`;

        document.getElementById('evStatusBadge').className = `badge ${st.badge || ''}`;
        document.getElementById('evStatusBadge').innerHTML = st.label;
        document.getElementById('evBookingRef').textContent = ev.booking_ref;

        const times = [...new Set(group.map((x) => x.time_display))].join(' · ');

        document.getElementById('evMeta').innerHTML = `
            <div class="mb-4">
                <h6 class="fw-semibold small text-uppercase text-muted mb-2">Package</h6>
                <div class="row g-3">
                    ${field('Package', ev.package_name)}
                    ${field('Event Type', ev.event_type)}
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-semibold small text-uppercase text-muted mb-2">Client</h6>
                <div class="row g-3">
                    ${field('Name', ev.client_name)}
                    ${field('Contact', ev.client_phone, 'col-sm-6')}
                    <div class="col-12">${field('Email', ev.client_email)}</div>
                </div>
            </div>

            <div class="mb-4">
                <h6 class="fw-semibold small text-uppercase text-muted mb-2">Event</h6>
                <div class="row g-3">
                    ${field('Date', ev.date)}
                    ${field('Time', times)}
                    ${field('Venue', ev.venue)}
                    ${field('Address', ev.address)}
                </div>
            </div>

            <div>
                <h6 class="fw-semibold small text-uppercase text-muted mb-2">Schedule</h6>
                <div class="row g-3">
                    ${field('Assigned Staff', staffNames, 'col-sm-6')}
                    ${field('Status', st.label, 'col-sm-6')}
                </div>
            </div>`;

        const viewBooking = document.getElementById('evViewBooking');
        viewBooking.href = `/admin/booking?tab=all`;
        viewBooking.setAttribute('aria-label', `View booking ${ev.booking_ref}`);

        new bootstrap.Modal(document.getElementById('scheduleDetailModal')).show();
    }

    /* ---------------- Toast ---------------- */
    function toast(message, type) {
        const container = document.getElementById('scheduleToasts');
        if (!container) return;
        const el = document.createElement('div');
        el.className = `toast align-items-center text-bg-${type} border-0`;
        el.setAttribute('role', 'alert');
        el.innerHTML = `
        <div class="d-flex">
          <div class="toast-body"><i class="bi ${type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle'} me-2"></i>${esc(message)}</div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>`;
        container.appendChild(el);
        const t = new bootstrap.Toast(el, { delay: 3500 });
        t.show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    }

    /* ---------------- Render entry ---------------- */
    function render() {
        renderDateList();
        renderCalendar();
    }

    /* ---------------- Event binding ---------------- */
    function bind() {
        // Event type + status filters
        document.getElementById('typeFilter').addEventListener('change', (e) => {
            state.type = e.target.value; render();
        });
        document.getElementById('statusFilter').addEventListener('change', (e) => {
            state.status = e.target.value; render();
        });

        // Staff checkboxes
        document.querySelectorAll('.staff-filter-chk').forEach((chk) => {
            chk.addEventListener('change', () => {
                if (chk.classList.contains('staff-all-chk')) {
                    const checked = chk.checked;
                    document.querySelectorAll('.staff-filter-chk:not(.staff-all-chk)').forEach((c) => {
                        c.checked = checked;
                    });
                }
                state.staff = new Set();
                document.querySelectorAll('.staff-filter-chk:not(.staff-all-chk):checked').forEach((c) => {
                    state.staff.add(c.dataset.staffId);
                });
                render();
            });
        });

        // Calendar navigation + day selection (delegated)
        document.getElementById('miniCalendar').addEventListener('click', (e) => {
            const prev = e.target.closest('#miniPrev');
            const next = e.target.closest('#miniNext');
            const day = e.target.closest('.mini-day');
            if (prev || next) {
                const d = new Date(state.anchor.y, state.anchor.m + (prev ? -1 : 1), 1);
                const maxDay = daysInMonth(d);
                state.anchor = { y: d.getFullYear(), m: d.getMonth(), d: Math.min(state.anchor.d, maxDay) };
                render();
                return;
            }
            if (day) {
                const d = parseKey(day.dataset.date);
                state.anchor = { y: d.getFullYear(), m: d.getMonth(), d: d.getDate() };
                render();
            }
        });

        // Schedule card -> detail modal (delegated)
        const listEl = document.getElementById('dateSchedules');
        function openCard(card) {
            const bid = card.dataset.bookingId;
            const date = card.dataset.date;
            const group = EVENTS.filter((x) => String(x.booking_id) === String(bid) && x.date === date);
            openEventModal(group.length ? group : [EVENTS.find((x) => x.id === Number(card.dataset.id))]);
        }
        listEl.addEventListener('click', (e) => {
            const card = e.target.closest('.sched-card');
            if (card) openCard(card);
        });
        listEl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                const card = e.target.closest('.sched-card');
                if (card) { e.preventDefault(); openCard(card); }
            }
        });
    }

    /* ---------------- Init ---------------- */
    function init() {
        renderStaffCounts();
        bind();
        render();
    }

    document.addEventListener('DOMContentLoaded', init);
})();
