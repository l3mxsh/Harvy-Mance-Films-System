@if($allApproved && $isFullyPaid && $postProduction->status !== 'delivered')
    <div class="modal fade" id="unlockModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('post-production.unlock', $postProduction->booking_id) }}">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title fw-bold"><i class="bi bi-unlock me-2"></i>Unlock Deliverables</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-0">Unlock deliverables for the client? They will be able to download the approved files immediately.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark-soft btn-radius-sm" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success rounded-2">
                            Unlock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        (function () {
            var unlockForm = document.querySelector('#unlockModal form');
            if (unlockForm) {
                unlockForm.addEventListener('submit', function () {
                    var btn = unlockForm.querySelector('button[type="submit"]');
                    if (!btn || btn.disabled) return;
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Unlock';
                });
            }
        })();
    </script>
@endif
