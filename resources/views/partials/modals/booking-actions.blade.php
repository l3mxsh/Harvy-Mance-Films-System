{{-- APPROVE BOOKING MODAL --}}
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2 text-success"></i>Approve Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="approveForm">
                @csrf
                <div class="modal-body">
                    <p class="mb-1">Approving booking <strong id="approveBookingRef"></strong> for <strong
                            id="approveClientName"></strong>.</p>
                    <p class="text-muted small mb-3">Event date: <strong id="approveEventDate"></strong></p>

                    <div class="mb-3">
                        <label class="form-label">Select Team <span class="text-danger">*</span></label>
                        <select name="team_id" id="approveTeamSelect" class="form-select" required
                            onchange="checkTeamAvailability()">
                            <option value="">-- Select an available team --</option>
                            @foreach($availableTeams as $team)
                                @php
                                    $allNames = $team->members->pluck('name')
                                        ->merge($team->outsourcedMembers->map(fn($os) => $os->name . ' (OS)'))
                                        ->implode(', ');
                                    $totalCount = $team->members->count() + $team->outsourcedMembers->count();
                                @endphp
                                <option value="{{ $team->id }}" data-member-names="{{ $allNames }}">
                                    {{ $team->name }} ({{ $totalCount }} members)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="teamMembersPreview" class="mb-3" style="display:none;">
                        <label class="form-label small text-muted">Team Members</label>
                        <div id="teamMembersList" class="rounded p-2" style="font-size: 0.85rem;"></div>
                    </div>

                    <div id="availabilityResult" style="display:none;"></div>

                    @if($availableTeams->isEmpty())
                        <div class="alert alert-soft alert-soft-warning py-2 mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            No active teams available. <a href="{{ route('staff.admin.index', ['tab' => 'teams']) }}">Create a team first</a>.
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill" id="approveBtn" {{ $availableTeams->isEmpty() ? 'disabled' : '' }}>
                        <i class="bi bi-check-lg me-1"></i> Approve & Assign Team
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- REJECT BOOKING MODAL --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2 text-danger"></i>Reject Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">You are about to reject booking <strong id="rejectBookingRef"></strong>
                        submitted by <strong id="rejectClientName"></strong>.</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Rejection <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4"
                            placeholder="Please provide a reason why this booking is being rejected..."
                            required></textarea>
                        <div class="form-text">This reason will be visible in the bookings table.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" id="rejectBtn" class="btn btn-danger rounded-pill">
                        Reject Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- COMPLETE BOOKING MODAL --}}
<div class="modal fade" id="completeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-check2-circle me-2 text-success"></i>Complete Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="completeForm">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Mark booking <strong id="completeBookingRef"></strong> for <strong
                            id="completeClientName"></strong> as completed?</p>
                    <div class="complete-hint">
                        <span class="complete-hint-icon"><i class="bi bi-info-circle"></i></span>
                        <span class="complete-hint-text">Post-production tasks will be created for this booking.</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success rounded-pill" id="completeBtn">
                        <i class="bi bi-check-lg me-1"></i> Complete Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- RESCHEDULE APPROVE MODAL --}}
<div class="modal fade" id="rescheduleApproveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-check me-2 text-success"></i>Approve Reschedule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rescheduleApproveForm">
                @csrf
                <div class="modal-body">
                    <p class="mb-1">Approving reschedule for booking <strong id="rrApproveRef"></strong> — <strong
                            id="rrApproveClient"></strong>.</p>
                    <p class="text-muted small mb-3">New date: <strong id="rrApproveDate"></strong> at <strong
                            id="rrApproveTime"></strong></p>

                    <div class="mb-3">
                        <label class="form-label">Assign Team <span class="text-danger">*</span></label>
                        <select name="team_id" id="rrApproveTeamSelect" class="form-select" required
                            onchange="checkRescheduleTeamAvailability()">
                            <option value="">-- Select a team --</option>
                            @foreach($availableTeams as $team)
                                @php
                                    $allNames = $team->members->pluck('name')
                                        ->merge($team->outsourcedMembers->map(fn($os) => $os->name . ' (OS)'))
                                        ->implode(', ');
                                    $totalCount = $team->members->count() + $team->outsourcedMembers->count();
                                @endphp
                                <option value="{{ $team->id }}" data-member-names="{{ $allNames }}">
                                    {{ $team->name }} ({{ $totalCount }} members)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div id="rrTeamMembersPreview" class="mb-3" style="display:none;">
                        <label class="form-label small text-muted">Team Members</label>
                        <div id="rrTeamMembersList" class="rounded p-2" style="font-size:0.85rem;"></div>
                    </div>
                    <div id="rrAvailabilityResult" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill" id="rrApproveBtn">
                        <i class="bi bi-check-lg me-1"></i>Approve & Assign Team
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- RESCHEDULE REJECT MODAL --}}
<div class="modal fade" id="rescheduleRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2 text-danger"></i>Reject Reschedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rescheduleRejectForm">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">Rejecting reschedule request for booking <strong id="rrRejectRef"></strong>.</p>
                    <div class="mb-3">
                        <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="rejection_reason" rows="3" required
                            placeholder="Provide a reason..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill"><i class="bi bi-x-lg me-1"></i>Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
