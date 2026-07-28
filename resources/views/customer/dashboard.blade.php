<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Booking - HarvyMance Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
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

        @if($daysUntilDeletion !== null)
            @if($daysUntilDeletion <= 3)
                <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
                    <div>
                        <strong>Account Deletion Warning:</strong> Your account will be automatically deleted in <strong>{{ $daysUntilDeletion }} day(s)</strong>.
                        Please download all your deliverables before then.
                    </div>
                </div>
            @elseif($daysUntilDeletion <= 7)
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-clock-history me-3 fs-4"></i>
                    <div>
                        <strong>Reminder:</strong> Your account will be automatically deleted in <strong>{{ $daysUntilDeletion }} day(s)</strong> after delivery.
                        Make sure to download your files.
                    </div>
                </div>
            @endif
        @endif

        {{-- ==================== OVERVIEW CARDS ==================== --}}
        <div class="row g-3 mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="card overview-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="overview-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">Control Number</small>
                                <div class="fw-bold">{{ $account->control_number }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card overview-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="overview-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">Booking Status</small>
                                <div class="fw-bold">{{ ucfirst($booking->status) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card overview-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="overview-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">Event Date</small>
                                <div class="fw-bold">{{ $booking->event_date->format('M d, Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card overview-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="overview-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                            <div class="ms-3">
                                <small class="text-muted">Total Amount</small>
                                <div class="fw-bold">&#8369;{{ number_format($booking->total_price, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">

            {{-- ==================== LEFT COLUMN ==================== --}}
            <div class="col-lg-8">

                {{-- BOOKING STATUS TIMELINE --}}
                <div class="card section-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-signpost-split me-2"></i>Booking Progress</h6>
                    </div>
                    <div class="card-body">
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
                                    } elseif ($dp->status === 'rejected') {
                                        $currentKey = 'downpayment';
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
                            <div class="text-center py-3">
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
                    </div>
                </div>

                {{-- POST-PRODUCTION TRACKING --}}
                @if($booking->status === 'completed')
                    @php $postProduction = $booking->postProduction; @endphp
                    @if($postProduction)
                        <div class="card section-card mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-film me-2"></i>Post-Production Tracking</h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $ppSteps = [
                                        ['key' => 'editing',       'label' => 'Editing Started',      'icon' => 'bi-pencil-square'],
                                        ['key' => 'in_progress',   'label' => 'In Progress',           'icon' => 'bi-arrow-repeat'],
                                        ['key' => 'quality_check', 'label' => 'Quality Check',         'icon' => 'bi-shield-check'],
                                        ['key' => 'ready',         'label' => 'Ready for Delivery',    'icon' => 'bi-check2-all'],
                                        ['key' => 'delivered',     'label' => 'Delivered',             'icon' => 'bi-box-seam'],
                                    ];
                                    $ppKeys = collect($ppSteps)->pluck('key')->toArray();
                                    $ppCurrent = $postProduction->status;
                                    $ppIdx = array_search($ppCurrent, $ppKeys);
                                    if ($ppIdx === false) $ppIdx = 0;
                                    $ppPercent = round(($ppIdx / (count($ppSteps) - 1)) * 100);
                                @endphp

                                <div class="progress-track mb-3">
                                    <div class="bar bg-primary" style="width: {{ $ppPercent }}%"></div>
                                </div>

                                <div class="row g-2">
                                    @foreach($ppSteps as $ppIdx2 => $pp)
                                        @php
                                            $ppState = 'text-muted';
                                            if ($ppIdx2 < $ppIdx) $ppState = 'text-success fw-bold';
                                            elseif ($ppIdx2 === $ppIdx) $ppState = 'text-primary fw-bold';
                                        @endphp
                                        <div class="col text-center">
                                            <i class="bi {{ $pp['icon'] }} {{ $ppState }}" style="font-size:1.2rem;"></i>
                                            <div class="small {{ $ppState }}">{{ $pp['label'] }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($postProduction->expected_completion_date)
                                    <div class="mt-3 pt-3 border-top">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted"><i class="bi bi-calendar-event me-1"></i>Expected Completion</small>
                                            <small class="fw-bold">{{ $postProduction->expected_completion_date->format('M d, Y') }}</small>
                                        </div>
                                    </div>
                                @endif

                                @if($postProduction->progress_notes)
                                    <div class="mt-3 pt-3 border-top">
                                        <small class="text-muted fw-bold d-block mb-1">Latest Update</small>
                                        <p class="small mb-0">{{ $postProduction->progress_notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif

                {{-- DELIVERABLES SECTION --}}
                @if($booking->postProduction && $booking->postProduction->tasks->count() > 0)
                    @php
                        $ppTasks = $booking->postProduction->tasks;
                        $totalTasks = $ppTasks->count();
                        $approvedTasks = $ppTasks->where('admin_review_status', 'approved')->count();
                        $allApproved = $totalTasks > 0 && $approvedTasks === $totalTasks;
                        $deliverablesReady = $booking->deliverables_unlocked;
                    @endphp
                    <div class="card section-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-box-arrow-down me-2"></i>Deliverables</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <div class="detail-row">
                                        <span class="detail-label">PP Status</span>
                                        <span class="detail-value">
                                            @if($booking->postProduction->status === 'delivered')
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Delivered</span>
                                            @elseif($booking->postProduction->status === 'ready')
                                                <span class="badge bg-info"><i class="bi bi-hourglass me-1"></i>Ready for Delivery</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="bi bi-arrow-repeat me-1"></i>In Progress</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-row">
                                        <span class="detail-label">Task Progress</span>
                                        <span class="detail-value">{{ $approvedTasks }}/{{ $totalTasks }} approved</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-row">
                                        <span class="detail-label">File Access</span>
                                        <span class="detail-value">
                                            @if($deliverablesReady && $hasFinalPayment)
                                                <span class="badge bg-success"><i class="bi bi-unlock me-1"></i>Unlocked</span>
                                            @else
                                                <span class="badge bg-secondary"><i class="bi bi-lock me-1"></i>Locked</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="detail-row">
                                        <span class="detail-label">Payment</span>
                                        <span class="detail-value">
                                            @if($hasFinalPayment)
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Fully Paid</span>
                                            @else
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Balance Due</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if(!$deliverablesReady && $allApproved && $hasFinalPayment)
                                <div class="alert alert-info py-2 mb-0">
                                    <i class="bi bi-info-circle me-1"></i>Your deliverables are ready. Waiting for admin to unlock files.
                                </div>
                            @elseif(!$deliverablesReady && $allApproved && !$hasFinalPayment)
                                <div class="alert alert-warning py-2 mb-0">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Please complete your remaining balance first. Deliverables will be available for download after your final payment is verified and approved by admin.
                                </div>
                            @elseif(!$allApproved)
                                <div class="alert alert-secondary py-2 mb-0">
                                    <i class="bi bi-hourglass me-1"></i>Your deliverables are still being prepared. You can download files once all tasks are completed and admin has unlocked them.
                                </div>
                            @endif

                            @if($deliverablesReady && $hasFinalPayment)
                                <div class="mt-3">
                                    <div class="row g-2">
                                        @foreach($ppTasks->where('admin_review_status', 'approved') as $task)
                                            <div class="col-md-6">
                                                <div class="border rounded p-3">
                                                    <div class="d-flex align-items-center gap-2 mb-2">
                                                        <span class="badge bg-dark">{{ str_replace('_', ' ', ucfirst($task->task_type)) }}</span>
                                                        <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i>Ready</span>
                                                    </div>
                                                    <a href="{{ $task->deliverable_link }}" target="_blank" class="btn btn-sm btn-primary w-100">
                                                        <i class="bi bi-download me-1"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- PACKAGE & ADD-ONS --}}
                <div class="card section-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Package & Services</h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <span class="detail-label">Package</span>
                            <span class="detail-value">{{ $booking->package->name ?? 'N/A' }}</span>
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
                        <div class="detail-row">
                            <span class="detail-label">Package Price</span>
                            <span class="detail-value fw-bold text-success">&#8369;{{ number_format($booking->package->price ?? 0, 2) }}</span>
                        </div>

                        @if($booking->addons->count() > 0)
                            <hr class="my-2">
                            <div class="detail-row">
                                <span class="detail-label">Selected Add-Ons</span>
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

                {{-- EVENT DETAILS --}}
                <div class="card section-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-event me-2"></i>Event Details</h6>
                    </div>
                    <div class="card-body">
                        @if($booking->event_type)
                            <div class="detail-row">
                                <span class="detail-label">Event Type</span>
                                <span class="detail-value">{{ $booking->event_type }}</span>
                            </div>
                        @endif
                        <div class="detail-row">
                            <span class="detail-label">Event Date</span>
                            <span class="detail-value">{{ $booking->event_date->format('l, F d, Y') }}</span>
                        </div>
                        @if($booking->event_time)
                            <div class="detail-row">
                                <span class="detail-label">Event Time</span>
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

            </div>

            {{-- ==================== RIGHT COLUMN ==================== --}}
            <div class="col-lg-4">

                {{-- PAYMENT INFO --}}
                <div class="card section-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2"></i>Payment Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="payment-row">
                            <span>Total Amount</span>
                            <span class="fw-bold">&#8369;{{ number_format($booking->total_price, 2) }}</span>
                        </div>
                        <div class="payment-row">
                            <span>Downpayment (30%)</span>
                            <span>&#8369;{{ number_format($booking->downpayment_amount, 2) }}</span>
                        </div>
                        <div class="payment-row">
                            <span>Remaining Balance</span>
                            <span class="fw-bold text-danger">&#8369;{{ number_format($booking->total_price - $booking->downpayment_amount, 2) }}</span>
                        </div>
                        <hr>

                        {{-- DOWNPAYMENT STATUS --}}
                        @if($latestDownpayment)
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <small class="text-muted">Payment Status</small>
                                    @if($latestDownpayment->status === 'pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-hourglass-split me-1"></i>Pending Verification
                                        </span>
                                    @elseif($latestDownpayment->status === 'verified')
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Verified
                                        </span>
                                    @elseif($latestDownpayment->status === 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Rejected
                                        </span>
                                    @endif
                                </div>
                                <div class="small text-muted mb-2">
                                    Submitted: {{ $latestDownpayment->submitted_at ? $latestDownpayment->submitted_at->format('M d, Y g:i A') : '—' }}
                                </div>
                                @if($latestDownpayment->rejection_reason)
                                    <div class="rejection-reason-box small">
                                        <strong class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Rejected:</strong>
                                        {{ $latestDownpayment->rejection_reason }}
                                    </div>
                                @endif
                            </div>
                            @if($latestDownpayment->status === 'rejected')
                                <a href="{{ route('customer.downpayment') }}" class="btn btn-warning w-100 btn-action">
                                    <i class="bi bi-arrow-repeat me-1"></i>Resubmit Payment Proof
                                </a>
                            @endif
                        @else
                            @if(in_array($booking->status, ['approved']))
                                <div class="text-center mb-3">
                                    <span class="badge bg-warning text-dark px-3 py-2" style="font-size:0.85rem;">
                                        <i class="bi bi-hourglass-split me-1"></i> Awaiting Downpayment
                                    </span>
                                </div>
                                <a href="{{ route('customer.downpayment') }}" class="btn btn-primary w-100 btn-action">
                                    <i class="bi bi-credit-card me-1"></i>Submit Downpayment Proof
                                </a>
                            @else
                                <div class="text-center">
                                    <span class="badge bg-secondary px-3 py-2" style="font-size:0.85rem;">
                                        <i class="bi bi-lock me-1"></i> Payment Pending Approval
                                    </span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- FINAL PAYMENT SECTION --}}
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
                        <div class="card section-card mb-4">
                            <div class="card-header bg-white border-bottom">
                                <h6 class="mb-0 fw-bold"><i class="bi bi-wallet2 me-2"></i>Final Payment</h6>
                            </div>
                            <div class="card-body">
                                <div class="detail-row mb-2">
                                    <span class="detail-label">Remaining Balance</span>
                                    <span class="detail-value fw-bold text-danger">&#8369;{{ number_format($remainingBalance, 2) }}</span>
                                </div>
                                <div class="detail-row mb-3">
                                    <span class="detail-label">Final Payment Status</span>
                                    <span class="detail-value">
                                        @if($booking->final_payment_status === 'paid')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Paid</span>
                                        @elseif($latestFinalPayment && $latestFinalPayment->status === 'pending')
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending Verification</span>
                                        @elseif($latestFinalPayment && $latestFinalPayment->status === 'rejected')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bi bi-clock me-1"></i>Not Yet Submitted</span>
                                        @endif
                                    </span>
                                </div>

                                @if($booking->final_payment_status !== 'paid')
                                    @if($latestFinalPayment && $latestFinalPayment->status === 'rejected')
                                        <a href="{{ route('customer.final-payment') }}" class="btn btn-warning w-100 btn-action">
                                            <i class="bi bi-arrow-repeat me-1"></i>Resubmit Final Payment
                                        </a>
                                    @elseif(!$latestFinalPayment || ($latestFinalPayment && $latestFinalPayment->status !== 'pending'))
                                        <a href="{{ route('customer.final-payment') }}" class="btn btn-primary w-100 btn-action">
                                            <i class="bi bi-credit-card me-1"></i>Submit Final Payment
                                        </a>
                                    @else
                                        <div class="text-center">
                                            <span class="badge bg-info px-3 py-2" style="font-size:0.85rem;">
                                                <i class="bi bi-hourglass-split me-1"></i>Awaiting Verification
                                            </span>
                                        </div>
                                    @endif
                                    <div class="alert alert-light border py-2 px-3 mb-0 mt-3 small">
                                        <i class="bi bi-info-circle me-1"></i>Your deliverables will only be available for download after your final payment is verified by admin.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif

                {{-- ASSIGNED TEAM --}}
                @if($booking->team)
                    <div class="card section-card mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-people me-2"></i>Assigned Team</h6>
                        </div>
                        <div class="card-body">
                            <div class="detail-row">
                                <span class="detail-label">Team</span>
                                <span class="detail-value fw-bold text-primary">{{ $booking->team->name }}</span>
                            </div>
                            @if($booking->team->members->count() > 0)
                                <div class="mt-2">
                                    @foreach($booking->team->members as $member)
                                        <span class="team-member-chip">
                                            <span class="avatar">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                                            {{ $member->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- QUICK INFO --}}
                <div class="card section-card mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i>Booking Info</h6>
                    </div>
                    <div class="card-body">
                        <div class="detail-row">
                            <span class="detail-label">Booking Ref</span>
                            <span class="detail-value"><code>{{ $booking->booking_ref }}</code></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Submitted</span>
                            <span class="detail-value">{{ $booking->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Last Updated</span>
                            <span class="detail-value">{{ $booking->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                {{-- CLIENT ACTIONS --}}
                <div class="card section-card">
                    <div class="card-header bg-white border-bottom">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-lightning me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="card-body d-grid gap-2">
                        @if(in_array($booking->status, ['approved']) && (!$latestDownpayment || $latestDownpayment->status === 'rejected'))
                            <a href="{{ route('customer.downpayment') }}" class="btn btn-primary btn-action">
                                <i class="bi bi-credit-card me-2"></i>Submit Downpayment
                            </a>
                        @endif

                        @php
                            $cancellation = $booking->cancellationRequest;
                            $canCancel = in_array($booking->status, ['pending', 'approved', 'ongoing'])
                                && (!$cancellation || $cancellation->status === 'rejected');
                        @endphp

                        @if($cancellation)
                            @if($cancellation->status === 'pending')
                                <div class="alert alert-warning py-2 small mb-0">
                                    <i class="bi bi-hourglass-split me-1"></i><strong>Cancellation Pending:</strong>
                                    @if($cancellation->refund_amount > 0)
                                        Refund of ₱{{ number_format($cancellation->refund_amount, 2) }} ({{ $cancellation->refund_percentage }}%) is under review.
                                    @else
                                        No refund applicable. Awaiting admin confirmation.
                                    @endif
                                </div>
                            @elseif($cancellation->status === 'refunded')
                                <div class="alert alert-success py-2 small mb-0">
                                    <i class="bi bi-check-circle me-1"></i><strong>Refunded:</strong>
                                    ₱{{ number_format($cancellation->refund_amount, 2) }} has been processed.
                                    @if($cancellation->refund_reference)
                                        <br><span class="text-muted">Ref: {{ $cancellation->refund_reference }}</span>
                                    @endif
                                    @if($cancellation->admin_notes)
                                        <br>{{ $cancellation->admin_notes }}
                                    @endif
                                </div>
                            @elseif($cancellation->status === 'rejected')
                                <div class="alert alert-danger py-2 small mb-0">
                                    <i class="bi bi-x-circle me-1"></i><strong>Refund Rejected:</strong> {{ $cancellation->admin_notes }}
                                </div>
                            @endif
                        @endif

                        @if($canCancel)
                            <button class="btn btn-outline-danger btn-action" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle me-2"></i>Cancel Booking
                            </button>
                        @endif

                        @php
                            $leadTime = (int) \App\Models\Setting::getValue('reschedule_lead_time_days', 5);
                            $daysToEvent = now()->diffInDays($booking->event_date, false);
                            $canReschedule = in_array($booking->status, ['pending', 'approved', 'ongoing'])
                                && $daysToEvent >= $leadTime;
                            $pendingReschedule = $booking->pendingReschedule;
                            $lastReschedule = $booking->rescheduleRequests()->latest()->first();
                        @endphp
                        @if(session('reschedule_error'))
                            <div class="alert alert-danger py-2 small mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ session('reschedule_error') }}
                            </div>
                        @endif

                        @if($pendingReschedule)
                            <div class="alert alert-warning py-2 small mb-0">
                                <i class="bi bi-hourglass-split me-1"></i>Reschedule request pending admin review.
                            </div>
                        @elseif($lastReschedule && $lastReschedule->status === 'rejected')
                            <div class="alert alert-danger py-2 small mb-0">
                                <i class="bi bi-x-circle me-1"></i><strong>Reschedule Rejected:</strong> {{ $lastReschedule->rejection_reason }}
                            </div>
                            @if($canReschedule)
                                <button class="btn btn-outline-warning btn-action" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                                    <i class="bi bi-calendar-event me-2"></i>Request Reschedule
                                </button>
                            @endif
                        @elseif($canReschedule)
                            <button class="btn btn-outline-warning btn-action" data-bs-toggle="modal" data-bs-target="#rescheduleModal">
                                <i class="bi bi-calendar-event me-2"></i>Request Reschedule
                            </button>
                        @elseif(in_array($booking->status, ['pending', 'approved', 'ongoing']))
                            <button class="btn btn-outline-secondary btn-action" disabled title="Reschedule must be requested at least {{ $leadTime }} days before the event.">
                                <i class="bi bi-calendar-x me-2"></i>Reschedule Unavailable
                            </button>
                        @endif

                        <a href="{{ route('customer.change-password') }}" class="btn btn-outline-dark btn-action">
                            <i class="bi bi-key me-2"></i>Change Password
                        </a>
                        <button class="btn btn-outline-dark btn-action" onclick="window.print()">
                            <i class="bi bi-printer me-2"></i>Print Details
                        </button>
                        <form method="POST" action="{{ route('customer.logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-action w-100">
                                <i class="bi bi-box-arrow-left me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer class="client-footer mt-4">
        &copy; {{ date('Y') }} HarvyMance Films. All rights reserved.
    </footer>

    {{-- CANCEL BOOKING MODAL --}}
    @php
        $daysUntilEvent = (int) now()->startOfDay()->diffInDays($booking->event_date->startOfDay(), false);
        $refundPercent = \App\Http\Controllers\CancellationController::computeRefundPercentage($booking);
        $amountPaidSoFar = $booking->downpayments()->where('status','verified')->sum('amount');
        $estimatedRefund = round($amountPaidSoFar * ($refundPercent / 100), 2);
    @endphp
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-x-circle me-2"></i>Cancel Booking</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('customer.cancellation.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-{{ $refundPercent > 0 ? 'info' : 'warning' }} py-2 small mb-3">
                            @if($refundPercent > 0)
                                <i class="bi bi-info-circle me-1"></i>
                                Based on the refund policy, you are eligible for a <strong>{{ $refundPercent }}% refund</strong>
                                (≈ <strong>₱{{ number_format($estimatedRefund, 2) }}</strong>) since the event is {{ $daysUntilEvent }} day(s) away.
                            @else
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                <strong>No refund available.</strong> The event is {{ $daysUntilEvent }} day(s) away, which is within the no-refund window. You may still cancel.
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason for Cancellation <span class="text-muted small">(optional)</span></label>
                            <textarea name="reason" class="form-control" rows="3"
                                placeholder="Let us know why you're cancelling..."></textarea>
                        </div>
                        <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Go Back</button>
                        <button type="submit" class="btn btn-danger fw-semibold">
                            <i class="bi bi-x-circle me-1"></i>Confirm Cancellation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- RESCHEDULE MODAL --}}
    <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title fw-bold"><i class="bi bi-calendar-event me-2"></i>Request Reschedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('customer.reschedule.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Current event date: <strong>{{ $booking->event_date->format('M d, Y') }}</strong>.
                            You have <strong>unlimited reschedules</strong> for this booking.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Event Date <span class="text-danger">*</span></label>
                            <input type="date" name="requested_date" class="form-control" required
                                min="{{ now()->addDays($leadTime + 1)->format('Y-m-d') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Event Time <span class="text-danger">*</span></label>
                            <input type="time" name="requested_time" class="form-control" required
                                value="{{ $booking->event_time }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning fw-semibold">
                            <i class="bi bi-send me-1"></i>Submit Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/client-dashboard.js') }}"></script>
</body>

</html>
