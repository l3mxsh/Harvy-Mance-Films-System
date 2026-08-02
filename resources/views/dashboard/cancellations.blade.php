<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancellations &amp; Refunds</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cancellations.css') }}">
</head>

<body>
    @include('partials.sidebar')

    <div class="sidebar-content">
        <div class="sidebar-topnav">
            <button id="sidebarToggle" class="sidebar-toggle">&#9776;</button>
            <span class="fw-semibold">Cancellations &amp; Refunds</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">

            {{-- ==================== SUMMARY CARDS ==================== --}}
            <div class="row g-3 mb-4">
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Pending</h6>
                                <h4 class="mb-0 fw-bold">{{ $summaryPending }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Refunded</h6>
                                <h4 class="mb-0 fw-bold">{{ $summaryRefunded }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-x-circle"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Rejected</h6>
                                <h4 class="mb-0 fw-bold">{{ $summaryRejected }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg col-md-4 col-6">
                    <div class="summary-card">
                        <div class="d-flex align-items-center">
                            <div class="summary-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-1 small">Total Requests</h6>
                                <h4 class="mb-0 fw-bold">{{ $summaryTotal }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== FILTERS ==================== --}}
            <section class="surface-card mb-4">
                <form method="GET" action="{{ route('cancellation.admin.index') }}" class="d-flex align-items-end flex-wrap gap-2">
                    <div class="flex-grow-1" style="min-width: 220px;">
                        <label class="form-label small text-muted mb-1">Search</label>
                        <input type="text" class="form-control form-control-sm" name="search" placeholder="Booking ref, client name or email..." value="{{ request('search') }}">
                    </div>
                    <div style="width: 180px;">
                        <label class="form-label small text-muted mb-1">Status</label>
                        <select class="form-select form-select-sm" name="status">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark btn-sm rounded-2 p-2 mb-1">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ route('cancellation.admin.index') }}" class="btn btn-outline-dark btn-sm rounded-pill mb-1" title="Clear filters">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            {{-- ==================== CANCELLATION REQUESTS ==================== --}}
            <section class="surface-card">
                <div class="section-head d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h2 class="section-title">Cancellation Requests</h2>
                    <span class="badge bg-light text-dark border">{{ $cancellations->total() }} total</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Booking Ref</th>
                                <th>Client</th>
                                <th>Event Date</th>
                                <th class="text-end">Amount Paid</th>
                                <th class="text-end">Refund</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cancellations->filter(fn($c) => $c->booking) as $c)
                                @php $amountPaid = $c->booking->downpayments->where('status','verified')->sum('amount'); @endphp
                                <tr>
                                    <td><code>{{ $c->booking->booking_ref }}</code></td>
                                    <td>
                                        <div>{{ $c->booking->client_name }}</div>
                                        <small class="text-muted">{{ $c->booking->client_email }}</small>
                                    </td>
                                    <td>{{ $c->booking->event_date->format('M d, Y') }}</td>
                                    <td class="text-end">&#8369;{{ number_format($amountPaid, 2) }}</td>
                                    <td class="text-end">
                                        @if($c->refund_amount > 0)
                                            <span class="fw-bold text-success">&#8369;{{ number_format((float) $c->refund_amount, 2) }}</span>
                                            <div><small class="text-muted">{{ $c->refund_percentage }}%</small></div>
                                        @else
                                            <span class="text-muted">No refund</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($c->reason)
                                            <span class="d-inline-block text-truncate" style="max-width:150px;" title="{{ $c->reason }}">{{ $c->reason }}</span>
                                        @else
                                            <span class="text-muted fst-italic">&mdash;</span>
                                        @endif
                                    </td>
                                    <td>
                                        @include('partials.status-badge', ['status' => $c->status])
                                        @if($c->status === 'refunded' && $c->refund_reference)
                                            <div><small class="text-muted">Ref: {{ $c->refund_reference }}</small></div>
                                        @endif
                                    </td>
                                    <td>{{ $c->created_at->format('M d, Y g:i A') }}</td>
                                    <td class="text-center">
                                        @if($c->status === 'pending')
                                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill"
                                                    onclick="openRefundModal({{ $c->id }}, '{{ $c->booking->booking_ref }}', '{{ number_format((float) $c->refund_amount, 2) }}', {{ $c->refund_amount > 0 ? 'true' : 'false' }})">
                                                    <i class="bi bi-check-lg me-1"></i> {{ $c->refund_amount > 0 ? 'Process Refund' : 'Confirm Cancel' }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill"
                                                    onclick="openRejectModal({{ $c->id }}, '{{ $c->booking->booking_ref }}')">
                                                    <i class="bi bi-x-lg me-1"></i> Reject
                                                </button>
                                            </div>
                                        @elseif($c->status === 'refunded' && $c->refund_proof)
                                            <a href="{{ Storage::url($c->refund_proof) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill">
                                                <i class="bi bi-file-earmark me-1"></i>View Proof
                                            </a>
                                        @else
                                            <span class="text-muted fst-italic small">Processed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No cancellation requests found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            {{ $cancellations->links('vendor.pagination.bootstrap-5') }}
        </div>
    </div>

    {{-- ==================== PROCESS REFUND MODAL ==================== --}}
    <div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="refundForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-1">Processing cancellation for booking <strong id="refundBookingRef"></strong>.</p>
                        <p class="text-muted small mb-3">Refund amount: <strong id="refundAmount" class="text-success"></strong></p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reference Number <span class="req">(optional)</span></label>
                            <input type="text" name="refund_reference" class="form-control" placeholder="e.g. GCash ref, bank transaction ID">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Proof of Refund <span class="req">(optional)</span></label>
                            <input type="file" name="refund_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes <span class="req">(optional)</span></label>
                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="Additional notes for the customer..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark rounded-pill"><i class="bi bi-check-lg me-1"></i>Mark as Refunded</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== REJECT REFUND MODAL ==================== --}}
    <div class="modal fade" id="rejectRefundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Reject Refund Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="rejectRefundForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">Rejecting cancellation for booking <strong id="rejectRefundRef"></strong>.</p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason <span class="text-danger">*</span></label>
                            <textarea name="admin_notes" class="form-control" rows="3" required
                                placeholder="Explain why the refund is being rejected..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill"><i class="bi bi-x-lg me-1"></i>Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/sidebar.js') }}"></script>
    <script>
        function openRefundModal(id, ref, amount, hasRefund) {
            document.getElementById('refundForm').action = '/admin/cancellations/' + id + '/approve';
            document.getElementById('refundBookingRef').textContent = ref;
            document.getElementById('refundAmount').textContent = hasRefund ? '₱' + amount : 'No refund applicable';
            document.getElementById('refundForm').reset();
            new bootstrap.Modal(document.getElementById('refundModal')).show();
        }

        function openRejectModal(id, ref) {
            document.getElementById('rejectRefundForm').action = '/admin/cancellations/' + id + '/reject';
            document.getElementById('rejectRefundRef').textContent = ref;
            document.querySelector('#rejectRefundForm textarea').value = '';
            new bootstrap.Modal(document.getElementById('rejectRefundModal')).show();
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
