<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status - HarvyMance Films</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
</head>

<body>

    {{-- ==================== NAVBAR ==================== --}}
    <nav class="navbar booking-navbar shadow-sm">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="d-inline-flex align-items-center">
                <img src="{{ asset('storage/images/Black Logo.png') }}" alt="HarvyMance Films" height="34">
            </a>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-primary-dark btn-sm-pill btn-login">
                    Dashboard
                </a>
            @else
                <a href="{{ route('customer.login') }}" class="btn btn-primary-dark btn-sm-pill btn-login">
                    Login
                </a>
            @endauth
        </div>
    </nav>

    {{-- ==================== HERO ==================== --}}
    <header class="booking-hero">
        <div class="container">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width: 72px; height: 72px; background: {{ $booking->status === 'rejected' ? '#f8d7da' : '#d1e7dd' }};">
                <i class="bi {{ $booking->status === 'rejected' ? 'bi-x-circle text-danger' : 'bi-check-circle text-success' }}" style="font-size: 2rem;"></i>
            </div>
            <h1 class="booking-hero-title">{{ $booking->status === 'rejected' ? 'Booking Rejected' : 'Booking Submitted!' }}</h1>
            <p class="booking-hero-sub">{{ $booking->status === 'rejected' ? 'Your booking has been reviewed and could not be approved.' : 'Your booking request has been received and is awaiting review.' }}</p>
        </div>
    </header>

    <main class="container booking-main" style="padding-top: 2rem;">

        @if(session('success'))
            <div class="alert alert-soft alert-soft-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-9">

                {{-- ==================== REFERENCE + STATUS ==================== --}}
                <section class="surface-card">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <span class="section-optional" style="margin-left:0;">Booking Reference</span>
                            <div class="fw-bold fs-4 mt-1">{{ $booking->booking_ref }}</div>
                        </div>
                        <div class="col-md-6 text-md-end mt-3 mt-md-0">
                            @switch($booking->status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark fs-6"><i class="bi bi-clock me-1"></i>Pending Approval</span>
                                    @break
                                @case('approved')
                                    <span class="badge bg-info fs-6"><i class="bi bi-check-circle me-1"></i>Awaiting Payment</span>
                                    @break
                                @case('ongoing')
                                    <span class="badge bg-primary fs-6"><i class="bi bi-camera-video me-1"></i>Confirmed</span>
                                    @break
                                @case('completed')
                                    <span class="badge bg-success fs-6"><i class="bi bi-check-all me-1"></i>Completed</span>
                                    @break
                                @case('rejected')
                                    <span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                    @break
                                @case('cancelled')
                                    <span class="badge bg-danger fs-6"><i class="bi bi-x-circle me-1"></i>Cancelled</span>
                                    @break
                            @endswitch
                        </div>
                    </div>

                    @if($booking->status === 'rejected' && $booking->rejection_reason)
                        <hr class="soft-divider">
                        <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Rejection Reason</h6>
                        <p class="mb-0 text-muted small">{{ $booking->rejection_reason }}</p>
                    @endif
                </section>

                <div class="row g-4">

                    {{-- ==================== PACKAGE INFO ==================== --}}
                    <div class="col-md-6">
                        <section class="surface-card confirmation-section h-100 mb-0">
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
                                <hr class="soft-divider">
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
                        </section>
                    </div>

                    {{-- ==================== CUSTOMER INFO ==================== --}}
                    <div class="col-md-6">
                        <section class="surface-card confirmation-section h-100 mb-0">
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
                        </section>
                    </div>

                    {{-- ==================== EVENT INFO ==================== --}}
                    <div class="col-md-6">
                        <section class="surface-card confirmation-section h-100 mb-0">
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
                        </section>
                    </div>

                    {{-- ==================== ASSIGNED TEAM ==================== --}}
                    @if($booking->team)
                        <div class="col-md-6">
                            <section class="surface-card confirmation-section h-100 mb-0" style="border-left: 3px solid #198754;">
                                <h6><i class="bi bi-people-fill me-2" style="color:#198754;"></i>Assigned Team</h6>
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
                            </section>
                        </div>
                    @endif

                    {{-- ==================== PAYMENT SUMMARY ==================== --}}
                    <div class="col-md-6 {{ !$booking->team ? '' : '' }}">
                        <aside class="price-summary h-100 d-flex flex-column justify-content-center mb-0">
                            <h6 class="summary-title"><i class="bi bi-credit-card me-2"></i>Payment Summary</h6>
                            <div class="summary-row">
                                <span class="summary-key">Total Amount</span>
                                <span class="summary-val">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-key">Downpayment (30%)</span>
                                <span class="summary-val">&#8369;{{ number_format($booking->downpayment_amount, 2) }}</span>
                            </div>
                            <div class="summary-row total">
                                <span>Remaining Balance</span>
                                <span>&#8369;{{ number_format($booking->total_price - $booking->downpayment_amount, 2) }}</span>
                            </div>
                        </aside>
                    </div>
                </div>

                {{-- ==================== NEXT STEPS ==================== --}}
                <section class="surface-card mt-4">
                    <div class="section-head">
                        <h2 class="section-title">Next Steps</h2>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="d-flex align-items-start">
                                <div>
                                    <div class="fw-semibold small">1. Admin Review</div>
                                    <small class="text-muted">Your booking will be reviewed within 24–48 hours.</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex align-items-start">
                                <div>
                                    <div class="fw-semibold small">2. Client Account</div>
                                    <small class="text-muted">Once approved, we'll email your client login credentials.</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex align-items-start">
                                <div>
                                    <div class="fw-semibold small">3. Downpayment</div>
                                    <small class="text-muted">Log in to your dashboard and complete the required downpayment to secure your booking.</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="d-flex align-items-start">
                                <div>
                                    <div class="fw-semibold small">4. Event & Delivery</div>
                                    <small class="text-muted">Track your booking progress and download your final files from your client dashboard.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ==================== ACTIONS ==================== --}}
                <div class="d-flex flex-wrap justify-content-center gap-2 step-actions">
                    <a href="{{ url('/') }}" class="btn btn-primary-dark">
                        <i class="bi bi-plus-circle me-1"></i> Book Another Event
                    </a>
                </div>
                <p class="summary-note text-center mt-3">
                    Once approved, you'll receive an email with your Control Number and Password to monitor your booking above.
                </p>
            </div>
        </div>
    </main>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="booking-footer">
        <div class="container text-center">
            <small>&copy; {{ date('Y') }} HarvyMance Films. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>