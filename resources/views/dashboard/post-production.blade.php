<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-Production Management - Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/post-production.css') }}">
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Post-Production Management</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-film"></i></div>
                                <div class="ms-3">
                                    <small class="text-muted">Total Projects</small>
                                    <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-arrow-repeat"></i></div>
                                <div class="ms-3">
                                    <small class="text-muted">In Progress</small>
                                    <div class="fw-bold fs-4">{{ $stats['in_progress'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-hourglass-split"></i></div>
                                <div class="ms-3">
                                    <small class="text-muted">Awaiting Review</small>
                                    <div class="fw-bold fs-4">{{ $stats['awaiting_review'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle"></i></div>
                                <div class="ms-3">
                                    <small class="text-muted">Completed</small>
                                    <div class="fw-bold fs-4">{{ $stats['completed'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i>All Post-Production Projects</h6>
                    <span class="badge bg-secondary">{{ $postProductions->total() }} total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ref</th>
                                    <th>Client / Event</th>
                                    <th>Tasks</th>
                                    <th>Progress</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($postProductions as $pp)
                                    @php
                                        $totalTasks = $pp->tasks->count();
                                        $completedTasks = $pp->tasks->where('admin_review_status', 'approved')->count();
                                        $pendingReview = $pp->tasks->where('admin_review_status', 'pending')->where('status', 'completed')->count();
                                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                                    @endphp
                                    <tr>
                                        <td><code>{{ $pp->booking->booking_ref ?? 'N/A' }}</code></td>
                                        <td>
                                            <div>{{ $pp->booking->client_name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $pp->booking->event_type ?? '' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">{{ $totalTasks }}</span>
                                            @if($pendingReview > 0)
                                                <span class="badge bg-warning text-dark">{{ $pendingReview }} to review</span>
                                            @endif
                                        </td>
                                        <td style="min-width: 120px;">
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <small class="text-muted">{{ $completedTasks }}/{{ $totalTasks }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    'in_progress' => ['bg-warning text-dark', 'bi-arrow-repeat', 'In Progress'],
                                                    'ready' => ['bg-success', 'bi-check2-all', 'Ready for Delivery'],
                                                    'delivered' => ['bg-dark', 'bi-box-seam', 'Delivered'],
                                                ];
                                                [$cls, $icon, $label] = $statusMap[$pp->status] ?? ['bg-secondary', 'bi-circle', ucfirst($pp->status)];
                                            @endphp
                                            <span class="badge {{ $cls }}"><i class="bi {{ $icon }} me-1"></i>{{ $label }}</span>
                                        </td>
                                        <td>{{ $pp->expected_completion_date ? $pp->expected_completion_date->format('M d, Y') : '—' }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('post-production.show', $pp->id) }}" class="btn btn-sm btn-outline-dark">
                                                <i class="bi bi-eye me-1"></i>Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            No post-production projects found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($postProductions->hasPages())
                    <div class="card-footer bg-white border-top">
                        {{ $postProductions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>

</html>
