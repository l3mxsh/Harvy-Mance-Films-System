<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verification</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payment-verification.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Payment Verification</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            {{-- FILTER TABS --}}
            <ul class="nav nav-pills mb-4" id="paymentTabs">
                <li class="nav-item">
                    <a class="nav-link {{ !request('status') ? 'active' : '' }}" href="{{ route('payment-verification.index') }}">
                        <i class="bi bi-collection me-1"></i> All Payments
                        @if($stats['total'] > 0)
                            <span class="badge bg-light text-dark ms-1">{{ $stats['total'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') === 'pending' ? 'active' : '' }}"
                        href="{{ route('payment-verification.index', ['status' => 'pending']) }}">
                        <i class="bi bi-hourglass-split me-1"></i> Pending
                        @if($stats['pending'] > 0)
                            <span class="badge bg-danger ms-1">{{ $stats['pending'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') === 'verified' ? 'active' : '' }}"
                        href="{{ route('payment-verification.index', ['status' => 'verified']) }}">
                        <i class="bi bi-check-circle me-1"></i> Verified
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request('status') === 'rejected' ? 'active' : '' }}"
                        href="{{ route('payment-verification.index', ['status' => 'rejected']) }}">
                        <i class="bi bi-x-circle me-1"></i> Rejected
                    </a>
                </li>
            </ul>

            <section class="surface-card">
                <div class="section-head d-flex justify-content-between align-items-center">
                    <h2 class="section-title">Payment Submissions</h2>
                    <span class="badge bg-light text-dark border">{{ $downpayments->total() }} total</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Type</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Payment Proof</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($downpayments as $downpayment)
                                                            <tr>
                                                                <td><code>{{ $downpayment->booking->booking_ref ?? 'N/A' }}</code></td>
                                                                <td>
                                                                    @if($downpayment->payment_type === 'final')
                                                                        <span class="badge bg-info text-white">Final Payment</span>
                                                                    @else
                                                                        <span class="badge bg-light text-dark border">Downpayment</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div>{{ $downpayment->booking->client_name ?? 'N/A' }}</div>
                                                                    <small class="text-muted">{{ $downpayment->booking->client_email ?? '' }}</small>
                                                                </td>
                                                                <td class="fw-semibold">&#8369;{{ number_format($downpayment->amount, 2) }}</td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill"
                                                                        onclick="previewProof('{{ asset('storage/' . $downpayment->payment_proof) }}')">
                                                                        <i class="bi bi-image me-1"></i> View
                                                                    </button>
                                                                </td>
                                                                <td>{{ $downpayment->submitted_at ? $downpayment->submitted_at->format('M d, Y g:i A') : '—' }}</td>
                                                                <td>
                                                                    @include('partials.status-badge', ['status' => $downpayment->status])
                                                                    @if($downpayment->status === 'verified')
                                                                        <div class="mt-1">
                                                                            <span class="badge bg-success">Payment Received</span>
                                                                        </div>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center">
                                                                    @php
                                $viewPayload = [
                                    'booking_ref' => $downpayment->booking->booking_ref ?? null,
                                    'status' => $downpayment->booking->status ?? null,
                                    'client_name' => $downpayment->booking->client_name ?? null,
                                    'client_email' => $downpayment->booking->client_email ?? null,
                                    'client_phone' => $downpayment->booking->client_phone ?? null,
                                    'package_name' => $downpayment->booking->package->name ?? null,
                                    'package_price' => (float) ($downpayment->booking->package->price ?? 0),
                                    'services' => $downpayment->booking->package ? $downpayment->booking->package->services->pluck('service_name')->toArray() : [],
                                    'addons' => $downpayment->booking->addons->map(fn($a) => ['name' => $a->name, 'price' => (float) $a->pivot->price])->values()->toArray(),
                                    'addons_total' => (float) $downpayment->booking->addons->sum('pivot.price'),
                                    'event_type' => $downpayment->booking->event_type ?? null,
                                    'event_date' => $downpayment->booking->event_date ? \Carbon\Carbon::parse($downpayment->booking->event_date)->format('F d, Y') : null,
                                    'event_time' => $downpayment->booking->event_time ? date('g:i A', strtotime($downpayment->booking->event_time)) : null,
                                    'event_venue' => $downpayment->booking->event_venue ?? null,
                                    'event_address' => $downpayment->booking->event_address ?? null,
                                    'event_description' => $downpayment->booking->event_description ?? null,
                                    'total_price' => (float) ($downpayment->booking->total_price ?? 0),
                                    'downpayment' => (float) ($downpayment->booking->downpayment_amount ?? 0),
                                    'balance' => (float) (($downpayment->booking->total_price ?? 0) - ($downpayment->booking->downpayment_amount ?? 0)),
                                    'team_name' => $downpayment->booking->team->name ?? null,
                                    'payment_status' => $downpayment->status,
                                    'notes' => $downpayment->booking->notes ?? null,
                                    'created_at' => $downpayment->booking->created_at ? $downpayment->booking->created_at->format('M d, Y g:i A') : null,
                                ];
                                                                    @endphp
                                                                    <div class="d-flex gap-2 justify-content-center flex-wrap">

                                                                        @if($downpayment->status === 'pending')
                                                                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill"
                                                                                title="Verify Payment"
                                                                                onclick="openVerifyModal('{{ $downpayment->id }}', '{{ $downpayment->booking->booking_ref ?? 'N/A' }}')">
                                                                                <i class="bi bi-check-lg me-1"></i> Verify
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill"
                                                                                title="Reject Payment"
                                                                                onclick="openRejectModal('{{ $downpayment->id }}', '{{ $downpayment->booking->booking_ref ?? 'N/A' }}')">
                                                                                <i class="bi bi-x-lg me-1"></i> Reject
                                                                            </button>
                                                                        @endif
                                                                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" title="View Booking Details"
                                                                            onclick='window.viewPayloads = window.viewPayloads || {}; window.viewPayloads[{{ $downpayment->id }}] = @json($viewPayload); openViewModal(window.viewPayloads[{{ $downpayment->id }}])'>
                                                                            <i class="bi bi-eye"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No payment submissions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($downpayments->hasPages())
                    <div class="border-top pt-3 mt-3">
                        {{ $downpayments->links() }}
                    </div>
                @endif
            </section>
        </div>
    </div>

    @include('partials.modals.payment-actions')

    @include('partials.modals.booking-details')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/payment-verification.js') }}"></script>

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
