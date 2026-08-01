<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancellations - Admin</title>
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
            <span class="fw-semibold">Cancellations & Refunds</span>
            <span></span>
        </div>

        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-x-circle me-2"></i>Cancellation Requests</h6>
                    <span class="badge bg-secondary">{{ $cancellations->total() }} total</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Booking Ref</th>
                                    <th>Client</th>
                                    <th>Event Date</th>
                                    <th>Amount Paid</th>
                                    <th>Refund</th>
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
                                        <td>₱{{ number_format($amountPaid, 2) }}</td>
                                        <td>
                                            @if($c->refund_amount > 0)
                                                <span class="text-success fw-bold">₱{{ number_format($c->refund_amount, 2) }}</span>
                                                <div><small class="text-muted">{{ $c->refund_percentage }}%</small></div>
                                            @else
                                                <span class="text-muted">No refund</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($c->reason)
                                                <span class="d-inline-block text-truncate" style="max-width:150px;" title="{{ $c->reason }}">{{ $c->reason }}</span>
                                            @else
                                                <span class="text-muted fst-italic">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($c->status === 'pending')
                                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                                            @elseif($c->status === 'refunded')
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Refunded</span>
                                                @if($c->refund_reference)
                                                    <div><small class="text-muted">Ref: {{ $c->refund_reference }}</small></div>
                                                @endif
                                            @elseif($c->status === 'rejected')
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejected</span>
                                            @elseif($c->status === 'approved')
                                                <span class="badge bg-info"><i class="bi bi-check me-1"></i>Approved</span>
                                            @endif
                                        </td>
                                        <td>{{ $c->created_at->format('M d, Y g:i A') }}</td>
                                        <td class="text-center">
                                            @if($c->status === 'pending')
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-success btn-sm"
                                                        onclick="openConfirmModal({{ $c->id }}, '{{ $c->booking->booking_ref }}', '{{ number_format($c->refund_amount, 2) }}', {{ $c->refund_amount > 0 ? 'true' : 'false' }})">
                                                        <i class="bi bi-check-lg"></i> {{ $c->refund_amount > 0 ? 'Process Refund' : 'Confirm Cancel' }}
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm"
                                                        onclick="openRejectModal({{ $c->id }}, '{{ $c->booking->booking_ref }}')">
                                                        <i class="bi bi-x-lg"></i> Reject
                                                    </button>
                                                </div>
                                            @elseif($c->status === 'refunded' && $c->refund_proof)
                                                <a href="{{ Storage::url($c->refund_proof) }}" target="_blank" class="btn btn-sm btn-outline-dark">
                                                    <i class="bi bi-file-earmark me-1"></i>View Proof
                                                </a>
                                            @else
                                                <span class="text-muted fst-italic small">Processed</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>No cancellation requests found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($cancellations->hasPages())
                    <div class="card-footer bg-white border-top">{{ $cancellations->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- PROCESS REFUND MODAL --}}
    <div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-check-circle me-2"></i>Process Refund</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="refundForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-1">Processing refund for booking <strong id="refundBookingRef"></strong>.</p>
                        <p class="text-muted small mb-3">Refund amount: <strong id="refundAmount" class="text-success"></strong></p>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reference Number <span class="text-muted small">(optional)</span></label>
                            <input type="text" name="refund_reference" class="form-control" placeholder="e.g. GCash ref, bank transaction ID">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Proof of Refund <span class="text-muted small">(optional)</span></label>
                            <input type="file" name="refund_proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Notes <span class="text-muted small">(optional)</span></label>
                            <textarea name="admin_notes" class="form-control" rows="2" placeholder="Additional notes for the customer..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-pill"><i class="bi bi-check-lg me-1"></i>Mark as Refunded</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- REJECT REFUND MODAL --}}
    <div class="modal fade" id="rejectRefundModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-x-circle me-2"></i>Reject Refund Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="rejectRefundForm">
                    @csrf
                    <div class="modal-body">
                        <p class="mb-3">Rejecting refund for booking <strong id="rejectRefundRef"></strong>.</p>
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
        function openConfirmModal(id, ref, amount) {
            openRefundModal(id, ref, amount);
        }
        function openRefundModal(id, ref, amount) {
            document.getElementById('refundForm').action = '/admin/cancellations/' + id + '/approve';
            document.getElementById('refundBookingRef').textContent = ref;
            document.getElementById('refundAmount').textContent = '₱' + amount;
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
</body>
</html>
