@php
$currentRoute = request()->route()->getName();
@endphp

<nav id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <h5 class="text-white mb-0">HarvyMance Films</h5>
        <button id="sidebarClose" class="btn btn-sm btn-outline-light d-lg-none">&times;</button>
    </div>

    <div class="sidebar-user">
        <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        <div class="user-info">
            <span class="user-name">{{ Auth::user()->name }}</span>
            <span class="user-role badge bg-secondary">{{ Auth::user()->role }}</span>
        </div>
    </div>

    <ul class="sidebar-nav">
        <li>
            <a href="{{ route('dashboard') }}" class="{{ $currentRoute === 'dashboard' ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('package') }}" class="{{ $currentRoute === 'package' ? 'active' : '' }}">
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
            </a>
        </li>
        <li>
            <a href="{{ route('team.index') }}" class="{{ $currentRoute === 'team.index' ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Teams
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
            <a href="{{ url('/login') }}">
                <i class="bi bi-person-badge"></i> Booking Monitoring
            </a>
        </li>
        <li>
            <a href="#">
                <i class="bi bi-people"></i> Clients
            </a>
        </li>
        <li>
            <a href="{{ route('settings.index') }}" class="{{ $currentRoute === 'settings.index' ? 'active' : '' }}">
                <i class="bi bi-gear"></i> Settings
            </a>
        </li>
    </ul>

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
