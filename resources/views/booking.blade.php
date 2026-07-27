<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Now - HarvyMance Films</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    <div class="booking-hero text-center">
        <div class="container">
            <h1><i class="bi bi-camera-video me-2"></i>Book Your Event</h1>
            <p class="mb-0">Capture every moment with our professional photography and videography services</p>
        </div>
    </div>

    <div class="booking-nav shadow-sm">
        <div class="container">
            <div class="stepper" id="stepper">
                <div class="stepper-step active" data-step="1">
                    <div class="stepper-number">1</div>
                    <span class="stepper-label d-none d-md-inline">Package</span>
                </div>
                <div class="stepper-connector"></div>
                <div class="stepper-step" data-step="2">
                    <div class="stepper-number">2</div>
                    <span class="stepper-label d-none d-md-inline">Your Info</span>
                </div>
                <div class="stepper-connector"></div>
                <div class="stepper-step" data-step="3">
                    <div class="stepper-number">3</div>
                    <span class="stepper-label d-none d-md-inline">Event Details</span>
                </div>
                <div class="stepper-connector"></div>
                <div class="stepper-step" data-step="4">
                    <div class="stepper-number">4</div>
                    <span class="stepper-label d-none d-md-inline">Confirmation</span>
                </div>
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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h5 class="mb-3 fw-bold">
                            <i class="bi bi-box-seam me-2"></i>Select Your Package
                        </h5>

                        <div class="row g-3 mb-4">
                            @forelse($packages as $pkg)
                                <div class="col-md-6">
                                    <div class="package-card" data-package-id="{{ $pkg->id }}" data-price="{{ $pkg->price }}" onclick="selectPackage(this)">
                                        <div class="check-indicator">
                                            <i class="bi bi-check-lg d-none"></i>
                                        </div>
                                        <h6 class="fw-bold mb-1">{{ $pkg->name }}</h6>
                                        <div class="package-price mb-2">&#8369;{{ number_format($pkg->price, 2) }}</div>
                                        @if($pkg->description)
                                            <p class="text-muted small mb-2">{{ $pkg->description }}</p>
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
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-box-seam fs-1 d-block mb-2"></i>
                                        No packages available at the moment. Please check back later.
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        @if($addons->count() > 0)
                            <h5 class="mb-3 fw-bold">
                                <i class="bi bi-plus-circle me-2"></i>Add-Ons
                                <small class="text-muted fw-normal" style="font-size: 0.8rem;">(Optional)</small>
                            </h5>
                            <div class="row g-3">
                                @foreach($addons as $addon)
                                    <div class="col-md-6">
                                        <div class="addon-card" data-addon-id="{{ $addon->id }}" data-price="{{ $addon->price }}">
                                            <div class="form-check">
                                                <input class="form-check-input addon-checkbox" type="checkbox"
                                                       value="{{ $addon->id }}" id="addon_{{ $addon->id }}"
                                                       onchange="toggleAddon(this)">
                                                <label class="form-check-label w-100" for="addon_{{ $addon->id }}">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div>
                                                            <div class="fw-semibold">{{ $addon->name }}</div>
                                                            @if($addon->description)
                                                                <small class="text-muted">{{ Str::limit($addon->description, 60) }}</small>
                                                            @endif
                                                        </div>
                                                        <span class="fw-bold text-nowrap ms-2">&#8369;{{ number_format($addon->price, 2) }}</span>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-4">
                        <div class="price-summary" id="priceSummary">
                            <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2"></i>Price Summary</h6>
                            <div id="summaryPackage" class="summary-row" style="display: none;">
                                <span>Package</span>
                                <span id="summaryPackageName">-</span>
                            </div>
                            <div id="summaryPackagePrice" class="summary-row" style="display: none;">
                                <span class="ps-3 text-white-50 small" id="summaryPackagePriceLabel">-</span>
                                <span id="summaryPackagePriceValue">-</span>
                            </div>
                            <div id="summaryAddonsContainer">
                                <div id="summaryAddonsHeader" class="summary-row text-white-50" style="display: none;">
                                    <span>Add-Ons</span>
                                    <span></span>
                                </div>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <span id="summaryTotal">&#8369;0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-next" onclick="goToStep(2)" id="step1Next">
                        Continue <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            {{-- ==================== STEP 2: Customer Information ==================== --}}
            <div class="step-content" id="step2">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h5 class="mb-3 fw-bold">
                            <i class="bi bi-person me-2"></i>Your Information
                        </h5>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="client_name" id="clientName"
                                               value="{{ $user ? $user->name : old('client_name', '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="client_email" id="clientEmail"
                                               value="{{ $user ? $user->email : old('client_email', '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Contact Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="client_phone" id="clientPhone"
                                               value="{{ old('client_phone', '') }}" placeholder="e.g. 09171234567" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Additional Notes / Special Requests</label>
                                        <textarea class="form-control" name="notes" id="clientNotes" rows="3"
                                                  placeholder="Any special requirements, preferences, or notes...">{{ old('notes', '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="price-summary">
                            <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2"></i>Booking Summary</h6>
                            <div class="summary-row">
                                <span>Package</span>
                                <span id="summary2PackageName">-</span>
                            </div>
                            <div class="summary-row">
                                <span>Add-Ons</span>
                                <span id="summary2AddonsCount">0</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <span id="summary2Total">&#8369;0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-back" onclick="goToStep(1)">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn btn-next" onclick="goToStep(3)">
                        Continue <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            {{-- ==================== STEP 3: Event Details ==================== --}}
            <div class="step-content" id="step3">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h5 class="mb-3 fw-bold">
                            <i class="bi bi-calendar-event me-2"></i>Event Details
                        </h5>
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Event Type <span class="text-danger">*</span></label>
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
                                        <label class="form-label">Event Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="event_date" id="eventDate"
                                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                               value="{{ old('event_date', '') }}" required onchange="checkDateAvailability()">
                                        <div id="dateStatus" class="mt-1"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Event Start Time <span class="text-danger">*</span></label>
                                        <input type="time" class="form-control" name="event_time" id="eventTime"
                                               value="{{ old('event_time', '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Event Venue <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="event_venue" id="eventVenue"
                                               value="{{ old('event_venue', '') }}" placeholder="e.g. Grand Ballroom" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Complete Venue Address</label>
                                        <input type="text" class="form-control" name="event_address" id="eventAddress"
                                               value="{{ old('event_address', '') }}" placeholder="Full address of the venue">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Event Description / Special Instructions</label>
                                        <textarea class="form-control" name="event_description" id="eventDescription" rows="3"
                                                  placeholder="Describe the event, special moments to capture, or any specific instructions...">{{ old('event_description', '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="inventoryAlert" class="mt-3" style="display: none;">
                            <div class="alert alert-warning d-flex align-items-start">
                                <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                                <div>
                                    <strong>Availability Notice</strong>
                                    <p class="mb-0 mt-1" id="inventoryAlertMessage"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="price-summary">
                            <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2"></i>Booking Summary</h6>
                            <div class="summary-row">
                                <span>Package</span>
                                <span id="summary3PackageName">-</span>
                            </div>
                            <div class="summary-row">
                                <span>Add-Ons</span>
                                <span id="summary3AddonsCount">0</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total</span>
                                <span id="summary3Total">&#8369;0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-back" onclick="goToStep(2)">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn btn-next" onclick="goToStep(4)">
                        Continue <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </div>
            </div>

            {{-- ==================== STEP 4: Booking Confirmation ==================== --}}
            <div class="step-content" id="step4">
                <div class="row g-4">
                    <div class="col-lg-8">
                        <h5 class="mb-3 fw-bold">
                            <i class="bi bi-clipboard-check me-2"></i>Booking Confirmation
                        </h5>

                        <div class="confirmation-section">
                            <h6><i class="bi bi-box-seam me-2"></i>Package Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Selected Package</span>
                                <span class="detail-value" id="confirmPackageName">-</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Package Price</span>
                                <span class="detail-value" id="confirmPackagePrice">-</span>
                            </div>
                            <div class="detail-row" id="confirmServicesRow">
                                <span class="detail-label">Included Services</span>
                                <span class="detail-value" id="confirmServices">-</span>
                            </div>
                            <div id="confirmAddonsSection" style="display: none;">
                                <hr class="my-2">
                                <div class="detail-row">
                                    <span class="detail-label">Selected Add-Ons</span>
                                    <span class="detail-value" id="confirmAddons">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Add-On Charges</span>
                                    <span class="detail-value" id="confirmAddonsPrice">-</span>
                                </div>
                            </div>
                        </div>

                        <div class="confirmation-section">
                            <h6><i class="bi bi-person me-2"></i>Customer Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Name</span>
                                <span class="detail-value" id="confirmName">-</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Email</span>
                                <span class="detail-value" id="confirmEmail">-</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Contact Number</span>
                                <span class="detail-value" id="confirmPhone">-</span>
                            </div>
                        </div>

                        <div class="confirmation-section">
                            <h6><i class="bi bi-calendar-event me-2"></i>Event Information</h6>
                            <div class="detail-row">
                                <span class="detail-label">Event Type</span>
                                <span class="detail-value" id="confirmEventType">-</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Event Date</span>
                                <span class="detail-value" id="confirmEventDate">-</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Event Time</span>
                                <span class="detail-value" id="confirmEventTime">-</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Venue</span>
                                <span class="detail-value" id="confirmVenue">-</span>
                            </div>
                            <div class="detail-row" id="confirmAddressEventRow">
                                <span class="detail-label">Venue Address</span>
                                <span class="detail-value" id="confirmEventAddress">-</span>
                            </div>
                            <div class="detail-row" id="confirmDescRow">
                                <span class="detail-label">Description</span>
                                <span class="detail-value" id="confirmEventDesc">-</span>
                            </div>
                        </div>

                        <div class="confirmation-section">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="termsCheck" onchange="toggleTerms()">
                                <label class="form-check-label" for="termsCheck">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal"><strong>Terms and Conditions</strong></a>
                                    and confirm that all information provided are correct.
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="payment-summary-card">
                            <h6 class="fw-bold mb-3"><i class="bi bi-credit-card me-2"></i>Payment Summary</h6>
                            <div class="payment-row">
                                <span>Total Amount</span>
                                <span id="confirmTotalAmount">&#8369;0.00</span>
                            </div>
                            <div class="payment-row downpayment">
                                <span>Downpayment (30%)</span>
                                <span id="confirmDownpayment">&#8369;0.00</span>
                            </div>
                            <div class="payment-row balance">
                                <span>Remaining Balance</span>
                                <span id="confirmBalance">&#8369;0.00</span>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-white rounded shadow-sm">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                <strong>Next Steps:</strong><br>
                                1. Submit your booking request<br>
                                2. Wait for admin approval<br>
                                3. Pay the 30% downpayment<br>
                                4. Receive booking confirmation
                            </small>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-back" onclick="goToStep(3)">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </button>
                    <button type="button" class="btn btn-submit-booking" id="submitBookingBtn" disabled onclick="startOtpVerification()">
                        <i class="bi bi-shield-lock me-1"></i> Verify & Submit Booking
                    </button>
                </div>
            </div>
        </form>
    </div>

    <footer class="py-4 mt-4" style="background: #1a1a2e; color: rgba(255,255,255,0.7);">
        <div class="container text-center">
            <small>&copy; {{ date('Y') }} HarvyMance Films. All rights reserved.</small>
        </div>
    </footer>

    <div class="modal fade" id="otpModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-shield-lock me-2"></i>Email Verification</h5>
                </div>
                <div class="modal-body text-center">
                    <div id="otpSendingState">
                        <div class="spinner-border text-primary mb-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mb-0">Sending verification code to your email...</p>
                    </div>
                    <div id="otpInputState" style="display: none;">
                        <p class="text-muted mb-3">A 6-digit verification code has been sent to<br><strong id="otpEmailDisplay"></strong></p>
                        <div class="mb-3">
                            <input type="text" class="form-control form-control-lg text-center"
                                   id="otpInput" maxlength="6" placeholder="000000"
                                   style="font-size: 1.5rem; letter-spacing: 8px; font-weight: bold;"
                                   autocomplete="off">
                        </div>
                        <div id="otpError" class="alert alert-danger py-2 small" style="display: none;"></div>
                        <div id="otpSuccess" class="alert alert-success py-2 small" style="display: none;"></div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted small" id="otpCountdown">Expires in 5:00</span>
                            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" id="otpResendBtn" onclick="resendOtp()" disabled>
                                Resend Code
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between" id="otpFooter" style="display: none;">
                    <button type="button" class="btn btn-secondary" onclick="cancelOtp()">Cancel</button>
                    <button type="button" class="btn btn-dark" id="otpVerifyBtn" onclick="verifyOtp()" disabled>
                        <i class="bi bi-check-circle me-1"></i> Verify
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-file-text me-2"></i>Terms and Conditions</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Booking & Reservation</h6>
                    <p>All bookings are subject to availability and admin approval. A booking is not confirmed until you receive a confirmation notification.</p>

                    <h6>2. Downpayment</h6>
                    <p>A 30% downpayment is required to secure your booking. The downpayment must be paid within the deadline specified in your booking confirmation.</p>

                    <h6>3. Cancellation Policy</h6>
                    <p>Cancellations made 7 days before the event will receive a full refund of the downpayment. Cancellations within 7 days of the event will forfeit the downpayment.</p>

                    <h6>4. Rescheduling</h6>
                    <p>Rescheduling requests are subject to availability. Please contact us at least 48 hours before your original event date.</p>

                    <h6>5. Equipment & Materials</h6>
                    <p>Required equipment and materials will be automatically reserved upon booking approval. Any damage to rented equipment will be charged accordingly.</p>

                    <h6>6. Deliverables</h6>
                    <p>Final edited photos and videos will be delivered within the timeframe specified in your selected package. Raw files are not included unless specified.</p>

                    <h6>7. Liability</h6>
                    <p>HarvyMance Films is not liable for events beyond our control, including natural disasters, power outages, or other force majeure events.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/booking.js') }}"></script>
</body>

</html>
