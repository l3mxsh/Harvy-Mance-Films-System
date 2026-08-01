{{-- VERIFY PAYMENT MODAL --}}
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-check-circle me-2 text-success"></i>Verify Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="verifyForm">
                @csrf
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to approve and verify this payment for booking <strong
                            id="verifyBookingRef"></strong>? This will update the booking payment status.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="verifyPaymentBtn" class="btn btn-success rounded-pill">
                        <i class="bi bi-check-lg me-1"></i> Verify Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- REJECT PAYMENT MODAL --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-x-circle me-2 text-danger"></i>Reject Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectForm">
                @csrf
                <div class="modal-body">
                    <p class="mb-3">You are about to reject the payment submission for booking <strong
                            id="rejectBookingRef"></strong>.</p>
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Reason for Rejection <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4"
                            placeholder="Please provide a reason why this payment is being rejected..."
                            required></textarea>
                        <div class="form-text">This reason will be visible to the client.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn border-secondary rounded-pill" data-bs-dismiss="modal">
                        <i class="bi bi-arrow-left me-1"></i> Cancel
                    </button>
                    <button type="submit" id="rejectPaymentBtn" class="btn btn-danger rounded-pill">
                        <i class="bi bi-x-lg me-1"></i> Reject Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- IMAGE PREVIEW MODAL --}}
<div class="modal fade" id="proofPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-image me-2 text-primary"></i>Payment Proof</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="proofPreviewImage" src="" alt="Payment Proof" class="img-fluid rounded" style="max-height: 500px;">
            </div>
        </div>
    </div>
</div>
