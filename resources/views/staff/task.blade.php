<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details - Staff</title>
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
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <a href="{{ route('staff.dashboard') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>

        <div class="row g-4 mt-2">
            <div class="col-lg-8">
                <div class="card section-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Task Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <span class="detail-label">Control Number</span>
                                    <span class="detail-value"><code>{{ $task->postProduction->booking->customerAccount->control_number ?? 'N/A' }}</code></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <span class="detail-label">Client Name</span>
                                    <span class="detail-value">{{ $task->postProduction->booking->client_name ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <span class="detail-label">Event Type</span>
                                    <span class="detail-value">{{ $task->postProduction->booking->event_type ?? 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <span class="detail-label">Event Date</span>
                                    <span class="detail-value">{{ $task->postProduction->booking->event_date ? $task->postProduction->booking->event_date->format('M d, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <span class="detail-label">Task Type</span>
                                    <span class="detail-value"><span class="badge bg-dark">{{ str_replace('_', ' ', ucfirst($task->task_type)) }}</span></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-row">
                                    <span class="detail-label">Due Date</span>
                                    <span class="detail-value fw-bold">{{ $task->postProduction->expected_completion_date ? $task->postProduction->expected_completion_date->format('M d, Y') : '—' }}</span>
                                </div>
                            </div>
                        </div>

                        @if($task->instructions)
                            <hr>
                            <div>
                                <small class="text-muted fw-bold">Instructions from Admin</small>
                                <p class="small mb-0 mt-1">{{ $task->instructions }}</p>
                            </div>
                        @endif

                        @if($task->revision_notes)
                            <div class="alert alert-danger py-2 mt-3 mb-0">
                                <strong><i class="bi bi-exclamation-triangle me-1"></i>Revision Notes:</strong> {{ $task->revision_notes }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- UPDATE TASK --}}
                @if($task->admin_review_status !== 'approved')
                    <div class="card section-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Update Task</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('staff.task.update', $task->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select name="status" class="form-select" required>
                                        <option value="not_started" {{ $task->status === 'not_started' ? 'selected' : '' }}>Not Started</option>
                                        <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Deliverable Link <span class="text-danger" id="linkRequired">*</span></label>
                                    <input type="url" name="deliverable_link" class="form-control"
                                           value="{{ old('deliverable_link', $task->deliverable_link) }}"
                                           placeholder="https://drive.google.com/... or https://onedrive.live.com/..." id="deliverableLink">
                                    <div class="form-text">Google Drive, OneDrive, Dropbox, or any file sharing link. <strong>Required to mark as completed.</strong></div>
                                    @error('deliverable_link')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Remarks (optional)</label>
                                    <textarea name="remarks" rows="3" class="form-control"
                                              placeholder="Add any notes about your work...">{{ old('remarks', $task->remarks) }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Update Task
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="card section-card mb-4">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                            <h6 class="fw-bold text-success">Task Approved</h6>
                            <p class="text-muted small mb-0">This task has been approved by the admin.</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card section-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-clipboard-data me-2"></i>Task Status</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $taskStatusMap = [
                                'not_started' => ['secondary', 'bi-circle', 'Not Started'],
                                'in_progress' => ['warning', 'bi-arrow-repeat', 'In Progress'],
                                'completed' => ['success', 'bi-check-circle', 'Completed'],
                            ];
                            [$tCls, $tIcon, $tLabel] = $taskStatusMap[$task->status] ?? ['secondary', 'bi-circle', ucfirst($task->status)];
                        @endphp
                        <div class="text-center mb-3">
                            <i class="bi {{ $tIcon }} text-{{ $tCls }} d-block" style="font-size: 2.5rem;"></i>
                            <span class="badge bg-{{ $tCls }} px-3 py-2 mt-2" style="font-size: 0.9rem;">{{ $tLabel }}</span>
                        </div>
                        <hr>
                        <div class="detail-row">
                            <span class="detail-label">Review Status</span>
                            <span class="detail-value">
                                @if($task->admin_review_status === 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($task->admin_review_status === 'revision_requested')
                                    <span class="badge bg-danger">Revision Requested</span>
                                @else
                                    <span class="badge bg-secondary">Pending</span>
                                @endif
                            </span>
                        </div>
                        @if($task->completed_at)
                            <div class="detail-row">
                                <span class="detail-label">Completed At</span>
                                <span class="detail-value">{{ $task->completed_at->format('M d, Y g:i A') }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($task->deliverable_link)
                    <div class="card section-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-link-45deg me-2"></i>Current Deliverable</h6>
                        </div>
                        <div class="card-body">
                            <a href="{{ $task->deliverable_link }}" target="_blank" class="btn btn-outline-primary w-100">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Open Deliverable Link
                            </a>
                            @if($task->remarks)
                                <div class="mt-3">
                                    <small class="text-muted fw-bold">Your Remarks</small>
                                    <p class="small mb-0 mt-1">{{ $task->remarks }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const statusSelect = document.querySelector('select[name="status"]');
        const linkInput = document.getElementById('deliverableLink');
        const linkRequired = document.getElementById('linkRequired');

        if (statusSelect && linkInput) {
            statusSelect.addEventListener('change', function() {
                if (this.value === 'completed') {
                    linkInput.required = true;
                    linkRequired.style.display = 'inline';
                } else {
                    linkInput.required = false;
                    linkRequired.style.display = 'none';
                }
            });

            if (statusSelect.value === 'completed') {
                linkInput.required = true;
                linkRequired.style.display = 'inline';
            }
        }
    </script>
</body>

</html>
