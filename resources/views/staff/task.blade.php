<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Details - HarvyMance Films</title>
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
        @if(session('error'))
            <div class="alert alert-soft alert-soft-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <a href="{{ route('staff.dashboard') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4 mt-2">
            <div>
                <h1 class="section-title fs-3 mb-1">Task Details</h1>
            </div>
            @include('partials.status-badge', ['status' => $task->status])
        </div>

        <div class="row g-4">
            {{-- ==================== BOOKING & TASK INFO ==================== --}}
            <div class="col-lg-6">
                <section class="surface-card">
                    <div class="section-head">
                        <h2 class="section-title">Booking &amp; Task Info</h2>
                    </div>
                    <div class="row g-0">
                        <div class="col-md-6 pe-md-3">
                            <div class="detail-row">
                                <span class="detail-label">Booking Ref.</span>
                                <span class="detail-value"><code>{{ $task->postProduction->booking->customerAccount->control_number ?? 'N/A' }}</code></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Client Name</span>
                                <span class="detail-value">{{ $task->postProduction->booking->client_name ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Event Type</span>
                                <span class="detail-value">{{ $task->postProduction->booking->event_type ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6 ps-md-3">
                            <div class="detail-row">
                                <span class="detail-label">Event Date</span>
                                <span class="detail-value">{{ $task->postProduction->booking->event_date ? $task->postProduction->booking->event_date->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Task Type</span>
                                <span class="detail-value"><span class="badge bg-dark rounded-pill">{{ str_replace('_', ' ', ucfirst($task->task_type)) }}</span></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Due Date</span>
                                <span class="detail-value fw-bold">{{ $task->postProduction->expected_completion_date ? $task->postProduction->expected_completion_date->format('M d, Y') : '—' }}</span>
                            </div>
                        </div>
                    </div>

                    @if($task->instructions)
                        <div class="hint-box mt-3">
                            <i class="bi bi-chat-left-text"></i>
                            <div>
                                <strong>Instructions from Admin</strong>
                                <p class="mb-0 mt-1">{{ $task->instructions }}</p>
                            </div>
                        </div>
                    @endif

                    @if($task->revision_notes)
                        <div class="revision-note mt-3">
                            <i class="bi bi-exclamation-triangle"></i>
                            <div>
                                <strong>Revision Notes</strong>
                                <p class="mb-0 mt-1">{{ $task->revision_notes }}</p>
                            </div>
                        </div>
                    @endif

                    <hr class="my-3">
                    <div class="row g-0">
                        <div class="col-md-6 pe-md-3">
                            <div class="detail-row">
                                <span class="detail-label">Review Status</span>
                                <span class="detail-value">
                                    @if($task->admin_review_status === 'approved')
                                        @include('partials.status-badge', ['status' => 'approved'])
                                    @elseif($task->admin_review_status === 'revision_requested')
                                        @include('partials.status-badge', ['status' => 'revision_requested'])
                                    @else
                                        @include('partials.status-badge', ['status' => 'awaiting_review', 'label' => 'Pending Review'])
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 ps-md-3">
                            <div class="detail-row">
                                <span class="detail-label">Completed At</span>
                                <span class="detail-value">{{ $task->completed_at ? $task->completed_at->format('M d, Y g:i A') : '—' }}</span>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            {{-- ==================== UPDATE TASK ==================== --}}
            <div class="col-lg-6">
                @if($task->admin_review_status !== 'approved')
                    <section class="surface-card h-100">
                        <form method="POST" action="{{ route('staff.task.update', $task->id) }}" class="m-0">
                            @csrf
                            @method('PUT')

                            <div class="section-head">
                                <h2 class="section-title">Update Task</h2>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="not_started" {{ $task->status === 'not_started' ? 'selected' : '' }}>Not Started</option>
                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed" {{ $task->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deliverable Link <span class="text-danger" id="linkRequired">*</span></label>
                                <input type="url" name="deliverable_link" class="form-control"
                                       value="{{ old('deliverable_link', $task->deliverable_link) }}"
                                       placeholder="https://drive.google.com/... or https://onedrive.live.com/..." id="deliverableLink">
                                <div class="form-text">Google Drive, OneDrive, Dropbox, or any file sharing link. <strong>Required to mark as completed.</strong></div>
                                @error('deliverable_link')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-0">
                                <label class="form-label">Remarks (optional)</label>
                                <textarea name="remarks" rows="3" class="form-control"
                                          placeholder="Add any notes about your work...">{{ old('remarks', $task->remarks) }}</textarea>
                            </div>

                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary-dark btn-sm-pill">Update Task</button>
                            </div>
                        </form>

                        <hr class="my-4">
                        <div class="section-head">
                            <h2 class="section-title">Current Deliverable</h2>
                        </div>
                        @if($task->deliverable_link)
                            <a href="{{ $task->deliverable_link }}" target="_blank" class="btn btn-outline-dark-soft btn-sm-pill w-100">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Open Deliverable Link
                            </a>
                        @endif
                        @if($task->remarks)
                            <div class="mt-3">
                                <small class="text-muted fw-bold">Your Remarks</small>
                                <p class="small mb-0 mt-1">{{ $task->remarks }}</p>
                            </div>
                        @endif
                    </section>
                @else
                    <section class="surface-card h-100">
                        <div class="text-center py-4">
                            <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                            <h6 class="fw-bold text-success mb-1">Task Approved</h6>
                            <p class="text-muted small mb-0">This task has been approved by the admin.</p>
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </main>

    @include('partials.auth-footer')

    @if(session('success'))
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;">
            <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const successToastEl = document.getElementById('successToast');
        if (successToastEl) {
            new bootstrap.Toast(successToastEl).show();
        }

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
