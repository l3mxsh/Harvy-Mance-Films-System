<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - HarvyMance Films</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client-login.css') }}">
</head>

<body>

    {{-- ==================== TOP NAV ==================== --}}
    <nav class="navbar booking-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ route('staff.dashboard') }}" class="d-inline-flex align-items-center">
                <img src="{{ asset('storage/images/Black Logo.png') }}" alt="HarvyMance Films" height="34">
            </a>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="staff-avatar">{{ strtoupper(substr($staff->name, 0, 1)) }}</div>
                    <div class="d-none d-md-block">
                        <div class="fw-semibold lh-1">{{ $staff->name }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('staff.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-dark rounded-pill">
                        <i class="bi bi-box-arrow-left me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container py-4" style="flex: 1 0 auto;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h1 class="section-title fs-3 mb-1">My Tasks</h1>
                <p class="section-sub mb-0">Post-production tasks assigned to you.</p>
            </div>
            @if($staff->is_temporary)
                <span class="badge bg-warning text-dark rounded-pill">
                    <i class="bi bi-clock me-1"></i>Temporary Access
                </span>
            @endif
        </div>

        {{-- ==================== SUMMARY CARDS ==================== --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="summary-card">
                    <div class="d-flex align-items-center">
                        <div class="summary-icon bg-dark bg-opacity-10 text-dark">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1 small">Total Tasks</h6>
                            <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="summary-card">
                    <div class="d-flex align-items-center">
                        <div class="summary-icon bg-secondary bg-opacity-10 text-secondary">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1 small">Not Started</h6>
                            <h4 class="mb-0 fw-bold">{{ $stats['not_started'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="summary-card">
                    <div class="d-flex align-items-center">
                        <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1 small">In Progress</h6>
                            <h4 class="mb-0 fw-bold">{{ $stats['in_progress'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="summary-card">
                    <div class="d-flex align-items-center">
                        <div class="summary-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1 small">Completed</h6>
                            <h4 class="mb-0 fw-bold">{{ $stats['completed'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ==================== TASKS TABLE ==================== --}}
        <section class="surface-card">
            <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="section-title"><i class="bi bi-list-check me-2"></i>Assigned Tasks</h2>
                <span class="badge bg-light text-dark border">{{ $tasks->count() }} total</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Control No.</th>
                            <th>Client</th>
                            <th>Event Type</th>
                            <th>Event Date</th>
                            <th>Task Type</th>
                            <th>Status</th>
                            <th>Due Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $task)
                            <tr>
                                <td>
                                    <code>{{ $task->postProduction->booking->customerAccount->control_number ?? 'N/A' }}</code>
                                </td>
                                <td>{{ $task->postProduction->booking->client_name ?? 'N/A' }}</td>
                                <td>{{ $task->postProduction->booking->event_type ?? 'N/A' }}</td>
                                <td>{{ $task->postProduction->booking->event_date ? $task->postProduction->booking->event_date->format('M d, Y') : 'N/A' }}</td>
                                <td><span class="badge bg-dark rounded-pill">{{ str_replace('_', ' ', ucfirst($task->task_type)) }}</span></td>
                                <td>
                                    @php
                                        $taskStatusMap = [
                                            'not_started' => ['secondary', 'Not Started'],
                                            'in_progress' => ['warning', 'In Progress'],
                                            'completed' => ['success', 'Completed'],
                                        ];
                                        [$tCls, $tLabel] = $taskStatusMap[$task->status] ?? ['secondary', ucfirst($task->status)];
                                    @endphp
                                    <span class="badge bg-{{ $tCls }} rounded-pill">{{ $tLabel }}</span>
                                </td>
                                <td>{{ $task->postProduction->expected_completion_date ? $task->postProduction->expected_completion_date->format('M d, Y') : '—' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('staff.task.show', $task->id) }}" class="btn btn-sm btn-outline-dark rounded-3">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No tasks assigned to you.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    @include('partials.auth-footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
