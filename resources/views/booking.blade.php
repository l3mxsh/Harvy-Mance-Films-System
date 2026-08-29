<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now - HarvyMance Films</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            <h1 class="booking-hero-title">Book Your Event</h1>
            <p class="booking-hero-sub">Choose your package and complete your booking in a few simple steps.</p>
        </div>
    </header>

    {{-- ==================== STEPPER ==================== --}}
    <div class="booking-nav">
        <div class="container">
            <div class="stepper" id="stepper">
                <div class="stepper-step active" data-step="1">
                    <div class="stepper-number">1</div>
                    <span class="stepper-label">Package</span>
                </div>
                <div class="stepper-connector"></div>
                <div class="stepper-step" data-step="2">
                    <div class="stepper-number">2</div>
                    <span class="stepper-label">Your Info</span>
                </div>
                <div class="stepper-connector"></div>
                <div class="stepper-step" data-step="3">
                    <div class="stepper-number">3</div>
                    <span class="stepper-label">Event Details</span>
                </div>
                <div class="stepper-connector"></div>
                <div class="stepper-step" data-step="4">
                    <div class="stepper-number">4</div>
                    <span class="stepper-label">Confirmation</span>
                </div>
            </div>
        </div>
    </div>

    <main class="container booking-main">
        @if(session('success'))
            <div class="alert alert-soft alert-soft-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-soft alert-soft-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form id="bookingForm" method="POST" action="{{ route('booking.store') }}">
            @csrf

            <input type="hidden" name="package_id" id="selectedPackageId" value="{{ old('package_id', '') }}">
            <div id="addonInputsContainer"></div>
            <input type="hidden" name="total_price" id="hiddenTotalPrice" value="0">
            <input type="hidden" name="otp_verified" id="otpVerified" value="0">
            <input type="hidden" name="terms_agreed" id="hiddenTermsAgreed" value="0">

            {{-- ==================== STEP 1: Package & Add-Ons ==================== --}}
            <div class="step-content active" id="step1">
                <div class="row g-4 g-lg-5">
                    <div class="col-12">

                        <section class="surface-card m-2">
                            <div class="section-head">
                                <h2 class="section-title">Select Your Package</h2>
                                <p class="section-sub">Pick the coverage that fits your event.</p>
                            </div>

                            <div class="row g-3">
                                @forelse($packages as $pkg)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="package-card h-100" data-package-id="{{ $pkg->id }}" data-price="{{ $pkg->price }}" onclick="selectPackage(this)">
                                            <div class="check-indicator">
                                                <i class="bi bi-check-lg d-none"></i>
                                            </div>
                                            <h6 class="package-name">{{ $pkg->name }}</h6>
                                            <div class="package-price">&#8369;{{ number_format($pkg->price, 2) }}</div>
                                            @if($pkg->description)
                                                <p class="package-desc">{{ $pkg->description }}</p>
                                            @endif
                                            <ul class="service-list">
                                                @foreach($pkg->services as $service)
                                                    <li>{{ $service->service_name }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="empty-state">
                                            <i class="bi bi-box-seam"></i>
                                            <p class="mb-0">No packages available at the moment. Please check back later.</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </section>

                        @if($addons->count() > 0)
                            <section class="surface-card m-2">
                                <div class="section-head">
                                    <h2 class="section-title">Add-Ons <span class="section-optional">Optional</span></h2>
                                    <p class="section-sub">Enhance your package with extra services.</p>
                                </div>

                                <div class="row g-3">
                                    @foreach($addons as $addon)
                                        <div class="col-md-6 col-lg-4">
                                            <div class="addon-card" data-addon-id="{{ $addon->id }}" data-price="{{ $addon->price }}">
                                                <div class="form-check">
                                                    <input class="form-check-input addon-checkbox" type="checkbox"
                                                           value="{{ $addon->id }}" id="addon_{{ $addon->id }}"
                                                           onchange="toggleAddon(this)">
                                                    <label class="form-check-label w-100" for="addon_{{ $addon->id }}">
                                                        <div class="d-flex justify-content-between align-items-start gap-3">
                                                            <div>
                                                                <div class="fw-semibold">{{ $addon->name }}</div>
                                                                @if($addon->description)
                                                                    <small class="text-muted">{{ Str::limit($addon->description, 60) }}</small>
                                                                @endif
                                                            </div>
                                                            <span class="addon-price text-nowrap">&#8369;{{ number_format($addon->price, 2) }}</span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        @endif

                        <aside class="price-summary m-2" id="priceSummary">
                            <h6 class="summary-title">Order Summary</h6>

                            <div id="summaryPackage" class="summary-row" style="display: none;">
                                <span class="summary-key">Package</span>
                                <span class="summary-val" id="summaryPackageName">-</span>
                            </div>
                            <div id="summaryPackagePrice" class="summary-row" style="display: none;">
                                <span class="summary-key ps-2" id="summaryPackagePriceLabel">-</span>
                                <span class="summary-val" id="summaryPackagePriceValue">-</span>
                            </div>

                            <div id="summaryAddonsContainer">
                                <div id="summaryAddonsHeader" class="summary-row summary-group" style="display: none;">
                                    <span>Add-Ons</span>
                                    <span></span>
                                </div>
                            </div>

                            <div class="summary-row total">
                                <span>Total</span>
                                <span id="summaryTotal">&#8369;0.00</span>
                            </div>

                            <p class="summary-note">No payment required today.</p>
                        </aside>

                        <div class="d-flex justify-content-end step-actions mx-2">
                            <button type="button" class="btn btn-dark rounded-pill" onclick="goToStep(2)" id="step1Next">
                                Continue <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== STEP 2: Client Information ==================== --}}
            <div class="step-content" id="step2">
                <section class="surface-card m-2">
                    <div class="section-head">
                        <h2 class="section-title">Your Information</h2>
                        <p class="section-sub">We'll use these details to contact you about your booking.</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name <span class="req">*</span></label>
                            <input type="text" class="form-control" name="client_name" id="clientName"
                                   value="{{ $user ? $user->name : old('client_name', '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address <span class="req">*</span></label>
                            <input type="email" class="form-control" name="client_email" id="clientEmail"
                                   value="{{ $user ? $user->email : old('client_email', '') }}" required>
                            <div id="emailStatus" class="mt-1"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Contact Number <span class="req">*</span></label>
                            <input type="text" class="form-control" name="client_phone" id="clientPhone"
                                   value="{{ old('client_phone', '') }}" placeholder="e.g. 0912-345-6789" maxlength="13" inputmode="numeric" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Additional Notes / Special Requests</label>
                            <textarea class="form-control" name="notes" id="clientNotes" rows="3"
                                      placeholder="Any special requirements, preferences, or notes...">{{ old('notes', '') }}</textarea>
                        </div>
                    </div>
                </section>

                <div class="step-actions d-flex justify-content-between gap-3 mx-2">
                    <button type="button" class="btn btn-outline-dark rounded-pill" onclick="goToStep(1)">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn btn-dark rounded-pill" onclick="goToStep(3)">
                        Continue <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            {{-- ==================== STEP 3: Event Details ==================== --}}
            <div class="step-content" id="step3">
                <section class="surface-card m-2">
                    <div class="section-head">
                        <h2 class="section-title">Event Details</h2>
                        <p class="section-sub">Tell us when and where your event takes place.</p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Event Type <span class="req">*</span></label>
                            <select class="form-select" name="event_type" id="eventType" required>
                                <option value="">Select event type</option>
                                <option value="Wedding" {{ old('event_type') === 'Wedding' ? 'selected' : '' }}>Wedding</option>
                                <option value="Debut" {{ old('event_type') === 'Debut' ? 'selected' : '' }}>Debut</option>
                                <option value="Birthday" {{ old('event_type') === 'Birthday' ? 'selected' : '' }}>Birthday</option>
                                <option value="Corporate Event" {{ old('event_type') === 'Corporate Event' ? 'selected' : '' }}>Corporate Event</option>
                                <option value="Other Events" {{ old('event_type') === 'Other Events' ? 'selected' : '' }}>Other Events</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Event Date <span class="req">*</span></label>
                            <input type="date" class="form-control" name="event_date" id="eventDate"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   value="{{ old('event_date', '') }}" required oninput="checkDateAvailability()" onchange="checkDateAvailability()">
                            <div id="dateStatus" class="mt-1"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Event Start Time <span class="req">*</span></label>
                            <input type="time" class="form-control" name="event_time" id="eventTime"
                                   value="{{ old('event_time', '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Event Venue <span class="req">*</span></label>
                            <input type="text" class="form-control" name="event_venue" id="eventVenue"
                                   value="{{ old('event_venue', '') }}" placeholder="e.g. Grand Ballroom" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Complete Venue Address <span class="req">*</span></label>
                            <input type="text" class="form-control" name="event_address" id="eventAddress"
                                   value="{{ old('event_address', '') }}" placeholder="Full address of the venue" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Event Description / Special Instructions</label>
                            <textarea class="form-control" name="event_description" id="eventDescription" rows="3"
                                      placeholder="Describe the event, special moments to capture, or any specific instructions...">{{ old('event_description', '') }}</textarea>
                        </div>
                    </div>
                </section>

                <div id="inventoryAlert" class="mt-3" style="display: none;">
                    <div class="alert alert-soft alert-soft-warning d-flex align-items-start">
                        <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
                        <div>
                            <strong>Availability Notice</strong>
                            <p class="mb-0 mt-1" id="inventoryAlertMessage"></p>
                        </div>
                    </div>
                </div>

                <div class="step-actions d-flex justify-content-between gap-3 mx-2">
                    <button type="button" class="btn btn-outline-dark rounded-pill" onclick="goToStep(2)">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn btn-dark rounded-pill" onclick="goToStep(4)">
                        Continue <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            {{-- ==================== STEP 4: Booking Confirmation ==================== --}}
            <div class="step-content" id="step4">

                {{-- Package Details --}}
                <section class="confirm-card mx-2">
                    <h6 class="confirm-card-title">Package Information</h6>
                    <div class="confirm-fields">
                        <div class="confirm-field">
                            <span class="confirm-label">Selected Package</span>
                            <span class="confirm-value" id="confirmPackageName">-</span>
                        </div>
                        <div class="confirm-field">
                            <span class="confirm-label">Package Price</span>
                            <span class="confirm-value" id="confirmPackagePrice">-</span>
                        </div>
                    </div>
                    <div class="confirm-field" id="confirmServicesRow">
                        <span class="confirm-label">Included Services</span>
                        <div class="confirm-services-pills" id="confirmServices"></div>
                    </div>
                    <div id="confirmAddonsSection" style="display: none;">
                        <div class="confirm-fields" style="margin-top: 0.75rem;">
                            <div class="confirm-field">
                                <span class="confirm-label">Selected Add-Ons</span>
                                <span class="confirm-value" id="confirmAddons">-</span>
                            </div>
                            <div class="confirm-field">
                                <span class="confirm-label">Add-On Charges</span>
                                <span class="confirm-value" id="confirmAddonsPrice">-</span>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Client + Event Info side-by-side --}}
                <div class="confirm-grid">
                    <section class="confirm-card mx-2">
                        <h6 class="confirm-card-title">Client Information</h6>
                        <div class="confirm-fields">
                            <div class="confirm-field">
                                <span class="confirm-label">Name</span>
                                <span class="confirm-value" id="confirmName">-</span>
                            </div>
                            <div class="confirm-field">
                                <span class="confirm-label">Email</span>
                                <span class="confirm-value" id="confirmEmail">-</span>
                            </div>
                            <div class="confirm-field">
                                <span class="confirm-label">Contact Number</span>
                                <span class="confirm-value" id="confirmPhone">-</span>
                            </div>
                        </div>
                    </section>

                    <section class="confirm-card mx-2">
                        <h6 class="confirm-card-title">Event Information</h6>
                        <div class="confirm-fields">
                            <div class="confirm-field">
                                <span class="confirm-label">Event Type</span>
                                <span class="confirm-value" id="confirmEventType">-</span>
                            </div>
                            <div class="confirm-field">
                                <span class="confirm-label">Event Date</span>
                                <span class="confirm-value" id="confirmEventDate">-</span>
                            </div>
                            <div class="confirm-field">
                                <span class="confirm-label">Event Time</span>
                                <span class="confirm-value" id="confirmEventTime">-</span>
                            </div>
                            <div class="confirm-field">
                                <span class="confirm-label">Venue</span>
                                <span class="confirm-value" id="confirmVenue">-</span>
                            </div>
                            <div class="confirm-field" id="confirmAddressEventRow">
                                <span class="confirm-label">Venue Address</span>
                                <span class="confirm-value" id="confirmEventAddress">-</span>
                            </div>
                            <div class="confirm-field" id="confirmDescRow">
                                <span class="confirm-label">Description</span>
                                <span class="confirm-value" id="confirmEventDesc">-</span>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- Payment Breakdown --}}
                <section class="confirm-card mx-2">
                    <h6 class="confirm-card-title">Payment Summary</h6>
                    <div class="confirm-fields">
                        <div class="confirm-field">
                            <span class="confirm-label">Downpayment (30%)</span>
                            <span class="confirm-value" id="confirmDownpayment">&#8369;0.00</span>
                        </div>
                        <div class="confirm-field">
                            <span class="confirm-label">Remaining Balance</span>
                            <span class="confirm-value" id="confirmBalance">&#8369;0.00</span>
                        </div>
                    </div>

                    <div class="next-steps">
                        <div class="next-steps-title">Next Steps</div>
                        <ol>
                            <li>Submit your booking request</li>
                            <li>Wait for admin approval</li>
                            <li>Pay the 30% downpayment</li>
                            <li>Receive booking confirmation</li>
                        </ol>
                    </div>
                </section>

                <section class="surface-card mx-2">
                    <div class="form-check terms-check">
                        <input class="form-check-input" type="checkbox" id="termsCheck" onchange="toggleTerms()">
                        <label class="form-check-label" for="termsCheck">
                            I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal"><strong>Terms and Conditions</strong></a>
                            and confirm that all information provided are correct.
                        </label>
                    </div>
                </section>

                <div class="step-actions d-flex justify-content-between gap-3 mx-2">
                    <button type="button" class="btn btn-outline-dark rounded-pill" onclick="goToStep(3)">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn btn-dark rounded-pill" id="submitBookingBtn" disabled onclick="startOtpVerification()">
                        <i class="bi bi-shield-lock me-1"></i> Verify &amp; Submit Booking
                    </button>
                </div>
            </div>
        </form>
    </main>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="booking-footer">
        <div class="container text-center">
            <small>&copy; {{ date('Y') }} HarvyMance Films. All rights reserved.</small>
        </div>
    </footer>

    {{-- ==================== MODALS ==================== --}}
    @include('partials.booking-modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let lastScrollY = window.scrollY;
        const navbar = document.querySelector('.landing-navbar');
        const bookingNav = document.querySelector('.booking-nav');

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;

            navbar.classList.toggle('scrolled', currentScrollY > 10);

            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                navbar.classList.add('nav-hidden');
                bookingNav.classList.add('nav-hidden');
            } else {
                navbar.classList.remove('nav-hidden');
                bookingNav.classList.remove('nav-hidden');
            }

            lastScrollY = currentScrollY;
        }, { passive: true });
    </script>

    <script src="{{ asset('js/booking.js') }}"></script>
</body>

</html>
