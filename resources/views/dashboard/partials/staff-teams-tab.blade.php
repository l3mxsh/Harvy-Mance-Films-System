{{-- Teams tab content (AJAX) --}}
                <div class="row g-3 mb-4">
                    <div class="col-lg-6 col-md-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Total Teams</h6>
                                    <h4 class="mb-0 fw-bold">{{ $totalTeams }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="summary-card">
                            <div class="d-flex align-items-center">
                                <div class="summary-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="text-muted mb-1 small">Active Teams</h6>
                                    <h4 class="mb-0 fw-bold">{{ $activeTeams }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @if($tab === 'teams')
                <section class="surface-card mb-4">
                    <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h2 class="section-title">All Teams</h2>
                        <span class="badge bg-light text-dark border" id="teamTotalBadge">{{ $teams->total() }} total</span>
                    </div>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label small text-muted mb-1">Search</label>
                            <input type="text" id="teamSearchInput" class="form-control" placeholder="Search teams..."
                                value="{{ request('search') }}" autocomplete="off">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted mb-1">Status</label>
                            <select id="teamStatusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-1">
                            <button type="button" class="btn btn-dark w-100 rounded-3" onclick="openCreateTeamModal()">Add
                                Team
                            </button>
                        </div>
                    </div>
                </section>

                <section class="surface-card">
                    {{-- Desktop table (hidden on mobile) --}}
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Team Name</th>
                                    <th>Description</th>
                                    <th>Members</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="teamTableBody">
                                @include('dashboard.partials.team-rows', compact('teams'))
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile cards (hidden on desktop) --}}
                    <div class="d-md-none" id="teamMobileBody">
                        @include('dashboard.partials.team-mobile-rows', compact('teams'))
                    </div>
                    <div id="teamPagination" class="@if(!$teams->hasPages()) d-none @endif">
                        @if($teams->hasPages())
                            {{ $teams->links('vendor.pagination.bootstrap-5') }}
                        @endif
                    </div>
                </section>
            @endif
