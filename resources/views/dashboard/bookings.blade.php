<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Bookings Management</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            {{-- TABS --}}
            <ul class="nav nav-pills mb-4" id="bookingTabs">
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') !== 'reschedule' ? 'active' : '' }}"
                        href="{{ route('booking.admin.index') }}">
                        <i class="bi bi-journal-check me-1"></i> All Bookings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('tab') === 'reschedule' ? 'active' : '' }}"
                        href="{{ route('booking.admin.index', ['tab' => 'reschedule']) }}">
                        <i class="bi bi-calendar-event me-1"></i> Reschedule Requests
                        @if($pendingRescheduleCount > 0)
                            <span class="badge bg-danger ms-1">{{ $pendingRescheduleCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            @if(request('tab') === 'reschedule')
                {{-- RESCHEDULE REQUESTS TAB --}}
                <section class="surface-card">
                    <div class="section-head d-flex justify-content-between align-items-center">
                        <h2 class="section-title"><i class="bi bi-calendar-event me-2"></i>Reschedule Requests</h2>
                        <span class="badge bg-light text-dark border">{{ $rescheduleRequests->count() }} total</span>
                    </div>
                    <div class="table-responsive">
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
                                            @if($rr->status === 'pending')
                                                <span class="badge bg-warning text-dark"><i
                                                        class="bi bi-hourglass-split me-1"></i>Pending</span>
                                            @elseif($rr->status === 'approved')
                                                <span class="badge bg-success"><i
                                                        class="bi bi-check-circle me-1"></i>Approved</span>
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
                                        <td class="text-center">
                                            @if($rr->status === 'pending')
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill"
                                                        onclick="openRescheduleApproveModal(
                                                                        '{{ $rr->id }}',
                                                                        '{{ $rr->booking->booking_ref }}',
                                                                        '{{ $rr->booking->client_name }}',
                                                                        '{{ $rr->requested_date->format('Y-m-d') }}',
                                                                        '{{ $rr->requested_time }}'
                                                                    )">
                                                        <i class="bi bi-check-lg me-1"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill"
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
                </section>
            @else
                {{-- ALL BOOKINGS TAB --}}
                <section class="surface-card">
                    <div class="section-head d-flex justify-content-between align-items-center">
                        <h2 class="section-title"><i class="bi bi-journal-check me-2"></i>All Bookings</h2>
                        <span class="badge bg-light text-dark border">{{ $bookings->total() }} total</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Ref</th>
                                    <th>Client</th>
                                    <th>Event Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
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
                                        <td>{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</td>
                                        <td>&#8369;{{ number_format($booking->total_price, 2) }}</td>
                                        <td>
                                            @php
        $badgeClass = match ($booking->status) {
            'pending' => 'bg-warning text-dark',
            'approved' => 'bg-success',
            'ongoing' => 'bg-primary',
            'completed' => 'bg-secondary',
            'rejected' => 'bg-danger',
            default => 'bg-secondary',
        };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}" id="status-badge-{{ $booking->id }}">{{ ucfirst($booking->status) }}</span>
                                            @if($booking->status === 'rejected' && $booking->rejection_reason)
                                                <button class="btn btn-link btn-sm p-0 text-danger text-decoration-none"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ $booking->rejection_reason }}">
                                                    <i class="bi bi-info-circle"></i>
                                                </button>
                                            @endif
                                        </td>
                                        <td id="payment-cell-{{ $booking->id }}">
                                            @php $dp = $booking->latestDownpayment; @endphp
                                            @if($dp)
                                                @if($dp->status === 'pending')
                                                    <span class="badge bg-warning text-dark"><i
                                                            class="bi bi-hourglass-split me-1"></i>Submitted</span>
                                                @elseif($dp->status === 'verified')
                                                    <span class="badge bg-success"><i
                                                            class="bi bi-check-circle me-1"></i>Verified</span>
                                                @elseif($dp->status === 'rejected')
                                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                                @endif
                                            @elseif($booking->status === 'approved')
                                                <span class="badge bg-info text-white"><i
                                                        class="bi bi-clock me-1"></i>Awaiting</span>
                                            @else
                                                <span class="text-muted fst-italic">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center" id="actions-cell-{{ $booking->id }}">
                                            @php
        $viewPayload = [
            'booking_ref' => $booking->booking_ref,
            'status' => $booking->status,
            'client_name' => $booking->client_name,
            'client_email' => $booking->client_email,
            'client_phone' => $booking->client_phone,
            'package_name' => $booking->package->name ?? null,
            'package_price' => (float) ($booking->package->price ?? 0),
            'services' => $booking->package ? $booking->package->services->pluck('service_name')->toArray() : [],
            'addons' => $booking->addons->map(fn($a) => ['name' => $a->name, 'price' => (float) $a->pivot->price])->values()->toArray(),
            'addons_total' => (float) $booking->addons->sum('pivot.price'),
            'event_type' => $booking->event_type,
            'event_date' => \Carbon\Carbon::parse($booking->event_date)->format('F d, Y'),
            'event_time' => $booking->event_time ? date('g:i A', strtotime($booking->event_time)) : null,
            'event_venue' => $booking->event_venue,
            'event_address' => $booking->event_address,
            'event_description' => $booking->event_description,
            'total_price' => (float) $booking->total_price,
            'downpayment' => (float) $booking->downpayment_amount,
            'balance' => (float) ($booking->total_price - $booking->downpayment_amount),
            'team_name' => $booking->team->name ?? null,
            'payment_status' => $booking->latestDownpayment->status ?? null,
            'notes' => $booking->notes,
            'created_at' => $booking->created_at->format('M d, Y g:i A'),
        ];
                                            @endphp
                                            <div class="d-flex gap-2 justify-content-center flex-wrap">

                                                @if($booking->status === 'pending')
                                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill"
                                                        title="Approve Booking"
                                                        onclick="openApproveModal('{{ $booking->id }}', '{{ $booking->booking_ref }}', '{{ $booking->client_name }}', '{{ $booking->event_date->format('Y-m-d') }}')">
                                                        <i class="bi bi-check-lg me-1"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill"
                                                        title="Reject Booking"
                                                        onclick="openRejectModal('{{ $booking->id }}', '{{ $booking->booking_ref }}', '{{ $booking->client_name }}')">
                                                        <i class="bi bi-x-lg me-1"></i> Reject
                                                    </button>
                                                @elseif($booking->status === 'rejected')
                                                    <small
                                                        class="text-danger text-muted fst-italic align-self-center">Rejected</small>
                                                @elseif($booking->status === 'approved')
                                                    <small
                                                        class="text-success text-muted fst-italic align-self-center">Approved</small>
                                                @elseif($booking->status === 'ongoing')
                                                    <form action="{{ route('booking.complete', $booking->id) }}" method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('Mark booking {{ $booking->booking_ref }} as completed? This will start the post-production phase.')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            <i class="bi bi-check-lg"></i> Complete
                                                        </button>
                                                    </form>
                                                @elseif($booking->status === 'completed')
                                                    @if($booking->postProduction)
                                                        <a href="{{ route('post-production.show', $booking->postProduction->id) }}"
                                                            class="btn btn-sm btn-outline-dark">
                                                            <i class="bi bi-film me-1"></i>View Post-Production
                                                        </a>
                                                    @else
                                                        <a href="{{ route('post-production.create', $booking->id) }}"
                                                            class="btn btn-sm btn-primary">
                                                            <i class="bi bi-film me-1"></i>Proceed to Post-Production
                                                        </a>
                                                    @endif
                                                @endif
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
                                                    title="View Details" onclick='window.viewPayloads = window.viewPayloads || {}; window.viewPayloads[{{ $booking->id }}] = @json($viewPayload); openViewModal(window.viewPayloads[{{ $booking->id }}])'>
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            No bookings found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($bookings->hasPages())
                        <div class="border-top pt-3 mt-3">
                            {{ $bookings->links() }}
                        </div>
                    @endif
                </section>
            @endif
        </div>
    </div>

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
                                No active teams available. <a href="{{ route('team.index') }}">Create a team first</a>.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-dark" id="approveBtn" {{ $availableTeams->isEmpty() ? 'disabled' : '' }}>
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
                        <button type="button" class="btn btn-sm border-secondary rounded-pill p-2" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill p-2">
                            Reject Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- APPROVE RESULT MODAL --}}
    <div class="modal fade" id="approveResultModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div id="approveResultIcon" class="fs-1 mb-2"></div>
                    <p class="mb-0" id="approveResultMsg"></p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                    <button type="button" class="btn btn-primary-dark rounded-pill px-4" data-bs-dismiss="modal">OK</button>
                </div>
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
                        <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary-dark" id="rrApproveBtn">
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
                        <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"><i class="bi bi-x-lg me-1"></i>Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- VIEW BOOKING MODAL --}}
    <div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Booking Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div
                        class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 pb-3 border-bottom">
                        <div>
                            <div class="text-muted small">Booking Reference</div>
                            <div class="fw-bold fs-5" id="viewRef">—</div>
                        </div>
                        <span id="viewStatusBadge" class="badge fs-6"></span>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold small text-uppercase text-muted mb-2">Customer</h6>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="text-muted small">Name</div>
                                <div class="fw-medium" id="viewClientName">—</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-muted small">Email</div>
                                <div class="fw-medium" id="viewClientEmail">—</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-muted small">Contact Number</div>
                                <div class="fw-medium" id="viewClientPhone">—</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold small text-uppercase text-muted mb-2">Package & Add-Ons</h6>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="text-muted small">Package</div>
                                <div class="fw-medium" id="viewPackageName">—</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Package Price</div>
                                <div class="fw-medium" id="viewPackagePrice">—</div>
                            </div>
                            <div class="col-12" id="viewServicesWrap" style="display:none;">
                                <div class="text-muted small mb-1">Included Services</div>
                                <div id="viewServices" class="d-flex flex-wrap gap-1"></div>
                            </div>
                            <div class="col-12" id="viewAddonsSection" style="display:none;">
                                <div class="text-muted small mb-1">Add-Ons</div>
                                <div id="viewAddonsList" class="mb-1"></div>
                                <div class="small text-muted">Add-On Charges: <span class="fw-medium text-dark"
                                        id="viewAddonsTotal">—</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold small text-uppercase text-muted mb-2">Event</h6>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="text-muted small">Type</div>
                                <div class="fw-medium" id="viewEventType">—</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-muted small">Date</div>
                                <div class="fw-medium" id="viewEventDate">—</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-muted small">Time</div>
                                <div class="fw-medium" id="viewEventTime">—</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Venue</div>
                                <div class="fw-medium" id="viewVenue">—</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Address</div>
                                <div class="fw-medium" id="viewAddress">—</div>
                            </div>
                            <div class="col-12" id="viewEventDescWrap" style="display:none;">
                                <div class="text-muted small mb-1">Event Description</div>
                                <div class="alert alert-soft alert-soft-info py-2 mb-0" id="viewEventDesc">—</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-semibold small text-uppercase text-muted mb-2">Payment</h6>
                        <div class="row g-3">
                            <div class="col-sm-4">
                                <div class="text-muted small">Total Price</div>
                                <div class="fw-bold" id="viewTotal">—</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-muted small">Downpayment (30%)</div>
                                <div class="fw-medium" id="viewDownpayment">—</div>
                            </div>
                            <div class="col-sm-4">
                                <div class="text-muted small">Remaining Balance</div>
                                <div class="fw-medium" id="viewBalance">—</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Assigned Team</div>
                                <div class="fw-medium" id="viewTeam">—</div>
                            </div>
                            <div class="col-sm-6">
                                <div class="text-muted small">Payment Status</div>
                                <div id="viewPaymentStatus">—</div>
                            </div>
                        </div>
                    </div>

                    <div id="viewNotesWrap" style="display:none;" class="mb-4">
                        <h6 class="fw-semibold small text-uppercase text-muted mb-2">Notes</h6>
                        <div class="alert alert-soft py-2 mb-0" id="viewNotes">—</div>
                    </div>

                    <div class="text-muted small border-top pt-2">Submitted: <span id="viewCreatedAt">—</span></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/admin-bookings.js') }}"></script>

    {{-- FLASH TOASTS --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="flashToasts" style="z-index: 1080;">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('#flashToasts .toast').forEach(function (el) {
                new bootstrap.Toast(el, { delay: 4000 }).show();
            });
        });
    </script>
</body>

</html>