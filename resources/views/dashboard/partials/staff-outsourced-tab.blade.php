{{-- Outsourced staff tab content (AJAX) --}}
                <div class="row g-3 mb-4">
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Total In-House</h6>
                                    <h4 class="mb-0 fw-bold">{{ $totalStaff }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Active</h6>
                                    <h4 class="mb-0 fw-bold">{{ $activeStaff }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-person-x"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Inactive</h6>
                                    <h4 class="mb-0 fw-bold">{{ $inactiveStaff }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Outsourced</h6>
                                    <h4 class="mb-0 fw-bold">{{ $outsourcedStaff->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @if($tab === 'outsourced')
                <section class="surface-card">
                    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="section-title">
                            Outsourced Staff
                            <span class="badge bg-warning text-dark ms-1">Record Only</span>
                        </h2>
                        <button type="button" class="btn btn-sm btn-outline-dark rounded-pill"
                            onclick="openCreateOutsourcedModal()">
                            <i class="bi bi-plus-circle me-1"></i> Add Outsourced
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Notes</th>
                                    <th>Added</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($outsourcedStaff as $os)
                                    <tr>
                                        <td class="fw-semibold">{{ $os->name }}</td>
                                        <td>{{ $os->email ?? '—' }}</td>
                                        <td>{{ $os->contact_number ?? '—' }}</td>
                                        <td><span class="text-muted small">{{ $os->notes ?? '—' }}</span></td>
                                        <td>{{ $os->created_at->format('M d, Y') }}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-outline-secondary rounded-3"
                                                    onclick="openEditOutsourcedModal('{{ $os->id }}', '{{ addslashes($os->name) }}', '{{ addslashes($os->email ?? '') }}', '{{ $os->contact_number }}', '{{ addslashes($os->notes ?? '') }}')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger rounded-3"
                                                    onclick="openDeleteOutsourcedModal('{{ $os->id }}', '{{ addslashes($os->name) }}')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-person-lines-fill fs-1 d-block mb-2"></i>
                                            No outsourced staff records yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif
