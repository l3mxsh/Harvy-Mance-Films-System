<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post-Production Details - Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Post-Production Details</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            {{-- HEADER --}}
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                <div>
                    <a href="{{ route('post-production.index') }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i>Back to Post-Production
                    </a>
                </div>
                <div class="d-flex gap-2 align-items-center flex-wrap">
                    @include('partials.status-badge', [
                        'status' => $postProduction->status,
                        'label' => match ($postProduction->status) {
                            'in_progress' => 'In Progress',
                            'ready' => 'Ready for Delivery',
                            'delivered' => 'Delivered',
                            default => null,
                        },
                    ])
                    @if($allApproved && !$isFullyPaid && $postProduction->status !== 'delivered')
                        <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 0.85rem;">
                            Waiting for Final Payment
                            (₱{{ number_format($remainingBalance, 2) }} remaining)
                        </span>
                    @endif
                    @if($allApproved && $isFullyPaid && $postProduction->status !== 'delivered')
                        <button type="button" class="btn btn-success rounded-pill" data-bs-toggle="modal" data-bs-target="#unlockModal">
                            <i class="bi bi-unlock me-1"></i>Unlock Deliverables
                        </button>
                    @endif
                </div>
            </div>

            @include('partials.modals.unlock-deliverables')

            <div class="bento-grid">
                {{-- TASKS --}}
                <section class="surface-card panel-tasks">
                    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="section-title"><i class="bi bi-list-task me-2"></i>Assigned Tasks</h2>
                        <span class="badge bg-light text-dark border">{{ $postProduction->tasks->count() }} total</span>
                    </div>

                    @forelse($postProduction->tasks as $task)
                        <div class="task-card mb-3">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <span
                                        class="badge bg-light text-dark border">{{ str_replace('_', ' ', ucfirst($task->task_type)) }}</span>
                                    @php
                                        $hasTaskDetails = $task->instructions
                                            || $task->revision_notes
                                            || $task->deliverable_link
                                            || $task->remarks
                                            || $task->completed_at
                                            || ($task->outsourcedStaff && !$task->staff_id)
                                            || (!$task->staff_id && $task->admin_review_status !== 'approved')
                                            || ($task->status === 'completed' && $task->admin_review_status === 'pending');
                                    @endphp
                                    @include('partials.status-badge', ['status' => $task->status, 'label' => str_replace('_', ' ', ucfirst($task->status))])
                                    @if($task->admin_review_status === 'approved')
                                        @include('partials.status-badge', ['status' => 'approved'])
                                    @elseif($task->admin_review_status === 'revision_requested')
                                        @include('partials.status-badge', ['status' => 'revision_requested'])
                                    @elseif($task->status === 'completed' && $task->admin_review_status === 'pending')
                                        @include('partials.status-badge', ['status' => 'awaiting_review', 'label' => 'Awaiting Review'])
                                    @endif
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-person me-1"></i>{{ $task->assigneeName() }}
                                </div>
                            </div>

                            @if($hasTaskDetails)
                                <div class="border-top mt-3 pt-3">
                                    @if($task->instructions)
                                        <div class="small mb-2">
                                            <strong>Instructions:</strong> {{ $task->instructions }}
                                        </div>
                                    @endif

                                    @if($task->revision_notes)
                                        <div class="revision-note mb-2">
                                            <i class="bi bi-exclamation-triangle"></i>
                                            <div><strong>Revision Notes:</strong> {{ $task->revision_notes }}</div>
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
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-dark rounded-2"
                                                data-bs-toggle="modal" data-bs-target="#outsourcedModal{{ $task->id }}">
                                                <i class="bi bi-person-plus me-1"></i>Create Temporary Account for
                                                {{ $task->outsourcedStaff->name }}
                                            </button>
                                        </div>
                                        <div class="modal fade" id="outsourcedModal{{ $task->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('post-production.outsourced-account', $task->id) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h6 class="modal-title fw-bold"><i
                                                                    class="bi bi-person-plus me-2"></i>Create Temporary Staff
                                                                Account</h6>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-muted small mb-3">A temporary login will be generated and
                                                                emailed. Access expires automatically once all their tasks are done.
                                                            </p>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Full Name <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="name" class="form-control" required
                                                                    value="{{ $task->outsourcedStaff->name }}">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Email Address <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="email" name="email" class="form-control" required
                                                                    placeholder="staff@email.com">
                                                                <div class="form-text">Credentials will be sent to this email.</div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-dark rounded-pill"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-dark rounded-pill"><i
                                                                    class="bi bi-send me-1"></i>Create & Send Credentials</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- ADMIN MANUAL LINK (admin-assigned or outsourced with no temp account yet) --}}
                                    @if(!$task->staff_id && $task->admin_review_status !== 'approved')
                                        <div class="mb-2">
                                            <form method="POST" action="{{ route('post-production.admin-link', $task->id) }}"
                                                class="d-flex gap-2 align-items-center">
                                                @csrf
                                                <input type="url" name="deliverable_link" class="form-control"
                                                    placeholder="Paste deliverable link..." value="{{ $task->deliverable_link }}"
                                                    required>
                                                <button type="submit" class="btn btn-sm btn-dark rounded-2 text-nowrap">
                                                    <i class="bi bi-save me-1"></i>Save Link
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    {{-- ADMIN ACTIONS --}}
                                    @if($task->status === 'completed' && $task->admin_review_status === 'pending')
                                        <div class="d-flex gap-2 justify-content-end">
                                            <form method="POST" action="{{ route('post-production.approve-task', $task->id) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-dark rounded-2">
                                                    <i class="bi bi-check-lg me-1"></i>Approve
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-dark rounded-2"
                                                data-bs-toggle="modal" data-bs-target="#revisionModal{{ $task->id }}">
                                                <i class="bi bi-arrow-return-left me-1"></i>Request Revision
                                            </button>
                                        </div>

                                        {{-- Revision Modal --}}
                                        <div class="modal fade" id="revisionModal{{ $task->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('post-production.request-revision', $task->id) }}">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h6 class="modal-title fw-bold">Request Revision</h6>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">Revision Notes <span
                                                                        class="text-danger">*</span></label>
                                                                <textarea name="revision_notes" rows="4" class="form-control"
                                                                    required
                                                                    placeholder="Explain what needs to be revised..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-dark rounded-pill"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger rounded-pill">Submit
                                                                Revision</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No tasks assigned yet.
                        </div>
                    @endforelse
                </section>

                {{-- NOTES --}}
                <section class="surface-card panel-notes">
                    <div class="section-head">
                        <h2 class="section-title"><i class="bi bi-journal-text me-2"></i>Production Notes</h2>
                    </div>
                    <form method="POST" action="{{ route('post-production.update-notes', $postProduction->id) }}">
                        @csrf
                        @method('PUT')
                        <textarea name="notes" rows="4" class="form-control mb-3"
                            placeholder="Add production notes...">{{ $postProduction->notes }}</textarea>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-dark rounded-pill px-4">
                                Save Notes
                            </button>
                        </div>
                    </form>
                </section>

                {{-- BOOKING INFO --}}
                <section class="surface-card panel-booking">
                    <div class="section-head">
                        <h2 class="section-title"><i class="bi bi-journal-text me-2"></i>Booking Info</h2>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Booking Ref</span>
                        <span
                            class="detail-value"><code>{{ $postProduction->booking->booking_ref ?? 'N/A' }}</code></span>
                    </div>
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
                        <span
                            class="detail-value">{{ $postProduction->booking->event_date ? $postProduction->booking->event_date->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Due Date</span>
                        <span
                            class="detail-value fw-bold">{{ $postProduction->expected_completion_date ? $postProduction->expected_completion_date->format('M d, Y') : '—' }}</span>
                    </div>
                </section>

                <section class="surface-card panel-payment">
                    <div class="section-head">
                        <h2 class="section-title"><i class="bi bi-cash-stack me-2"></i>Payment Status</h2>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Total Price</span>
                        <span
                            class="detail-value fw-bold">&#8369;{{ number_format($postProduction->booking->total_price ?? 0, 2) }}</span>
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
                                @include('partials.status-badge', ['status' => 'fully_paid', 'label' => 'Fully Paid'])
                            @else
                                @include('partials.status-badge', ['status' => 'unpaid'])
                            @endif
                        </span>
                    </div>
                    @if(!$isFullyPaid)
                        <div class="hint-box mt-3">
                            <i class="bi bi-info-circle mt-1"></i>
                            <div>Deliverables cannot be unlocked until the client pays the remaining balance.</div>
                        </div>
                    @endif
                </section>

                <section class="surface-card panel-summary">
                    <div class="section-head">
                        <h2 class="section-title"><i class="bi bi-clipboard-data me-2"></i>Task Summary</h2>
                    </div>
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
                                <div class="progress-bar bg-success"
                                    style="width: {{ round(($approved / $total) * 100) }}%"></div>
                            </div>
                            <small class="text-muted">{{ round(($approved / $total) * 100) }}% complete</small>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>

    {{-- FLASH TOASTS --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="flashToasts" style="z-index: 1080;">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('#flashToasts .toast').forEach(function (el) {
                new bootstrap.Toast(el, { delay: 4000 }).show();
            });
        });
    </script>
    <script>
        document.addEventListener('submit', function (e) {
            var btn = e.submitter;
            if (!btn || btn.tagName !== 'BUTTON' || btn.disabled) return;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>' + btn.textContent.trim();
        });
    </script>
</body>

</html>