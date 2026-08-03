// ---- Dashboard: flash toast init ----
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('#flashToasts .toast').forEach(function (el) {
        new bootstrap.Toast(el, { delay: 4000 }).show();
    });
});
