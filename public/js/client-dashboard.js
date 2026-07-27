/* client-dashboard.js */
document.addEventListener('DOMContentLoaded', function () {
    // Tooltip init
    var tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(function (el) { new bootstrap.Tooltip(el); });

    // Password toggle
    var toggleBtns = document.querySelectorAll('.toggle-pw');
    toggleBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var target = document.getElementById(this.dataset.target);
            if (!target) return;
            var icon = this.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                target.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    });

    // Confirm password match
    var newPw = document.getElementById('new_password');
    var confirmPw = document.getElementById('new_password_confirmation');
    var matchIndicator = document.getElementById('pwMatchIndicator');
    if (newPw && confirmPw && matchIndicator) {
        function checkMatch() {
            if (!confirmPw.value) { matchIndicator.textContent = ''; return; }
            if (newPw.value === confirmPw.value) {
                matchIndicator.textContent = 'Passwords match';
                matchIndicator.className = 'form-text text-success';
            } else {
                matchIndicator.textContent = 'Passwords do not match';
                matchIndicator.className = 'form-text text-danger';
            }
        }
        newPw.addEventListener('input', checkMatch);
        confirmPw.addEventListener('input', checkMatch);
    }
});
