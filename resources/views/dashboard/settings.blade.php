<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Settings</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-gear me-2"></i>General Settings</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('settings.update') }}">
                                @csrf
                                @method('PUT')

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Client Account Auto-Archiving</label>
                                    <p class="text-muted small mb-2">
                                        After a booking is delivered, the client's account will be automatically archived after the selected number of days, removing them from the active client list.
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

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Reschedule Minimum Lead Time</label>
                                    <p class="text-muted small mb-2">
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

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Refund Policy</label>
                                    <p class="text-muted small mb-2">
                                        Define refund tiers based on days before the event. The highest matching tier applies.
                                    </p>
                                    <div id="refundTiers">
                                        @foreach($refundPolicy as $i => $tier)
                                            <div class="d-flex gap-2 mb-2 refund-tier-row">
                                                <div class="input-group input-group-sm" style="max-width:160px;">
                                                    <input type="number" name="refund_tiers[{{ $i }}][days]" class="form-control" value="{{ $tier['days'] }}" min="0" placeholder="Days before">
                                                    <span class="input-group-text">days</span>
                                                </div>
                                                <div class="input-group input-group-sm" style="max-width:130px;">
                                                    <input type="number" name="refund_tiers[{{ $i }}][percent]" class="form-control" value="{{ $tier['percent'] }}" min="0" max="100" placeholder="%">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.refund-tier-row').remove()"><i class="bi bi-trash"></i></button>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1" onclick="addRefundTier()">
                                        <i class="bi bi-plus me-1"></i>Add Tier
                                    </button>
                                    <div class="form-text">Example: 14 days → 100%, 7 days → 50%, 0 days → 0%</div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Save Settings
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>Upcoming Account Archiving</h6>
                            <span class="badge bg-secondary">{{ $upcomingDeletions->count() }} account(s)</span>
                        </div>
                        <div class="card-body p-0">
                            @if($upcomingDeletions->isEmpty())
                                <div class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No delivered bookings with active client accounts.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Client</th>
                                                <th>Booking Ref</th>
                                                <th>Delivered</th>
                                                <th>Auto-Delete</th>
                                                <th>Days Left</th>
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
                                                    <td>
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
                        </div>
                    </div>
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
            row.className = 'd-flex gap-2 mb-2 refund-tier-row';
            row.innerHTML = '<div class="input-group input-group-sm" style="max-width:160px;"><input type="number" name="refund_tiers[' + tierCount + '][days]" class="form-control" min="0" placeholder="Days before"><span class="input-group-text">days</span></div>'
                + '<div class="input-group input-group-sm" style="max-width:130px;"><input type="number" name="refund_tiers[' + tierCount + '][percent]" class="form-control" min="0" max="100" placeholder="%"><span class="input-group-text">%</span></div>'
                + '<button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest(\'.refund-tier-row\').remove()"><i class="bi bi-trash"></i></button>';
            container.appendChild(row);
            tierCount++;
        }
    </script>
</body>

</html>
