/* payment-verification.js */
document.addEventListener('DOMContentLoaded', function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) { return new bootstrap.Tooltip(el); });
});

function previewProof(imageUrl) {
    document.getElementById('proofPreviewImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('proofPreviewModal')).show();
}
