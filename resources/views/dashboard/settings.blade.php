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
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Settings</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">

            <div class="row g-4">
                {{-- ==================== GENERAL SETTINGS ==================== --}}
                <div class="col-lg-6">
                    <section class="surface-card h-100">
                        <div class="section-head">
                            <h2 class="section-title"><i class="bi bi-gear me-2"></i>General Settings</h2>
                        </div>

                        <form method="POST" action="{{ route('settings.update') }}">
                            @csrf
                            @method('PUT')

                            <div class="setting-item">
                                <div class="d-flex gap-3">
                                    <div class="setting-icon bg-warning bg-opacity-10 text-warning">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
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
                                    <div class="setting-icon bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-calendar-event"></i>
                                    </div>
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
                                    <div class="setting-icon bg-success bg-opacity-10 text-success">
                                        <i class="bi bi-cash-stack"></i>
                                    </div>
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
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <div class="form-text mb-0">Example: 14 days → 100%, 7 days → 50%, 0 days → 0%</div>
                                            <button type="button" class="btn btn-sm btn-outline-dark rounded-pill" onclick="addRefundTier()">
                                                <i class="bi bi-plus me-1"></i>Add Tier
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="settings-save-bar">
                                <a href="{{ route('settings.index') }}" class="btn btn-outline-dark rounded-pill">Cancel</a>
                                <button type="submit" class="btn btn-dark rounded-pill">Save Settings</button>
                            </div>
                        </form>
                    </section>
                </div>

                {{-- ==================== UPCOMING ACCOUNT ARCHIVING ==================== --}}
                <div class="col-lg-6">
                    <section class="surface-card h-100">
                        <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h2 class="section-title"><i class="bi bi-clock-history me-2"></i>Upcoming Account Archiving</h2>
                            <span class="badge bg-light text-dark border">{{ $upcomingDeletions->count() }} account(s)</span>
                        </div>

                        @if($upcomingDeletions->isEmpty())
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No delivered bookings with active client accounts.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Booking Ref</th>
                                            <th>Delivered</th>
                                            <th>Auto-Delete</th>
                                            <th class="text-end">Days Left</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($upcomingDeletions as $item)
                                            <tr>
                                                <td>
                                                    <div>{{ $item['booking']->client_name }}</div>
                                                    <small class="text-muted">{{ $item['booking']->client_email }}</small>
                                                </td>
                                                <td><code>{{ $item['booking']->booking_ref }}</code></td>
                                                <td>{{ $item['delivered_at']->format('M d, Y') }}</td>
                                                <td>{{ $item['delete_at']->format('M d, Y') }}</td>
                                                <td class="text-end">
                                                    @if($item['days_remaining'] <= 3)
                                                        <span class="badge bg-danger">{{ $item['days_remaining'] }} day(s)</span>
                                                    @elseif($item['days_remaining'] <= 7)
                                                        <span class="badge bg-warning text-dark">{{ $item['days_remaining'] }} day(s)</span>
                                                    @else
                                                        <span class="badge bg-success">{{ $item['days_remaining'] }} day(s)</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
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
