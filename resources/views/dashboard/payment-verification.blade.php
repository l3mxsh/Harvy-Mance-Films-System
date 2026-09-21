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
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}?v=3">
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

        {{-- FILTER TABS --}}
        <ul class="nav nav-pills mb-0 px-4 pt-4 justify-content-center justify-content-md-start" id="paymentTabs">
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

        <div class="container-fluid p-4">
            <section class="surface-card">
                <div class="section-head d-flex justify-content-between align-items-center">
                    <h2 class="section-title">Payment Submissions</h2>
                    <span class="badge bg-light text-dark border" id="paymentTotalBadge">{{ $downpayments->total() }} total</span>
                </div>
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Ref</th>
                                <th>Type</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th class="d-none d-lg-table-cell">Payment Proof</th>
                                <th class="d-none d-lg-table-cell">Submitted</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="paymentTableBody">
                            @include('dashboard.partials.payment-rows', compact('downpayments'))
                        </tbody>
                    </table>
                </div>

                <div class="d-md-none" id="paymentMobileBody">
                    @include('dashboard.partials.payment-mobile-rows', compact('downpayments'))
                </div>
                <div id="paymentPagination" class="@if(!$downpayments->hasPages()) d-none @endif">
                    {{ $downpayments->links('vendor.pagination.bootstrap-5') }}
                </div>
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
