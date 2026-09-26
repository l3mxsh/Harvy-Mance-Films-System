{{-- Outsourced staff tab content (AJAX) --}}
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
            @if($tab === 'outsourced')
                <section class="surface-card mb-4">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-9 col-lg-10">
                            <input type="text" id="staffSearchInput" class="form-control"
                                placeholder="Search outsourced by name, email, contact..." value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <div class="col-12 col-md-3 col-lg-2">
                            <button type="button" class="btn btn-outline-dark rounded-pill w-100"
                                onclick="openCreateOutsourcedModal()">
                                <i class="bi bi-plus-circle me-1"></i> Add Outsourced
                            </button>
                        </div>
                    </div>
                </section>

                <section class="surface-card">
                    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="section-title">
                            Outsourced Staff
                            <span class="badge bg-warning text-dark ms-1">Record Only</span>
                        </h2>
                        <span class="badge bg-light text-dark border" id="staffTotalBadge">{{ $outsourcedStaff->total() }} total</span>
                    </div>
                    {{-- Desktop table (hidden on mobile) --}}
                    <div class="table-responsive d-none d-md-block">
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
                            <tbody id="staffTableBody">
                                @include('dashboard.partials.outsourced-rows', compact('outsourcedStaff'))
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards (hidden on desktop) --}}
                    <div class="d-md-none" id="staffMobileBody">
                        @include('dashboard.partials.outsourced-mobile-rows', compact('outsourcedStaff'))
                    </div>
                    <div id="staffPagination" class="@if(!$outsourcedStaff->hasPages()) d-none @endif">
                        @if($outsourcedStaff->hasPages())
                            {{ $outsourcedStaff->links('vendor.pagination.bootstrap-5') }}
                        @endif
                    </div>
                </section>
            @endif
