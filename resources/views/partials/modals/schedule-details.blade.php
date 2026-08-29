{{-- SCHEDULE DETAIL MODAL --}}
<div class="modal fade" id="scheduleDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar2-week me-2"></i>Schedule Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 pb-3 border-bottom">
                    <div>
                        <div class="text-muted small">Booking Reference</div>
                        <div class="fw-bold fs-5" id="evBookingRef">—</div>
                    </div>
                    <span id="evStatusBadge" class="badge fs-6"></span>
                </div>

                <div id="evMeta"></div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-outline-dark rounded-pill" id="evViewBooking">
                    <i class="bi bi-box-arrow-up-right me-1"></i> View Booking
                </a>
                <button type="button" class="btn btn-dark rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
