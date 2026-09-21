@forelse($downpayments as $downpayment)
    @php
        $viewPayload = [
            'booking_ref' => $downpayment->booking->booking_ref ?? null,
            'status' => $downpayment->booking->status ?? null,
            'client_name' => $downpayment->booking->client_name ?? null,
            'client_email' => $downpayment->booking->client_email ?? null,
            'client_phone' => $downpayment->booking->client_phone ?? null,
            'package_name' => $downpayment->booking->package->name ?? null,
            'package_price' => (float) ($downpayment->booking->package->price ?? 0),
            'services' => $downpayment->booking->package ? $downpayment->booking->package->services->pluck('service_name')->toArray() : [],
            'addons' => $downpayment->booking->addons->map(fn($a) => ['name' => $a->name, 'price' => (float) $a->pivot->price])->values()->toArray(),
            'addons_total' => (float) $downpayment->booking->addons->sum('pivot.price'),
            'event_type' => $downpayment->booking->event_type ?? null,
            'event_date' => $downpayment->booking->event_date ? \Carbon\Carbon::parse($downpayment->booking->event_date)->format('F d, Y') : null,
            'event_time' => $downpayment->booking->event_time ? date('g:i A', strtotime($downpayment->booking->event_time)) : null,
            'event_venue' => $downpayment->booking->event_venue ?? null,
            'event_address' => $downpayment->booking->event_address ?? null,
            'event_description' => $downpayment->booking->event_description ?? null,
            'total_price' => (float) ($downpayment->booking->total_price ?? 0),
            'downpayment' => (float) ($downpayment->booking->downpayment_amount ?? 0),
            'balance' => (float) (($downpayment->booking->total_price ?? 0) - ($downpayment->booking->downpayment_amount ?? 0)),
            'team_name' => $downpayment->booking->team->name ?? null,
            'payment_status' => $downpayment->status,
            'notes' => $downpayment->booking->notes ?? null,
            'created_at' => $downpayment->booking->created_at ? $downpayment->booking->created_at->format('M d, Y g:i A') : null,
        ];
    @endphp
    <tr>
        <td><code>{{ $downpayment->booking->booking_ref ?? 'N/A' }}</code></td>
        <td>
            @if($downpayment->payment_type === 'final')
                <span class="badge bg-info text-white">Final Payment</span>
            @else
                <span class="badge bg-light text-dark border">Downpayment</span>
            @endif
        </td>
        <td>
            <div>{{ $downpayment->booking->client_name ?? 'N/A' }}</div>
            <small class="text-muted">{{ $downpayment->booking->client_email ?? '' }}</small>
        </td>
        <td class="fw-semibold">&#8369;{{ number_format($downpayment->amount, 2) }}</td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3"
                onclick="previewProof('{{ asset('storage/' . $downpayment->payment_proof) }}')">
                <i class="bi bi-image me-1"></i> View
            </button>
        </td>
        <td>{{ $downpayment->submitted_at ? $downpayment->submitted_at->format('M d, Y g:i A') : '—' }}</td>
        <td>
            @include('partials.status-badge', ['status' => $downpayment->status])
        </td>
        <td class="text-center">
            <div class="d-flex gap-2 justify-content-center flex-nowrap">
                @if($downpayment->status === 'pending')
                    <button type="button" class="btn btn-sm btn-outline-success rounded-3 text-nowrap flex-shrink-0"
                        title="Approve Payment"
                        onclick="openVerifyModal('{{ $downpayment->id }}', '{{ $downpayment->booking->booking_ref ?? 'N/A' }}')">
                        <i class="bi bi-check-lg me-1"></i> Approve
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3 text-nowrap flex-shrink-0"
                        title="Reject Payment"
                        onclick="openRejectModal('{{ $downpayment->id }}', '{{ $downpayment->booking->booking_ref ?? 'N/A' }}')">
                        <i class="bi bi-x-lg me-1"></i> Reject
                    </button>
                @endif
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 text-nowrap flex-shrink-0"
                    title="View Booking Details"
                    onclick='window.viewPayloads = window.viewPayloads || {}; window.viewPayloads[{{ $downpayment->id }}] = @json($viewPayload); openViewModal(window.viewPayloads[{{ $downpayment->id }}])'>
                    <i class="bi bi-eye"></i>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-4 text-muted">
            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
            No payment submissions found.
        </td>
    </tr>
@endforelse
