<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}?v=9">
    <link rel="stylesheet" href="{{ asset('css/reports.css') }}?v=4">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Reports</span>
            <span></span>
        </div>

        {{-- ==================== TABS ==================== --}}
        <ul class="nav nav-pills mb-0 px-4 pt-4 justify-content-start" id="reportTabs">
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'overview' ? 'active' : '' }}"
                    href="{{ route('reports.index', array_merge(['tab' => 'overview'], $rangeQuery)) }}">
                    <i class="bi bi-speedometer2 me-1"></i> Overview
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'bookings' ? 'active' : '' }}"
                    href="{{ route('reports.index', array_merge(['tab' => 'bookings'], $rangeQuery)) }}">
                    <i class="bi bi-journal-text me-1"></i> Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab === 'performance' ? 'active' : '' }}"
                    href="{{ route('reports.index', array_merge(['tab' => 'performance'], $rangeQuery)) }}">
                    <i class="bi bi-bar-chart-line me-1"></i> Packages &amp; Add-Ons
                </a>
            </li>
        </ul>

        <div class="container-fluid p-4">

            {{-- ==================== HEADER ==================== --}}
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
                <div>
                    <h1 class="section-title fs-3 mb-1">Business Reports</h1>
                    <p class="section-sub mb-0">
                        <span id="reportRangeLabel">{{ $rangeLabel }}</span>
                    </p>
                </div>
                <button type="button" class="btn btn-outline-dark rounded-pill no-print" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>Print Report
                </button>
            </div>

            @include('dashboard.partials.report-range-filter')

            <div id="reportTabContent" data-tab="{{ $tab }}">
                @include('dashboard.partials.report-' . $tab . '-tab')
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/reports.js') }}?v=1"></script>
</body>

</html>
