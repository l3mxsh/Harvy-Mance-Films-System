/* invoice.js - HarvyMance Films client invoice modal */
document.addEventListener('DOMContentLoaded', function () {
    var printBtn = document.getElementById('invoicePrintBtn');
    if (printBtn) {
        printBtn.addEventListener('click', function () { printInvoice(); });
    }

    var downloadBtn = document.getElementById('invoiceDownloadBtn');
    if (downloadBtn) {
        downloadBtn.addEventListener('click', function () { downloadInvoice(); });
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

function printInvoice() {
    var clone = cloneInvoiceTo('invoicePrintArea');
    if (!clone) return;
    window.print();
}

function downloadInvoice() {
    if (typeof html2pdf === 'undefined') {
        alert('Unable to load the PDF generator. Please check your connection, or use the Print option instead.');
        return;
    }

    var source = document.getElementById('invoiceArea');
    if (!source) return;

    var clone = cloneInvoiceTo('invoicePrintArea');
    var node = clone ? clone.querySelector('.invoice-sheet') : source;

    var btn = document.getElementById('invoiceDownloadBtn');
    var originalHtml = btn.innerHTML;
    var filename = source.getAttribute('data-invoice-filename') || 'invoice.pdf';

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Preparing PDF\u2026';

    var options = {
        margin: 12,
        filename: filename,
        image: { type: 'jpeg', quality: 0.95 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
    };

    html2pdf().set(options).from(node).save()
        .then(function () { restoreDownloadBtn(btn, originalHtml); })
        .catch(function () {
            restoreDownloadBtn(btn, originalHtml);
            alert('There was a problem generating the PDF. Please try again.');
        });
}

function restoreDownloadBtn(btn, originalHtml) {
    btn.disabled = false;
    btn.innerHTML = originalHtml;
}
