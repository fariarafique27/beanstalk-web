@extends('layouts.app')

@section('title', 'Super Admin - Overview')

@section('page-header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <h3 class="fw-bold mb-1">Super Admin Console</h3>
        <p class="text-muted mb-0">Manage system organizations, admins, and permissions</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#createOrgAdminModal">
        <i class="ti ti-plus me-1"></i> Add Org Admin
    </button>
</div>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="bg-primary text-white rounded-3 p-3 me-3">
                    <i class="ti ti-building fs-2"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase fs-7 fw-bold">Organizations</span>
                    <h3 class="mb-0 fw-bold">12</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="bg-success text-white rounded-3 p-3 me-3">
                    <i class="ti ti-user-check fs-2"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase fs-7 fw-bold">Active Admins</span>
                    <h3 class="mb-0 fw-bold">10</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="bg-warning text-white rounded-3 p-3 me-3">
                    <i class="ti ti-clock fs-2"></i>
                </div>
                <div>
                    <span class="text-muted text-uppercase fs-7 fw-bold">Pending Invites</span>
                    <h3 class="mb-0 fw-bold">2</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Organizations & Admins Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent py-3">
        <h5 class="fw-bold mb-0">Registered Organizations & Admins</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Organization</th>
                    <th>Org Admin</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Permissions</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold">Acme Corp</td>
                    <td>John Doe</td>
                    <td>john@acme.com</td>
                    <td><span class="badge bg-success-subtle text-success">Active</span></td>
                    <td><span class="badge bg-secondary">Full Access</span></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-outline-secondary"><i class="ti ti-pencil"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Create Org Admin & Grant Permissions -->
<div class="modal fade" id="createOrgAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="ti ti-user-plus me-1"></i> Invite New Organization Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/org-admins/invite" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Organization Name</label>
                            <input type="text" name="org_name" class="form-control" placeholder="e.g. Acme Corp" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Admin Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Jane Smith" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-medium">Admin Email Address</label>
                            <input type="email" name="email" class="form-control" placeholder="jane@acme.com" required>
                            <div class="form-text">An invitation link to set password will be sent to this email.</div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="fw-bold mb-3"><i class="ti ti-key me-1"></i> Assign Permissions</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="employees.manage" id="perm1" checked>
                                <label class="form-check-label fw-medium" for="perm1">Employee Management</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="payroll.manage" id="perm2">
                                <label class="form-check-label fw-medium" for="perm2">Payroll Access</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="attendance.manage" id="perm3" checked>
                                <label class="form-check-label fw-medium" for="perm3">Attendance Tracking</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="settings.manage" id="perm4">
                                <label class="form-check-label fw-medium" for="perm4">Company Settings</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="ti ti-send me-1"></i> Send Invitation</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection