/* client-downpayment.js - payment proof preview for downpayment + final payment */
function initPaymentForm(fileId, previewId, imgId, removeId, btnId, formEl, modalEl) {
    var fileInput = document.getElementById(fileId);
    var previewContainer = document.getElementById(previewId);
    var imagePreview = document.getElementById(imgId);
    var removeBtn = document.getElementById(removeId);
    var submitBtn = document.getElementById(btnId);

    function clearPreview() {
        if (fileInput) fileInput.value = '';
        if (imagePreview) imagePreview.src = '';
        if (previewContainer) previewContainer.style.display = 'none';
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            var file = fileInput.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                clearPreview();
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB.');
                clearPreview();
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', clearPreview);
    }

    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', clearPreview);
    }

    if (formEl && submitBtn) {
        formEl.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Submitting...';
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    initPaymentForm(
        'dp_payment_proof', 'dp_previewContainer', 'dp_imagePreview', 'dp_removePreview', 'dp_submitBtn',
        document.getElementById('downpaymentForm'), document.getElementById('downpaymentModal')
    );

    initPaymentForm(
        'fp_payment_proof', 'fp_previewContainer', 'fp_imagePreview', 'fp_removePreview', 'fp_submitBtn',
        document.getElementById('finalPaymentForm'), document.getElementById('finalPaymentModal')
    );

    initPaymentForm(
        'payment_proof', 'previewContainer', 'imagePreview', 'removePreview', 'submitBtn',
        document.getElementById('downpaymentForm') || document.getElementById('finalPaymentForm'), null
    );
});
