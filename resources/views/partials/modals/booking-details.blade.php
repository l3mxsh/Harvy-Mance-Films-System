{{-- BOOKING DETAILS MODAL --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Booking Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body view-details-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4 pb-3 border-bottom">
                    <div>
                        <div class="text-muted small">Booking Reference</div>
                        <div class="fw-bold fs-5" id="viewRef">—</div>
                    </div>
                    <span id="viewStatusBadge" class="badge fs-6"></span>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold small text-uppercase text-muted mb-2">Client</h6>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="text-muted small">Name</div>
                            <div class="fw-medium" id="viewClientName">—</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">Email</div>
                            <div class="fw-medium" id="viewClientEmail">—</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">Contact Number</div>
                            <div class="fw-medium" id="viewClientPhone">—</div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold small text-uppercase text-muted mb-2">Package & Add-Ons</h6>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="text-muted small">Package</div>
                            <div class="fw-medium" id="viewPackageName">—</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">Package Price</div>
                            <div class="fw-medium" id="viewPackagePrice">—</div>
                        </div>
                        <div class="col-12" id="viewServicesWrap" style="display:none;">
                            <div class="text-muted small mb-1">Included Services</div>
                            <div id="viewServices" class="d-flex flex-wrap gap-1"></div>
                        </div>
                        <div class="col-12" id="viewAddonsSection" style="display:none;">
                            <div class="text-muted small mb-1">Add-Ons</div>
                            <div id="viewAddonsList" class="mb-1"></div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold small text-uppercase text-muted mb-2">Event</h6>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="text-muted small">Type</div>
                            <div class="fw-medium" id="viewEventType">—</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">Date</div>
                            <div class="fw-medium" id="viewEventDate">—</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">Time</div>
                            <div class="fw-medium" id="viewEventTime">—</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">Venue</div>
                            <div class="fw-medium" id="viewVenue">—</div>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-muted small">Address</div>
                            <div class="fw-medium" id="viewAddress">—</div>
                        </div>
                        <div class="col-12" id="viewEventDescWrap" style="display:none;">
                            <div class="text-muted small mb-1">Event Description</div>
                            <div class="info-box mb-0">
                                <i class="bi bi-info-circle-fill info-box-icon"></i>
                                <span id="viewEventDesc">—</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <h6 class="fw-semibold small text-uppercase text-muted mb-2">Payment</h6>
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="text-muted small">Total Price</div>
                            <div class="fw-bold" id="viewTotal">—</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">Downpayment (30%)</div>
                            <div class="fw-medium" id="viewDownpayment">—</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">Remaining Balance</div>
                            <div class="fw-medium" id="viewBalance">—</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">Assigned Team</div>
                            <div class="fw-medium" id="viewTeam">—</div>
                        </div>
                        <div class="col-sm-4">
                            <div class="text-muted small">Payment Status</div>
                            <div id="viewPaymentStatus">—</div>
                        </div>
                    </div>
                </div>

                <div id="viewNotesWrap" style="display:none;" class="mb-4">
                    <h6 class="fw-semibold small text-uppercase text-muted mb-2">Notes</h6>
                    <div class="alert alert-soft py-2 mb-0" id="viewNotes">—</div>
                </div>

                <div class="text-muted small border-top pt-2">Submitted: <span id="viewCreatedAt">—</span></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
