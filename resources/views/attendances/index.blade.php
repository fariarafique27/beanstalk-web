@extends('layouts.app')
@section('title', 'Today Attendances')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header & Action Bar -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                <h3 class="fw-bold tracking-tight mb-0">Today's Attendance Overview</h3>
            </div>
            <p class="text-secondary mb-0 fs-7">Monitor live employee check-ins, check-outs, and daily attendance status.</p>
        </div>
    </div>

    <!-- Summary Metrics Cards -->
    <div class="row g-3 mb-4">
        <!-- Present Card -->
        <div class="col-12 col-sm-6">
            <div class="card stat-card card-accent-success shadow-sm rounded-4 p-3.5 p-md-4 bg-white h-100">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="flex-grow-1 min-w-0">
                        <span class="text-uppercase text-secondary fw-bold fs-8 tracking-wider d-block mb-1">Today's Present</span>
                        <h2 class="fw-extrabold mb-0 text-dark fs-1">{{ $totalPresent ?? 0 }}</h2>
                    </div>
                    <div class="icon-shape bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                        <i class="ti ti-user-check fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light-subtle fs-8 text-secondary d-flex align-items-center gap-1 flex-wrap">
                    <span class="text-success fw-bold d-inline-flex align-items-center"><i class="ti ti-trending-up me-1"></i> Recorded</span>
                    <span>present staff members</span>
                </div>
            </div>
        </div>

        <!-- Absent Card -->
        <div class="col-12 col-sm-6">
            <div class="card stat-card card-accent-danger shadow-sm rounded-4 p-3.5 p-md-4 bg-white h-100">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div class="flex-grow-1 min-w-0">
                        <span class="text-uppercase text-secondary fw-bold fs-8 tracking-wider d-block mb-1">Today's Absent</span>
                        <h2 class="fw-extrabold mb-0 text-dark fs-1">{{ $totalAbsent ?? 0 }}</h2>
                    </div>
                    <div class="icon-shape bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 46px; height: 46px;">
                        <i class="ti ti-user-x fs-3"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light-subtle fs-8 text-secondary d-flex align-items-center gap-1 flex-wrap">
                    <span class="text-danger fw-bold d-inline-flex align-items-center"><i class="ti ti-alert-circle me-1"></i> Missing</span>
                    <span>unaccounted or absent logs</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Data Section -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-header bg-white border-bottom border-light p-3 p-md-3.5 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Attendance Logs</h5>
                <span class="fs-8 text-secondary">Real-time listing of daily employee clock-ins and status</span>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-sm-auto">
                <div class="input-group input-group-sm search-box w-100">
                    <span class="input-group-text bg-light border-0"><i class="ti ti-search text-secondary"></i></span>
                    <input type="text" class="form-control bg-light border-0" placeholder="Search attendance...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-borderless table-hover align-middle mb-0" style="min-width: 700px;">
                <thead class="bg-light-subtle text-secondary fs-8 text-uppercase tracking-wider">
                    <tr>
                        <th class="ps-4 py-3">User ID</th>
                        <th class="py-3">Name</th>
                        <th class="py-3">Check In</th>
                        <th class="py-3">Check Out</th>
                        <th class="py-3">Status</th>
                        <th class="pe-4 py-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($attendances as $item)
                        @php
                            // Safely convert item to an array whether it's an array or an object
                            $itemArray = is_array($item) ? $item : (is_object($item) ? (array) $item : []);
                        @endphp

                        @if(!empty($itemArray))
                            <tr class="border-bottom border-light-subtle">
                                <td class="ps-4 text-secondary fw-medium">#{{ $itemArray['user_id'] ?? $itemArray['id'] ?? 'N/A' }}</td>
                                <td class="fw-semibold text-dark">{{ $itemArray['user_name'] ?? 'N/A' }}</td>
                                <td class="text-secondary">{{ $itemArray['check_in'] ?? 'N/A' }}</td>
                                <td class="text-secondary">{{ $itemArray['check_out'] ?? 'N/A' }}</td>
                                <td>
                                    @if(isset($itemArray['status']) && strtolower($itemArray['status']) === 'present')
                                        <span class="badge status-badge bg-success-subtle text-success rounded-pill px-3 py-1">
                                            <i class="ti ti-point-filled"></i> Present
                                        </span>
                                    @else
                                        <span class="badge status-badge bg-danger-subtle text-danger rounded-pill px-3 py-1">
                                            <i class="ti ti-point-filled"></i> Absent
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('attendances.show', $itemArray['employee_id'] ?? ($itemArray['user_id'] ?? 1)) }}" class="btn btn-light btn-sm rounded-2 fw-semibold px-2.5 py-1.5 d-inline-flex align-items-center gap-1 text-primary">
                                        <i class="ti ti-history fs-5"></i> History Logs
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="empty-state p-4">
                                    <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 text-secondary">
                                        <i class="ti ti-calendar-off fs-1"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1">No Attendance Records Found</h6>
                                    <p class="text-secondary fs-7 mb-0">No attendance records found for today.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection