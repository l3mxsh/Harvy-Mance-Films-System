<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}?v=6">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Staff Management</span>
            <span></span>
        </div>
        {{-- ==================== TABS ==================== --}}
        <ul class="nav nav-pills mb-0 px-4 pt-4 justify-content-center justify-content-md-start" id="staffTabs">
                <li class="nav-item">
                    <a class="nav-link {{ in_array($tab, ['in-house', 'outsourced']) ? '' : 'active' }}"
                        href="{{ route('staff.admin.index') }}">
                        <i class="bi bi-people me-1"></i> All Staff
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'in-house' ? 'active' : '' }}"
                        href="{{ route('staff.admin.index', ['tab' => 'in-house']) }}">
                        <i class="bi bi-person-badge me-1"></i> In-House Staff
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'outsourced' ? 'active' : '' }}"
                        href="{{ route('staff.admin.index', ['tab' => 'outsourced']) }}">
                        <i class="bi bi-person-lines-fill me-1"></i> Outsourced Staff
                        <span class="badge bg-success text-light ms-1">Record Only</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $tab === 'teams' ? 'active' : '' }}"
                        href="{{ route('staff.admin.index', ['tab' => 'teams']) }}">
                        <i class="bi bi-people-fill me-1"></i> Teams
                    </a>
                </li>
        </ul>

        <div class="container-fluid p-4">
            <div id="staffTabContent" data-tab="{{ $tab }}">
                @if($tab === 'teams')
                    @include('dashboard.partials.staff-teams-tab')
                @elseif($tab === 'outsourced')
                    @include('dashboard.partials.staff-outsourced-tab')
                @else
                    @include('dashboard.partials.staff-list-tab')
                @endif
            </div>
        </div>

        </div>
    </div>

    @include('dashboard.partials.staff-modals')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/staff.js') }}"></script>

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
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="toast align-items-center text-bg-danger border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            @endforeach
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
