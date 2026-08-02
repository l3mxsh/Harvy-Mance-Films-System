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
                                            @include('partials.status-badge', ['status' => $booking->status, 'id' => 'status-badge-'.$booking->id])
                                        </td>
                                        <td id="payment-cell-{{ $booking->id }}">
                                            @php $dp = $booking->latestDownpayment; @endphp
                                            @if($dp)
                                                @include('partials.status-badge', ['status' => $dp->status === 'pending' ? 'submitted' : $dp->status, 'label' => $dp->status === 'pending' ? 'Submitted' : null])
                                            @elseif($booking->status === 'approved')
                                                @include('partials.status-badge', ['status' => 'awaiting', 'label' => 'Awaiting'])
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
                                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill"
                                                        title="Complete Booking"
                                                        onclick="openCompleteModal('{{ $booking->id }}', '{{ $booking->booking_ref }}', '{{ $booking->client_name }}')">
                                                        <i class="bi bi-check-lg me-1"></i> Complete
                                                    </button>
                                                @elseif($booking->status === 'completed')
                                                    @if($booking->postProduction)
                                                        <a href="{{ route('post-production.show', $booking->postProduction->id) }}"
                                                            class="btn btn-sm btn-outline-dark">
                                                            <i class="bi bi-film me-1"></i>View Post-Production
                                                        </a>
                                                    @else
                                                        <a href="{{ route('post-production.create', $booking->id) }}"
                                                            class="btn btn-sm btn-outline-dark rounded-2">
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

    @include('partials.modals.booking-actions')

    @include('partials.modals.booking-details')

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