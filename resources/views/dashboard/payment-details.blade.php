<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details - Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payment-verification.css') }}">
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Payment Verification Details</span>
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

            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <a href="{{ route('payment-verification.index') }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-arrow-left me-1"></i>Back to Payment Verification
                    </a>
                    <h5 class="fw-bold mb-0 mt-1">
                        <i class="bi bi-receipt me-2"></i>Payment for Booking
                        <code>{{ $downpayment->booking->booking_ref ?? 'N/A' }}</code>
                        @if($downpayment->payment_type === 'final')
                            <span class="badge bg-success ms-2">Final Payment</span>
                        @else
                            <span class="badge bg-info ms-2">Downpayment</span>
                        @endif
                    </h5>
                </div>
                <div>
                    @php
                        $badgeClass = match($downpayment->status) {
                            'pending' => 'bg-warning text-dark',
                            'verified' => 'bg-success',
                            'rejected' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }} px-3 py-2" style="font-size: 0.9rem;">
                        @if($downpayment->status === 'pending')
                            <i class="bi bi-hourglass-split me-1"></i>Pending Verification
                        @elseif($downpayment->status === 'verified')
                            <i class="bi bi-check-circle me-1"></i>Verified
                        @elseif($downpayment->status === 'rejected')
                            <i class="bi bi-x-circle me-1"></i>Rejected
                        @endif
                    </span>
                </div>
            </div>

            <div class="row g-4">
                {{-- LEFT: PAYMENT PROOF --}}
                <div class="col-lg-7">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-image me-2"></i>Payment Proof</h6>
                        </div>
                        <div class="card-body text-center p-4">
                            @if($downpayment->payment_proof)
                                <a href="{{ asset('storage/' . $downpayment->payment_proof) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $downpayment->payment_proof) }}"
                                         alt="Payment Proof"
                                         class="img-fluid rounded proof-image"
                                         style="max-height: 400px; cursor: zoom-in;">
                                </a>
                                <p class="text-muted small mt-2 mb-0">
                                    <i class="bi bi-info-circle me-1"></i>Click image to open full size
                                </p>
                            @else
                                <div class="text-muted py-5">
                                    <i class="bi bi-image fs-1 d-block mb-2"></i>
                                    No payment proof uploaded.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RIGHT: DETAILS --}}
                <div class="col-lg-5">
                    {{-- PAYMENT DETAILS --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Payment Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="detail-row">
                                <span class="detail-label">Amount Submitted</span>
                                <span class="detail-value fw-bold text-primary fs-5">&#8369;{{ number_format($downpayment->amount, 2) }}</span>
                            </div>
                            @php
                                if ($downpayment->payment_type === 'final') {
                                    $requiredAmount = ($downpayment->booking->total_price ?? 0) - ($downpayment->booking->downpayments()->where('status', 'verified')->sum('amount') ?? 0);
                                    $label = 'Remaining Balance';
                                } else {
                                    $requiredAmount = $downpayment->booking->downpayment_amount ?? 0;
                                    $label = 'Required Downpayment';
                                }
                            @endphp
                            <div class="detail-row">
                                <span class="detail-label">{{ $label }}</span>
                                <span class="detail-value fw-bold">&#8369;{{ number_format($requiredAmount, 2) }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Amount Difference</span>
                                @php $diff = $requiredAmount - $downpayment->amount; @endphp
                                <span class="detail-value fw-bold {{ $diff > 0 ? 'text-danger' : 'text-success' }}">
                                    @if($diff > 0)
                                        &#8369;{{ number_format($diff, 2) }} short
                                    @elseif($diff < 0)
                                        &#8369;{{ number_format(abs($diff), 2) }} over
                                    @else
                                        Exact amount
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Submission Date</span>
                                <span class="detail-value">{{ $downpayment->submitted_at ? $downpayment->submitted_at->format('M d, Y g:i A') : '—' }}</span>
                            </div>
                            @if($downpayment->verified_at)
                                <div class="detail-row">
                                    <span class="detail-label">Verified Date</span>
                                    <span class="detail-value text-success">{{ $downpayment->verified_at->format('M d, Y g:i A') }}</span>
                                </div>
                            @endif
                            @if($downpayment->rejection_reason)
                                <div class="rejection-reason-box mt-3">
                                    <div class="text-danger fw-bold small mb-1">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Rejection Reason
                                    </div>
                                    <p class="mb-0 small">{{ $downpayment->rejection_reason }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- BOOKING INFO --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-journal-text me-2"></i>Booking Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="detail-row">
                                <span class="detail-label">Booking Ref</span>
                                <span class="detail-value"><code>{{ $downpayment->booking->booking_ref ?? 'N/A' }}</code></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Client</span>
                                <span class="detail-value">{{ $downpayment->booking->client_name ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email</span>
                                <span class="detail-value">{{ $downpayment->booking->client_email ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Phone</span>
                                <span class="detail-value">{{ $downpayment->booking->client_phone ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Package</span>
                                <span class="detail-value">{{ $downpayment->booking->package->name ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Event Date</span>
                                <span class="detail-value">{{ $downpayment->booking->event_date ? $downpayment->booking->event_date->format('M d, Y') : 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Total Price</span>
                                <span class="detail-value fw-bold">&#8369;{{ number_format($downpayment->booking->total_price ?? 0, 2) }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Booking Status</span>
                                <span class="detail-value">
                                    @php
                                        $bStatus = match($downpayment->booking->status ?? '') {
                                            'pending' => 'bg-warning text-dark',
                                            'approved' => 'bg-success',
                                            'ongoing' => 'bg-primary',
                                            'completed' => 'bg-secondary',
                                            'rejected' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $bStatus }}">{{ ucfirst($downpayment->booking->status ?? 'N/A') }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    @if($downpayment->status === 'pending')
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-2"></i>Verification Actions</h6>
                            </div>
                            <div class="card-body d-grid gap-2">
                                <form method="POST" action="{{ route('payment-verification.verify', $downpayment->id) }}" id="verifyForm">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100 btn-action" id="verifyBtn">
                                        <i class="bi bi-check-circle me-1"></i>Approve & Verify Payment
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger w-100 btn-action" onclick="openRejectModal()">
                                    <i class="bi bi-x-circle me-1"></i>Reject Payment
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- REJECT PAYMENT MODAL --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Reject Payment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('payment-verification.reject', $downpayment->id) }}" id="rejectForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">You are about to reject the payment submission for booking <strong>{{ $downpayment->booking->booking_ref ?? 'N/A' }}</strong>.</p>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label fw-semibold">Reason for Rejection <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4"
                                placeholder="Please provide a reason why this payment is being rejected..."
                                required></textarea>
                            <div class="form-text">This reason will be visible to the client.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-danger" id="rejectConfirmBtn">
                            <i class="bi bi-x-lg me-1"></i> Reject Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/payment-details.js') }}"></script>
</body>

</html>
