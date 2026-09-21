{{-- All bookings tab content (loaded via AJAX) --}}
                <section class="surface-card">
                    <div class="section-head d-flex justify-content-between align-items-center">
                        <h2 class="section-title">All Bookings</h2>
                        <span class="badge bg-light text-dark border">{{ $bookings->total() }} total</span>
                    </div>
                    <div class="table-responsive d-none d-md-block">
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
                                                    <button type="button" class="btn btn-sm btn-outline-success rounded-3"
                                                        title="Approve Booking"
                                                        onclick="openApproveModal('{{ $booking->id }}', '{{ $booking->booking_ref }}', '{{ $booking->client_name }}', '{{ $booking->event_date->format('Y-m-d') }}')">
                                                        <i class="bi bi-check-lg me-1"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
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
                                                    @if($booking->event_date->lt(\Carbon\Carbon::today()))
                                                        <button type="button" class="btn btn-sm btn-outline-success rounded-3"
                                                            title="Complete Booking"
                                                            onclick="openCompleteModal('{{ $booking->id }}', '{{ $booking->booking_ref }}', '{{ $booking->client_name }}')">
                                                            <i class="bi bi-check-lg me-1"></i> Complete
                                                        </button>
                                                    @else
                                                        <small class="text-muted fst-italic align-self-center"
                                                            title="Available after the event date {{ $booking->event_date->format('M d, Y') }}">
                                                            After event
                                                        </small>
                                                    @endif
                                                @elseif($booking->status === 'completed')
                                                    @if($booking->postProduction)
                                                        <a href="{{ route('post-production.show', $booking->postProduction->id) }}"
                                                            class="btn btn-sm btn-outline-dark rounded-3">
                                                            <i class="bi bi-film me-1"></i>View Post-Production
                                                        </a>
                                                    @else
                                                        <a href="{{ route('post-production.create', $booking->id) }}"
                                                            class="btn btn-sm btn-outline-dark rounded-3">
                                                            <i class="bi bi-film me-1"></i>Proceed to Post-Production
                                                        </a>
                                                    @endif
                                                @endif
                                                <button type="button" class="btn btn-sm btn-outline-secondary rounded-3"
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

                    <div class="d-md-none" id="bookingsMobileList">
                        @forelse($bookings as $booking)
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
                            <div class="mobile-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <div class="fw-semibold">{{ $booking->client_name }}</div>
                                        <small class="text-muted">{{ $booking->booking_ref }}</small>
                                    </div>
                                    <div class="text-end flex-shrink-0 ms-2">
                                        <div class="mb-1">
                                            @include('partials.status-badge', ['status' => $booking->status, 'id' => 'status-badge-m-'.$booking->id])
                                        </div>
                                        <small id="payment-cell-m-{{ $booking->id }}">
                                            @php $dp = $booking->latestDownpayment; @endphp
                                            @if($dp)
                                                @include('partials.status-badge', ['status' => $dp->status === 'pending' ? 'submitted' : $dp->status, 'label' => $dp->status === 'pending' ? 'Submitted' : null])
                                            @elseif($booking->status === 'approved')
                                                @include('partials.status-badge', ['status' => 'awaiting', 'label' => 'Awaiting'])
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap gap-1 mb-2 small">
                                    <span class="mobile-card-label w-100 mb-0">Event Date</span>
                                    <span class="mobile-card-value">{{ \Carbon\Carbon::parse($booking->event_date)->format('M d, Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-2 mb-2">
                                    <span class="mobile-card-label mb-0 align-self-center">Total</span>
                                    <span class="fw-semibold">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                                </div>
                                <div id="actions-cell-m-{{ $booking->id }}" class="row g-2">
                                    @if($booking->status === 'pending')
                                        <div class="col-12">
                                            <button type="button" class="btn btn-sm btn-outline-success rounded-3 w-100"
                                                onclick="openApproveModal('{{ $booking->id }}', '{{ $booking->booking_ref }}', '{{ $booking->client_name }}', '{{ $booking->event_date->format('Y-m-d') }}')">
                                                <i class="bi bi-check-lg me-1"></i> Approve
                                            </button>
                                        </div>
                                        <div class="col-12">
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-3 w-100"
                                                onclick="openRejectModal('{{ $booking->id }}', '{{ $booking->booking_ref }}', '{{ $booking->client_name }}')">
                                                <i class="bi bi-x-lg me-1"></i> Reject
                                            </button>
                                        </div>
                                    @elseif($booking->status === 'rejected')
                                        <div class="col-12">
                                            <small class="text-danger text-muted fst-italic d-flex align-items-center">Rejected</small>
                                        </div>
                                    @elseif($booking->status === 'ongoing')
                                        @if($booking->event_date->lt(\Carbon\Carbon::today()))
                                            <div class="col-12">
                                                <button type="button" class="btn btn-sm btn-outline-success rounded-3 w-100"
                                                    onclick="openCompleteModal('{{ $booking->id }}', '{{ $booking->booking_ref }}', '{{ $booking->client_name }}')">
                                                    <i class="bi bi-check-lg me-1"></i> Complete
                                                </button>
                                            </div>
                                        @else
                                            <div class="col-12">
                                                <small class="text-muted fst-italic d-flex align-items-center">After event</small>
                                            </div>
                                        @endif
                                    @elseif($booking->status === 'completed')
                                        <div class="col-12">
                                            @if($booking->postProduction)
                                                <a href="{{ route('post-production.show', $booking->postProduction->id) }}" class="btn btn-sm btn-outline-dark rounded-3 w-100">
                                                    <i class="bi bi-film me-1"></i>View Post-Production
                                                </a>
                                            @else
                                                <a href="{{ route('post-production.create', $booking->id) }}" class="btn btn-sm btn-outline-dark rounded-3 w-100">
                                                    <i class="bi bi-film me-1"></i>Proceed to Post-Production
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 w-100"
                                            onclick='window.viewPayloads = window.viewPayloads || {}; window.viewPayloads[{{ $booking->id }}] = @json($viewPayload); openViewModal(window.viewPayloads[{{ $booking->id }}])'>
                                            <i class="bi bi-eye"></i> View
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No bookings found.
                            </div>
                        @endforelse
                    </div>
                    @if($bookings->hasPages())
                        <div>
                            {{ $bookings->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                </section>
