{{-- RESCHEDULE MODAL --}}
<div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i>Request Reschedule</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('customer.reschedule.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-soft alert-soft-info py-2 small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Current event date: <strong>{{ $booking->event_date->format('M d, Y') }}</strong>.
                        You have <strong>unlimited reschedules</strong> for this booking.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Event Date <span class="text-danger">*</span></label>
                        <input type="date" name="requested_date" class="form-control" required
                            min="{{ now()->addDays($leadTime + 1)->format('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Event Time <span class="text-danger">*</span></label>
                        <input type="time" name="requested_time" class="form-control" required
                            value="{{ $booking->event_time }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark-soft rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-dark rounded-pill">
                        <i class="bi bi-send me-1"></i>Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
