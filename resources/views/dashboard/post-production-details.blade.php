<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-Production Details - Admin</title>
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
            <span class="fw-semibold">Post-Production Details</span>
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

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <a href="{{ route('post-production.index') }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i>Back to Post-Production
                    </a>
                    <h5 class="fw-bold mb-0 mt-1">
                        <i class="bi bi-film me-2"></i>Post-Production
                        <code>{{ $postProduction->booking->booking_ref ?? 'N/A' }}</code>
                    </h5>
                </div>
                <div class="d-flex gap-2">
                    @php
                        $statusMap = [
                            'in_progress' => ['bg-warning text-dark', 'bi-arrow-repeat', 'In Progress'],
                            'ready' => ['bg-success', 'bi-check2-all', 'Ready for Delivery'],
                            'delivered' => ['bg-dark', 'bi-box-seam', 'Delivered'],
                        ];
                        [$cls, $icon, $label] = $statusMap[$postProduction->status] ?? ['bg-secondary', 'bi-circle', ucfirst($postProduction->status)];
                    @endphp
                    <span class="badge {{ $cls }} px-3 py-2" style="font-size: 0.9rem;">
                        <i class="bi {{ $icon }} me-1"></i>{{ $label }}
                    </span>
                    @if($allApproved && !$isFullyPaid && $postProduction->status !== 'delivered')
                        <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 0.85rem;">
                            <i class="bi bi-cash-stack me-1"></i>Waiting for Final Payment (₱{{ number_format($remainingBalance, 2) }} remaining)
                        </span>
                    @endif
                    @if($allApproved && $isFullyPaid && $postProduction->status !== 'delivered')
                        <form method="POST" action="{{ route('post-production.unlock', $postProduction->booking_id) }}">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Unlock deliverables for the client?')">
                                <i class="bi bi-unlock me-1"></i>Unlock Deliverables
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                {{-- LEFT COLUMN --}}
                <div class="col-lg-8">

                    {{-- TASKS --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-list-task me-2"></i>Assigned Tasks ({{ $postProduction->tasks->count() }})</h6>
                        </div>
                        <div class="card-body p-0">
                            @forelse($postProduction->tasks as $task)
                                <div class="border-bottom p-3 {{ $loop->last ? 'border-bottom-0' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge bg-dark">{{ str_replace('_', ' ', ucfirst($task->task_type)) }}</span>
                                                @php
                                                    $taskStatusMap = [
                                                        'not_started' => ['secondary', 'Not Started'],
                                                        'in_progress' => ['warning', 'In Progress'],
                                                        'completed' => ['success', 'Completed'],
                                                    ];
                                                    [$tCls, $tLabel] = $taskStatusMap[$task->status] ?? ['secondary', ucfirst($task->status)];
                                                @endphp
                                                <span class="badge bg-{{ $tCls }}">{{ $tLabel }}</span>
                                                @if($task->admin_review_status === 'approved')
                                                    <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Approved</span>
                                                @elseif($task->admin_review_status === 'revision_requested')
                                                    <span class="badge bg-danger"><i class="bi bi-arrow-return-left me-1"></i>Revision Requested</span>
                                                @elseif($task->status === 'completed' && $task->admin_review_status === 'pending')
                                                    <span class="badge bg-info"><i class="bi bi-hourglass me-1"></i>Awaiting Review</span>
                                                @endif
                                            </div>
                                            <div class="small text-muted">
                                                <i class="bi bi-person me-1"></i>{{ $task->assigneeName() }}
                                            </div>
                                        </div>
                                    </div>

                                    @if($task->instructions)
                                        <div class="small mb-2">
                                            <strong>Instructions:</strong> {{ $task->instructions }}
                                        </div>
                                    @endif

                                    @if($task->revision_notes)
                                        <div class="alert alert-danger py-1 px-2 mb-2 small">
                                            <strong><i class="bi bi-exclamation-triangle me-1"></i>Revision Notes:</strong> {{ $task->revision_notes }}
                                        </div>
                                    @endif

                                    @if($task->deliverable_link)
                                        <div class="mb-2">
                                            <strong class="small">Deliverable:</strong>
                                            <a href="{{ $task->deliverable_link }}" target="_blank" class="text-primary small">
                                                <i class="bi bi-link-45deg me-1"></i>{{ $task->deliverable_link }}
                                            </a>
                                        </div>
                                    @endif

                                    @if($task->remarks)
                                        <div class="small mb-2">
                                            <strong>Staff Remarks:</strong> {{ $task->remarks }}
                                        </div>
                                    @endif

                                    @if($task->completed_at)
                                        <div class="small text-muted mb-2">
                                            Completed: {{ $task->completed_at->format('M d, Y g:i A') }}
                                        </div>
                                    @endif

                                    {{-- OUTSOURCED TEMP ACCOUNT (only when outsourced staff assigned, no login yet) --}}
                                    @if($task->outsourcedStaff && !$task->staff_id)
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal" data-bs-target="#outsourcedModal{{ $task->id }}">
                                                <i class="bi bi-person-plus me-1"></i>Create Temporary Account for {{ $task->outsourcedStaff->name }}
                                            </button>
                                        </div>
                                        <div class="modal fade" id="outsourcedModal{{ $task->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('post-production.outsourced-account', $task->id) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h6 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Create Temporary Staff Account</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-muted small mb-3">A temporary login will be generated and emailed. Access expires automatically once all their tasks are done.</p>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" required value="{{ $task->outsourcedStaff->name }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                                                <input type="email" name="email" class="form-control" required placeholder="staff@email.com">
                                                                <div class="form-text">Credentials will be sent to this email.</div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-dark"><i class="bi bi-send me-1"></i>Create & Send Credentials</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- ADMIN MANUAL LINK (admin-assigned or outsourced with no temp account yet) --}}
                                    @if(!$task->staff_id && $task->admin_review_status !== 'approved')
                                        <div class="mt-2">
                                            <form method="POST" action="{{ route('post-production.admin-link', $task->id) }}" class="d-flex gap-2 align-items-center">
                                                @csrf
                                                <input type="url" name="deliverable_link" class="form-control form-control-sm"
                                                    placeholder="Paste deliverable link..."
                                                    value="{{ $task->deliverable_link }}"
                                                    required>
                                                <button type="submit" class="btn btn-sm btn-dark text-nowrap">
                                                    <i class="bi bi-save me-1"></i>Save Link
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    {{-- ADMIN ACTIONS --}}
                                    @if($task->status === 'completed' && $task->admin_review_status === 'pending')
                                        <div class="d-flex gap-2 mt-2">
                                            <form method="POST" action="{{ route('post-production.approve-task', $task->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="bi bi-check-lg me-1"></i>Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#revisionModal{{ $task->id }}">
                                                <i class="bi bi-arrow-return-left me-1"></i>Request Revision
                                            </button>
                                        </div>

                                        {{-- Revision Modal --}}
                                        <div class="modal fade" id="revisionModal{{ $task->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form method="POST" action="{{ route('post-production.request-revision', $task->id) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h6 class="modal-title fw-bold">Request Revision</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Revision Notes <span class="text-danger">*</span></label>
                                                                <textarea name="revision_notes" rows="4" class="form-control" required
                                                                          placeholder="Explain what needs to be revised..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Submit Revision</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No tasks assigned yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- NOTES --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Production Notes</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('post-production.update-notes', $postProduction->id) }}">
                                @csrf
                                @method('PUT')
                                <textarea name="notes" rows="4" class="form-control mb-3"
                                          placeholder="Add production notes...">{{ $postProduction->notes }}</textarea>
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-save me-1"></i>Save Notes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Booking Info</h6>
                        </div>
                        <div class="card-body">
                            <div class="detail-row">
                                <span class="detail-label">Client</span>
                                <span class="detail-value">{{ $postProduction->booking->client_name ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Event Type</span>
                                <span class="detail-value">{{ $postProduction->booking->event_type ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Event Date</span>
                                <span class="detail-value">{{ $postProduction->booking->event_date ? $postProduction->booking->event_date->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Due Date</span>
                                <span class="detail-value fw-bold">{{ $postProduction->expected_completion_date ? $postProduction->expected_completion_date->format('M d, Y') : '—' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-cash-stack me-2"></i>Payment Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="detail-row">
                                <span class="detail-label">Total Price</span>
                                <span class="detail-value fw-bold">&#8369;{{ number_format($postProduction->booking->total_price ?? 0, 2) }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Total Paid</span>
                                <span class="detail-value fw-bold text-success">&#8369;{{ number_format($totalPaid, 2) }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Remaining</span>
                                <span class="detail-value fw-bold {{ $remainingBalance > 0 ? 'text-danger' : 'text-success' }}">
                                    &#8369;{{ number_format($remainingBalance, 2) }}
                                </span>
                            </div>
                            <hr class="my-2">
                            <div class="detail-row">
                                <span class="detail-label">Final Payment</span>
                                <span class="detail-value">
                                    @if($isFullyPaid)
                                        <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Fully Paid</span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2"><i class="bi bi-x-circle me-1"></i>Unpaid</span>
                                    @endif
                                </span>
                            </div>
                            @if(!$isFullyPaid)
                                <div class="alert alert-warning py-2 px-3 mb-0 mt-3 small">
                                    <i class="bi bi-info-circle me-1"></i>Deliverables cannot be unlocked until the client pays the remaining balance.
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-clipboard-data me-2"></i>Task Summary</h6>
                        </div>
                        <div class="card-body">
                            @php
                                $total = $postProduction->tasks->count();
                                $approved = $postProduction->tasks->where('admin_review_status', 'approved')->count();
                                $pendingReview = $postProduction->tasks->where('status', 'completed')->where('admin_review_status', 'pending')->count();
                                $inProgress = $postProduction->tasks->where('status', 'in_progress')->count();
                                $notStarted = $postProduction->tasks->where('status', 'not_started')->count();
                            @endphp
                            <div class="detail-row">
                                <span class="detail-label">Total Tasks</span>
                                <span class="detail-value">{{ $total }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Approved</span>
                                <span class="detail-value text-success fw-bold">{{ $approved }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Awaiting Review</span>
                                <span class="detail-value text-info">{{ $pendingReview }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">In Progress</span>
                                <span class="detail-value text-warning">{{ $inProgress }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Not Started</span>
                                <span class="detail-value text-muted">{{ $notStarted }}</span>
                            </div>

                            @if($total > 0)
                                <div class="mt-3">
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: {{ round(($approved / $total) * 100) }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ round(($approved / $total) * 100) }}% complete</small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
</body>

</html>
