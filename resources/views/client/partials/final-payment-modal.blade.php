{{-- FINAL PAYMENT MODAL --}}
@php
    $fpTotalPaid = $booking->downpayments()->where('status', 'verified')->sum('amount');
    $fpRemaining = max(0, $booking->total_price - $fpTotalPaid);
    $fpLatest = $booking->downpayments()->where('payment_type', 'final')->latest()->first();
    $fpIsRejected = $fpLatest && $fpLatest->status === 'rejected';
@endphp
<div class="modal fade" id="finalPaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-credit-card me-2"></i>{{ $fpIsRejected ? 'Resubmit Final Payment' : 'Submit Final Payment' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="finalPaymentForm" style="display:contents;"
                  action="{{ $fpIsRejected ? route('client.final-payment.resubmit', $fpLatest->id) : route('client.final-payment.submit') }}">
                @csrf
                <input type="hidden" name="payment_context" value="final">
                <div class="modal-body py-3">
                    <div class="row g-2 mb-3">
                        <div class="col-12 col-sm-4">
                            <div class="amount-tile">
                                <div class="amount-tile-label">Total</div>
                                <div class="amount-tile-value">&#8369;{{ number_format($booking->total_price, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="amount-tile">
                                <div class="amount-tile-label">Paid</div>
                                <div class="amount-tile-value text-success">&#8369;{{ number_format($fpTotalPaid, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="amount-tile">
                                <div class="amount-tile-label">Balance</div>
                                <div class="amount-tile-value text-danger">&#8369;{{ number_format($fpRemaining, 2) }}</div>
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
                        <div class="proof-upload @error('payment_proof') is-invalid @enderror">
                            <input type="file" class="proof-upload-input" id="fp_payment_proof" name="payment_proof"
                                accept="image/*" required>
                            <label class="proof-upload-drop" for="fp_payment_proof">
                                <span class="proof-upload-icon"><i class="bi bi-cloud-arrow-up"></i></span>
                                <span class="proof-upload-title">Tap to upload</span>
                                <span class="proof-upload-hint">or drag &amp; drop your receipt here</span>
                                <span class="proof-upload-meta">JPEG, PNG, JPG or GIF &middot; Max 5MB</span>
                            </label>
                        </div>
                        @error('payment_proof')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
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

                    <div class="how-to-pay">
                        <h6><i class="bi bi-info-circle me-1"></i>How to pay</h6>
                        <ol>
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
