<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Verification - Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payment-verification.css') }}">
</head>

<body class="bg-light">
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Payment Verification</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- STATS CARDS --}}
            <div class="row g-3 mb-4">
                <div class="col-lg-2 col-md-4">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-credit-card"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">Total</small>
                                    <div class="fw-bold fs-4">{{ $stats['total'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-arrow-down-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">Downpayments</small>
                                    <div class="fw-bold fs-4">{{ $stats['downpayment_total'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-arrow-up-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">Final Payments</small>
                                    <div class="fw-bold fs-4">{{ $stats['final_total'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-hourglass-split"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">Pending</small>
                                    <div class="fw-bold fs-4">{{ $stats['pending'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">Verified</small>
                                    <div class="fw-bold fs-4">{{ $stats['verified'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card border-0 shadow-sm stat-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                                    <i class="bi bi-x-circle"></i>
                                </div>
                                <div class="ms-3">
                                    <small class="text-muted">Rejected</small>
                                    <div class="fw-bold fs-4">{{ $stats['rejected'] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FILTER TABS --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-list-check me-2"></i>Payment Submissions</h6>
                    <span class="badge bg-secondary">{{ $downpayments->total() }} total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ref</th>
                                    <th>Type</th>
                                    <th>Client</th>
                                    <th>Package</th>
                                    <th>Amount</th>
                                    <th>Payment Proof</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($downpayments as $downpayment)
                                    <tr>
                                        <td><code>{{ $downpayment->booking->booking_ref ?? 'N/A' }}</code></td>
                                        <td>
                                            @if($downpayment->payment_type === 'final')
                                                <span class="badge bg-success">Final Payment</span>
                                            @else
                                                <span class="badge bg-info">Downpayment</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $downpayment->booking->client_name ?? 'N/A' }}</div>
                                            <small class="text-muted">{{ $downpayment->booking->client_email ?? '' }}</small>
                                        </td>
                                        <td>{{ $downpayment->booking->package->name ?? 'N/A' }}</td>
                                        <td class="fw-bold">&#8369;{{ number_format($downpayment->amount, 2) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="previewProof('{{ asset('storage/' . $downpayment->payment_proof) }}')">
                                                <i class="bi bi-image me-1"></i>View
                                            </button>
                                        </td>
                                        <td>{{ $downpayment->submitted_at ? $downpayment->submitted_at->format('M d, Y g:i A') : '—' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($downpayment->status) {
                                                    'pending' => 'bg-warning text-dark',
                                                    'verified' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($downpayment->status) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('payment-verification.show', $downpayment->id) }}" class="btn btn-sm btn-outline-dark">
                                                <i class="bi bi-eye me-1"></i>Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            No payment submissions found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($downpayments->hasPages())
                    <div class="card-footer bg-white border-top">
                        {{ $downpayments->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- IMAGE PREVIEW MODAL --}}
    <div class="modal fade" id="proofPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title"><i class="bi bi-image me-2"></i>Payment Proof</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img id="proofPreviewImage" src="" alt="Payment Proof" class="img-fluid rounded" style="max-height: 500px;">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script src="{{ asset('js/payment-verification.js') }}"></script>
</body>

</html>
