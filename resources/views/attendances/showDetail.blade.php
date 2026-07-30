@extends('layouts.app')
@section('title', 'Employee Attendance History')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <a href="{{ route('attendances.index') }}" class="btn btn-light btn-sm rounded-2 text-secondary d-inline-flex align-items-center gap-1">
                    <i class="ti ti-arrow-left"></i> Back to Overview
                </a>
            </div>
            <h3 class="fw-bold tracking-tight mb-0">Attendance History Logs</h3>
            <p class="text-secondary mb-0 fs-7">Detailed clock-in history for User ID: #{{ $id }}</p>
        </div>
    </div>

    <!-- Main Data Section -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Search and Filter Form Header -->
        <form method="GET" action="{{ route('attendances.show', $id) }}" class="card-header bg-white border-bottom border-light p-3 p-md-3.5 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Logs History</h5>
                <span class="fs-8 text-secondary">Filter and search past attendance records</span>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap w-100 w-md-auto">
                <!-- Status Filter -->
                <select name="status" class="form-select form-select-sm bg-light border-0 w-auto" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="Present" {{ request('status') == 'Present' ? 'selected' : '' }}>Present</option>
                    <option value="Absent" {{ request('status') == 'Absent' ? 'selected' : '' }}>Absent</option>
                </select>

                <!-- Search Input -->
                <div class="input-group input-group-sm search-box flex-grow-1 flex-md-grow-0" style="width: 220px;">
                    <span class="input-group-text bg-light border-0"><i class="ti ti-search text-secondary"></i></span>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control bg-light border-0" placeholder="Search logs...">
                </div>

                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('attendances.show', $id) }}" class="btn btn-sm btn-light text-secondary px-2" title="Reset Filters">
                        <i class="ti ti-rotate-clockwise"></i>
                    </a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-borderless table-hover align-middle mb-0" style="min-width: 600px;">
                <thead class="bg-light-subtle text-secondary fs-8 text-uppercase tracking-wider">
                    <tr>
                        <th class="ps-4 py-3">Attendance Date</th>
                        <th class="py-3">Check In</th>
                        <th class="py-3">Check Out</th>
                        <th class="py-3">Remarks</th>
                        <th class="pe-4 py-3">Status</th>
                    </tr>
                </thead>
            <tbody class="fs-7">
                @forelse($attendances['data'] ?? [] as $item)
                    @php
                        $itemArray = is_array($item) ? $item : (is_object($item) ? (array) $item : []);
                        $logs = $itemArray['attendance_logs'] ?? [];
                        $firstLog = count($logs) > 0 ? $logs[0] : [];
                    @endphp

                    @if(!empty($itemArray))
                        <tr class="border-bottom border-light-subtle">
                            <td class="ps-4 text-secondary fw-medium">
                                {{ $itemArray['attendance_date'] ?? 'N/A' }}
                            </td>
                            <td class="text-secondary">
                                {{ $firstLog['check_in_time'] ?? 'N/A' }}
                            </td>
                            <td class="text-secondary">
                                {{ $firstLog['check_out_time'] ?? 'N/A' }}
                            </td>
                            <td class="text-secondary">
                                {{ $itemArray['remarks'] ?? 'N/A' }}
                            </td>
                            <td class="pe-4">
                                @if(isset($itemArray['status']) && strtolower($itemArray['status']) === 'present')
                                    <span class="badge status-badge bg-success-subtle text-success rounded-pill px-3 py-1">
                                        <i class="ti ti-point-filled"></i> Present
                                    </span>
                                @else
                                <span class="badge status-badge bg-danger-subtle text-danger rounded-pill px-3 py-1">
                                    <i class="ti ti-point-filled"></i> {{ ucfirst($itemArray['status'] ?? 'Absent') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                @endif
                @empty
                    <tr>
                        <!-- Updated colspan to 5 to match your 5 header columns -->
                        <td colspan="5" class="text-center py-5">
                            <div class="empty-state p-4">
                                <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 text-secondary">
                                    <i class="ti ti-calendar-off fs-1"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">No History Logs Found</h6>
                                <p class="text-secondary fs-7 mb-0">No attendance logs found matching your filters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            </table>
        </div>

        <!-- Pagination Footer placed cleanly at the BOTTOM outside the table -->
        @if(isset($attendances['links']) && count($attendances['links']) > 3)
            <div class="card-footer bg-white border-top border-light py-3">
                <!-- Changed to justify-content-center so it sits in the middle -->
                <div class="d-flex justify-content-center">
                    <ul class="pagination pagination-sm mb-0">
                        @foreach($attendances['links'] as $link)
                            <li class="page-item {{ $link['active'] ? 'active' : '' }} {{ is_null($link['url']) ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $link['url'] ?? '#' }}">{!! $link['label'] !!}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection