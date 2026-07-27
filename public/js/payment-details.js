/* payment-details.js */
document.addEventListener('DOMContentLoaded', function () {
    var verifyForm = document.getElementById('verifyForm');
    var verifyBtn = document.getElementById('verifyBtn');

    if (verifyForm) {
        verifyForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to approve and verify this payment? This will update the booking payment status.')) {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Verifying...';
                verifyForm.submit();
            }
        });
    }
});

function openRejectModal() {
    document.getElementById('rejection_reason').value = '';
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
