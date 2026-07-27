/* client-downpayment.js */
document.addEventListener('DOMContentLoaded', function () {
    var fileInput = document.getElementById('payment_proof');
    var previewContainer = document.getElementById('previewContainer');
    var imagePreview = document.getElementById('imagePreview');
    var removeBtn = document.getElementById('removePreview');
    var submitBtn = document.getElementById('submitBtn');
    var form = document.getElementById('downpaymentForm') || document.getElementById('finalPaymentForm');

    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            var file = e.target.files[0];
            if (!file) return;

            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                fileInput.value = '';
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB.');
                fileInput.value = '';
                return;
            }

            var reader = new FileReader();
            reader.onload = function (ev) {
                imagePreview.src = ev.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            fileInput.value = '';
            imagePreview.src = '#';
            previewContainer.style.display = 'none';
        });
    }

    if (form) {
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Submitting...';
        });
    }
});
