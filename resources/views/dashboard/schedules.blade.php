<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Schedules</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/schedule.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle" aria-label="Toggle sidebar">&#9776;</button>
            <span class="fw-semibold">Staff Schedules</span>
            <span></span>
        </div>

        <div class="container-fluid p-3 p-lg-4">
            <div class="schedule-layout">

                <div class="row g-3 g-lg-4">

                    {{-- ==================== SIDEBAR ==================== --}}
                    <div class="col-lg-4 col-xl-3">

                        <div class="side-widget">
                            <div class="widget-title"><i class="bi bi-calendar3"></i> Calendar</div>
                            <div id="miniCalendar"></div>
                        </div>

                        <div class="side-widget">
                            <div class="widget-title"><i class="bi bi-funnel"></i> Filters</div>

                            <div class="mb-3">
                                <div class="filter-label mb-1">Staff</div>
                                <label class="filter-item">
                                    <input type="checkbox" class="form-check-input staff-filter-chk staff-all-chk" checked>
                                    <span class="fw-semibold">All Staff</span>
                                </label>
                                <div class="staff-filter-list">
                                    @foreach($allStaff as $s)
                                        <label class="filter-item staff-filter-item" data-staff-id="{{ $s['id'] }}">
                                            <input type="checkbox" class="form-check-input staff-filter-chk"
                                                data-staff-id="{{ $s['id'] }}" checked>
                                            <span class="text-truncate">{{ $s['name'] }}</span>
                                            <span class="filter-count">0</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="typeFilter" class="filter-label d-block">Event Type</label>
                                <select id="typeFilter" class="form-select" aria-label="Filter by event type">
                                    <option value="all">All types</option>
                                    @foreach($eventTypes as $t)
                                        <option value="{{ strtolower($t) }}">{{ $t }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="statusFilter" class="filter-label d-block">Status</label>
                                <select id="statusFilter" class="form-select" aria-label="Filter by status">
                                    <option value="all">All statuses</option>
                                    <option value="assigned">Pending</option>
                                    <option value="confirmed">Confirmed</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                    </div>

                    {{-- ==================== SCHEDULES FOR DATE ==================== --}}
                    <div class="col-lg-8 col-xl-9">

                        <div class="calendar-surface">
                            <div class="date-list-head">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h2 class="calendar-title" id="dateTitle">Today</h2>
                                    <span class="count-badge" id="eventCount">0 schedules</span>
                                </div>
                            </div>
                            <div id="dateSchedules" class="date-schedules"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @include('partials.modals.schedule-details')

    {{-- FLASH TOASTS --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="flashToasts" style="z-index: 1080;">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="scheduleToasts" style="z-index: 1080;"></div>

    <script>
        window.SCHEDULE_DATA = {
            events: @json($events),
            staff: @json($allStaff),
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/schedule.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('#flashToasts .toast').forEach(function (el) {
                new bootstrap.Toast(el, { delay: 4000 }).show();
            });
        });
    </script>
</body>

</html>
