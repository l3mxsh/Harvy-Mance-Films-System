<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start Post-Production - Admin</title>
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
            <span class="fw-semibold">Start Post-Production</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <a href="{{ route('post-production.index') }}" class="text-decoration-none text-muted small">
                                <i class="bi bi-arrow-left me-1"></i>Back to Post-Production
                            </a>
                            <h5 class="fw-bold mb-0 mt-1">
                                <i class="bi bi-film me-2"></i>Start Post-Production
                            </h5>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Booking Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="detail-row">
                                        <span class="detail-label">Booking Ref</span>
                                        <span class="detail-value"><code>{{ $booking->booking_ref }}</code></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-row">
                                        <span class="detail-label">Client</span>
                                        <span class="detail-value">{{ $booking->client_name }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-row">
                                        <span class="detail-label">Event Type</span>
                                        <span class="detail-value">{{ $booking->event_type ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-row">
                                        <span class="detail-label">Event Date</span>
                                        <span class="detail-value">{{ $booking->event_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('post-production.store', $booking->id) }}" id="createForm">
                        @csrf

                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-2"></i>General Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="expected_completion_date" class="form-label fw-semibold">
                                            Expected Completion Date <span class="text-danger">*</span>
                                        </label>
                                        <input type="date" name="expected_completion_date" id="expected_completion_date"
                                               class="form-control @error('expected_completion_date') is-invalid @enderror"
                                               value="{{ old('expected_completion_date') }}" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                        @error('expected_completion_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="notes" class="form-label fw-semibold">General Notes / Instructions</label>
                                        <textarea name="notes" id="notes" rows="2"
                                                  class="form-control @error('notes') is-invalid @enderror"
                                                  placeholder="Overall instructions for the post-production team...">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-list-task me-2"></i>Task Assignments</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addTaskBtn">
                                    <i class="bi bi-plus-lg me-1"></i>Add Task
                                </button>
                            </div>
                            <div class="card-body" id="tasksContainer">
                        @php $oldTasks = old('tasks', [['assignee_type' => 'inhouse', 'staff_id' => '', 'outsourced_staff_id' => '', 'task_type' => '', 'instructions' => '']]); @endphp
                                @foreach($oldTasks as $idx => $oldTask)
                                    <div class="task-entry border rounded p-3 mb-3" data-index="{{ $idx }}">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold text-primary mb-0"><i class="bi bi-person-gear me-1"></i>Task #{{ $idx + 1 }}</h6>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-task" style="display: {{ count($oldTasks) > 1 ? 'block' : 'none' }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold small">Assignee Type <span class="text-danger">*</span></label>
                                                <select name="tasks[{{ $idx }}][assignee_type]" class="form-select assignee-type-select" required>
                                                    <option value="inhouse" {{ ($oldTask['assignee_type'] ?? 'inhouse') === 'inhouse' ? 'selected' : '' }}>In-House Staff</option>
                                                    <option value="outsourced" {{ ($oldTask['assignee_type'] ?? '') === 'outsourced' ? 'selected' : '' }}>Outsourced Staff</option>
                                                    <option value="admin" {{ ($oldTask['assignee_type'] ?? '') === 'admin' ? 'selected' : '' }}>Admin (manual link)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 assignee-inhouse {{ ($oldTask['assignee_type'] ?? 'inhouse') !== 'inhouse' ? 'd-none' : '' }}">
                                                <label class="form-label fw-semibold small">In-House Staff</label>
                                                <select name="tasks[{{ $idx }}][staff_id]" class="form-select">
                                                    <option value="">-- Select staff --</option>
                                                    @foreach($staff as $s)
                                                        <option value="{{ $s->id }}" {{ (old("tasks.{$idx}.staff_id") == $s->id) ? 'selected' : '' }}>{{ $s->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 assignee-outsourced {{ ($oldTask['assignee_type'] ?? '') !== 'outsourced' ? 'd-none' : '' }}">
                                                <label class="form-label fw-semibold small">Outsourced Staff</label>
                                                <select name="tasks[{{ $idx }}][outsourced_staff_id]" class="form-select">
                                                    <option value="">-- Select outsourced --</option>
                                                    @foreach($outsourcedStaff as $os)
                                                        <option value="{{ $os->id }}" {{ (old("tasks.{$idx}.outsourced_staff_id") == $os->id) ? 'selected' : '' }}>{{ $os->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3 assignee-admin {{ ($oldTask['assignee_type'] ?? '') !== 'admin' ? 'd-none' : '' }}">
                                                <label class="form-label fw-semibold small">Assigned To</label>
                                                <input type="text" class="form-control" value="Admin" disabled>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold small">Task Type <span class="text-danger">*</span></label>
                                                <select name="tasks[{{ $idx }}][task_type]" class="form-select" required>
                                                    <option value="">-- Select type --</option>
                                                    <option value="photo_editing" {{ old("tasks.{$idx}.task_type") === 'photo_editing' ? 'selected' : '' }}>Photo Editing</option>
                                                    <option value="video_editing" {{ old("tasks.{$idx}.task_type") === 'video_editing' ? 'selected' : '' }}>Video Editing</option>
                                                    <option value="both" {{ old("tasks.{$idx}.task_type") === 'both' ? 'selected' : '' }}>Both</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label fw-semibold small">Instructions</label>
                                                <input type="text" name="tasks[{{ $idx }}][instructions]" class="form-control"
                                                    placeholder="Specific instructions..."
                                                    value="{{ old("tasks.{$idx}.instructions") }}">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-play-circle me-1"></i>Start Post-Production
                            </button>
                            <a href="{{ route('post-production.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        const inHouseOptions = @json($staff->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->toArray());
        const outsourcedOptions = @json($outsourcedStaff->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->toArray());
        let taskIndex = {{ count($oldTasks) }};

        function buildInHouseOpts() {
            return inHouseOptions.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
        }
        function buildOutsourcedOpts() {
            return outsourcedOptions.map(s => `<option value="${s.id}">${s.name}</option>`).join('');
        }

        function toggleAssigneeFields(select) {
            const entry = select.closest('.task-entry');
            entry.querySelector('.assignee-inhouse').classList.toggle('d-none', select.value !== 'inhouse');
            entry.querySelector('.assignee-outsourced').classList.toggle('d-none', select.value !== 'outsourced');
            entry.querySelector('.assignee-admin').classList.toggle('d-none', select.value !== 'admin');
        }

        document.getElementById('tasksContainer').addEventListener('change', function(e) {
            if (e.target.classList.contains('assignee-type-select')) {
                toggleAssigneeFields(e.target);
            }
        });

        document.getElementById('addTaskBtn').addEventListener('click', function() {
            const container = document.getElementById('tasksContainer');
            const idx = taskIndex++;
            const html = `
                <div class="task-entry border rounded p-3 mb-3" data-index="${idx}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-primary mb-0"><i class="bi bi-person-gear me-1"></i>Task #${idx + 1}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-task"><i class="bi bi-trash"></i></button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Assignee Type <span class="text-danger">*</span></label>
                            <select name="tasks[${idx}][assignee_type]" class="form-select assignee-type-select" required>
                                <option value="inhouse" selected>In-House Staff</option>
                                <option value="outsourced">Outsourced Staff</option>
                                <option value="admin">Admin (manual link)</option>
                            </select>
                        </div>
                        <div class="col-md-3 assignee-inhouse">
                            <label class="form-label fw-semibold small">In-House Staff</label>
                            <select name="tasks[${idx}][staff_id]" class="form-select">
                                <option value="">-- Select staff --</option>
                                ${buildInHouseOpts()}
                            </select>
                        </div>
                        <div class="col-md-3 assignee-outsourced d-none">
                            <label class="form-label fw-semibold small">Outsourced Staff</label>
                            <select name="tasks[${idx}][outsourced_staff_id]" class="form-select">
                                <option value="">-- Select outsourced --</option>
                                ${buildOutsourcedOpts()}
                            </select>
                        </div>
                        <div class="col-md-3 assignee-admin d-none">
                            <label class="form-label fw-semibold small">Assigned To</label>
                            <input type="text" class="form-control" value="Admin" disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Task Type <span class="text-danger">*</span></label>
                            <select name="tasks[${idx}][task_type]" class="form-select" required>
                                <option value="">-- Select type --</option>
                                <option value="photo_editing">Photo Editing</option>
                                <option value="video_editing">Video Editing</option>
                                <option value="both">Both</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Instructions</label>
                            <input type="text" name="tasks[${idx}][instructions]" class="form-control" placeholder="Specific instructions...">
                        </div>
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
            updateRemoveButtons();
        });

        document.getElementById('tasksContainer').addEventListener('click', function(e) {
            if (e.target.closest('.remove-task')) {
                e.target.closest('.task-entry').remove();
                updateRemoveButtons();
            }
        });

        function updateRemoveButtons() {
            const entries = document.querySelectorAll('.task-entry');
            entries.forEach(entry => {
                entry.querySelector('.remove-task').style.display = entries.length > 1 ? 'block' : 'none';
            });
        }
    </script>
</body>

</html>
