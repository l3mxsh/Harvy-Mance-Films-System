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
