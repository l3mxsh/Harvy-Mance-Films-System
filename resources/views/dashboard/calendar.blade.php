<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <style>
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #dee2e6;
        }
        .calendar-header-cell {
            background: #1a1a2e; color: #fff; text-align: center;
            padding: 10px 4px; font-weight: 600; font-size: 0.85rem;
        }
        .calendar-cell {
            background: #fff; min-height: 110px; padding: 6px 8px;
            position: relative;
        }
        .calendar-cell.other-month { background: #f8f9fa; }
        .calendar-cell.today { box-shadow: inset 0 0 0 2px #0d6efd; }
        .calendar-day {
            font-weight: 600; font-size: 0.85rem; margin-bottom: 4px;
            color: #495057;
        }
        .calendar-cell.other-month .calendar-day { color: #adb5bd; }
        .schedule-dot {
            display: block; font-size: 0.7rem; padding: 2px 6px; margin-bottom: 2px;
            border-radius: 4px; color: #fff; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis; cursor: default;
        }
        .dot-assigned { background: #0dcaf0; color: #000; }
        .dot-confirmed { background: #198754; }
        .dot-completed { background: #6c757d; }
        .dot-cancelled { background: #dc3545; }
    </style>
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Staff Calendar</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            {{-- MONTH NAV --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('staff-schedule.calendar', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}"
                    class="btn btn-outline-dark">
                    <i class="bi bi-chevron-left"></i> {{ $month->copy()->subMonth()->format('F Y') }}
                </a>
                <h5 class="fw-bold mb-0">{{ $month->format('F Y') }}</h5>
                <a href="{{ route('staff-schedule.calendar', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}"
                    class="btn btn-outline-dark">
                    {{ $month->copy()->addMonth()->format('F Y') }} <i class="bi bi-chevron-right"></i>
                </a>
            </div>

            {{-- LEGEND --}}
            <div class="mb-3 d-flex gap-3 flex-wrap">
                <span><span class="schedule-dot dot-assigned d-inline-block" style="width:12px;height:12px;vertical-align:middle;"></span> Assigned</span>
                <span><span class="schedule-dot dot-confirmed d-inline-block" style="width:12px;height:12px;vertical-align:middle;"></span> Confirmed</span>
                <span><span class="schedule-dot dot-completed d-inline-block" style="width:12px;height:12px;vertical-align:middle;"></span> Completed</span>
                <span><span class="schedule-dot dot-cancelled d-inline-block" style="width:12px;height:12px;vertical-align:middle;"></span> Cancelled</span>
            </div>

            {{-- CALENDAR --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="calendar-grid">
                        @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $dayName)
                            <div class="calendar-header-cell">{{ $dayName }}</div>
                        @endforeach

                        @php
                            $start = $startOfMonth->copy()->startOfWeek();
                            $end = $endOfMonth->copy()->endOfWeek();
                            $current = $start->copy();
                        @endphp

                        @while($current <= $end)
                            @php
                                $dateKey = $current->format('Y-m-d');
                                $isOtherMonth = $current->month !== $month->month;
                                $isToday = $current->isToday();
                                $daySchedules = $calendarData[$dateKey] ?? [];
                            @endphp
                            <div class="calendar-cell {{ $isOtherMonth ? 'other-month' : '' }} {{ $isToday ? 'today' : '' }}">
                                <div class="calendar-day">{{ $current->day }}</div>
                                @foreach($daySchedules as $s)
                                    <span class="schedule-dot dot-{{ $s->status }}"
                                        title="{{ $s->staff->name }} — {{ $s->booking->package->name ?? 'Booking' }} ({{ $s->event_time }})">
                                        {{ $s->staff->name }}
                                    </span>
                                @endforeach
                            </div>
                            @php($current->addDay())
                        @endphp
                    </div>
                </div>
            </div>

            {{-- STAFF AVAILABILITY LEGEND --}}
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Staff Availability This Month</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($allStaff as $s)
                            @php
                                $assignedCount = \App\Models\StaffSchedule::where('staff_id', $s->id)
                                    ->whereMonth('event_date', $month->month)
                                    ->whereYear('event_date', $month->year)
                                    ->whereIn('status', ['assigned', 'confirmed'])
                                    ->count();
                            @endphp
                            <div class="col-lg-3 col-md-4 col-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:36px;height:36px;font-size:0.85rem;">
                                        {{ strtoupper(substr($s->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">{{ $s->name }}</div>
                                        @if($assignedCount > 0)
                                            @include('partials.status-badge', ['status' => 'assigned', 'label' => $assignedCount.' assigned'])
                                        @else
                                            @include('partials.status-badge', ['status' => 'available', 'label' => 'Available'])
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>

</html>
