{{-- DOWNPAYMENT MODAL --}}
@php $dpIsRejected = $latestDownpayment && $latestDownpayment->status === 'rejected'; @endphp
<div class="modal fade" id="downpaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-credit-card me-2"></i>{{ $dpIsRejected ? 'Resubmit Downpayment' : 'Submit Downpayment' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="downpaymentForm" style="display:contents;"
                  action="{{ $dpIsRejected ? route('client.downpayment.resubmit', $latestDownpayment->id) : route('client.downpayment.submit') }}">
                @csrf
                <input type="hidden" name="payment_context" value="downpayment">
                <div class="modal-body py-3">
                    <div class="row g-2 mb-2">
                        <div class="col-4">
                            <div class="border rounded p-2 text-center h-100">
                                <div class="text-muted" style="font-size:0.7rem;">Total</div>
                                <div class="fw-bold">&#8369;{{ number_format($booking->total_price, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 text-center h-100 border-dark">
                                <div class="text-muted" style="font-size:0.7rem;">Downpayment (30%)</div>
                                <div class="fw-bold">&#8369;{{ number_format($booking->downpayment_amount, 2) }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="border rounded p-2 text-center h-100">
                                <div class="text-muted" style="font-size:0.7rem;">Balance</div>
                                <div class="fw-bold text-danger">&#8369;{{ number_format($booking->total_price - $booking->downpayment_amount, 2) }}</div>
                            </div>
                        </div>
                    </div>

                    @if($dpIsRejected)
                        <div class="rejection-reason-box mt-2 mb-2">
                            <div class="text-danger fw-bold small mb-1">
                                <i class="bi bi-x-circle me-1"></i>Rejection Reason
                            </div>
                            <p class="mb-0 small">{{ $latestDownpayment->rejection_reason }}</p>
                        </div>
                    @endif

                    <div class="mb-2">
                        <label for="dp_amount" class="form-label">Payment Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">&#8369;</span>
                            <input type="number" class="form-control bg-light" id="dp_amount" name="amount" step="0.01"
                                   value="{{ $booking->downpayment_amount }}" readonly
                                   style="cursor:not-allowed;">
                        </div>
                        <div class="form-text">The required downpayment amount is fixed.</div>
                    </div>

                    <div class="mb-2">
                        <label for="dp_payment_proof" class="form-label">Payment Proof <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('payment_proof') is-invalid @enderror"
                               id="dp_payment_proof" name="payment_proof" accept="image/*" required>
                        @error('payment_proof')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Upload a screenshot or receipt (JPEG, PNG, JPG, GIF - Max 5MB)</div>
                    </div>

                    <div class="mb-2" id="dp_previewContainer" style="display: none;">
                        <label class="form-label">Image Preview</label>
                        <div class="image-preview-wrapper">
                            <img id="dp_imagePreview" src="#" alt="Payment Proof Preview">
                            <button type="button" class="btn-remove-preview" id="dp_removePreview" aria-label="Remove preview">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="bg-light rounded p-2 small" style="font-size:0.78rem;">
                        <h6 class="fw-semibold mb-1" style="font-size:0.8rem;"><i class="bi bi-info-circle me-1"></i>How to pay</h6>
                        <ol class="mb-0 ps-3">
                            <li>Transfer <strong>&#8369;{{ number_format($booking->downpayment_amount, 2) }}</strong> to our designated payment account.</li>
                            <li>Take a screenshot/photo of the payment confirmation.</li>
                            <li>Upload the proof and submit. We'll review within 24-48 hours.</li>
                        </ol>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill" id="dp_submitBtn">
                        <i class="bi bi-send me-1"></i>{{ $dpIsRejected ? 'Resubmit Payment Proof' : 'Submit Payment Proof' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
