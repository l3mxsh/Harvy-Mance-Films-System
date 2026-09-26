/* invoice.js - HarvyMance Films client invoice modal */
var invoicePreviewDataUrl = null;
var invoicePreviewPromise = null;

document.addEventListener('DOMContentLoaded', function () {
    var pngBtn = document.getElementById('invoicePngBtn');
    if (pngBtn) {
        pngBtn.addEventListener('click', function () { downloadInvoicePng(); });
    }

    var invoiceModal = document.getElementById('invoiceModal');
    if (invoiceModal) {
        invoiceModal.addEventListener('show.bs.modal', function () {
            renderInvoicePreview()['catch'](function () { });
        });
    }
});

function cloneInvoiceTo(targetId) {
    var source = document.getElementById('invoiceArea');
    var target = document.getElementById(targetId);
    if (!source || !target) return null;
    target.innerHTML = '';
    target.appendChild(source.cloneNode(true));
    return target;
}

function restoreDownloadBtn(btn, originalHtml) {
    btn.disabled = false;
    btn.innerHTML = originalHtml;
}

function invoiceFilename() {
    var source = document.getElementById('invoiceArea');
    var name = source ? source.getAttribute('data-invoice-filename') : null;
    return (name || 'invoice.png').replace(/\.pdf$/i, '.png');
}

function saveInvoiceDataUrl(dataUrl, filename) {
    var link = document.createElement('a');
    link.download = filename;
    link.href = dataUrl;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function captureInvoiceCanvas() {
    if (typeof html2canvas === 'undefined') {
        return Promise.reject(new Error('html2canvas-unavailable'));
    }

    var source = document.getElementById('invoiceArea');
    if (!source) return Promise.reject(new Error('invoice-missing'));

    var clone = cloneInvoiceTo('invoicePrintArea');
    var node = clone ? clone.querySelector('.invoice-sheet') : source;

    return html2canvas(node, {
        scale: 2,
        useCORS: true,
        backgroundColor: '#ffffff',
        logging: false,
        windowWidth: 900
    });
}

function showInvoicePreview(dataUrl) {
    var img = document.getElementById('invoicePreviewImg');
    var loading = document.getElementById('invoicePreviewLoading');
    if (img) {
        img.src = dataUrl;
        img.classList.remove('d-none');
    }
    if (loading) loading.classList.add('d-none');
}

function renderInvoicePreview() {
    var img = document.getElementById('invoicePreviewImg');
    var loading = document.getElementById('invoicePreviewLoading');
    if (!img) return Promise.reject(new Error('preview-missing'));

    if (invoicePreviewDataUrl) {
        showInvoicePreview(invoicePreviewDataUrl);
        return Promise.resolve(invoicePreviewDataUrl);
    }

    if (invoicePreviewPromise) return invoicePreviewPromise;

    if (loading) loading.classList.remove('d-none');
    img.classList.add('d-none');

    invoicePreviewPromise = captureInvoiceCanvas()
        .then(function (canvas) {
            invoicePreviewDataUrl = canvas.toDataURL('image/png');
            showInvoicePreview(invoicePreviewDataUrl);
            return invoicePreviewDataUrl;
        })
        ['catch'](function (err) {
            invoicePreviewPromise = null;
            if (loading) loading.classList.add('d-none');
            throw err;
        });

    return invoicePreviewPromise;
}

function downloadInvoicePng() {
    var btn = document.getElementById('invoicePngBtn');
    if (!btn) return;

    var originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Preparing PNG\u2026';

    renderInvoicePreview()
        .then(function (dataUrl) {
            saveInvoiceDataUrl(dataUrl, invoiceFilename());
        })
        ['catch'](function () {
            alert('There was a problem generating the PNG. Please try again.');
        })
        .then(function () {
            restoreDownloadBtn(btn, originalHtml);
        });
}
