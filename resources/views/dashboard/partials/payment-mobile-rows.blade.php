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
    <div class="mobile-card">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <div class="fw-semibold">{{ $downpayment->booking->client_name ?? 'N/A' }}</div>
                <small class="text-muted"><code>{{ $downpayment->booking->booking_ref ?? 'N/A' }}</code></small>
            </div>
            <div class="text-end flex-shrink-0 ms-2">
                <div class="mb-1">
                    @if($downpayment->payment_type === 'final')
                        <span class="badge bg-info text-white">Final Payment</span>
                    @else
                        <span class="badge bg-light text-dark border">Downpayment</span>
                    @endif
                </div>
                @include('partials.status-badge', ['status' => $downpayment->status])
            </div>
        </div>
        <div class="mobile-card-info">
            <div class="mobile-card-field">
                <div class="mobile-card-label">Amount</div>
                <div class="mobile-card-value fw-semibold">&#8369;{{ number_format($downpayment->amount, 2) }}</div>
            </div>
            <div class="mobile-card-field">
                <div class="mobile-card-label">Submitted</div>
                <div class="mobile-card-value">{{ $downpayment->submitted_at ? $downpayment->submitted_at->format('M d, Y g:i A') : '—' }}</div>
            </div>
        </div>
        <div class="mobile-card-actions">
            @if($downpayment->status === 'pending')
                <button type="button" class="btn btn-sm btn-outline-success rounded-3"
                    onclick="openVerifyModal('{{ $downpayment->id }}', '{{ $downpayment->booking->booking_ref ?? 'N/A' }}')">
                    <i class="bi bi-check-lg me-1"></i> Approve
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger rounded-3"
                    onclick="openRejectModal('{{ $downpayment->id }}', '{{ $downpayment->booking->booking_ref ?? 'N/A' }}')">
                    <i class="bi bi-x-lg me-1"></i> Reject
                </button>
            @endif
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3"
                onclick="previewProof('{{ asset('storage/' . $downpayment->payment_proof) }}')">
                <i class="bi bi-image me-1"></i> View Proof
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-3"
                onclick='window.viewPayloads = window.viewPayloads || {}; window.viewPayloads[{{ $downpayment->id }}] = @json($viewPayload); openViewModal(window.viewPayloads[{{ $downpayment->id }}])'>
                <i class="bi bi-eye me-1"></i> Details
            </button>
        </div>
    </div>
@empty
    <div class="text-center py-4 text-muted">
        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
        No payment submissions found.
    </div>
@endforelse
