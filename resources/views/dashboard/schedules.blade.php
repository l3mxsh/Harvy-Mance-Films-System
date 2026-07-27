<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Schedules</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Staff Schedules</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- FILTERS --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('staff-schedule.index') }}" class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Staff Member</label>
                            <select name="staff_id" class="form-select">
                                <option value="">All Staff</option>
                                @foreach($allStaff as $s)
                                    <option value="{{ $s->id }}" {{ request('staff_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Month</label>
                            <input type="month" name="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Status</option>
                                <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-dark flex-grow-1"><i class="bi bi-funnel me-1"></i> Filter</button>
                            <a href="{{ route('staff-schedule.calendar') }}" class="btn btn-outline-primary" title="Calendar View">
                                <i class="bi bi-calendar3"></i>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- SCHEDULE TABLE --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-event me-2"></i>Schedule List</h6>
                    <span class="badge bg-secondary">{{ $schedules->total() }} entries</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Staff</th>
                                    <th>Booking Ref</th>
                                    <th>Package</th>
                                    <th>Event Date</th>
                                    <th>Event Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                    <tr>
                                        <td class="fw-semibold">{{ $schedule->staff->name }}</td>
                                        <td><code>{{ $schedule->booking->booking_ref ?? 'N/A' }}</code></td>
                                        <td>{{ $schedule->booking->package->name ?? 'N/A' }}</td>
                                        <td>{{ $schedule->event_date->format('M d, Y') }}</td>
                                        <td>{{ $schedule->event_time }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($schedule->status) {
                                                    'assigned' => 'bg-info',
                                                    'confirmed' => 'bg-success',
                                                    'completed' => 'bg-secondary',
                                                    'cancelled' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($schedule->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                            No schedules found for this period.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($schedules->hasPages())
                    <div class="card-footer bg-white border-top">{{ $schedules->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>

</html>
