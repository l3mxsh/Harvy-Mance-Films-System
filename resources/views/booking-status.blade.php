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
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
</head>

<body>

    {{-- ==================== NAVBAR ==================== --}}
    @include('partials.landing-navbar')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var logo = document.querySelector('.landing-logo');
            if (logo) logo.src = logo.dataset.darkLogo;
            window.addEventListener('scroll', function () {
                if (logo) logo.src = logo.dataset.darkLogo;
            }, { passive: true });
        });
    </script>

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

        <div class="row justify-content-center">
            <div class="col-lg-9">

                {{-- ==================== REFERENCE + STATUS ==================== --}}
                <div class="confirm-total-banner mx-2 mb-3">
                    <div>
                        <span class="confirm-total-label">Booking Reference</span>
                        <div class="confirm-total-price" style="font-size: 1.15rem;">{{ $booking->booking_ref }}</div>
                    </div>
                    <div>
                        @switch($booking->status)
                            @case('pending')
                                <span class="badge bg-warning text-dark fs-6">Pending Approval</span>
                                @break
                            @case('approved')
                                <span class="badge bg-info fs-6">Awaiting Payment</span>
                                @break
                            @case('ongoing')
                                <span class="badge bg-primary fs-6">Confirmed</span>
                                @break
                            @case('completed')
                                <span class="badge bg-success fs-6">Completed</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger fs-6">Rejected</span>
                                @break
                            @case('cancelled')
                                <span class="badge bg-danger fs-6">Cancelled</span>
                                @break
                        @endswitch
                    </div>
                </div>

                @if($booking->status === 'rejected' && $booking->rejection_reason)
                    <div class="confirm-card mx-2 mb-3" style="border-left: 3px solid #dc3545;">
                        <h6 class="confirm-card-title text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Rejection Reason</h6>
                        <div class="confirm-value">{{ $booking->rejection_reason }}</div>
                    </div>
                @endif

                {{-- ==================== PACKAGE INFO ==================== --}}
                <div class="confirm-card mx-2 mb-3">
                    <h6 class="confirm-card-title">Package Information</h6>
                    <div class="confirm-fields">
                        <div class="confirm-field">
                            <span class="confirm-label">Package</span>
                            <span class="confirm-value">{{ $booking->package->name ?? 'N/A' }}</span>
                        </div>
                        @if($booking->package && $booking->package->services->count() > 0)
                            <div class="confirm-field">
                                <span class="confirm-label">Services</span>
                                <div class="confirm-services-pills">
                                    @foreach($booking->package->services as $service)
                                        <span class="confirm-service-pill">{{ $service->service_name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="confirm-field">
                            <span class="confirm-label">Package Price</span>
                            <span class="confirm-value">&#8369;{{ number_format($booking->package->price ?? 0, 2) }}</span>
                        </div>
                        @if($booking->addons->count() > 0)
                            <div class="confirm-field">
                                <span class="confirm-label">Add-Ons</span>
                                <div class="confirm-services-pills">
                                    @foreach($booking->addons as $addon)
                                        <span class="confirm-service-pill">{{ $addon->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="confirm-field">
                                <span class="confirm-label">Add-On Charges</span>
                                <span class="confirm-value">&#8369;{{ number_format($booking->addons->sum('pivot.price'), 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ==================== CLIENT + EVENT SIDE BY SIDE ==================== --}}
                <div class="confirm-grid mb-3">
                    <div class="confirm-card mx-2 mb-0">
                        <h6 class="confirm-card-title">Client Information</h6>
                        <div class="confirm-fields">
                            <div class="confirm-field">
                                <span class="confirm-label">Name</span>
                                <span class="confirm-value">{{ $booking->client_name }}</span>
                            </div>
                            @if($booking->client_email)
                                <div class="confirm-field">
                                    <span class="confirm-label">Email</span>
                                    <span class="confirm-value">{{ $booking->client_email }}</span>
                                </div>
                            @endif
                            @if($booking->client_phone)
                                <div class="confirm-field">
                                    <span class="confirm-label">Contact</span>
                                    <span class="confirm-value">{{ $booking->client_phone }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="confirm-card mx-2 mb-0">
                        <h6 class="confirm-card-title">Event Information</h6>
                        <div class="confirm-fields">
                            @if($booking->event_type)
                                <div class="confirm-field">
                                    <span class="confirm-label">Event Type</span>
                                    <span class="confirm-value">{{ $booking->event_type }}</span>
                                </div>
                            @endif
                            <div class="confirm-field">
                                <span class="confirm-label">Date</span>
                                <span class="confirm-value">{{ $booking->event_date->format('F d, Y') }}</span>
                            </div>
                            @if($booking->event_time)
                                <div class="confirm-field">
                                    <span class="confirm-label">Time</span>
                                    <span class="confirm-value">{{ date('g:i A', strtotime($booking->event_time)) }}</span>
                                </div>
                            @endif
                            @if($booking->event_venue)
                                <div class="confirm-field">
                                    <span class="confirm-label">Venue</span>
                                    <span class="confirm-value">{{ $booking->event_venue }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ==================== ASSIGNED TEAM + PAYMENT SIDE BY SIDE ==================== --}}
                <div class="confirm-grid mb-3">
                    @if($booking->team)
                        <div class="confirm-card mx-2 mb-0" style="border-left: 3px solid #198754;">
                            <h6 class="confirm-card-title">Assigned Team</h6>
                            <div class="confirm-fields">
                                <div class="confirm-field">
                                    <span class="confirm-label">Team</span>
                                    <span class="confirm-value">{{ $booking->team->name }}</span>
                                </div>
                                @if($booking->team->members->count() > 0)
                                    <div class="confirm-field">
                                        <span class="confirm-label">Members</span>
                                        <div class="confirm-services-pills">
                                            @foreach($booking->team->members as $member)
                                                <span class="confirm-service-pill">{{ $member->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="confirm-card mx-2 mb-0">
                        <h6 class="confirm-card-title">Payment Summary</h6>
                        <div class="confirm-fields">
                            <div class="confirm-field">
                                <span class="confirm-label">Total Amount</span>
                                <span class="confirm-value">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                            </div>
                            <div class="confirm-field">
                                <span class="confirm-label">Downpayment (30%)</span>
                                <span class="confirm-value">&#8369;{{ number_format($booking->downpayment_amount, 2) }}</span>
                            </div>
                            <div class="confirm-field">
                                <span class="confirm-label">Remaining Balance</span>
                                <span class="confirm-value">&#8369;{{ number_format($booking->total_price - $booking->downpayment_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== NEXT STEPS ==================== --}}
                <div class="confirm-card mx-2 mb-3">
                    <h6 class="confirm-card-title">Next Steps</h6>
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div class="confirm-field">
                                <span class="confirm-label">1. Admin Review</span>
                                <span class="confirm-value" style="font-size:0.82rem; font-weight:500;">Your booking will be reviewed within 24–48 hours.</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="confirm-field">
                                <span class="confirm-label">2. Client Account</span>
                                <span class="confirm-value" style="font-size:0.82rem; font-weight:500;">Once approved, we'll email your client login credentials.</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="confirm-field">
                                <span class="confirm-label">3. Downpayment</span>
                                <span class="confirm-value" style="font-size:0.82rem; font-weight:500;">Log in and complete the required downpayment to secure your booking.</span>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="confirm-field">
                                <span class="confirm-label">4. Event & Delivery</span>
                                <span class="confirm-value" style="font-size:0.82rem; font-weight:500;">Track your booking and download final files from your dashboard.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ==================== ACTIONS ==================== --}}
                <div class="d-flex flex-wrap justify-content-center gap-2 step-actions">
                    <a href="{{ url('/') }}" class="btn btn-primary-dark mx-2">
                        Book Another Event
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
    <script>
        let lastScrollY = window.scrollY;
        const navbar = document.querySelector('.landing-navbar');

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;

            navbar.classList.toggle('scrolled', currentScrollY > 10);

            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                navbar.classList.add('nav-hidden');
            } else {
                navbar.classList.remove('nav-hidden');
            }

            lastScrollY = currentScrollY;
        }, { passive: true });
    </script>
</body>

</html>
