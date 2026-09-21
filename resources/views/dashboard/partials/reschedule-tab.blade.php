{{-- Reschedule requests tab content (loaded via AJAX) --}}
                <section class="surface-card">
                    <div class="section-head d-flex justify-content-between align-items-center">
                        <h2 class="section-title">Reschedule Requests</h2>
                        <span class="badge bg-light text-dark border">{{ $rescheduleRequests->count() }} total</span>
                    </div>
                    <div class="table-responsive d-none d-md-block">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Booking Ref</th>
                                    <th>Client</th>
                                    <th>Original Date</th>
                                    <th>Requested Date</th>
                                    <th>Requested Time</th>
                                    <th>Current Team</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rescheduleRequests->filter(fn($rr) => $rr->booking) as $rr)
                                    <tr>
                                        <td><code>{{ $rr->booking->booking_ref }}</code></td>
                                        <td>
                                            <div>{{ $rr->booking->client_name }}</div>
                                            <small class="text-muted">{{ $rr->booking->client_email }}</small>
                                        </td>
                                        <td>{{ $rr->booking->event_date->format('M d, Y') }}</td>
                                        <td>{{ $rr->requested_date->format('M d, Y') }}</td>
                                        <td>{{ date('g:i A', strtotime($rr->requested_time)) }}</td>
                                        <td>
                                            @if($rr->booking->team)
                                                <span class="badge bg-light text-dark border">{{ $rr->booking->team->name }}</span>
                                            @else
                                                <span class="text-muted fst-italic">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @include('partials.status-badge', ['status' => $rr->status])
                                            @if($rr->status === 'approved' && $rr->newTeam)
                                                <div><small class="text-muted">Team: {{ $rr->newTeam->name }}</small></div>
                                            @elseif($rr->status === 'rejected' && $rr->rejection_reason)
                                                <div><small class="text-muted">{{ $rr->rejection_reason }}</small></div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($rr->status === 'pending')
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-outline-success rounded-3"
                                                        onclick="openRescheduleApproveModal(
                                                                        '{{ $rr->id }}',
                                                                        '{{ $rr->booking->booking_ref }}',
                                                                        '{{ $rr->booking->client_name }}',
                                                                        '{{ $rr->requested_date->format('Y-m-d') }}',
                                                                        '{{ $rr->requested_time }}'
                                                                    )">
                                                        <i class="bi bi-check-lg me-1"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                                                        onclick="openRescheduleRejectModal('{{ $rr->id }}', '{{ $rr->booking->booking_ref }}')">
                                                        <i class="bi bi-x-lg me-1"></i> Reject
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic small">Processed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            No reschedule requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-md-none">
                        @forelse($rescheduleRequests->filter(fn($rr) => $rr->booking) as $rr)
                            <div class="mobile-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-semibold">{{ $rr->booking->client_name }}</div>
                                        <small class="text-muted"><code>{{ $rr->booking->booking_ref }}</code></small>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        @include('partials.status-badge', ['status' => $rr->status])
                                    </div>
                                </div>
                                <div class="row g-3 mb-2">
                                    <div class="col-6">
                                        <div class="mobile-card-label">Original Date</div>
                                        <div class="mobile-card-value">{{ $rr->booking->event_date->format('M d, Y') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mobile-card-label">Requested Date</div>
                                        <div class="mobile-card-value">{{ $rr->requested_date->format('M d, Y') }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mobile-card-label">Requested Time</div>
                                        <div class="mobile-card-value">{{ date('g:i A', strtotime($rr->requested_time)) }}</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mobile-card-label">Current Team</div>
                                        <div class="mobile-card-value">
                                            @if($rr->booking->team)
                                                {{ $rr->booking->team->name }}
                                            @else
                                                <span class="text-muted fst-italic">—</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($rr->status === 'approved' && $rr->newTeam)
                                    <div class="small text-muted mb-1">New Team: {{ $rr->newTeam->name }}</div>
                                @elseif($rr->status === 'rejected' && $rr->rejection_reason)
                                    <div class="small text-muted mb-1">Reason: {{ $rr->rejection_reason }}</div>
                                @endif
                                @if($rr->status === 'pending')
                                    <div class="row g-2 border-top pt-2">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-3 w-100"
                                                onclick="openRescheduleApproveModal(
                                                                            '{{ $rr->id }}',
                                                                            '{{ $rr->booking->booking_ref }}',
                                                                            '{{ $rr->booking->client_name }}',
                                                                            '{{ $rr->requested_date->format('Y-m-d') }}',
                                                                            '{{ $rr->requested_time }}'
                                                                        )">
                                                <i class="bi bi-check-lg me-1"></i> Approve
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3 w-100"
                                                onclick="openRescheduleRejectModal('{{ $rr->id }}', '{{ $rr->booking->booking_ref }}')">
                                                <i class="bi bi-x-lg me-1"></i> Reject
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No reschedule requests found.
                            </div>
                        @endforelse
                    </div>
                </section>
