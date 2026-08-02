    {{-- ==================== SCHEDULE DETAIL MODAL ==================== --}}
    <div class="modal fade schedule-modal" id="scheduleDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header align-items-start">
                    <div>
                        <span class="badge bg-secondary me-2" id="evStatusBadge"></span>
                        <h5 class="modal-title d-inline" id="evTitle"></h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="evMeta"></div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-outline-dark rounded-pill" id="evViewBooking" target="_blank">
                       View Booking
                    </a>
                    <button type="button" class="btn btn-dark rounded-pill" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
