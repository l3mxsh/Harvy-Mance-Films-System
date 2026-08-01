{{-- FINAL PAYMENT MODAL --}}
@php
    $fpTotalPaid = $booking->downpayments()->where('status', 'verified')->sum('amount');
    $fpRemaining = max(0, $booking->total_price - $fpTotalPaid);
    $fpLatest = $booking->downpayments()->where('payment_type', 'final')->latest()->first();
    $fpIsRejected = $fpLatest && $fpLatest->status === 'rejected';
@endphp
<div class="modal fade" id="finalPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-credit-card me-2"></i>{{ $fpIsRejected ? 'Resubmit Final Payment' : 'Submit Final Payment' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="finalPaymentForm" style="display:contents;"
                  action="{{ $fpIsRejected ? route('customer.final-payment.resubmit', $fpLatest->id) : route('customer.final-payment.submit') }}">
                @csrf
                <input type="hidden" name="payment_context" value="final">
                <div class="modal-body py-3">
                    <div class="row g-2 mb-2">
                        <div class="col-4">
                            <div class="border rounded p-2 text-center h-100">
                                <div class="text-muted" style="font-size:0.7rem;">Total</div>
                                <div class="fw-bold">&#8369;{{ number_format($booking->total_price, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 text-center h-100">
                                <div class="text-muted" style="font-size:0.7rem;">Paid</div>
                                <div class="fw-bold text-success">&#8369;{{ number_format($fpTotalPaid, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 text-center h-100 border-dark">
                                <div class="text-muted" style="font-size:0.7rem;">Balance</div>
                                <div class="fw-bold text-danger">&#8369;{{ number_format($fpRemaining, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    @if($fpIsRejected)
                        <div class="rejection-reason-box mt-2 mb-2">
                            <div class="text-danger fw-bold small mb-1">
                                <i class="bi bi-x-circle me-1"></i>Rejection Reason
                            </div>
                            <p class="mb-0 small">{{ $fpLatest->rejection_reason }}</p>
                        </div>
                    @endif

                    <div class="mb-2">
                        <label for="fp_amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">&#8369;</span>
                            <input type="number" class="form-control bg-light" id="fp_amount" name="amount" step="0.01"
                                   value="{{ $fpRemaining }}" readonly
                                   style="cursor:not-allowed;">
                        </div>
                        <div class="form-text">The remaining balance is fixed.</div>
                    </div>

                    <div class="mb-2">
                        <label for="fp_payment_proof" class="form-label">Payment Proof <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('payment_proof') is-invalid @enderror"
                               id="fp_payment_proof" name="payment_proof" accept="image/*" required>
                        @error('payment_proof')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Upload a screenshot or receipt (JPEG, PNG, JPG, GIF - Max 5MB)</div>
                    </div>

                    <div class="mb-2" id="fp_previewContainer" style="display: none;">
                        <label class="form-label">Image Preview</label>
                        <div class="image-preview-wrapper">
                            <img id="fp_imagePreview" src="#" alt="Payment Proof Preview">
                            <button type="button" class="btn-remove-preview" id="fp_removePreview" aria-label="Remove preview">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="bg-light rounded p-2 small" style="font-size:0.78rem;">
                        <h6 class="fw-semibold mb-1" style="font-size:0.8rem;"><i class="bi bi-info-circle me-1"></i>How to pay</h6>
                        <ol class="mb-0 ps-3">
                            <li>Transfer <strong>&#8369;{{ number_format($fpRemaining, 2) }}</strong> to our designated payment account.</li>
                            <li>Take a screenshot/photo of the payment confirmation.</li>
                            <li>Upload the proof and submit. We'll review within 24-48 hours.</li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill" id="fp_submitBtn">
                        <i class="bi bi-send me-1"></i>{{ $fpIsRejected ? 'Resubmit Payment Proof' : 'Submit Payment Proof' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
