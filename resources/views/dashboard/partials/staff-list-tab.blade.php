{{-- All / In-House staff tab content (AJAX) --}}
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
            @if(!in_array($tab, ['outsourced', 'teams']))
                <section class="surface-card mb-4">
                    <div class="row g-2 align-items-center">
                        <div class="col-9 col-md-9 col-lg-10">
                            <input type="text" id="staffSearchInput" class="form-control"
                                placeholder="Search by name or email..." value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <div class="col-3 col-md-3 col-lg-2">
                            <select id="staffStatusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                    </div>
                </section>
            @endif
            @if(!in_array($tab, ['outsourced', 'teams']))
                <section class="surface-card">
                    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="section-title">
                            {{ $tab === 'in-house' ? 'In-House Staff' : 'All Staff' }}
                        </h2>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border" id="staffTotalBadge">{{ $staff->total() }}
                                total</span>
                            <button type="button" class="btn btn-dark rounded-pill" onclick="openCreateModal()">
                                <i class="bi bi-plus-circle me-1"></i> Add Staff
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="staffTableBody">
                                @include('dashboard.partials.staff-rows', compact('staff'))
                            </tbody>
                        </table>
                    </div>
                    <div id="staffPagination" class="@if(!$staff->hasPages()) d-none @endif">
                        @if($staff->hasPages())
                            {{ $staff->links('vendor.pagination.bootstrap-5') }}
                        @endif
                    </div>
                </section>
            @endif
