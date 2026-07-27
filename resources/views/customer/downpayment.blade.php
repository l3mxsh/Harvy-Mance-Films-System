<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Downpayment - HarvyMance Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client-downpayment.css') }}">
</head>

<body class="client-dashboard-body">

    {{-- TOP NAV --}}
    <div class="client-topnav">
        <a href="{{ route('customer.dashboard') }}" class="brand">
            <i class="bi bi-camera-video me-2"></i>HarvyMance Films
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="small opacity-75 d-none d-md-inline">
                <i class="bi bi-person me-1"></i> {{ $account->client_name }}
            </span>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-gear"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('customer.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                    <li><a class="dropdown-item" href="{{ route('customer.change-password') }}"><i class="bi bi-key me-2"></i>Change Password</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('customer.logout') }}">
                            @csrf
                            <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-left me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="container py-4">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">

                {{-- PAGE HEADER --}}
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">
                            <i class="bi bi-credit-card me-2"></i>Downpayment Submission
                        </h4>
                        <p class="text-muted mb-0 small">
                            Submit your 30% downpayment proof for verification
                        </p>
                    </div>
                    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-dark btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>

                {{-- PAYMENT SUMMARY CARD --}}
                <div class="card section-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-calculator me-2"></i>Payment Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="payment-info-box">
                                    <div class="label">Total Package Amount</div>
                                    <div class="value">&#8369;{{ number_format($booking->total_price, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="payment-info-box highlight">
                                    <div class="label">Required Downpayment (30%)</div>
                                    <div class="value text-primary">&#8369;{{ number_format($booking->downpayment_amount, 2) }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="payment-info-box">
                                    <div class="label">Remaining Balance</div>
                                    <div class="value text-danger">&#8369;{{ number_format($booking->total_price - $booking->downpayment_amount, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CURRENT PAYMENT STATUS --}}
                @if($latestDownpayment)
                    <div class="card section-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Current Payment Status</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="detail-row">
                                        <span class="detail-label">Status</span>
                                        <span class="detail-value">
                                            @if($latestDownpayment->status === 'pending')
                                                <span class="badge bg-warning text-dark px-3 py-2">
                                                    <i class="bi bi-hourglass-split me-1"></i> Pending Verification
                                                </span>
                                            @elseif($latestDownpayment->status === 'verified')
                                                <span class="badge bg-success px-3 py-2">
                                                    <i class="bi bi-check-circle me-1"></i> Verified
                                                </span>
                                            @elseif($latestDownpayment->status === 'rejected')
                                                <span class="badge bg-danger px-3 py-2">
                                                    <i class="bi bi-x-circle me-1"></i> Rejected
                                                </span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Amount Submitted</span>
                                        <span class="detail-value fw-bold">&#8369;{{ number_format($latestDownpayment->amount, 2) }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="detail-label">Submission Date</span>
                                        <span class="detail-value">{{ $latestDownpayment->submitted_at->format('M d, Y g:i A') }}</span>
                                    </div>
                                    @if($latestDownpayment->verified_at)
                                        <div class="detail-row">
                                            <span class="detail-label">Verified Date</span>
                                            <span class="detail-value">{{ $latestDownpayment->verified_at->format('M d, Y g:i A') }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-row">
                                        <span class="detail-label">Submitted Proof</span>
                                        <span class="detail-value">
                                            <a href="{{ asset('storage/' . $latestDownpayment->payment_proof) }}" target="_blank" class="text-primary text-decoration-none">
                                                <i class="bi bi-image me-1"></i> View Proof
                                            </a>
                                        </span>
                                    </div>
                                    @if($latestDownpayment->rejection_reason)
                                        <div class="rejection-reason-box mt-3">
                                            <div class="label text-danger fw-bold mb-1">
                                                <i class="bi bi-exclamation-triangle me-1"></i>Rejection Reason
                                            </div>
                                            <p class="mb-0">{{ $latestDownpayment->rejection_reason }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- SUBMISSION FORM --}}
                @if(!$latestDownpayment || $latestDownpayment->status === 'rejected')
                    <div class="card section-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-upload me-2"></i>
                                {{ $latestDownpayment && $latestDownpayment->status === 'rejected' ? 'Resubmit Payment Proof' : 'Submit Payment Proof' }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($latestDownpayment && $latestDownpayment->status === 'rejected')
                                <div class="alert alert-warning py-2 mb-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Your previous submission was rejected. Please review the rejection reason above and resubmit with corrected proof.
                                </div>
                            @endif

                            <form method="POST" action="{{ $latestDownpayment && $latestDownpayment->status === 'rejected' ? route('customer.downpayment.resubmit', $latestDownpayment->id) : route('customer.downpayment.submit') }}"
                                  enctype="multipart/form-data" id="downpaymentForm">
                                @csrf

                                <div class="mb-3">
                                    <label for="amount" class="form-label fw-semibold">
                                        Payment Amount <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">&#8369;</span>
                                        <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                               id="amount" name="amount" step="0.01" min="0.01"
                                               value="{{ old('amount', $booking->downpayment_amount) }}"
                                               placeholder="Enter payment amount">
                                        @error('amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Required amount: &#8369;{{ number_format($booking->downpayment_amount, 2) }}</div>
                                </div>

                                <div class="mb-3">
                                    <label for="payment_proof" class="form-label fw-semibold">
                                        Payment Proof <span class="text-danger">*</span>
                                    </label>
                                    <input type="file" class="form-control @error('payment_proof') is-invalid @enderror"
                                           id="payment_proof" name="payment_proof" accept="image/*" required>
                                    @error('payment_proof')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Upload a screenshot or receipt of your payment (JPEG, PNG, JPG, GIF - Max 5MB)</div>
                                </div>

                                <div class="mb-3" id="previewContainer" style="display: none;">
                                    <label class="form-label fw-semibold">Image Preview</label>
                                    <div class="image-preview-wrapper">
                                        <img id="imagePreview" src="#" alt="Payment Proof Preview" class="img-fluid rounded">
                                        <button type="button" class="btn-remove-preview" id="removePreview">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">
                                        <i class="bi bi-send me-1"></i>Submit Payment Proof
                                    </button>
                                    <a href="{{ route('customer.dashboard') }}" class="btn btn-outline-secondary">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- PAYMENT INSTRUCTIONS --}}
                <div class="card section-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Payment Instructions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="instruction-step">
                                    <div class="step-number">1</div>
                                    <h6>Transfer Payment</h6>
                                    <p class="text-muted small mb-0">Transfer the required downpayment amount to our designated payment account.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="instruction-step">
                                    <div class="step-number">2</div>
                                    <h6>Take Screenshot</h6>
                                    <p class="text-muted small mb-0">Take a screenshot or photo of your payment confirmation/receipt.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="instruction-step">
                                    <div class="step-number">3</div>
                                    <h6>Upload & Submit</h6>
                                    <p class="text-muted small mb-0">Upload the proof above and submit for verification. We'll review within 24-48 hours.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer class="client-footer mt-4">
        &copy; {{ date('Y') }} HarvyMance Films. All rights reserved.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/client-downpayment.js') }}"></script>
</body>

</html>
