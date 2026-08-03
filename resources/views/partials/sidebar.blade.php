@php
$currentRoute = request()->route()->getName();
$pendingPaymentsCount = \App\Models\Downpayment::where('status', 'pending')->count();
$archivedClientsCount = \App\Models\ClientAccount::whereNotNull('archived_at')->count();
@endphp

<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <a href="{{ url('/') }}" class="sidebar-brand d-inline-flex align-items-center">
            <img src="{{ asset('storage/images/Black Logo.png') }}" alt="HarvyMance Films" height="32">
        </a>
        <button id="sidebarClose" class="sidebar-close d-lg-none" aria-label="Close">&times;</button>
    </div>

    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('admin.dashboard') }}" class="{{ $currentRoute === 'admin.dashboard' ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('package') }}" class="{{ str_starts_with($currentRoute, 'package') ? 'active' : '' }}">
                <i class="bi bi-film"></i> Package
            </a>
        </li>
        <li>
            <a href="{{ route('inventory') }}" class="{{ $currentRoute === 'inventory' ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Inventory
            </a>
        </li>
        <li>
            <a href="{{ route('booking.admin.index') }}" class="{{ str_starts_with($currentRoute, 'booking') && $currentRoute !== 'booking.approve' ? 'active' : '' }}">
                <i class="bi bi-journal-check"></i> Bookings
            </a>
        </li>
        <li>
            <a href="{{ route('payment-verification.index') }}" class="{{ str_starts_with($currentRoute, 'payment-verification') ? 'active' : '' }}">
                <i class="bi bi-credit-card"></i> Payment Verification
                @if($pendingPaymentsCount > 0)
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
            <a href="{{ route('staff.admin.index') }}" class="{{ $currentRoute === 'staff.admin.index' ? 'active' : '' }}">
                <i class="bi bi-people"></i> Staff
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
        <li>
            <a href="{{ route('clients.admin.index') }}" class="{{ str_starts_with($currentRoute, 'clients.admin') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Clients
                @if($archivedClientsCount > 0)
                    <span class="badge bg-secondary ms-auto">{{ $archivedClientsCount }}</span>
                @endif
            </a>
        </li>
        <li>
            <a href="{{ route('users.admin.index') }}" class="{{ str_starts_with($currentRoute, 'users.admin') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> User Management
            </a>
        </li>
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
