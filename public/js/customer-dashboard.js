/* customer-dashboard.js */
function showResultModal(message, isSuccess) {
    document.getElementById('resultModalMsg').textContent = message;
    var icon = document.getElementById('resultModalIcon');
    if (isSuccess) {
        icon.className = 'fs-1 mb-2 text-success';
        icon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
    } else {
        icon.className = 'fs-1 mb-2 text-danger';
        icon.innerHTML = '<i class="bi bi-x-circle-fill"></i>';
    }
    new bootstrap.Modal(document.getElementById('resultModal')).show();
}
