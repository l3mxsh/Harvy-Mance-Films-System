@forelse($cancellations->filter(fn($c) => $c->booking) as $c)
    @php $amountPaid = $c->booking->downpayments->where('status','verified')->sum('amount'); @endphp
    <div class="mobile-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <div class="fw-semibold">{{ $c->booking->client_name }}</div>
                <small class="text-muted"><code>{{ $c->booking->booking_ref }}</code></small>
            </div>
            <div class="text-end flex-shrink-0 ms-2">
                @include('partials.status-badge', ['status' => $c->status])
            </div>
        </div>
        <div class="mb-2">
            <span class="mobile-card-label w-100 mb-0">Event Date</span>
            <span class="mobile-card-value d-block">{{ $c->booking->event_date->format('M d, Y') }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="mobile-card-label mb-0 align-self-center">Amount Paid</span>
            <span class="fw-semibold">&#8369;{{ number_format($amountPaid, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between mb-2">
            <span class="mobile-card-label mb-0 align-self-center">Refund</span>
            @if($c->refund_amount > 0)
                <span class="fw-bold text-success">&#8369;{{ number_format((float) $c->refund_amount, 2) }} <small class="text-muted">({{ $c->refund_percentage }}%)</small></span>
            @else
                <span class="mobile-card-value">No refund</span>
            @endif
        </div>
        <div class="mb-2">
            <span class="mobile-card-label w-100 mb-0">Reason</span>
            <span class="mobile-card-value d-block">{{ $c->reason ?: '—' }}</span>
        </div>
        <div class="mb-2">
            <span class="mobile-card-label w-100 mb-0">Submitted</span>
            <span class="mobile-card-value d-block">{{ $c->created_at->format('M d, Y g:i A') }}</span>
        </div>
        @if($c->status === 'refunded' && $c->refund_reference)
            <div class="mb-2">
                <span class="mobile-card-label w-100 mb-0">Refund Ref</span>
                <span class="mobile-card-value d-block">{{ $c->refund_reference }}</span>
            </div>
        @endif
        <div class="mt-2">
            @if($c->status === 'pending')
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill flex-fill"
                        onclick="openRefundModal({{ $c->id }}, '{{ $c->booking->booking_ref }}', '{{ number_format((float) $c->refund_amount, 2) }}', {{ $c->refund_amount > 0 ? 'true' : 'false' }})">
                        <i class="bi bi-check-lg me-1"></i> {{ $c->refund_amount > 0 ? 'Refund' : 'Confirm' }}
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill flex-fill"
                        onclick="openRejectModal({{ $c->id }}, '{{ $c->booking->booking_ref }}')">
                        <i class="bi bi-x-lg me-1"></i> Reject
                    </button>
                </div>
            @elseif($c->status === 'refunded' && $c->refund_proof)
                <a href="{{ Storage::url($c->refund_proof) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill w-100">
                    <i class="bi bi-file-earmark me-1"></i>View Proof
                </a>
            @else
                <span class="text-muted fst-italic small">Processed</span>
            @endif
        </div>
    </div>
@empty
    <div class="text-center py-5 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No cancellation requests found.
    </div>
@endforelse
