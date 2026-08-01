{{-- ==================== CREATE OUTSOURCED MODAL ==================== --}}
<div class="modal fade" id="createOutsourcedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-badge me-2 text-warning"></i>Add Outsourced Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('outsourced-staff.store') }}">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-3">Record only — no login account. Used for shoot-day assignments and post-production tracking.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="e.g. staff@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" placeholder="e.g. 09XXXXXXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" name="notes" class="form-control" placeholder="e.g. Photographer, Videographer...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning rounded-pill"><i class="bi bi-check-lg me-1"></i>Add Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== EDIT OUTSOURCED MODAL ==================== --}}
<div class="modal fade" id="editOutsourcedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2 text-secondary"></i>Edit Outsourced Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editOutsourcedForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editOsName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" id="editOsEmail" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" id="editOsContact" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Notes</label>
                        <input type="text" name="notes" id="editOsNotes" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill"><i class="bi bi-check-lg me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== DELETE OUTSOURCED MODAL ==================== --}}
<div class="modal fade" id="deleteOutsourcedModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-trash me-2 text-danger"></i>Delete Outsourced Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="deleteOutsourcedForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Delete record for <strong id="deleteOsName"></strong>? This cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill"><i class="bi bi-trash me-1"></i>Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== VIEW STAFF MODAL ==================== --}}
<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-badge me-2 text-primary"></i>Staff Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless mb-0">
                    <tr><td class="text-muted" style="width:140px;">Name</td><td class="fw-semibold" id="viewName"></td></tr>
                    <tr><td class="text-muted">Email</td><td id="viewEmail"></td></tr>
                    <tr><td class="text-muted">Contact</td><td id="viewContact"></td></tr>
                    <tr><td class="text-muted">Status</td><td id="viewStatus"></td></tr>
                    <tr><td class="text-muted">Joined</td><td id="viewJoined"></td></tr>
                    <tr><td class="text-muted">Last Login</td><td id="viewLastLogin"></td></tr>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ==================== CREATE STAFF MODAL ==================== --}}
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2 text-primary"></i>Add Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('staff.admin.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required placeholder="staff@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" class="form-control" placeholder="e.g. 09XXXXXXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="createPassword" class="form-control" required minlength="6" placeholder="Minimum 6 characters">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="createPassword" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password_confirmation" id="createPasswordConfirmation" class="form-control" required minlength="6" placeholder="Re-enter password">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="createPasswordConfirmation" tabindex="-1">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill"><i class="bi bi-check-lg me-1"></i> Create Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ==================== EDIT STAFF MODAL ==================== --}}
<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-pencil me-2 text-secondary"></i>Edit Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editName" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="editEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contact Number</label>
                        <input type="text" name="contact_number" id="editContact" class="form-control">
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-person-check me-2 text-secondary"></i>Account Status</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold" id="editStatusLabel">Active</div>
                            <small class="text-muted">Enable or disable this staff account.</small>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editStatusSwitch" role="switch">
                        </div>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="bi bi-key me-2 text-secondary"></i>Reset Password</h6>

                    <input type="hidden" name="notify" id="editNotify" value="0">
                    <div id="editPasswordSection" class="d-none mx-auto">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small mb-0">New Password</label>
                                <button type="button" class="btn btn-link btn-sm p-0 text-primary border-0" id="editGeneratePwBtn">
                                    Generate Random Password
                                </button>
                            </div>
                            <div class="input-group">
                                <input type="password" name="new_password" id="editNewPassword" class="form-control"
                                    minlength="6" placeholder="Leave blank to keep current password" autocomplete="off">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="editNewPassword" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password_confirmation" id="editNewPasswordConfirmation"
                                    class="form-control" minlength="6">
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="editNewPasswordConfirmation" tabindex="-1">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" id="editEmailPwBtn" class="btn btn-dark rounded-pill w-100">
                          Email New Password
                        </button>
                    </div>
                </div>
            </form>

            {{-- Outsource staff: generate & email temporary password --}}
            <div class="modal-body pt-0" id="editGenerateSection">
                <form method="POST" id="editGenerateForm">
                    @csrf
                    <p class="text-muted small">Generate a random temporary password and email it to this staff member.</p>
                    <button type="submit" class="btn btn-warning rounded-pill">
                        <i class="bi bi-envelope me-1"></i> Generate & Email Temporary Password
                    </button>
                </form>
            </div>

            {{-- Account status toggle form (referenced via form attribute, kept outside editForm) --}}
            <form method="POST" id="editToggleForm" class="d-none">
                @csrf
            </form>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editForm" class="btn btn-dark rounded-pill">
                    <i class="bi bi-check-lg me-1"></i> Update Staff
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ==================== DELETE STAFF MODAL ==================== --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-trash me-2 text-danger"></i>Delete Staff</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <p>Are you sure you want to permanently delete this staff account?</p>
                    <div class="alert alert-warning py-2 mb-0">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        This action cannot be undone. All data for <strong id="deleteStaffName"></strong> (<span id="deleteStaffEmail"></span>) will be permanently removed.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill"><i class="bi bi-trash me-1"></i> Delete Permanently</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($tab === 'teams')
    {{-- ==================== CREATE TEAM MODAL ==================== --}}
    <div class="modal fade" id="createTeamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-people me-2 text-primary"></i>Create Team</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('team.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Team Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="e.g. Team Alpha">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Brief description of this team..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Members</label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                                @forelse($allStaff as $s)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="member_ids[]" value="{{ $s->id }}" id="create_member_{{ $s->id }}">
                                        <label class="form-check-label" for="create_member_{{ $s->id }}">
                                            {{ $s->name }} <small class="text-muted">({{ $s->email }})</small>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-muted mb-0">No active staff found.</p>
                                @endforelse
                            </div>
                        </div>
                        @if($allOutsourced->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Outsourced Staff</label>
                            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                @foreach($allOutsourced as $os)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="outsourced_ids[]" value="{{ $os->id }}" id="create_os_{{ $os->id }}">
                                        <label class="form-check-label" for="create_os_{{ $os->id }}">
                                            {{ $os->name }} <small class="text-warning">(Outsourced)</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark rounded-pill"><i class="bi bi-check-lg me-1"></i> Create Team</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== EDIT TEAM MODAL ==================== --}}
    <div class="modal fade" id="editTeamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil me-2 text-secondary"></i>Edit Team</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="editTeamForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Team Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editTeamName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="editTeamDesc" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Members</label>
                            <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;" id="editMembersList">
                                @foreach($allStaff as $s)
                                    <div class="form-check">
                                        <input class="form-check-input edit-member-check" type="checkbox" name="member_ids[]" value="{{ $s->id }}" id="edit_member_{{ $s->id }}">
                                        <label class="form-check-label" for="edit_member_{{ $s->id }}">
                                            {{ $s->name }} <small class="text-muted">({{ $s->email }})</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @if($allOutsourced->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Outsourced Staff</label>
                            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                @foreach($allOutsourced as $os)
                                    <div class="form-check">
                                        <input class="form-check-input edit-outsourced-check" type="checkbox" name="outsourced_ids[]" value="{{ $os->id }}" id="edit_os_{{ $os->id }}">
                                        <label class="form-check-label" for="edit_os_{{ $os->id }}">
                                            {{ $os->name }} <small class="text-warning">(Outsourced)</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-dark rounded-pill"><i class="bi bi-check-lg me-1"></i> Update Team</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== TOGGLE TEAM STATUS MODAL ==================== --}}
    <div class="modal fade" id="toggleTeamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="toggleTeamHeader">
                    <h5 class="modal-title" id="toggleTeamTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="toggleTeamForm">
                    @csrf
                    <div class="modal-body"><p id="toggleTeamMessage"></p></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn" id="toggleTeamBtn">Confirm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== DELETE TEAM MODAL ==================== --}}
    <div class="modal fade" id="deleteTeamModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-trash me-2 text-danger"></i>Delete Team</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="deleteTeamForm">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p>Are you sure you want to permanently delete <strong id="deleteTeamName"></strong>?</p>
                        <div class="alert alert-warning py-2 mb-0">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            This action cannot be undone. Members will be unlinked but not deleted.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-dark rounded-pill" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger rounded-pill"><i class="bi bi-trash me-1"></i> Delete Team</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
