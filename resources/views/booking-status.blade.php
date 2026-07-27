<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status - HarvyMance Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg booking-navbar" style="background: #1a1a2e;">
        <div class="container">
            <a class="navbar-brand text-white" href="{{ url('/') }}">
                <i class="bi bi-camera-video me-2"></i>HarvyMance Films
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#bookingNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="bookingNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">
                            <i class="bi bi-arrow-left me-1"></i> New Booking
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/dashboard') }}">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                         style="width: 80px; height: 80px; background: {{ $booking->status === 'rejected' ? '#f8d7da' : '#d4edda' }};">
                        <i class="bi {{ $booking->status === 'rejected' ? 'bi-x-circle text-danger' : 'bi-check-circle text-success' }}" style="font-size: 2.5rem;"></i>
                    </div>
                    <h3 class="fw-bold">{{ $booking->status === 'rejected' ? 'Booking Rejected' : 'Booking Submitted!' }}</h3>
                    <p class="text-muted">{{ $booking->status === 'rejected' ? 'Your booking has been reviewed and could not be approved.' : 'Your booking request has been received and is awaiting review.' }}</p>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Booking Reference</label>
                                <div class="fw-bold fs-4" style="color: #1a1a2e;">{{ $booking->booking_ref }}</div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <label class="text-muted small">Status</label>
                                <div>
                                    @switch($booking->status)
                                        @case('pending')
                                            <span class="badge bg-warning text-dark fs-6">
                                                <i class="bi bi-clock me-1"></i>Pending Approval
                                            </span>
                                            @break
                                        @case('approved')
                                            <span class="badge bg-info fs-6">
                                                <i class="bi bi-check-circle me-1"></i>Awaiting Payment
                                            </span>
                                            @break
                                        @case('ongoing')
                                            <span class="badge bg-primary fs-6">
                                                <i class="bi bi-camera-video me-1"></i>Confirmed
                                            </span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-success fs-6">
                                                <i class="bi bi-check-all me-1"></i>Completed
                                            </span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-danger fs-6">
                                                <i class="bi bi-x-circle me-1"></i>Rejected
                                            </span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger fs-6">
                                                <i class="bi bi-x-circle me-1"></i>Cancelled
                                            </span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($booking->status === 'rejected' && $booking->rejection_reason)
                    <div class="card border-0 shadow-sm mb-4" style="border-left: 4px solid #dc3545 !important;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-danger mb-2">
                                <i class="bi bi-exclamation-triangle me-2"></i>Rejection Reason
                            </h6>
                            <p class="mb-0 text-muted">{{ $booking->rejection_reason }}</p>
                        </div>
                    </div>
                @endif

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="confirmation-section h-100">
                            <h6><i class="bi bi-box-seam me-2"></i>Package Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Package</span>
                                <span class="detail-value">{{ $booking->package->name ?? 'N/A' }}</span>
                            </div>
                            @if($booking->package && $booking->package->services->count() > 0)
                                <div class="detail-row">
                                    <span class="detail-label">Services</span>
                                    <span class="detail-value">
                                        @foreach($booking->package->services as $service)
                                            <span class="badge bg-light text-dark border">{{ $service->service_name }}</span>
                                        @endforeach
                                    </span>
                                </div>
                            @endif
                            <div class="detail-row">
                                <span class="detail-label">Package Price</span>
                                <span class="detail-value fw-bold">&#8369;{{ number_format($booking->package->price ?? 0, 2) }}</span>
                            </div>

                            @if($booking->addons->count() > 0)
                                <hr class="my-2">
                                <div class="detail-row">
                                    <span class="detail-label">Add-Ons</span>
                                    <span class="detail-value">
                                        @foreach($booking->addons as $addon)
                                            <span class="badge bg-light text-dark border">{{ $addon->name }}</span>
                                        @endforeach
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Add-On Charges</span>
                                    <span class="detail-value fw-bold">&#8369;{{ number_format($booking->addons->sum('pivot.price'), 2) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="confirmation-section h-100">
                            <h6><i class="bi bi-person me-2"></i>Customer Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Name</span>
                                <span class="detail-value">{{ $booking->client_name }}</span>
                            </div>
                            @if($booking->client_email)
                                <div class="detail-row">
                                    <span class="detail-label">Email</span>
                                    <span class="detail-value">{{ $booking->client_email }}</span>
                                </div>
                            @endif
                            @if($booking->client_phone)
                                <div class="detail-row">
                                    <span class="detail-label">Contact</span>
                                    <span class="detail-value">{{ $booking->client_phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="confirmation-section h-100">
                            <h6><i class="bi bi-calendar-event me-2"></i>Event Information</h6>
                            @if($booking->event_type)
                                <div class="detail-row">
                                    <span class="detail-label">Event Type</span>
                                    <span class="detail-value">{{ $booking->event_type }}</span>
                                </div>
                            @endif
                            <div class="detail-row">
                                <span class="detail-label">Date</span>
                                <span class="detail-value">{{ $booking->event_date->format('F d, Y') }}</span>
                            </div>
                            @if($booking->event_time)
                                <div class="detail-row">
                                    <span class="detail-label">Time</span>
                                    <span class="detail-value">{{ date('g:i A', strtotime($booking->event_time)) }}</span>
                                </div>
                            @endif
                            @if($booking->event_venue)
                                <div class="detail-row">
                                    <span class="detail-label">Venue</span>
                                    <span class="detail-value">{{ $booking->event_venue }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($booking->team)
                        <div class="col-md-6">
                            <div class="confirmation-section h-100" style="border-left: 4px solid #198754;">
                                <h6><i class="bi bi-people-fill me-2" style="color: #198754;"></i>Assigned Team</h6>
                                <div class="detail-row">
                                    <span class="detail-label">Team</span>
                                    <span class="detail-value fw-bold">{{ $booking->team->name }}</span>
                                </div>
                                @if($booking->team->members->count() > 0)
                                    <div class="detail-row">
                                        <span class="detail-label">Members</span>
                                        <span class="detail-value">
                                            @foreach($booking->team->members as $member)
                                                <span class="badge bg-success bg-opacity-10 text-success border">{{ $member->name }}</span>
                                            @endforeach
                                        </span>
                                    </div>
                                @endif
                                <div class="detail-row">
                                    <span class="detail-label">Event Date</span>
                                    <span class="detail-value">{{ $booking->event_date->format('F d, Y') }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Event Time</span>
                                    <span class="detail-value">{{ date('g:i A', strtotime($booking->event_time)) }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6">
                        <div class="payment-summary-card h-100 d-flex flex-column justify-content-center">
                            <h6 class="fw-bold mb-3"><i class="bi bi-credit-card me-2"></i>Payment Summary</h6>
                            <div class="payment-row">
                                <span>Total Amount</span>
                                <span>&#8369;{{ number_format($booking->total_price, 2) }}</span>
                            </div>
                            <div class="payment-row downpayment">
                                <span>Downpayment (30%)</span>
                                <span>&#8369;{{ number_format($booking->downpayment_amount, 2) }}</span>
                            </div>
                            <div class="payment-row balance">
                                <span>Remaining Balance</span>
                                <span>&#8369;{{ number_format($booking->total_price - $booking->downpayment_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-list-check me-2"></i>Next Steps</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="d-flex align-items-start">
                                    <div class="summary-icon bg-warning bg-opacity-10 text-warning me-3">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">1. Admin Review</div>
                                        <small class="text-muted">Your booking will be reviewed by our team within 24-48 hours.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-start">
                                    <div class="summary-icon bg-info bg-opacity-10 text-info me-3">
                                        <i class="bi bi-credit-card"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">2. Payment</div>
                                        <small class="text-muted">Pay the 30% downpayment via the payment instructions sent to your email.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-start">
                                    <div class="summary-icon bg-success bg-opacity-10 text-success me-3">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">3. Confirmation</div>
                                        <small class="text-muted">Receive your booking confirmation once payment is verified.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex align-items-start">
                                    <div class="summary-icon bg-primary bg-opacity-10 text-primary me-3">
                                        <i class="bi bi-camera-video"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold small">4. Event Day</div>
                                        <small class="text-muted">We'll be there to capture your special moments!</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="btn btn-dark me-2">
                        <i class="bi bi-plus-circle me-1"></i> Book Another Event
                    </a>
                    <a href="{{ url('/login') }}" class="btn btn-outline-dark me-2">
                        <i class="bi bi-person-badge me-1"></i> Booking Monitoring
                    </a>
                    <button class="btn btn-outline-dark" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> Print Details
                    </button>
                </div>

                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body p-4 text-center">
                        <h6 class="fw-bold mb-2"><i class="bi bi-info-circle me-2"></i>Booking Monitoring</h6>
                        <p class="text-muted mb-2 small">After your booking is approved, you will receive an email with your Control Number and Password to monitor your booking status.</p>
                        <a href="{{ url('/login') }}" class="btn btn-sm btn-outline-dark">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Go to Booking Monitoring
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="py-4 mt-4" style="background: #1a1a2e; color: rgba(255,255,255,0.7);">
        <div class="container text-center">
            <small>&copy; {{ date('Y') }} HarvyMance Films. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
