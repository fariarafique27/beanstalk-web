@extends('layouts.app')

@section('title', 'Org Super Admin Command Center')

@section('content')

{{-- Top Welcome Banner & Action Bar --}}
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <h3 class="fw-bold tracking-tight mb-0">System Overview</h3>
        </div>
        <p class="text-secondary mb-0 fs-7">Manage tenant organizations, track invitations, and monitor global HR operations.</p>
    </div>
    
    <div class="w-100 w-md-auto d-flex align-items-center justify-content-end ms-md-auto">
            <button class="btn btn-primary px-3.5 py-2.5 shadow-sm fw-semibold rounded-3 d-flex align-items-center justify-content-center gap-2 text-white" data-bs-toggle="modal" data-bs-target="#createOrgAdminModal">
                <i class="ti ti-plus fs-5"></i> Add New Organization
            </button>
    </div>
</div>

{{-- Notifications --}}
@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 py-2.5 px-3 mb-4 d-flex align-items-center gap-2">
        <i class="ti ti-circle-check fs-4 text-success"></i>
        <span>{{ session('success') }}</span>
        <button type="button" class="btn-close ms-auto fs-7" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->has('invite_error'))
    <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2.5 px-3 mb-4 d-flex align-items-center gap-2">
        <i class="ti ti-alert-triangle fs-4 text-danger"></i>
        <span>{{ $errors->first('invite_error') }}</span>
        <button type="button" class="btn-close ms-auto fs-7" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Dynamic Visual Stats Cards --}}
<div class="row g-3 mb-4">
    <!-- Total Organizations Card -->
    <div class="col-12 col-sm-6 col-lg-6">
        <div class="card stat-card card-accent-primary shadow-sm rounded-4 p-3.5 p-md-4 bg-white h-100">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="flex-grow-1 min-w-0">
                    <span class="text-uppercase text-secondary fw-bold fs-8 tracking-wider d-block mb-1">Total Tenants</span>
                    <h2 class="fw-extrabold mb-0 text-dark fs-1">{{ $stats['total_orgs'] ?? 0 }}</h2>
                </div>
                <div class="icon-shape bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                    <i class="ti ti-building-community fs-3"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top border-light-subtle fs-8 text-secondary d-flex align-items-center gap-1 flex-wrap">
                <span class="text-success fw-bold d-inline-flex align-items-center"><i class="ti ti-trending-up me-1"></i> Active</span>
                <span>tenants across instances</span>
            </div>
        </div>
    </div>

    <!-- Active Admins Card -->
    <div class="col-12 col-sm-6 col-lg-6">
        <div class="card stat-card card-accent-success shadow-sm rounded-4 p-3.5 p-md-4 bg-white h-100">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="flex-grow-1 min-w-0">
                    <span class="text-uppercase text-secondary fw-bold fs-8 tracking-wider d-block mb-1">Active Admins</span>
                    <h2 class="fw-extrabold mb-0 text-dark fs-1">{{ $stats['active_admins'] ?? 0 }}</h2>
                </div>
                <div class="icon-shape bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                    <i class="ti ti-shield-check fs-3"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top border-light-subtle fs-8 text-secondary d-flex align-items-center gap-1 flex-wrap">
                <span class="text-dark fw-bold">Verified</span>
                <span>company administrators</span>
            </div>
        </div>
    </div>

    <!-- Pending Invites Card -->
    <!-- <div class="col-12 col-sm-12 col-lg-4">
        <div class="card stat-card card-accent-warning shadow-sm rounded-4 p-3.5 p-md-4 bg-white h-100">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <div class="flex-grow-1 min-w-0">
                    <span class="text-uppercase text-secondary fw-bold fs-8 tracking-wider d-block mb-1">Pending Invites</span>
                    <h2 class="fw-extrabold mb-0 text-dark fs-1">{{ $stats['pending_invites'] ?? 0 }}</h2>
                </div>
                <div class="icon-shape bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                    <i class="ti ti-clock-hour-4 fs-3"></i>
                </div>
            </div>
            <div class="mt-3 pt-2 border-top border-light-subtle fs-8 text-secondary d-flex align-items-center gap-1 flex-wrap">
                <span class="text-warning fw-bold d-inline-flex align-items-center"><i class="ti ti-mail-forward me-1"></i> Awaiting</span>
                <span>account setup completion</span>
            </div>
        </div>
    </div>
</div> -->

{{-- Main Data Section --}}
    <div class="card-header bg-white border-bottom border-light p-3 p-md-3.5 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
        <div>
            <h5 class="fw-bold mb-0 text-dark">Organization Directory</h5>
            <span class="fs-8 text-secondary">Real-time listing of onboarded companies and access permissions</span>
        </div>
        <div class="search-box-compact">
            <i class="ti ti-search"></i>
            <input type="text" id="orgSearchInput" placeholder="Search organization...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-borderless table-hover align-middle mb-0" style="min-width: 700px;">
            <thead class="bg-light-subtle text-secondary fs-8 text-uppercase tracking-wider">
                <tr>
                    <th class="ps-5">Organization</th>
                    <th>Admin Name</th>
                    <th>Email Address</th>
                    <th>Status</th>
                    <!-- <th>Permissions</th>
                    <th class="pe-4 text-end">Action</th> -->
                </tr>
            </thead>
            <tbody class="fs-7">
                @forelse($organizations as $org)
                    <tr class="border-bottom border-light-subtle">
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="avatar-initials rounded-3 bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    {{ strtoupper(substr($org['org_name'] ?? 'O', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h6 class="fw-bold text-dark mb-0 fs-7 text-truncate">{{ $org['org_name'] ?? 'N/A' }}</h6>
                                    <span class="fs-8 text-muted">ID: #{{ $org['id'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="fw-medium text-dark">{{ $org['admin_name'] ?? 'Unassigned' }}</td>
                        <td class="text-secondary">{{ $org['admin_email'] ?? 'N/A' }}</td>
                        <td>
                            @if(($org['status'] ?? '') === 'active')
                                <span class="badge status-badge bg-success-subtle text-success rounded-pill px-3 py-1">
                                    <i class="ti ti-point-filled"></i> Active
                                </span>
                            @else
                                <span class="badge status-badge bg-warning-subtle text-warning rounded-pill px-3 py-1">
                                    <i class="ti ti-clock"></i> Pending Invite
                                </span>
                            @endif
                        </td>

                        <!-- <td>
                            <div class="d-flex gap-1 flex-wrap">
                                @php
                                    $rawPerms = $org['permissions'] ?? ['Standard'];
                                    
                                    // Normalize into a flat array of strings
                                    if (is_string($rawPerms)) {
                                        $rawPerms = explode(',', $rawPerms);
                                    } elseif (is_object($rawPerms) && method_exists($rawPerms, 'toArray')) {
                                        $rawPerms = $rawPerms->toArray();
                                    }
                                    
                                    if (!is_array($rawPerms)) {
                                        $rawPerms = [(string)$rawPerms];
                                    }
                                @endphp

                                @foreach($rawPerms as $perm)
                                    @php
                                        // Extract text safely without trim()
                                        if (is_array($perm)) {
                                            $text = $perm['name'] ?? $perm['title'] ?? json_encode($perm);
                                        } elseif (is_object($perm)) {
                                            $text = $perm->name ?? $perm->title ?? '';
                                        } else {
                                            $text = (string) $perm;
                                        }
                                    @endphp

                                    @if(!empty($text) && !is_array($text))
                                        <span class="badge bg-light text-secondary border border-light-subtle rounded-2 fs-8 fw-medium">
                                            {{ (string) $text }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="pe-4 text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle p-1" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical text-secondary fs-5"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 fs-7">
                                    {{-- Edit Trigger --}}
                                    <li>
                                        <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#editOrgModal{{ $org['id'] }}">
                                            <i class="ti ti-edit text-primary"></i> Edit Details
                                        </button>
                                    </li>
                                    
                                    {{-- Resend Email Trigger --}}
                                    <li>
                                        <form action="{{ route('organizations.resend', $org['id']) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-warning border-0 bg-transparent w-100 text-start">
                                                <i class="ti ti-send"></i> Resend Invite
                                            </button>
                                        </form>
                                    </li>

                                    <li><hr class="dropdown-divider"></li>

                                    {{-- Delete / Suspend Trigger --}}
                                    <li>
                                        <form action="{{ route('organizations.destroy', $org['id']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this organization?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 text-danger border-0 bg-transparent w-100 text-start">
                                                <i class="ti ti-trash"></i> Suspend Org
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td> -->
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="empty-state p-4">
                                <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 text-secondary">
                                    <i class="ti ti-building-off fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">No Organizations Found</h6>
                                <p class="text-secondary fs-7 mb-3">You haven't onboarded any organizations to the system yet.</p>
                                <button class="btn btn-primary btn-sm px-3 rounded-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#createOrgAdminModal">
                                    <i class="ti ti-plus me-1"></i> Add Your First Org
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Responsive Add Organization Modal --}}
<div class="modal fade" id="createOrgAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-3 p-md-4 border-0">
                <div>
                    <h5 class="modal-title fw-bold mb-1"><i class="ti ti-building-plus me-2 text-primary"></i> Invite New Organization</h5>
                    <p class="text-white-50 fs-8 mb-0">Set up company account and assign administrator access permissions.</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <!-- <form action="{{ route('org-admins.invite') }}" method="POST"> -->
                <form action="{{ route('org-admins.store') }}" method="POST">
                @csrf
                <div class="modal-body p-3 p-md-4 bg-white">
                    {{-- Modal Validation Error Alert --}}
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2">
                            <i class="ti ti-alert-circle fs-5 text-danger"></i>
                            <span class="fs-8">Please fix the errors below before submitting.</span>
                        </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold fs-7 text-dark">Organization / Company Name</label>
                            <input type="text" name="org_name" class="form-control form-control-lg fs-7 rounded-3" placeholder="e.g. Acme Corporation" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold fs-7 text-dark">Org Admin Full Name</label>
                            <input type="text" name="name" class="form-control form-control-lg fs-7 rounded-3" placeholder="e.g. Jane Smith" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold fs-7 text-dark">Admin Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg fs-7 rounded-3" placeholder="jane@acme.com" required>
                            <span class="fs-8 text-muted mt-1 d-block"><i class="ti ti-info-circle"></i> We will dispatch an activation token to this email address.</span>
                        </div>
                    </div>

                    <div class="my-4 border-top"></div>
                    <h6 class="fw-bold fs-7 text-dark mb-3"><i class="ti ti-shield-lock me-1 text-primary"></i> Grant Module Permissions</h6>
                        <div class="row g-3">
                            @foreach($permissions as $permission)
                                <div class="col-12 col-md-6">
                                    <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="fw-bold fs-7 mb-0">{{ ucwords(str_replace('.', ' ', is_array($permission) ? $permission['name'] : $permission->name)) }}</h6>
                                            <span class="fs-8 text-muted">Manage records & access</span>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ is_array($permission) ? $permission['name'] : $permission->name }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                </div>
                <div class="modal-footer bg-light p-3 border-0">
                    <button type="button" class="btn btn-light px-4 fs-7 rounded-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4 fs-7 rounded-2 fw-semibold"><i class="ti ti-send me-1"></i> Dispatch Invitation</button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
    .search-box-compact {
        display: flex;
        align-items: center;
        background: #f1f3f5;
        border-radius: 8px;
        padding: 6px 10px;
        gap: 6px;
        width: 220px;
        transition: width 0.15s ease;
        flex-shrink: 0;
    }
    .search-box-compact:focus-within {
        width: 260px;
        background: #e9ecef;
    }
    .search-box-compact i {
        color: #6c757d;
        font-size: 0.85rem;
        flex-shrink: 0;
    }
    .search-box-compact input {
        border: none;
        background: transparent;
        outline: none;
        font-size: 0.8rem;
        width: 100%;
        color: #212529;
    }
    @media (max-width: 576px) {
        .search-box-compact { width: 100%; }
    }
</style>


@endsection