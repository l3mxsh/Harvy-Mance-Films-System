<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Bookings Management</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- TABS --}}
            <ul class="nav nav-tabs mb-4" id="bookingTabs">
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') !== 'reschedule' ? 'active' : '' }}" href="{{ route('booking.admin.index') }}">All Bookings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') === 'reschedule' ? 'active' : '' }}" href="{{ route('booking.admin.index', ['tab' => 'reschedule']) }}">
                        Reschedule Requests
                        @if($pendingRescheduleCount > 0)
                            <span class="badge bg-danger ms-1">{{ $pendingRescheduleCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            @if(request('tab') === 'reschedule')
                {{-- RESCHEDULE REQUESTS TAB --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-event me-2"></i>Reschedule Requests</h6>
                        <span class="badge bg-secondary">{{ $rescheduleRequests->count() }} total</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Booking Ref</th>
                                        <th>Client</th>
                                        <th>Original Date</th>
                                        <th>Requested Date</th>
                                        <th>Requested Time</th>
                                        <th>Current Team</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
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
                                                    <span class="badge bg-primary">{{ $rr->booking->team->name }}</span>
                                                @else
                                                    <span class="text-muted fst-italic">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($rr->status === 'pending')
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                                @elseif($rr->status === 'approved')
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Approved</span>
                                                    @if($rr->newTeam)
                                                        <div><small class="text-muted">Team: {{ $rr->newTeam->name }}</small></div>
                                                    @endif
                                                @elseif($rr->status === 'rejected')
                                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                                    @if($rr->rejection_reason)
                                                        <div><small class="text-muted">{{ $rr->rejection_reason }}</small></div>
                                                    @endif
                                                @endif
                                            </td>
                                            <td>{{ $rr->created_at->format('M d, Y g:i A') }}</td>
                                            <td class="text-center">
                                                @if($rr->status === 'pending')
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-success btn-sm"
                                                            onclick="openRescheduleApproveModal(
                                                                '{{ $rr->id }}',
                                                                '{{ $rr->booking->booking_ref }}',
                                                                '{{ $rr->booking->client_name }}',
                                                                '{{ $rr->requested_date->format('Y-m-d') }}',
                                                                '{{ $rr->requested_time }}'
                                                            )">
                                                            <i class="bi bi-check-lg"></i> Approve
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                            onclick="openRescheduleRejectModal('{{ $rr->id }}', '{{ $rr->booking->booking_ref }}')">
                                                            <i class="bi bi-x-lg"></i> Reject
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted fst-italic small">Processed</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                No reschedule requests found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                {{-- ALL BOOKINGS TAB --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-journal-check me-2"></i>All Bookings</h6>
                        <span class="badge bg-secondary">{{ $bookings->total() }} total</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Ref</th>
                                        <th>Client</th>
                                        <th>Package</th>
                                        <th>Event Date</th>
                                        <th>Total</th>
                                        <th>Team</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Submitted</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($bookings as $booking)
                                        <tr>
                                            <td><code>{{ $booking->booking_ref }}</code></td>
                                            <td>
                                                <div>{{ $booking->client_name }}</div>
                                                <small class="text-muted">{{ $booking->client_email }}</small>
                                            </td>
                                            <td>{{ $booking->package->name ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</td>
                                            <td>₱{{ number_format($booking->total_price, 2) }}</td>
                                            <td>
                                                @if($booking->team)
                                                    <span class="badge bg-primary">{{ $booking->team->name }}</span>
                                                @else
                                                    <span class="text-muted fst-italic">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $badgeClass = match($booking->status) {
                                                        'pending' => 'bg-warning text-dark',
                                                        'approved' => 'bg-success',
                                                        'ongoing' => 'bg-primary',
                                                        'completed' => 'bg-secondary',
                                                        'rejected' => 'bg-danger',
                                                        default => 'bg-secondary',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                                                @if($booking->status === 'rejected' && $booking->rejection_reason)
                                                    <button class="btn btn-link btn-sm p-0 text-danger text-decoration-none" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $booking->rejection_reason }}">
                                                        <i class="bi bi-info-circle"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                @php $dp = $booking->latestDownpayment; @endphp
                                                @if($dp)
                                                    @if($dp->status === 'pending')
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Submitted</span>
                                                    @elseif($dp->status === 'verified')
                                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Verified</span>
                                                    @elseif($dp->status === 'rejected')
                                                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                                    @endif
                                                @elseif($booking->status === 'approved')
                                                    <span class="badge bg-info text-white"><i class="bi bi-clock me-1"></i>Awaiting</span>
                                                @else
                                                    <span class="text-muted fst-italic">—</span>
                                                @endif
                                            </td>
                                            <td>{{ $booking->created_at->format('M d, Y g:i A') }}</td>
                                            <td class="text-center">
                                                @if($booking->status === 'pending')
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-success btn-sm" title="Approve Booking"
                                                            onclick="openApproveModal('{{ $booking->id }}', '{{ $booking->booking_ref }}', '{{ $booking->client_name }}', '{{ $booking->event_date->format('Y-m-d') }}')">
                                                            <i class="bi bi-check-lg"></i> Approve
                                                        </button>
                                                        <button type="button" class="btn btn-outline-danger btn-sm" title="Reject Booking"
                                                            onclick="openRejectModal('{{ $booking->id }}', '{{ $booking->booking_ref }}', '{{ $booking->client_name }}')">
                                                            <i class="bi bi-x-lg"></i> Reject
                                                        </button>
                                                    </div>
                                                @elseif($booking->status === 'rejected')
                                                    <small class="text-danger text-muted fst-italic">Rejected</small>
                                                @elseif($booking->status === 'approved')
                                                    <small class="text-success text-muted fst-italic">Approved</small>
                                                @elseif($booking->status === 'ongoing')
                                                    <form action="{{ route('booking.complete', $booking->id) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Mark booking {{ $booking->booking_ref }} as completed? This will start the post-production phase.')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            <i class="bi bi-check-lg"></i> Complete
                                                        </button>
                                                    </form>
                                                @elseif($booking->status === 'completed')
                                                    @if($booking->postProduction)
                                                        <a href="{{ route('post-production.show', $booking->postProduction->id) }}" class="btn btn-sm btn-outline-dark">
                                                            <i class="bi bi-film me-1"></i>View Post-Production
                                                        </a>
                                                    @else
                                                        <a href="{{ route('post-production.create', $booking->id) }}" class="btn btn-sm btn-primary">
                                                            <i class="bi bi-film me-1"></i>Proceed to Post-Production
                                                        </a>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                                No bookings found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($bookings->hasPages())
                        <div class="card-footer bg-white border-top">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- APPROVE BOOKING MODAL --}}
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Approve Booking</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="approveForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-1">Approving booking <strong id="approveBookingRef"></strong> for <strong id="approveClientName"></strong>.</p>
                        <p class="text-muted small mb-3">Event date: <strong id="approveEventDate"></strong></p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Team <span class="text-danger">*</span></label>
                            <select name="team_id" id="approveTeamSelect" class="form-select" required onchange="checkTeamAvailability()">
                                <option value="">-- Select an available team --</option>
                                @foreach($availableTeams as $team)
                                    <option value="{{ $team->id }}" data-member-names="{{ $team->members->pluck('name')->implode(', ') }}">
                                        {{ $team->name }} ({{ $team->members->count() }} members)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="teamMembersPreview" class="mb-3" style="display:none;">
                            <label class="form-label small text-muted">Team Members</label>
                            <div id="teamMembersList" class="border rounded p-2" style="font-size: 0.85rem;"></div>
                        </div>

                        <div id="availabilityResult" style="display:none;"></div>

                        @if($availableTeams->isEmpty())
                            <div class="alert alert-warning py-2 mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                No active teams available. <a href="{{ route('team.index') }}">Create a team first</a>.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="approveBtn" {{ $availableTeams->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-check-lg me-1"></i> Approve & Assign Team
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REJECT BOOKING MODAL --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">
                        <i class="bi bi-x-circle me-2"></i>Reject Booking
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" id="rejectForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">You are about to reject booking <strong id="rejectBookingRef"></strong> submitted by <strong id="rejectClientName"></strong>.</p>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4"
                                placeholder="Please provide a reason why this booking is being rejected..."
                                required></textarea>
                            <div class="form-text">This reason will be visible in the bookings table.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-lg me-1"></i> Reject Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- RESCHEDULE APPROVE MODAL --}}
    <div class="modal fade" id="rescheduleApproveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="bi bi-calendar-check me-2"></i>Approve Reschedule</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="rescheduleApproveForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-1">Approving reschedule for booking <strong id="rrApproveRef"></strong> — <strong id="rrApproveClient"></strong>.</p>
                        <p class="text-muted small mb-3">New date: <strong id="rrApproveDate"></strong> at <strong id="rrApproveTime"></strong></p>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Assign Team <span class="text-danger">*</span></label>
                            <select name="team_id" id="rrApproveTeamSelect" class="form-select" required onchange="checkRescheduleTeamAvailability()">
                                <option value="">-- Select a team --</option>
                                @foreach($availableTeams as $team)
                                    <option value="{{ $team->id }}" data-member-names="{{ $team->members->pluck('name')->implode(', ') }}">
                                        {{ $team->name }} ({{ $team->members->count() }} members)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div id="rrTeamMembersPreview" class="mb-3" style="display:none;">
                            <label class="form-label small text-muted">Team Members</label>
                            <div id="rrTeamMembersList" class="border rounded p-2" style="font-size:0.85rem;"></div>
                        </div>
                        <div id="rrAvailabilityResult" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success" id="rrApproveBtn">
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
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Reject Reschedule</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="rescheduleRejectForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">Rejecting reschedule request for booking <strong id="rrRejectRef"></strong>.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="rejection_reason" rows="3" required
                                placeholder="Provide a reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-x-lg me-1"></i>Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });

        var currentEventDate = '';
        var rrCurrentDate = '';

        function openRescheduleApproveModal(rrId, bookingRef, clientName, newDate, newTime) {
            document.getElementById('rescheduleApproveForm').action = '/admin/reschedule/' + rrId + '/approve';
            document.getElementById('rrApproveRef').textContent = bookingRef;
            document.getElementById('rrApproveClient').textContent = clientName;
            document.getElementById('rrApproveDate').textContent = newDate;
            document.getElementById('rrApproveTime').textContent = newTime;
            document.getElementById('rrApproveTeamSelect').value = '';
            document.getElementById('rrTeamMembersPreview').style.display = 'none';
            document.getElementById('rrAvailabilityResult').style.display = 'none';
            rrCurrentDate = newDate;
            new bootstrap.Modal(document.getElementById('rescheduleApproveModal')).show();
        }

        function checkRescheduleTeamAvailability() {
            var select = document.getElementById('rrApproveTeamSelect');
            var teamId = select.value;
            var membersPreview = document.getElementById('rrTeamMembersPreview');
            var membersList = document.getElementById('rrTeamMembersList');
            var resultDiv = document.getElementById('rrAvailabilityResult');

            if (!teamId) { membersPreview.style.display = 'none'; resultDiv.style.display = 'none'; return; }

            var memberNames = select.options[select.selectedIndex].getAttribute('data-member-names');
            membersList.innerHTML = memberNames.split(', ').map(function(n) {
                return '<span class="badge bg-light text-dark border me-1 mb-1">' + n + '</span>';
            }).join('');
            membersPreview.style.display = 'block';

            fetch('/api/staff-schedule/check-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ team_id: teamId, event_date: rrCurrentDate })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                resultDiv.style.display = 'block';
                if (data.available) {
                    resultDiv.innerHTML = '<div class="alert alert-success py-2 mb-0"><i class="bi bi-check-circle me-1"></i>' + data.message + '</div>';
                    document.getElementById('rrApproveBtn').disabled = false;
                } else {
                    var names = data.unavailable_members.map(function(m) { return m.name + ' (has booking ' + m.booking_ref + ')'; }).join(', ');
                    resultDiv.innerHTML = '<div class="alert alert-danger py-2 mb-0"><i class="bi bi-exclamation-triangle me-1"></i><strong>Conflict:</strong> ' + names + '</div>';
                    document.getElementById('rrApproveBtn').disabled = true;
                }
            })
            .catch(function() {
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = '<div class="alert alert-warning py-2 mb-0"><i class="bi bi-info-circle me-1"></i>Could not check availability.</div>';
                document.getElementById('rrApproveBtn').disabled = false;
            });
        }

        function openRescheduleRejectModal(rrId, bookingRef) {
            document.getElementById('rescheduleRejectForm').action = '/admin/reschedule/' + rrId + '/reject';
            document.getElementById('rrRejectRef').textContent = bookingRef;
            document.querySelector('#rescheduleRejectForm textarea').value = '';
            new bootstrap.Modal(document.getElementById('rescheduleRejectModal')).show();
        }

        function openApproveModal(bookingId, bookingRef, clientName, eventDate) {
            document.getElementById('approveForm').action = '/admin/booking/' + bookingId + '/approve';
            document.getElementById('approveBookingRef').textContent = bookingRef;
            document.getElementById('approveClientName').textContent = clientName;
            document.getElementById('approveEventDate').textContent = eventDate;
            document.getElementById('approveTeamSelect').value = '';
            document.getElementById('teamMembersPreview').style.display = 'none';
            document.getElementById('availabilityResult').style.display = 'none';
            currentEventDate = eventDate;
            new bootstrap.Modal(document.getElementById('approveModal')).show();
        }

        function checkTeamAvailability() {
            var select = document.getElementById('approveTeamSelect');
            var teamId = select.value;
            var membersPreview = document.getElementById('teamMembersPreview');
            var membersList = document.getElementById('teamMembersList');
            var resultDiv = document.getElementById('availabilityResult');

            if (!teamId) {
                membersPreview.style.display = 'none';
                resultDiv.style.display = 'none';
                return;
            }

            var selectedOption = select.options[select.selectedIndex];
            var memberNames = selectedOption.getAttribute('data-member-names');
            membersList.innerHTML = memberNames.split(', ').map(function(name) {
                return '<span class="badge bg-light text-dark border me-1 mb-1">' + name + '</span>';
            }).join('');
            membersPreview.style.display = 'block';

            fetch('/api/staff-schedule/check-availability', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ team_id: teamId, event_date: currentEventDate })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                resultDiv.style.display = 'block';
                if (data.available) {
                    resultDiv.innerHTML = '<div class="alert alert-success py-2 mb-0"><i class="bi bi-check-circle me-1"></i> ' + data.message + '</div>';
                    document.getElementById('approveBtn').disabled = false;
                } else {
                    var names = data.unavailable_members.map(function(m) { return m.name + ' (has booking ' + m.booking_ref + ')'; }).join(', ');
                    resultDiv.innerHTML = '<div class="alert alert-danger py-2 mb-0"><i class="bi bi-exclamation-triangle me-1"></i> <strong>Conflict:</strong> ' + names + '</div>';
                    document.getElementById('approveBtn').disabled = true;
                }
            })
            .catch(function() {
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = '<div class="alert alert-warning py-2 mb-0"><i class="bi bi-info-circle me-1"></i> Could not check availability. Proceed with caution.</div>';
                document.getElementById('approveBtn').disabled = false;
            });
        }

        function openRejectModal(bookingId, bookingRef, clientName) {
            document.getElementById('rejectForm').action = '/admin/booking/' + bookingId + '/reject';
            document.getElementById('rejectBookingRef').textContent = bookingRef;
            document.getElementById('rejectClientName').textContent = clientName;
            document.getElementById('rejection_reason').value = '';
            new bootstrap.Modal(document.getElementById('rejectModal')).show();
        }
    </script>
</body>

</html>
