/* client-downpayment.js - payment proof preview for downpayment + final payment */
function initPaymentForm(fileId, previewId, imgId, removeId, btnId, formEl, modalEl) {
    var fileInput = document.getElementById(fileId);
    var previewContainer = document.getElementById(previewId);
    var imagePreview = document.getElementById(imgId);
    var removeBtn = document.getElementById(removeId);
    var submitBtn = document.getElementById(btnId);

    var uploadWrap = fileInput ? fileInput.closest('.proof-upload') : null;
    var dropTitle = uploadWrap ? uploadWrap.querySelector('.proof-upload-title') : null;
    var defaultTitle = dropTitle ? dropTitle.textContent.trim() : '';

    function setDropState(file) {
        if (!uploadWrap) return;
        if (file) {
            uploadWrap.classList.add('has-file');
            if (dropTitle) dropTitle.textContent = file.name;
        } else {
            uploadWrap.classList.remove('has-file');
            if (dropTitle) dropTitle.textContent = defaultTitle;
        }
    }

    function clearPreview() {
        if (fileInput) fileInput.value = '';
        if (imagePreview) imagePreview.src = '';
        if (previewContainer) previewContainer.style.display = 'none';
        setDropState(null);
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

            setDropState(file);

            var reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    if (uploadWrap) {
        ['dragenter', 'dragover'].forEach(function (evt) {
            uploadWrap.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
                uploadWrap.classList.add('is-dragging');
            });
        });

        ['dragleave', 'dragend', 'drop'].forEach(function (evt) {
            uploadWrap.addEventListener(evt, function (e) {
                e.preventDefault();
                e.stopPropagation();
                uploadWrap.classList.remove('is-dragging');
            });
        });

        uploadWrap.addEventListener('drop', function (e) {
            var files = e.dataTransfer && e.dataTransfer.files;
            if (!files || !files.length) return;

            try {
                fileInput.files = files;
            } catch (err) {
                alert('Please tap the upload area and select the file.');
                return;
            }

            fileInput.dispatchEvent(new Event('change', { bubbles: true }));
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
