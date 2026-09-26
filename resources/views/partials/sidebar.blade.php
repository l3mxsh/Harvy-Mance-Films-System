@php
$currentRoute = request()->route()->getName();
$showSidebarBadges = \App\Models\Setting::getValue('admin_show_sidebar_badges', '1') === '1';
$pendingPaymentsCount = \App\Models\Downpayment::where('status', 'pending')->count();
$archivedClientsCount = \App\Models\ClientAccount::whereNotNull('archived_at')->count();

$inCatalog     = str_starts_with($currentRoute, 'package') || $currentRoute === 'inventory';
$inOperations  = (str_starts_with($currentRoute, 'booking') && $currentRoute !== 'booking.approve')
                 || str_starts_with($currentRoute, 'payment-verification')
                 || str_starts_with($currentRoute, 'staff-schedule')
                 || str_starts_with($currentRoute, 'post-production')
                 || $currentRoute === 'cancellation.admin.index';
$inPeople      = $currentRoute === 'staff.admin.index'
                 || str_starts_with($currentRoute, 'clients.admin')
                 || str_starts_with($currentRoute, 'users.admin');
$inSystem      = str_starts_with($currentRoute, 'activity-logs') || str_starts_with($currentRoute, 'reports') || $currentRoute === 'settings.index';
@endphp

<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <a href="{{ url('/') }}" class="sidebar-brand d-inline-flex align-items-center">
            <img src="{{ asset('storage/images/Black Logo.png') }}" alt="HarvyMance Films" height="32">
        </a>
        <button id="sidebarClose" class="sidebar-close d-lg-none" aria-label="Close">&times;</button>
    </div>

    <ul class="sidebar-nav">
        {{-- Dashboard (no group) --}}
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        {{-- Catalog --}}
        <li class="sidebar-group">
            <button class="sidebar-group-toggle {{ $inCatalog ? '' : 'collapsed' }}" data-bs-toggle="collapse" data-bs-target="#group-catalog">
                <span><i class="bi bi-grid"></i> Services</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </button>
            <ul class="sidebar-group-items collapse {{ $inCatalog ? 'show' : '' }}" id="group-catalog">
                <li>
                    <a href="{{ route('package') }}" class="{{ str_starts_with($currentRoute, 'package') ? 'active' : '' }}">
                        <i class="bi bi-camera-video"></i> Package
                    </a>
                </li>
                <li>
                    <a href="{{ route('inventory') }}" class="{{ $currentRoute === 'inventory' ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i> Inventory
                    </a>
                </li>
            </ul>
        </li>

        {{-- Operations --}}
        <li class="sidebar-group">
            <button class="sidebar-group-toggle {{ $inOperations ? '' : 'collapsed' }}" data-bs-toggle="collapse" data-bs-target="#group-operations">
                <span><i class="bi bi-journal-check"></i> Operations</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </button>
            <ul class="sidebar-group-items collapse {{ $inOperations ? 'show' : '' }}" id="group-operations">
                <li>
                    <a href="{{ route('booking.admin.index') }}" class="{{ str_starts_with($currentRoute, 'booking') && $currentRoute !== 'booking.approve' ? 'active' : '' }}">
                        <i class="bi bi-journal-check"></i> Bookings
                    </a>
                </li>
                <li>
                    <a href="{{ route('payment-verification.index') }}" class="{{ str_starts_with($currentRoute, 'payment-verification') ? 'active' : '' }}">
                        <i class="bi bi-credit-card"></i> Payment Verification
                        @if($showSidebarBadges && $pendingPaymentsCount > 0)
                            <span class="badge bg-danger ms-auto">{{ $pendingPaymentsCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('staff-schedule.index') }}" class="{{ str_starts_with($currentRoute, 'staff-schedule') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event"></i> Schedules
                    </a>
                </li>
                <li>
                    <a href="{{ route('post-production.index') }}" class="{{ str_starts_with($currentRoute, 'post-production') ? 'active' : '' }}">
                        <i class="bi bi-film"></i> Post-Production
                    </a>
                </li>
                <li>
                    <a href="{{ route('cancellation.admin.index') }}" class="{{ $currentRoute === 'cancellation.admin.index' ? 'active' : '' }}">
                        <i class="bi bi-x-circle"></i> Cancellations
                    </a>
                </li>
            </ul>
        </li>

        {{-- People --}}
        <li class="sidebar-group">
            <button class="sidebar-group-toggle {{ $inPeople ? '' : 'collapsed' }}" data-bs-toggle="collapse" data-bs-target="#group-people">
                <span><i class="bi bi-people"></i> People</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </button>
            <ul class="sidebar-group-items collapse {{ $inPeople ? 'show' : '' }}" id="group-people">
                <li>
                    <a href="{{ route('staff.admin.index') }}" class="{{ $currentRoute === 'staff.admin.index' ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i> Staff
                    </a>
                </li>
                <li>
                    <a href="{{ route('clients.admin.index') }}" class="{{ str_starts_with($currentRoute, 'clients.admin') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Clients
                        @if($showSidebarBadges && $archivedClientsCount > 0)
                            <span class="badge bg-secondary ms-auto">{{ $archivedClientsCount }}</span>
                        @endif
                    </a>
                </li>
                <li>
                    <a href="{{ route('users.admin.index') }}" class="{{ str_starts_with($currentRoute, 'users.admin') ? 'active' : '' }}">
                        <i class="bi bi-person-gear"></i> User Management
                    </a>
                </li>
            </ul>
        </li>

        {{-- System --}}
        <li class="sidebar-group">
            <button class="sidebar-group-toggle {{ $inSystem ? '' : 'collapsed' }}" data-bs-toggle="collapse" data-bs-target="#group-system">
                <span><i class="bi bi-gear"></i> System</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </button>
            <ul class="sidebar-group-items collapse {{ $inSystem ? 'show' : '' }}" id="group-system">
                @if(Auth::user()->role === 'admin')
                    <li>
                        <a href="{{ route('reports.index') }}" class="{{ str_starts_with($currentRoute, 'reports') ? 'active' : '' }}">
                            <i class="bi bi-graph-up"></i> Reports
                        </a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('activity-logs.index') }}" class="{{ str_starts_with($currentRoute, 'activity-logs') ? 'active' : '' }}">
                        <i class="bi bi-activity"></i> Activity Logs
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.index') }}" class="{{ $currentRoute === 'settings.index' ? 'active' : '' }}">
                        <i class="bi bi-gear"></i> Settings
                    </a>
                </li>
            </ul>
        </li>
    </ul>

    <div class="sidebar-user">
        <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <span class="user-role">{{ Auth::user()->role }}</span>
        </div>
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-left"></i> Logout
            </button>
        </form>
    </div>
</nav>

<div id="sidebarOverlay" class="sidebar-overlay"></div>
