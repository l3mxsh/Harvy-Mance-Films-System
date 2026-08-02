{{-- CANCEL BOOKING MODAL --}}
@php
    $daysUntilEvent = (int) now()->startOfDay()->diffInDays($booking->event_date->startOfDay(), false);
    $refundPercent = \App\Http\Controllers\CancellationController::computeRefundPercentage($booking);
    $amountPaidSoFar = $booking->downpayments()->where('status','verified')->sum('amount');
    $estimatedRefund = round($amountPaidSoFar * ($refundPercent / 100), 2);
@endphp
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2 text-danger"></i>Cancel Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('client.cancellation.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-soft alert-soft-{{ $refundPercent > 0 ? 'info' : 'warning' }} py-2 small mb-3">
                        @if($refundPercent > 0)
                            <i class="bi bi-info-circle me-1"></i>
                            Based on the refund policy, you are eligible for a <strong>{{ $refundPercent }}% refund</strong>
                            (&#8776; <strong>&#8369;{{ number_format($estimatedRefund, 2) }}</strong>) since the event is {{ $daysUntilEvent }} day(s) away.
                        @else
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <strong>No refund available.</strong> The event is {{ $daysUntilEvent }} day(s) away, which is within the no-refund window. You may still cancel.
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason for Cancellation <span class="text-muted small">(optional)</span></label>
                        <textarea name="reason" class="form-control" rows="3"
                            placeholder="Let us know why you're cancelling..."></textarea>
                    </div>
                    <p class="text-danger small mb-0"><i class="bi bi-exclamation-triangle me-1"></i>This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Go Back</button>
                    <button type="submit" class="btn btn-danger rounded-pill">
                        <i class="bi bi-x-circle me-1"></i>Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
