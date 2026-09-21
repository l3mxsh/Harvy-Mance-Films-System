<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookings Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}?v=3">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Bookings Management</span>
            <span></span>
        </div>

        {{-- TABS --}}
        <ul class="nav nav-pills mb-0 px-4 pt-4 justify-content-center justify-content-md-start" id="bookingTabs">
            <li class="nav-item">
                <a class="nav-link {{ request('tab') !== 'reschedule' ? 'active' : '' }}"
                    href="{{ route('booking.admin.index') }}">
                    <i class="bi bi-journal-check me-1"></i> All Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('tab') === 'reschedule' ? 'active' : '' }}"
                    href="{{ route('booking.admin.index', ['tab' => 'reschedule']) }}">
                    <i class="bi bi-calendar-event me-1"></i> Reschedule Requests
                    @if($pendingRescheduleCount > 0)
                        <span class="badge bg-danger ms-1">{{ $pendingRescheduleCount }}</span>
                    @endif
                </a>
            </li>
        </ul>

        <div class="container-fluid p-4">
            <div id="bookingTabContent">
@if(request('tab') === 'reschedule')
    @include('dashboard.partials.reschedule-tab')
@else
    @include('dashboard.partials.bookings-tab')
@endif
</div>
        </div>
    </div>

    @include('partials.modals.booking-actions')

    @include('partials.modals.booking-details')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/admin-bookings.js') }}"></script>

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
