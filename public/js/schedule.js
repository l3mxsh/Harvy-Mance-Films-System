/* ============================================================
   HarvyMance Films — Staff Schedule Calendar
   Month / Week / Day views, filters, drag-and-drop rescheduling
   ============================================================ */
(function () {
    'use strict';

    /* ---------------- Data & constants ---------------- */
    const DATA = window.SCHEDULE_DATA || {};
    const EVENTS = DATA.events || [];
    const STAFF = DATA.staff || [];

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const STATUS_META = {
        assigned: { label: 'Pending', color: '#f59e0b', badge: 'bg-warning text-dark', icon: 'bi-hourglass-split' },
        confirmed: { label: 'Confirmed', color: '#198754', badge: 'bg-success', icon: 'bi-check-circle' },
        completed: { label: 'Completed', color: '#6c757d', badge: 'bg-secondary', icon: 'bi-check2-circle' },
        cancelled: { label: 'Cancelled', color: '#dc3545', badge: 'bg-danger', icon: 'bi-x-circle' },
    };

    const DURATION = 120; // minutes shown per event in day view

    const DAY_NAMES = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const DAY_NAMES_FULL = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const MONTH_NAMES = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];

    /* ---------------- State ---------------- */
    const today = new Date();
    const state = {
        view: 'month',
        anchor: { y: today.getFullYear(), m: today.getMonth(), d: today.getDate() },
        search: '',
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
    const esc = (s) => String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    const toMinutes = (t) => {
        const [h, m] = String(t).split(':').map(Number);
        return h * 60 + (m || 0);
    };
    const toTimeStr = (min) => `${pad(Math.floor(min / 60))}:${pad(min % 60)}`;
    const prettyTime = (min) => {
        let h = Math.floor(min / 60);
        const m = min % 60;
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;
        return `${h}:${pad(m)} ${ampm}`;
    };

    const anchorDate = () => new Date(state.anchor.y, state.anchor.m, state.anchor.d);

    function shiftAnchor(days, months, view) {
        const d = anchorDate();
        if (view === 'day') d.setDate(d.getDate() + days);
        else if (view === 'week') d.setDate(d.getDate() + days * 7);
        else {
            d.setMonth(d.getMonth() + months);
            d.setDate(1);
        }
        state.anchor = { y: d.getFullYear(), m: d.getMonth(), d: d.getDate() };
    }

    function filterEvents() {
        const q = state.search.trim().toLowerCase();
        return EVENTS.filter((ev) => {
            if (state.staff.size > 0 && !state.staff.has(String(ev.staff_id))) return false;
            if (state.type !== 'all' && ev.event_type.toLowerCase() !== state.type) return false;
            if (state.status !== 'all' && ev.status !== state.status) return false;
            if (q) {
                const hay = `${ev.staff_name} ${ev.package_name} ${ev.booking_ref} ${ev.client_name} ${ev.venue} ${ev.event_type}`.toLowerCase();
                if (!hay.includes(q)) return false;
            }
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

    /* ---------------- Toolbar title ---------------- */
    function title() {
        const a = anchorDate();
        if (state.view === 'month') return `${MONTH_NAMES[a.getMonth()]} ${a.getFullYear()}`;
        if (state.view === 'week') {
            const s = startOfWeek(a);
            const e = addDays(s, 6);
            const sameMonth = s.getMonth() === e.getMonth();
            return sameMonth
                ? `${MONTH_NAMES[s.getMonth()].slice(0, 3)} ${s.getDate()} – ${e.getDate()}, ${e.getFullYear()}`
                : `${MONTH_NAMES[s.getMonth()].slice(0, 3)} ${s.getDate()} – ${MONTH_NAMES[e.getMonth()].slice(0, 3)} ${e.getDate()}, ${e.getFullYear()}`;
        }
        return `${DAY_NAMES_FULL[a.getDay()]}, ${MONTH_NAMES[a.getMonth()]} ${a.getDate()}, ${a.getFullYear()}`;
    }

    /* ---------------- Event card markup ---------------- */
    function chipMarkup(ev, opts = {}) {
        const st = STATUS_META[ev.status] || STATUS_META.assigned;
        const tooltip = `${st.label} · ${ev.package_name} · ${ev.staff_name} · ${ev.time_display} · ${ev.venue}`;
        const venueRow = opts.showVenue
            ? `<span class="chip-venue"><i class="bi bi-geo-alt"></i>${esc(ev.venue)}</span>`
            : '';
        return `
        <div class="cal-chip st-${ev.status}" role="button" tabindex="0" draggable="true"
             data-id="${ev.id}" aria-label="${esc(st.label)} — ${esc(ev.package_name)} — ${esc(ev.staff_name)}"
             title="${esc(tooltip)}"
             data-title="${esc(ev.package_name)}" data-time="${esc(ev.time_display)}" data-staff="${esc(ev.staff_name)}">
          <span class="chip-title">${esc(ev.package_name)}</span>
          <span class="chip-staff"><i class="bi bi-person"></i>${esc(ev.staff_name)}</span>
          ${venueRow}
        </div>`;
    }

    /* ---------------- Month view ---------------- */
    function renderMonth(map) {
        const a = anchorDate();
        const first = new Date(a.getFullYear(), a.getMonth(), 1);
        const gridStart = startOfWeek(first);
        let html = '<div class="calendar-scroll"><div class="month-grid">';
        DAY_NAMES.forEach((n) => { html += `<div class="cal-weekday">${n}</div>`; });

        for (let i = 0; i < 42; i++) {
            const d = addDays(gridStart, i);
            const key = fmtKey(d);
            const evs = map[key] || [];
            const isOther = d.getMonth() !== a.getMonth();
            const isToday = isSameDay(d, today);
            const hasEvents = evs.length > 0;
            const visible = evs.slice(0, 3);
            const extra = evs.length - visible.length;

            html += `
            <div class="day-cell ${isOther ? 'other-month' : ''} ${isToday ? 'today' : ''} ${hasEvents ? '' : 'is-empty'}"
                 data-date="${key}" data-view="month" aria-label="${MONTH_NAMES[d.getMonth()]} ${d.getDate()}">
              <span class="day-number">${d.getDate()}</span>
              <div class="cell-events">
                ${visible.map((ev) => chipMarkup(ev)).join('')}
                ${extra > 0 ? `<button type="button" class="more-link" data-date="${key}" data-extra="${extra}">${extra} more…</button>` : ''}
              </div>
            </div>`;
        }
        html += '</div></div>';
        document.getElementById('calendarView').innerHTML = html;
    }

    /* ---------------- Week view ---------------- */
    function renderWeek(map) {
        const gridStart = startOfWeek(anchorDate());
        let html = '<div class="calendar-scroll"><div class="week-grid">';
        for (let i = 0; i < 7; i++) {
            const d = addDays(gridStart, i);
            const key = fmtKey(d);
            const evs = map[key] || [];
            const isToday = isSameDay(d, today);
            html += `
            <div class="week-col ${isToday ? 'today-col' : ''}" data-date="${key}">
              <div class="col-head ${isToday ? 'today-head' : ''}">
                <div class="col-weekday">${DAY_NAMES[d.getDay()]}</div>
                <div class="col-daynum">${d.getDate()}</div>
              </div>
              <div class="col-body" data-date="${key}" data-view="week" aria-label="${DAY_NAMES_FULL[d.getDay()]}, ${MONTH_NAMES[d.getMonth()]} ${d.getDate()}">
                ${evs.length ? evs.map((ev) => chipMarkup(ev, { showVenue: true })).join('') : '<div class="cell-empty-hint">No events</div>'}
              </div>
            </div>`;
        }
        html += '</div></div>';
        document.getElementById('calendarView').innerHTML = html;
    }

    /* ---------------- Day view (timeline) ---------------- */
    function layoutDay(evs) {
        const sorted = [...evs].sort((a, b) => toMinutes(a.time) - toMinutes(b.time));
        const items = sorted.map((ev) => ({
            ev,
            start: toMinutes(ev.time),
            end: toMinutes(ev.time) + DURATION,
        }));

        const startMin = Math.max(0, Math.min(360, ...items.map((i) => i.start)));
        const endMin = Math.min(1440, Math.max(1380, ...items.map((i) => i.end)));

        const clusters = [];
        let cluster = [];
        let clusterEnd = -1;
        items.forEach((it) => {
            if (cluster.length && it.start >= clusterEnd) {
                clusters.push(cluster);
                cluster = [];
                clusterEnd = -1;
            }
            cluster.push(it);
            clusterEnd = Math.max(clusterEnd, it.end);
        });
        if (cluster.length) clusters.push(cluster);

        const placed = [];
        clusters.forEach((c) => {
            const lanes = [];
            const assigned = [];
            c.forEach((it) => {
                let idx = lanes.findIndex((l) => l.end <= it.start);
                if (idx === -1) { lanes.push({ end: it.end }); idx = lanes.length - 1; }
                else { lanes[idx].end = it.end; }
                assigned.push({ it, lane: idx });
            });
            const total = Math.max(1, lanes.length);
            assigned.forEach((a) => {
                placed.push({
                    ev: a.it.ev,
                    top: ((a.it.start - startMin) / (endMin - startMin)) * 100,
                    height: ((a.it.end - a.it.start) / (endMin - startMin)) * 100,
                    left: (a.lane / total) * 100,
                    width: Math.max(1, (100 / total) - 0.4),
                });
            });
        });

        return { placed, startMin, endMin };
    }

    function renderDay(map) {
        const a = anchorDate();
        const key = fmtKey(a);
        const evs = map[key] || [];
        const { placed, startMin, endMin } = layoutDay(evs);
        const nowMin = today.getHours() * 60 + today.getMinutes();
        const isToday = isSameDay(a, today);

        let labels = '';
        for (let m = startMin; m <= endMin; m += 60) {
            labels += `<span class="timeline-hour-label" style="top:${((m - startMin) / (endMin - startMin)) * 100}%">${prettyTime(m)}</span>`;
        }

        let lines = '';
        for (let m = startMin; m <= endMin; m += 60) {
            const top = ((m - startMin) / (endMin - startMin)) * 100;
            const isNowLine = isToday && m <= nowMin && nowMin < m + 60;
            lines += `<div class="timeline-hour ${isNowLine ? 'now-line' : ''}" style="top:${top}%"></div>`;
        }

        let eventsHtml = '';
        placed.forEach((p) => {
            const ev = p.ev;
            eventsHtml += `
            <div class="timeline-event st-${ev.status}" role="button" tabindex="0" draggable="true" data-id="${ev.id}"
                 style="top:${p.top}%;left:${p.left}%;width:${p.width}%;height:${p.height}%"
                 aria-label="${esc((STATUS_META[ev.status] || STATUS_META.assigned).label)} — ${esc(ev.package_name)} — ${esc(ev.staff_name)} at ${esc(ev.time_display)}">
              <div class="te-time">${esc(ev.time_display)}</div>
              <div class="te-title">${esc(ev.package_name)}</div>
              <div class="te-staff"><i class="bi bi-person"></i>${esc(ev.staff_name)}</div>
              <div class="te-venue"><i class="bi bi-geo-alt"></i>${esc(ev.venue)}</div>
            </div>`;
        });

        const emptyHint = evs.length
            ? ''
            : `<div class="text-center text-muted py-5"><i class="bi bi-calendar-x fs-1 d-block mb-2 text-secondary"></i>No schedules on this day.</div>`;

        document.getElementById('calendarView').innerHTML = `
        <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom flex-wrap gap-2">
          <div>
            <strong class="fs-6">${DAY_NAMES_FULL[a.getDay()]}, ${MONTH_NAMES[a.getMonth()]} ${a.getDate()}, ${a.getFullYear()}</strong>
            <span class="count-badge ms-2">${evs.length} schedule${evs.length === 1 ? '' : 's'}</span>
          </div>
          ${evs.length ? '<span class="small text-muted">Drag an event to change its time</span>' : ''}
        </div>
        <div class="day-view">
          <div class="timeline d-flex" style="height:${(endMin - startMin) / 60 * 62}px;">
            <div class="timeline-hour-labels">${labels}</div>
            <div class="timeline-body" id="timelineBody" data-date="${key}" data-view="day">
              ${lines}
              <div class="timeline-events">${eventsHtml}</div>
            </div>
          </div>
          ${emptyHint}
        </div>`;
    }

    /* ---------------- Mini calendar ---------------- */
    function renderMini() {
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

    /* ---------------- Filters / legend wiring ---------------- */
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
    function openEventModal(id) {
        const ev = EVENTS.find((e) => e.id === id);
        if (!ev) return;
        const st = STATUS_META[ev.status] || STATUS_META.assigned;

        const box = (label, value, icon) => `
        <div class="detail-box">
          <div class="detail-label"><i class="bi ${icon} me-1"></i>${label}</div>
          <div class="detail-value">${esc(value)}</div>
        </div>`;

        document.getElementById('evStatusBadge').className = `badge ${st.badge || ''}`;
        document.getElementById('evStatusBadge').innerHTML = `<i class="bi ${st.icon || 'bi-circle'} me-1"></i>${st.label}`;
        document.getElementById('evTitle').textContent = ev.package_name;
        document.getElementById('evMeta').innerHTML = `
        <div class="row g-2">
          <div class="col-sm-6">${box('Staff', ev.staff_name, 'bi-person')}</div>
          <div class="col-sm-6">${box('Booking Ref', ev.booking_ref, 'bi-hash')}</div>
          <div class="col-sm-6">${box('Event Type', ev.event_type, 'bi-tag')}</div>
          <div class="col-sm-6">${box('Client', ev.client_name, 'bi-person-badge')}</div>
          <div class="col-sm-6">${box('Date', ev.date, 'bi-calendar2-event')}</div>
          <div class="col-sm-6">${box('Time', ev.time_display, 'bi-clock')}</div>
          <div class="col-sm-6">${box('Venue', ev.venue, 'bi-geo-alt')}</div>
          <div class="col-sm-6">${box('Address', ev.address, 'bi-map')}</div>
          <div class="col-12">${box('Contact', ev.client_phone + ' · ' + ev.client_email, 'bi-telephone')}</div>
        </div>`;

        const viewBooking = document.getElementById('evViewBooking');
        viewBooking.href = `/admin/booking?tab=all`;
        viewBooking.setAttribute('aria-label', `View booking ${ev.booking_ref}`);

        new bootstrap.Modal(document.getElementById('scheduleDetailModal')).show();
    }

    /* ---------------- Drag and drop ---------------- */
    let dragId = null;

    function isDropTarget(el) {
        return el && (el.classList.contains('day-cell') ||
            (el.classList.contains('col-body') && el.dataset.view === 'week') ||
            (el.classList.contains('timeline-body') && el.dataset.view === 'day'));
    }

    function dropInfo(target, clientX, clientY) {
        if (target.classList.contains('timeline-body')) {
            const rect = target.getBoundingClientRect();
            const items = (visibleEvents()[target.dataset.date] || []).map((e) => toMinutes(e.time));
            const startMin = Math.max(0, Math.min(360, ...items));
            const endMin = Math.min(1440, Math.max(1380, ...items.map((m) => m + DURATION)));
            const pct = Math.min(1, Math.max(0, (clientY - rect.top) / rect.height));
            const minutes = Math.round((startMin + pct * (endMin - startMin)) / 30) * 30;
            return { date: target.dataset.date, time: toTimeStr(Math.min(minutes, 1410)) };
        }
        return { date: target.closest('[data-date]').dataset.date, time: null };
    }

    function persistMove(id, date, time) {
        const ev = EVENTS.find((e) => e.id === id);
        if (!ev) return;
        const newTime = time || ev.time;
        if (ev.date === date && ev.time === newTime) return;

        ev.date = date;
        ev.time = newTime;
        ev.time_display = prettyTime(toMinutes(newTime));

        fetch(`/admin/schedule/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify({ event_date: date, event_time: newTime }),
        })
            .then((r) => r.json())
            .then((res) => {
                if (res.success) {
                    toast('Schedule moved to ' + date, 'success');
                } else {
                    toast('Could not save the schedule change.', 'danger');
                }
            })
            .catch(() => toast('Network error while saving the schedule.', 'danger'))
            .finally(() => { render(); });
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
        const viewEl = document.getElementById('calendarView');
        const titleEl = document.getElementById('calendarTitle');
        const map = visibleEvents();
        if (state.view === 'month') renderMonth(map);
        else if (state.view === 'week') renderWeek(map);
        else renderDay(map);
        if (titleEl) titleEl.textContent = title();
        document.querySelectorAll('.view-switcher .btn').forEach((b) => {
            b.classList.toggle('active', b.dataset.view === state.view);
            b.setAttribute('aria-pressed', b.dataset.view === state.view);
        });
        renderMini();
        const countEl = document.getElementById('eventCount');
        if (countEl) {
            const n = Object.values(map).reduce((sum, arr) => sum + arr.length, 0);
            countEl.textContent = `${n} schedule${n === 1 ? '' : 's'}`;
        }
        if (viewEl) viewEl.scrollTop = 0;
    }

    /* ---------------- Event binding ---------------- */
    function bind() {
        const viewEl = document.getElementById('calendarView');

        // View switcher
        document.querySelectorAll('.view-switcher .btn').forEach((b) => {
            b.addEventListener('click', () => {
                state.view = b.dataset.view;
                render();
            });
        });

        // Toolbar nav
        document.getElementById('navPrev').addEventListener('click', () => {
            shiftAnchor(state.view === 'month' ? 0 : -1, -1, state.view);
            render();
        });
        document.getElementById('navNext').addEventListener('click', () => {
            shiftAnchor(state.view === 'month' ? 0 : 1, 1, state.view);
            render();
        });
        document.getElementById('navToday').addEventListener('click', () => {
            state.anchor = { y: today.getFullYear(), m: today.getMonth(), d: today.getDate() };
            render();
        });

        // Search
        const searchEl = document.getElementById('scheduleSearch');
        let searchTimer = null;
        searchEl.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => { state.search = searchEl.value; render(); }, 220);
        });

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

        // Mini calendar navigation + day selection (delegated)
        document.getElementById('miniCalendar').addEventListener('click', (e) => {
            const prev = e.target.closest('#miniPrev');
            const next = e.target.closest('#miniNext');
            const day = e.target.closest('.mini-day');
            if (prev || next) {
                const d = new Date(state.anchor.y, state.anchor.m, 1);
                d.setMonth(d.getMonth() + (prev ? -1 : 1));
                state.anchor = { y: d.getFullYear(), m: d.getMonth(), d: state.anchor.d > daysInMonth(d) ? 1 : state.anchor.d };
                renderMini();
                return;
            }
            if (day) {
                const d = parseKey(day.dataset.date);
                state.anchor = { y: d.getFullYear(), m: d.getMonth(), d: d.getDate() };
                render();
            }
        });

        // Delegated: open event detail modal (click / Enter / Space)
        viewEl.addEventListener('click', (e) => {
            const more = e.target.closest('.more-link');
            if (more) {
                const d = parseKey(more.dataset.date);
                state.anchor = { y: d.getFullYear(), m: d.getMonth(), d: d.getDate() };
                state.view = 'day';
                render();
                return;
            }
            const chip = e.target.closest('.cal-chip, .timeline-event');
            if (chip) openEventModal(Number(chip.dataset.id));
        });

        viewEl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                const chip = e.target.closest('.cal-chip, .timeline-event');
                if (chip) { e.preventDefault(); openEventModal(Number(chip.dataset.id)); }
            }
        });

        // Drag and drop
        viewEl.addEventListener('dragstart', (e) => {
            const chip = e.target.closest('.cal-chip, .timeline-event');
            if (!chip) return;
            dragId = Number(chip.dataset.id);
            chip.classList.add('is-dragging');
            e.dataTransfer.effectAllowed = 'move';
            try { e.dataTransfer.setData('text/plain', String(dragId)); } catch (err) { /* noop */ }
        });

        viewEl.addEventListener('dragend', (e) => {
            const chip = e.target.closest('.cal-chip, .timeline-event');
            if (chip) chip.classList.remove('is-dragging');
            document.querySelectorAll('.drop-target').forEach((el) => el.classList.remove('drop-target'));
            dragId = null;
        });

        viewEl.addEventListener('dragover', (e) => {
            const target = e.target.closest('.day-cell, .col-body, .timeline-body');
            if (!target || !isDropTarget(target)) return;
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            document.querySelectorAll('.drop-target').forEach((el) => el.classList.remove('drop-target'));
            target.classList.add('drop-target');
        });

        viewEl.addEventListener('drop', (e) => {
            const target = e.target.closest('.day-cell, .col-body, .timeline-body');
            document.querySelectorAll('.drop-target').forEach((el) => el.classList.remove('drop-target'));
            if (!target || !isDropTarget(target) || dragId === null) return;
            e.preventDefault();
            const info = dropInfo(target, e.clientX, e.clientY);
            persistMove(dragId, info.date, info.time);
            dragId = null;
        });
    }

    const daysInMonth = (d) => new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();

    /* ---------------- Init ---------------- */
    function init() {
        renderStaffCounts();
        bind();
        render();
    }

    document.addEventListener('DOMContentLoaded', init);
})();
