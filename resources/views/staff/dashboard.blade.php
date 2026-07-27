<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - HarvyMance Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
</head>

<body class="client-dashboard-body">

    <div class="client-topnav">
        <a href="{{ route('staff.dashboard') }}" class="brand">
            <i class="bi bi-camera-video me-2"></i>HarvyMance Films
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="small opacity-75 d-none d-md-inline">
                <i class="bi bi-person me-1"></i> {{ $staff->name }}
            </span>
            <form method="POST" action="{{ route('staff.logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-light"><i class="bi bi-box-arrow-left me-1"></i>Logout</button>
            </form>
        </div>
    </div>

    <div class="container py-4">
        <h5 class="fw-bold mb-4"><i class="bi bi-clipboard-data me-2"></i>My Post-Production Tasks</h5>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card overview-card">
                    <div class="card-body text-center">
                        <div class="fw-bold fs-3 text-primary">{{ $stats['total'] }}</div>
                        <small class="text-muted">Total Tasks</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card overview-card">
                    <div class="card-body text-center">
                        <div class="fw-bold fs-3 text-secondary">{{ $stats['not_started'] }}</div>
                        <small class="text-muted">Not Started</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card overview-card">
                    <div class="card-body text-center">
                        <div class="fw-bold fs-3 text-warning">{{ $stats['in_progress'] }}</div>
                        <small class="text-muted">In Progress</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card overview-card">
                    <div class="card-body text-center">
                        <div class="fw-bold fs-3 text-success">{{ $stats['completed'] }}</div>
                        <small class="text-muted">Completed</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card section-card">
            <div class="card-header bg-white border-bottom">
                <h6 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i>Assigned Tasks</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
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
                                    <td><span class="badge bg-dark">{{ str_replace('_', ' ', ucfirst($task->task_type)) }}</span></td>
                                    <td>
                                        @php
                                            $taskStatusMap = [
                                                'not_started' => ['secondary', 'Not Started'],
                                                'in_progress' => ['warning', 'In Progress'],
                                                'completed' => ['success', 'Completed'],
                                            ];
                                            [$tCls, $tLabel] = $taskStatusMap[$task->status] ?? ['secondary', ucfirst($task->status)];
                                        @endphp
                                        <span class="badge bg-{{ $tCls }}">{{ $tLabel }}</span>
                                    </td>
                                    <td>{{ $task->postProduction->expected_completion_date ? $task->postProduction->expected_completion_date->format('M d, Y') : '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('staff.task.show', $task->id) }}" class="btn btn-sm btn-outline-dark">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No tasks assigned to you.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
