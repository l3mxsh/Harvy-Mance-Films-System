{{-- ==================== OTP MODAL ==================== --}}
<div class="modal fade" id="otpModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Verification</h5>
            </div>
            <div class="modal-body p-4">

                <div id="otpSendingState" class="text-center py-2">
                    <div class="spinner-border text-secondary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mb-0 text-muted small">Sending verification code to your email...</p>
                </div>

                <div id="otpInputState" style="display:none;">
                    <p class="text-center text-muted small mb-4">
                        A 6-digit code has been sent to<br>
                        <strong id="otpEmailDisplay" class="text-dark"></strong>
                    </p>

                    <input type="text" class="form-control form-control-lg text-center otp-input mb-3"
                           id="otpInput" maxlength="6" placeholder="000000" autocomplete="off">

                    <p id="otpError" class="small text-danger text-center mb-2" style="display:none;"></p>
                    <p id="otpSuccess" class="small text-success text-center mb-2" style="display:none;"></p>

                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="text-muted small" id="otpCountdown">Expires in 5:00</span>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none"
                                id="otpResendBtn" onclick="resendOtp()" disabled>Resend Code</button>
                    </div>
                </div>

            </div>
            <div class="modal-footer justify-content-between" id="otpFooter" style="display:none;">
                <button type="button" class="btn btn-outline-dark rounded-pill" onclick="cancelOtp()">Cancel</button>
                <button type="button" class="btn btn-dark rounded-pill" id="otpVerifyBtn" onclick="verifyOtp()">Verify</button>
            </div>
        </div>
    </div>
</div>

{{-- ==================== TERMS MODAL ==================== --}}
<div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body terms-body">
                <h6>1. Booking &amp; Reservation</h6>
                <p>All bookings are subject to availability and admin approval. A booking is not confirmed until you receive a confirmation notification.</p>

                <h6>2. Downpayment</h6>
                <p>A 30% downpayment is required to secure your booking. The downpayment must be paid within the deadline specified in your booking confirmation.</p>

                <h6>3. Cancellation Policy</h6>
                <p>Cancellations made 7 days before the event will receive a full refund of the downpayment. Cancellations within 7 days of the event will forfeit the downpayment.</p>

                <h6>4. Rescheduling</h6>
                <p>Rescheduling requests are subject to availability. Please contact us at least 48 hours before your original event date.</p>

                <h6>5. Equipment &amp; Materials</h6>
                <p>Required equipment and materials will be automatically reserved upon booking approval. Any damage to rented equipment will be charged accordingly.</p>

                <h6>6. Deliverables</h6>
                <p>Final edited photos and videos will be delivered within the timeframe specified in your selected package. Raw files are not included unless specified.</p>

                <h6>7. Liability</h6>
                <p>HarvyMance Films is not liable for events beyond our control, including natural disasters, power outages, or other force majeure events.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark rounded-pill" data-bs-dismiss="modal">I Understand</button>
            </div>
        </div>
    </div>
</div>
