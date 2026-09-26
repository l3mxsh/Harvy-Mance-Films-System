<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}?v=7">
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}?v=2">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Settings</span>
            <span></span>
        </div>

        {{-- ==================== TABS ==================== --}}
        @php
            $activeTab = 'preferences';
            if ($errors->has('current_password') || $errors->has('new_password')) {
                $activeTab = 'password';
            } elseif ($errors->has('client_auto_delete_days') || $errors->has('reschedule_lead_time_days') || $errors->has('refund_tiers')) {
                $activeTab = 'general';
            }
        @endphp
        <ul class="nav nav-pills mb-0 px-4 pt-4 justify-content-start" id="settingsTabs">
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'preferences' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-preferences" type="button">
                    <i class="bi bi-person-gear me-1"></i> Admin Preferences
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'password' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-password" type="button">
                    <i class="bi bi-shield-lock me-1"></i> Change Password
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}" data-bs-toggle="tab"
                    data-bs-target="#tab-general" type="button">
                    <i class="bi bi-gear me-1"></i> General Settings
                </button>
            </li>
        </ul>

        <div class="container-fluid p-4">

            <div class="tab-content" id="settingsTabsContent">

                {{-- ==================== ADMIN PREFERENCES ==================== --}}
                <div class="tab-pane fade {{ $activeTab === 'preferences' ? 'show active' : '' }}" id="tab-preferences"
                    role="tabpanel">
                    <section class="surface-card">
                        <div class="section-head">
                            <h2 class="section-title">Profile Information</h2>
                        </div>

                        <form method="POST" action="{{ route('settings.profile.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="setting-item">
                                <div class="d-flex gap-3">
                                    <div class="flex-grow-1">
                                        <label class="setting-label">Name &amp; Email</label>
                                        <p class="setting-hint">Update the name and email address shown on your admin account.</p>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" placeholder="Full name">
                                                @error('name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" placeholder="Email address">
                                                @error('email')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="setting-item">
                                <div class="d-flex gap-3">
                                    <div class="flex-grow-1">
                                        <label class="setting-label">Sidebar Notification Badges</label>
                                        <p class="setting-hint">Show pending payment and archived client count badges in the sidebar.</p>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch"
                                                id="showSidebarBadges" name="show_sidebar_badges" value="1"
                                                {{ $showSidebarBadges === '1' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="showSidebarBadges">Show count badges</label>
                                        </div>
                                        <input type="hidden" name="show_sidebar_badges" value="0">
                                    </div>
                                </div>
                            </div>

                            <div class="settings-save-bar">
                                <button type="submit" class="btn btn-dark rounded-3">Save Profile</button>
                            </div>
                        </form>

                        <hr class="my-3">

                        <div class="setting-label mb-1">Account Details</div>
                        <p class="setting-hint mb-3">Read-only information about your admin account.</p>
                        <div class="row g-2">
                            <div class="col-12 col-lg-3">
                                <div class="detail-box">
                                    <span class="detail-label">Role</span>
                                    <span class="detail-value text-capitalize">{{ $user->role }}</span>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="detail-box">
                                    <span class="detail-label">Status</span>
                                    <span class="detail-value text-capitalize">{{ $user->status }}</span>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="detail-box">
                                    <span class="detail-label">Member Since</span>
                                    <span class="detail-value">{{ $user->created_at?->format('M d, Y') }}</span>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="detail-box">
                                    <span class="detail-label">Last Login</span>
                                    <span class="detail-value">{{ $user->last_login_at?->format('M d, Y h:i A') ?? '—' }}</span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- ==================== CHANGE PASSWORD ==================== --}}
                <div class="tab-pane fade {{ $activeTab === 'password' ? 'show active' : '' }}" id="tab-password"
                    role="tabpanel">
                    <section class="surface-card">
                        <div class="section-head">
                            <h2 class="section-title">Change Password</h2>
                        </div>

                        <form method="POST" action="{{ route('settings.password.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="setting-item">
                                <div class="d-flex gap-3">
                                    <div class="flex-grow-1">
                                        <label class="setting-label">Current Password</label>
                                        <p class="setting-hint">Confirm your current password to continue.</p>
                                        <input type="password" name="current_password" class="form-control" placeholder="Current password">
                                        @error('current_password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="setting-item">
                                <div class="d-flex gap-3">
                                    <div class="flex-grow-1">
                                        <label class="setting-label">New Password</label>
                                        <p class="setting-hint">Use at least 6 characters.</p>
                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <input type="password" name="new_password" class="form-control" placeholder="New password">
                                                @error('new_password')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <input type="password" name="new_password_confirmation" class="form-control" placeholder="Confirm new password">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-save-bar">
                                <button type="submit" class="btn btn-dark rounded-3">Update Password</button>
                            </div>
                        </form>
                    </section>
                </div>

                {{-- ==================== GENERAL SETTINGS ==================== --}}
                <div class="tab-pane fade {{ $activeTab === 'general' ? 'show active' : '' }}" id="tab-general"
                    role="tabpanel">
                    <section class="surface-card">
                        <div class="section-head">
                            <h2 class="section-title">General Settings</h2>
                        </div>

                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="setting-item">
                                <div class="d-flex gap-3">
                                    <div class="flex-grow-1">
                                        <label class="setting-label">Client Account Auto-Archiving</label>
                                        <p class="setting-hint">
                                            After a booking is delivered, the client's account is automatically archived after the selected number of days, removing them from the active client list.
                                        </p>
                                        <select name="client_auto_delete_days" class="form-select">
                                            <option value="7" {{ $autoDeleteDays == '7' ? 'selected' : '' }}>7 days</option>
                                            <option value="14" {{ $autoDeleteDays == '14' ? 'selected' : '' }}>14 days</option>
                                            <option value="30" {{ $autoDeleteDays == '30' ? 'selected' : '' }}>30 days</option>
                                        </select>
                                        @error('client_auto_delete_days')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="setting-item">
                                <div class="d-flex gap-3">
                                    <div class="flex-grow-1">
                                        <label class="setting-label">Reschedule Minimum Lead Time</label>
                                        <p class="setting-hint">
                                            Minimum number of days before the event date that a client can request a reschedule.
                                        </p>
                                        <select name="reschedule_lead_time_days" class="form-select">
                                            <option value="5" {{ $rescheduleLeadTime == '5' ? 'selected' : '' }}>5 days</option>
                                            <option value="6" {{ $rescheduleLeadTime == '6' ? 'selected' : '' }}>6 days</option>
                                            <option value="7" {{ $rescheduleLeadTime == '7' ? 'selected' : '' }}>7 days</option>
                                        </select>
                                        @error('reschedule_lead_time_days')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="setting-item">
                                <div class="d-flex gap-3">
                                    <div class="flex-grow-1">
                                        <label class="setting-label">Refund Policy</label>
                                        <p class="setting-hint">
                                            Define refund tiers based on days before the event. The highest matching tier applies.
                                        </p>
                                        <div id="refundTiers" class="d-grid gap-2">
                                            @foreach($refundPolicy as $i => $tier)
                                                <div class="refund-tier-row d-flex align-items-center gap-2">
                                                    <div class="input-group input-group-sm flex-grow-1">
                                                        <input type="number" name="refund_tiers[{{ $i }}][days]" class="form-control" value="{{ $tier['days'] }}" min="0" placeholder="Days before">
                                                        <span class="input-group-text">days</span>
                                                    </div>
                                                    <i class="bi bi-arrow-right refund-tier-arrow"></i>
                                                    <div class="input-group input-group-sm flex-grow-1">
                                                        <input type="number" name="refund_tiers[{{ $i }}][percent]" class="form-control" value="{{ $tier['percent'] }}" min="0" max="100" placeholder="%">
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                    <button type="button" class="btn btn-outline-danger border-0" onclick="this.closest('.refund-tier-row').remove()" title="Remove tier">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="form-text mb-2">Example: 14 days → 100%, 7 days → 50%, 0 days → 0%</div>
                                        <button type="button" class="btn btn-dark rounded-3 w-100 w-sm-auto" onclick="addRefundTier()">
                                            <i class="bi bi-plus me-1"></i>Add Tier
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-save-bar">
                                <a href="{{ route('settings.index') }}" class="btn btn-outline-dark rounded-3">Cancel</a>
                                <button type="submit" class="btn btn-dark rounded-3">Save Settings</button>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        var tierCount = {{ count($refundPolicy) }};
        function addRefundTier() {
            var container = document.getElementById('refundTiers');
            var row = document.createElement('div');
            row.className = 'refund-tier-row d-flex align-items-center gap-2';
            row.innerHTML = '<div class="input-group input-group-sm flex-grow-1"><input type="number" name="refund_tiers[' + tierCount + '][days]" class="form-control" min="0" placeholder="Days before"><span class="input-group-text">days</span></div>'
                + '<i class="bi bi-arrow-right refund-tier-arrow"></i>'
                + '<div class="input-group input-group-sm flex-grow-1"><input type="number" name="refund_tiers[' + tierCount + '][percent]" class="form-control" min="0" max="100" placeholder="%"><span class="input-group-text">%</span></div>'
                + '<button type="button" class="btn btn-outline-danger border-0" onclick="this.closest(\'.refund-tier-row\').remove()" title="Remove tier"><i class="bi bi-x-lg"></i></button>';
            container.appendChild(row);
            tierCount++;
        }
    </script>

    {{-- FLASH TOASTS --}}
    <div class="toast-container position-fixed top-0 end-0 p-3" id="flashToasts" style="z-index: 1080;">
        @if(session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        @endif
        @if($errors->any())
            @foreach($errors->all() as $error)
                <div class="toast align-items-center text-bg-danger border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body"><i class="bi bi-exclamation-triangle me-2"></i>{{ $error }}</div>
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
