{{-- Invite User Modal - Uses session org_id, no $org variable needed --}}
<div class="modal fade" id="inviteUserModalAttendance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-3 p-md-4 border-0">
                <h5 class="modal-title fw-bold">
                    <i class="ti ti-user-plus me-2"></i> Add Employee
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                {{-- Organization ID from session - no need for $org --}}
                <input type="hidden" name="organization_id" value="{{ session('user.organization.id') ?? '' }}">

                <div class="modal-body p-4">
                    <div class="row g-3">
                        {{-- Employee Name --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark">Full Name</label>
                            <input type="text" name="name" class="form-control form-control-lg rounded-3" 
                                   placeholder="Enter full name" required>
                        </div>

                        {{-- Email Address --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg rounded-3" 
                                   placeholder="Enter email" required>
                        </div>

                        {{-- Temporary Password --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">Temporary Password</label>
                            <input type="password" id="tempPasswordAttendance" name="password" 
                                   class="form-control form-control-lg rounded-3" 
                                   placeholder="Enter password" required>
                            <small class="text-muted d-block mt-2">
                                <i class="ti ti-info-circle"></i> Employee will change this after first login
                            </small>
                        </div>

                        {{-- Phone (Optional) --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark">Phone Number (Optional)</label>
                            <input type="tel" name="phone" class="form-control form-control-lg rounded-3" 
                                   placeholder="Enter phone number">
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="mt-4 p-3 bg-info-subtle rounded-3">
                        <small class="text-dark">
                            <strong><i class="ti ti-bulb me-1"></i>Note:</strong> Share email and password with employee manually.
                        </small>
                    </div>
                </div>

                <div class="modal-footer bg-light p-3 border-0">
                    <button type="button" class="btn btn-light px-4 rounded-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-2 fw-semibold">
                        <i class="ti ti-plus me-1"></i> Add Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>