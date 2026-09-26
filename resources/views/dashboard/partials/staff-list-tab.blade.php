{{-- All / In-House staff tab content (AJAX) --}}
                <div class="row g-3 mb-4">
                    <div class="col-lg-3 col-md-4 col-12">
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
                    <div class="col-lg-3 col-md-4 col-12">
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
                    <div class="col-lg-3 col-md-4 col-12">
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
                    <div class="col-lg-3 col-md-4 col-12">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-person-badge"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Outsourced</h6>
                                    <h4 class="mb-0 fw-bold">{{ $totalOutsourced }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @if(!in_array($tab, ['outsourced', 'teams']))
                <section class="surface-card mb-4">
                    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="section-title">
                            {{ $tab === 'in-house' ? 'In-House Staff' : 'All Staff' }}
                        </h2>
                        <span class="badge bg-light text-dark border" id="staffTotalBadge">{{ $staff->total() }} total</span>
                    </div>
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted mb-1">Search</label>
                            <input type="text" id="staffSearchInput" class="form-control"
                                placeholder="Search by name or email..." value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small text-muted mb-1">Status</label>
                            <select id="staffStatusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-12 col-md-2 mb-1">
                            <button type="button" class="btn btn-dark w-100 rounded-3" onclick="openCreateModal()">Add
                                Staff
                            </button>
                        </div>
                    </div>
                </section>
            @endif
            @if(!in_array($tab, ['outsourced', 'teams']))
                <section class="surface-card">
                    {{-- Desktop table (hidden on mobile) --}}
                    <div class="table-responsive d-none d-md-block">
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

                    {{-- Mobile cards (hidden on desktop) --}}
                    <div class="d-md-none" id="staffMobileBody">
                        @include('dashboard.partials.staff-mobile-rows', compact('staff'))
                    </div>
                    <div id="staffPagination" class="@if(!$staff->hasPages()) d-none @endif">
                        @if($staff->hasPages())
                            {{ $staff->links('vendor.pagination.bootstrap-5') }}
                        @endif
                    </div>
                </section>
            @endif
