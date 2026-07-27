// post-production.js
document.addEventListener('DOMContentLoaded', function () {

    // Status form confirmation
    const statusForm = document.getElementById('statusForm');
    const statusSelect = document.getElementById('statusSelect');

    if (statusForm && statusSelect) {
        statusForm.addEventListener('submit', function (e) {
            const selected = statusSelect.options[statusSelect.selectedIndex].text;
            if (!confirm('Update status to "' + selected + '"?')) {
                e.preventDefault();
            }
        });
    }

});
