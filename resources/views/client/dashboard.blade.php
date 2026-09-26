<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booking - HarvyMance Films</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/invoice.css') }}">
</head>

<body>

    {{-- ==================== NAVBAR ==================== --}}
    <nav class="navbar booking-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ route('client.dashboard') }}" class="d-inline-flex align-items-center">
                <img src="{{ asset('storage/images/Black Logo.png') }}" alt="HarvyMance Films" height="34">
            </a>
            <span class="fw-semibold">{{ $account->client_name }}</span>
        </div>
    </nav>

    <main class="container py-4 client-dashboard">

        @if($daysUntilDeletion !== null && $deleteAt !== null)
            @php
                $bannerTitle = $daysUntilDeletion <= 3 ? 'Final Warning' : ($daysUntilDeletion <= 7 ? 'Access Ending Soon' : 'Download Window');
            @endphp
            <div class="download-window-banner mb-4">
                <div class="fw-semibold">{{ $bannerTitle }}</div>
                <div class="small">
                    You have access to your deliverables until <strong>{{ $deleteAt->format('M d, Y') }}</strong> ({{ $daysUntilDeletion }} day(s) left). After this date, your account will be archived and you can no longer log in.
                </div>
            </div>
        @endif

        {{-- ==================== HEADER ==================== --}}
        @php
            $statusBadgeClass = match($booking->status) {
                'pending' => 'bg-warning text-dark',
                'approved' => 'bg-success',
                'ongoing' => 'bg-primary',
                'completed' => 'bg-secondary',
                'rejected' => 'bg-danger',
                'cancelled' => 'bg-danger',
                default => 'bg-secondary',
            };
        @endphp
        <section class="surface-card mb-4 client-header">
            <div class="client-header-top">
                <div class="client-header-id">
                    <div class="client-header-name">
                        <h1 class="section-title">{{ $account->client_name }}</h1>
                        <span class="badge {{ $statusBadgeClass }}">{{ ucfirst($booking->status) }}</span>
                    </div>
                    <div class="client-meta">
                        <div class="client-meta-ref">{{ $booking->booking_ref }}</div>
                        <div class="client-meta-item">
                            <i class="bi bi-calendar-event"></i>
                            <span>{{ $booking->event_date->format('l, M d, Y') }}</span>
                        </div>
                        @if($booking->team)
                            <div class="client-meta-item">
                                <i class="bi bi-people"></i>
                                <span>{{ $booking->team->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="client-header-total">
                    <span class="client-header-total-label">Total Amount</span>
                    <span class="client-header-total-value">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                </div>
            </div>
        </section>

        <div class="row g-4">

            {{-- ==================== LEFT COLUMN ==================== --}}
            <div class="col-lg-8">

                {{-- BOOKING PROGRESS --}}
                <section class="surface-card mb-4">
                    <div class="section-head">
                        <h2 class="section-title"><i class="bi bi-signpost-split me-2"></i>Booking Progress</h2>
                    </div>
                    @php
                        $timelineSteps = [
                            ['key' => 'submitted',     'label' => 'Booking Submitted',       'desc' => 'Your booking request has been received.',               'icon' => 'bi-send'],
                            ['key' => 'review',        'label' => 'Admin Review',             'desc' => 'Our team is reviewing your booking request.',            'icon' => 'bi-search'],
                            ['key' => 'approved',      'label' => 'Booking Approved',         'desc' => 'Your booking has been approved by our admin.',           'icon' => 'bi-check-circle'],
                            ['key' => 'downpayment',   'label' => 'Awaiting Downpayment',     'desc' => 'Please pay the 30% downpayment to proceed.',            'icon' => 'bi-credit-card'],
                            ['key' => 'payment_submitted', 'label' => 'Payment Submitted',  'desc' => 'Your payment proof has been submitted.',                'icon' => 'bi-upload'],
                            ['key' => 'payment_verified',  'label' => 'Payment Verified',   'desc' => 'Your payment has been verified by admin.',              'icon' => 'bi-check2-all'],
                            ['key' => 'scheduled',     'label' => 'Event Scheduled',          'desc' => 'Your event is scheduled. Team has been assigned.',       'icon' => 'bi-calendar-check'],
                            ['key' => 'completed',     'label' => 'Event Completed',          'desc' => 'The event coverage has been completed.',                 'icon' => 'bi-camera-video'],
                            ['key' => 'postprod',      'label' => 'Post-Production',          'desc' => 'Your photos/videos are being edited.',                  'icon' => 'bi-film'],
                            ['key' => 'delivered',     'label' => 'Final Delivery',           'desc' => 'Your deliverables are ready for pickup/download.',      'icon' => 'bi-box-seam'],
                        ];

                        $statusMap = [
                            'pending'   => 'review',
                            'approved'  => 'approved',
                            'ongoing'   => 'scheduled',
                            'completed' => 'completed',
                            'rejected'  => null,
                            'cancelled' => null,
                        ];

                        $currentKey = $statusMap[$booking->status] ?? 'submitted';

                        if ($booking->status === 'completed' && $booking->postProduction) {
                            $ppMap = [
                                'editing'       => 'postprod',
                                'in_progress'   => 'postprod',
                                'quality_check' => 'postprod',
                                'ready'         => 'delivered',
                                'delivered'     => 'delivered',
                            ];
                            $currentKey = $ppMap[$booking->postProduction->status] ?? 'completed';
                        }

                        if ($booking->status === 'pending') {
                            $currentKey = $booking->created_at->diffInHours(now()) > 2 ? 'review' : 'submitted';
                        }

                        if ($booking->status === 'approved') {
                            $dp = $latestDownpayment;
                            if ($dp) {
                                if ($dp->status === 'pending') {
                                    $currentKey = 'payment_submitted';
                                } elseif ($dp->status === 'verified') {
                                    $currentKey = 'payment_verified';
                                } else {
                                    $currentKey = 'downpayment';
                                }
                            } else {
                                $currentKey = 'downpayment';
                            }
                        }

                        $currentIdx = collect($timelineSteps)->pluck('key')->search($currentKey);
                        if ($currentIdx === false) $currentIdx = 0;
                    @endphp

                    @if(in_array($booking->status, ['rejected', 'cancelled']))
                        <div class="text-center py-4">
                            <i class="bi {{ $booking->status === 'rejected' ? 'bi-x-circle text-danger' : 'bi-x-octagon text-warning' }} fs-1 d-block mb-2"></i>
                            <h6 class="fw-bold {{ $booking->status === 'rejected' ? 'text-danger' : 'text-warning' }}">
                                Booking {{ ucfirst($booking->status) }}
                            </h6>
                            @if($booking->status === 'rejected' && $booking->rejection_reason)
                                <p class="text-muted small mt-2 mb-0">
                                    <strong>Reason:</strong> {{ $booking->rejection_reason }}
                                </p>
                            @endif
                        </div>
                    @else
                        <ul class="timeline">
                            @foreach($timelineSteps as $idx => $step)
                                @php
                                    $state = 'pending';
                                    if ($idx < $currentIdx) $state = 'completed';
                                    elseif ($idx === $currentIdx) $state = 'active';
                                @endphp
                                <li class="timeline-item {{ $state }}">
                                    <div class="timeline-dot {{ $state }}">
                                        @if($state === 'completed')
                                            <i class="bi bi-check-lg"></i>
                                        @else
                                            <i class="bi {{ $step['icon'] }}"></i>
                                        @endif
                                    </div>
                                    <div class="timeline-content">
                                        <h6>{{ $step['label'] }}</h6>
                                        <p>{{ $step['desc'] }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- YOUR BOOKING --}}
                <section class="surface-card mb-4 client-booking-details">
                    <div class="section-head">
                        <h2 class="section-title"><i class="bi bi-journal-check me-2"></i>Your Booking</h2>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="small text-uppercase text-muted fw-semibold mb-1">Package & Add-Ons</h6>
                            <div class="detail-row">
                                <span class="detail-label">Package</span>
                                <span class="detail-value">{{ $booking->package->name ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Package Price</span>
                                <span class="detail-value">&#8369;{{ number_format($booking->package->price ?? 0, 2) }}</span>
                            </div>
                            @if($booking->package && $booking->package->services->count() > 0)
                                <div class="detail-row">
                                    <span class="detail-label">Included Services</span>
                                    <span class="detail-value">
                                        @foreach($booking->package->services as $service)
                                            <span class="badge bg-light text-dark border">{{ $service->service_name }}</span>
                                        @endforeach
                                    </span>
                                </div>
                            @endif
                            @if($booking->addons->count() > 0)
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
                                    <span class="detail-value">&#8369;{{ number_format($booking->addons->sum('pivot.price'), 2) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <h6 class="small text-uppercase text-muted fw-semibold mb-1">Event</h6>
                            @if($booking->event_type)
                                <div class="detail-row">
                                    <span class="detail-label">Type</span>
                                    <span class="detail-value">{{ $booking->event_type }}</span>
                                </div>
                            @endif
                            <div class="detail-row">
                                <span class="detail-label">Date</span>
                                <span class="detail-value">{{ $booking->event_date->format('l, F d, Y') }}</span>
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
                            @if($booking->event_address)
                                <div class="detail-row">
                                    <span class="detail-label">Venue Address</span>
                                    <span class="detail-value">{{ $booking->event_address }}</span>
                                </div>
                            @endif
                            @if($booking->event_description)
                                <div class="detail-row">
                                    <span class="detail-label">Special Requests</span>
                                    <span class="detail-value">{{ $booking->event_description }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>

                {{-- POST-PRODUCTION & DELIVERABLES --}}
                @if($booking->postProduction)
                    <section class="surface-card mb-4">
                        <div class="section-head">
                            <h2 class="section-title"><i class="bi bi-film me-2"></i>Deliverables</h2>
                        </div>
                        @php
                            $postProduction = $booking->postProduction;
                            $ppTasks = $postProduction->tasks;
                            $totalTasks = $ppTasks->count();
                            $approvedTasks = $ppTasks->where('admin_review_status', 'approved')->count();
                            $allApproved = $totalTasks > 0 && $approvedTasks === $totalTasks;
                            $deliverablesReady = $booking->deliverables_unlocked;
                        @endphp

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="summary-card h-100 text-center">
                                    <div class="small text-muted mb-1">Status</div>
                                    <div>
                                        @if($postProduction->status === 'delivered')
                                            <span class="badge bg-success rounded-pill">Delivered</span>
                                        @elseif($postProduction->status === 'ready')
                                            <span class="badge bg-info rounded-pill">Ready</span>
                                        @else
                                            <span class="badge bg-warning text-dark rounded-pill">In Progress</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="summary-card h-100 text-center">
                                    <div class="small text-muted mb-1">Tasks</div>
                                    <div>
                                        <span class="fw-semibold">{{ $approvedTasks }}/{{ $totalTasks }}</span>
                                        <span class="text-muted small"> approved</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="summary-card h-100 text-center">
                                    <div class="small text-muted mb-1">File Access</div>
                                    <div>
                                        @if($deliverablesReady && $hasFinalPayment)
                                            <span class="badge bg-success rounded-pill">Unlocked</span>
                                        @else
                                            <span class="badge bg-secondary rounded-pill">Locked</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-md-3">
                                <div class="summary-card h-100 text-center">
                                    <div class="small text-muted mb-1">Payment</div>
                                    <div>
                                        @if($hasFinalPayment)
                                            <span class="badge bg-success rounded-pill">Fully Paid</span>
                                        @else
                                            <span class="badge bg-danger rounded-pill">Balance Due</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($postProduction->progress_notes)
                            <div class="detail-row">
                                <span class="detail-label">Latest Update</span>
                                <span class="detail-value">{{ $postProduction->progress_notes }}</span>
                            </div>
                        @endif

                        @if(!$deliverablesReady && $allApproved && $hasFinalPayment)
                            <div class="alert alert-soft py-2 mb-3 mt-2">
                                <i class="bi bi-info-circle me-1"></i>Your deliverables are ready. Waiting for admin to unlock files.
                            </div>
                        @elseif(!$deliverablesReady && $allApproved && !$hasFinalPayment)
                            <div class="alert alert-soft py-2 mb-3 mt-2">
                                <i class="bi bi-info-circle me-1"></i>Please settle your remaining balance to unlock your deliverables.
                            </div>
                        @endif

                        @if($deliverablesReady && $hasFinalPayment)
                            <div class="row g-2">
                                @foreach($ppTasks->where('admin_review_status', 'approved') as $task)
                                    <div class="col-12">
                                        <div class="border rounded p-3 h-100 d-flex flex-column">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <span class="fw-semibold">{{ $task->task_type === 'both' ? 'Photos and Videos' : str_replace('_', ' ', ucfirst($task->task_type)) }}</span>
                                                <span class="small text-success fw-semibold"><i class="bi bi-check-circle me-1"></i>Ready to download</span>
                                            </div>
                                            <a href="{{ $task->deliverable_link }}" target="_blank" class="btn btn-primary-dark rounded-pill w-100">
                                                <i class="bi bi-download me-1"></i>Download
                                            </a>
                                            @if($postProduction->expected_completion_date)
                                                <div class="d-flex align-items-center justify-content-between small text-muted border-top mt-3 pt-2">
                                                    <span>Expected Completion</span>
                                                    <span class="fw-semibold text-dark">{{ $postProduction->expected_completion_date->format('M d, Y') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif

            </div>

            {{-- ==================== RIGHT COLUMN ==================== --}}
            <div class="col-lg-4 sticky-side">

                {{-- PAYMENTS --}}
                <section class="surface-card mb-4">
                    <div class="section-head">
                        <h2 class="section-title"><i class="bi bi-credit-card me-2"></i>Payment</h2>
                    </div>
                    <div class="payment-row">
                        <span>Total Amount</span>
                        <span class="fw-bold">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                    </div>
                    <div class="payment-row">
                        <span>Downpayment (30%)</span>
                        <span>&#8369;{{ number_format($booking->downpayment_amount, 2) }}</span>
                    </div>
                    <div class="payment-row">
                        <span>Final Payment (70%)</span>
                        <span class="fw-bold text-danger">&#8369;{{ number_format($booking->total_price - $booking->downpayment_amount, 2) }}</span>
                    </div>
                    <hr class="soft-divider">

                    <button type="button" class="btn btn-outline-dark-soft w-100 rounded-pill btn-action" data-bs-toggle="modal" data-bs-target="#invoiceModal">
                        <i class="bi bi-receipt me-2"></i>View Invoice
                    </button>

                    {{-- DOWNPAYMENT STATUS --}}
                    @if($latestDownpayment)
                        <div class="d-flex align-items-center justify-content-between mb-2 mt-3">
                            <small class="text-muted">Downpayment</small>
                            @if($latestDownpayment->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending Verification</span>
                            @elseif($latestDownpayment->status === 'verified')
                                <span class="badge bg-success">Verified</span>
                            @elseif($latestDownpayment->status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </div>
                        @if($latestDownpayment->submitted_at)
                            <div class="small text-muted mb-2">
                                Submitted: {{ $latestDownpayment->submitted_at->format('M d, Y g:i A') }}
                            </div>
                        @endif
                        @if($latestDownpayment->rejection_reason)
                            <div class="rejection-reason-box mt-3 mb-2">
                                <div class="text-danger fw-bold small mb-1">
                                    <i class="bi bi-x-circle me-1"></i>Rejection Reason
                                </div>
                                <p class="mb-0 small">{{ $latestDownpayment->rejection_reason }}</p>
                            </div>
                        @endif
                        @if($latestDownpayment->status === 'rejected')
                            <button type="button" class="btn btn-warning w-100 rounded-pill mb-3 btn-action" data-bs-toggle="modal" data-bs-target="#downpaymentModal">
                                <i class="bi bi-arrow-repeat me-1"></i>Resubmit Downpayment
                            </button>
                        @endif
                    @elseif($booking->status === 'approved')
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Downpayment</small>
                            <span class="badge bg-warning text-dark">Awaiting</span>
                        </div>
                        <button type="button" class="btn btn-primary-dark w-100 rounded-pill mb-3 btn-action" data-bs-toggle="modal" data-bs-target="#downpaymentModal">
                            <i class="bi bi-credit-card me-1"></i>Submit Downpayment
                        </button>
                    @endif

                    {{-- FINAL PAYMENT --}}
                    @if(in_array($booking->status, ['completed', 'ongoing']))
                        @php
                            $totalPaid = $booking->downpayments()->where('status', 'verified')->sum('amount');
                            $remainingBalance = $booking->total_price - $totalPaid;
                            $latestFinalPayment = $booking->downpayments()
                                ->where('payment_type', 'final')
                                ->latest()
                                ->first();
                        @endphp
                        @if($remainingBalance > 0)
                            <hr class="soft-divider">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Final Payment</small>
                                <span class="fw-bold text-danger">&#8369;{{ number_format($remainingBalance, 2) }}</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <small class="text-muted">Status</small>
                                @if($booking->final_payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($latestFinalPayment && $latestFinalPayment->status === 'pending')
                                    <span class="badge bg-warning text-dark">Pending Verification</span>
                                @elseif($latestFinalPayment && $latestFinalPayment->status === 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @else
                                    <span class="badge bg-secondary">Not Yet Submitted</span>
                                @endif
                            </div>
                            @if($booking->final_payment_status !== 'paid')
                                @if($latestFinalPayment && $latestFinalPayment->status === 'rejected')
                                    <button type="button" class="btn btn-warning w-100 rounded-pill mb-3 btn-action" data-bs-toggle="modal" data-bs-target="#finalPaymentModal">
                                        <i class="bi bi-arrow-repeat me-1"></i>Resubmit Final Payment
                                    </button>
                                @elseif(!$latestFinalPayment || ($latestFinalPayment && $latestFinalPayment->status !== 'pending'))
                                    <button type="button" class="btn btn-primary-dark w-100 rounded-pill mb-3 btn-action" data-bs-toggle="modal" data-bs-target="#finalPaymentModal">
                                        <i class="bi bi-credit-card me-1"></i>Submit Final Payment
                                    </button>
                                @else
                                    <p class="text-muted small mb-3"><i class="bi bi-hourglass-split me-1"></i>Awaiting verification of your final payment.</p>
                                @endif
                            @endif
                        @endif
                    @endif
                </section>

                {{-- ACTIONS --}}
                @php
                    $cancellation = $booking->cancellationRequest;
                    $canCancel = in_array($booking->status, ['pending', 'approved', 'ongoing'])
                        && (!$cancellation || $cancellation->status === 'rejected');

                    $leadTime = (int) \App\Models\Setting::getValue('reschedule_lead_time_days', 5);
                    $daysToEvent = now()->diffInDays($booking->event_date, false);
                    $canReschedule = in_array($booking->status, ['pending', 'approved', 'ongoing'])
                        && $daysToEvent >= $leadTime;
                    $pendingReschedule = $booking->pendingReschedule;
                    $lastReschedule = $booking->rescheduleRequests()->latest()->first();
                @endphp
                <section class="surface-card mb-4">
                    <div class="section-head">
                        <h2 class="section-title"><i class="bi bi-lightning me-2"></i>Actions</h2>
                    </div>
                    <div class="d-grid gap-2">
                        @if($cancellation)
                            @if($cancellation->status === 'pending')
                                <div class="alert alert-soft alert-soft-warning py-2 small mb-0">
                                    <i class="bi bi-hourglass-split me-1"></i><strong>Cancellation Pending:</strong>
                                    @if($cancellation->refund_amount > 0)
                                        Refund of &#8369;{{ number_format($cancellation->refund_amount, 2) }} ({{ $cancellation->refund_percentage }}%) is under review.
                                    @else
                                        No refund applicable. Awaiting admin confirmation.
                                    @endif
                                </div>
                            @elseif($cancellation->status === 'refunded')
                                <div class="alert alert-soft alert-soft-success py-2 small mb-0">
                                    <i class="bi bi-check-circle me-1"></i><strong>Refunded:</strong>
                                    &#8369;{{ number_format($cancellation->refund_amount, 2) }} has been processed.
                                    @if($cancellation->refund_reference)
                                        <br><span class="text-muted">Ref: {{ $cancellation->refund_reference }}</span>
                                    @endif
                                    @if($cancellation->admin_notes)
                                        <br>{{ $cancellation->admin_notes }}
                                    @endif
                                    @if($cancellation->refund_proof)
                                        <br>
                                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill mt-2"
                                            onclick="document.getElementById('refundProofImg').src='{{ Storage::url($cancellation->refund_proof) }}'; new bootstrap.Modal(document.getElementById('refundProofModal')).show()">
                                            <i class="bi bi-file-earmark-image me-1"></i>View Refund Proof
                                        </button>
                                    @endif
                                </div>
                            @elseif($cancellation->status === 'rejected')
                                <div class="alert alert-soft alert-soft-danger py-2 small mb-0">
                                    <i class="bi bi-x-circle me-1"></i><strong>Refund Rejected:</strong> {{ $cancellation->admin_notes }}
                                </div>
                            @endif
                        @endif

                        @if($pendingReschedule)
                            <div class="alert alert-soft alert-soft-warning py-2 small mb-0">
                                <i class="bi bi-hourglass-split me-1"></i>Reschedule request pending admin review.
                            </div>
                        @elseif($lastReschedule && $lastReschedule->status === 'rejected')
                            <div class="alert alert-soft alert-soft-danger py-2 small mb-0">
                                <i class="bi bi-x-circle me-1"></i><strong>Reschedule Rejected:</strong> {{ $lastReschedule->rejection_reason }}
                            </div>
                            @if($canReschedule)
                                <button class="btn btn-outline-warning rounded-pill btn-action" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                                    <i class="bi bi-calendar-event me-2"></i>Request Reschedule
                                </button>
                            @endif
                        @elseif($canReschedule)
                            <button class="btn btn-outline-warning rounded-pill btn-action" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                                <i class="bi bi-calendar-event me-2"></i>Request Reschedule
                            </button>
                        @elseif(in_array($booking->status, ['pending', 'approved', 'ongoing']))
                            <button class="btn btn-outline-secondary rounded-pill btn-action" disabled title="Reschedule must be requested at least {{ $leadTime }} days before the event.">
                                <i class="bi bi-calendar-x me-2"></i>Reschedule Unavailable
                            </button>
                        @endif

                        @if($canCancel)
                            <button class="btn btn-outline-danger rounded-pill btn-action" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle me-2"></i>Cancel Booking
                            </button>
                        @endif

                        <a href="{{ route('client.change-password') }}" class="btn btn-outline-dark-soft rounded-pill btn-action">
                            <i class="bi bi-key me-2"></i>Change Password
                        </a>
                        <button class="btn btn-outline-dark-soft rounded-pill btn-action" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Print Details
                        </button>
                        <form method="POST" action="{{ route('client.logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-pill btn-action w-100">
                                <i class="bi bi-box-arrow-left me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </section>

                {{-- ASSIGNED TEAM --}}
                @if($booking->team)
                    <section class="surface-card mb-4">
                        <div class="section-head">
                            <h2 class="section-title"><i class="bi bi-people me-2"></i>Assigned Team</h2>
                        </div>
                        <div class="mb-2">
                            <span class="fw-semibold">{{ $booking->team->name }}</span>
                        </div>
                        @if($booking->team->members->count() > 0 || $booking->team->outsourcedMembers->count() > 0)
                            <div>
                                @foreach($booking->team->members as $member)
                                    <span class="team-member-chip">
                                        <span class="avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                        {{ $member->name }}
                                    </span>
                                @endforeach
                                @foreach($booking->team->outsourcedMembers as $os)
                                    <span class="team-member-chip os-chip">
                                        <span class="avatar">{{ strtoupper(substr($os->name, 0, 1)) }}</span>
                                        {{ $os->name }} <small>(OS)</small>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </section>
                @endif

            </div>
        </div>
    </main>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="booking-footer">
        <div class="container text-center">
            <small>&copy; {{ date('Y') }} HarvyMance Films. All rights reserved.</small>
        </div>
    </footer>

    {{-- MODALS --}}
    @include('client.partials.downpayment-modal')
    @include('client.partials.final-payment-modal')
    @include('client.partials.cancel-modal')
    @include('client.partials.reschedule-modal')
    @include('client.partials.refund-proof-modal')
    @include('client.partials.invoice-modal')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="{{ asset('js/client-downpayment.js') }}"></script>
    <script src="{{ asset('js/client-dashboard.js') }}"></script>
    <script src="{{ asset('js/invoice.js') }}"></script>
    @if($errors->has('amount') || $errors->has('payment_proof'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var target = {{ old('payment_context') === 'final' ? "'finalPaymentModal'" : "'downpaymentModal'" }};
                new bootstrap.Modal(document.getElementById(target)).show();
            });
        </script>
    @endif

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
