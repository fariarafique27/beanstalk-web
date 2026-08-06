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
        <div class="card-header bg-white border-bottom border-light p-3 p-md-3.5 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <div>
                <h5 class="fw-bold mb-0 text-dark">Attendance Logs</h5>
                <span class="fs-8 text-secondary">Real-time listing of daily employee clock-ins and status</span>
            </div>

            <div class="d-flex align-items-center gap-2 flex-nowrap attendance-controls">
                <!-- Status Filter Pills -->
                <div class="filter-pill-group" id="statusFilterGroup">
                    <button type="button" class="filter-pill active" data-filter="all">All</button>
                    <button type="button" class="filter-pill" data-filter="present">Present</button>
                    <button type="button" class="filter-pill" data-filter="absent">Absent</button>
                </div>

                <!-- Search Box -->
                <div class="search-box-compact">
                    <i class="ti ti-search"></i>
                    <input type="text" id="attendanceSearchInput" placeholder="Search...">
                </div>
            </div>
        </div>

        <style>
            .attendance-controls {
                flex-shrink: 0;
            }

            .filter-pill-group {
                display: inline-flex;
                background: #f1f3f5;
                border-radius: 8px;
                padding: 3px;
                gap: 2px;
            }

            .filter-pill {
                border: none;
                background: transparent;
                color: #6c757d;
                font-size: 0.78rem;
                font-weight: 600;
                padding: 5px 12px;
                border-radius: 6px;
                white-space: nowrap;
                transition: all 0.15s ease;
            }

            .filter-pill:hover {
                color: #212529;
            }

            .filter-pill.active {
                background: #ffffff;
                color: #212529;
                box-shadow: 0 1px 2px rgba(0,0,0,0.08);
            }

            .filter-pill[data-filter="present"].active {
                color: #198754;
            }

            .filter-pill[data-filter="absent"].active {
                color: #dc3545;
            }

            .search-box-compact {
                display: flex;
                align-items: center;
                background: #f1f3f5;
                border-radius: 8px;
                padding: 6px 10px;
                gap: 6px;
                width: 160px;
                transition: width 0.15s ease;
            }

            .search-box-compact:focus-within {
                width: 200px;
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
                .attendance-controls {
                    width: 100%;
                }
                .search-box-compact {
                    width: 100%;
                }
                .filter-pill-group {
                    flex-shrink: 0;
                }
            }
        </style>

        <div class="table-responsive">
            <table class="table table-borderless table-hover align-middle mb-0" id="attendanceTable" style="min-width: 700px;">
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
                <tbody class="fs-7" id="attendanceTableBody">
                    @forelse($attendances as $item)
                        @php
                            $itemArray = is_array($item) ? $item : (is_object($item) ? (array) $item : []);
                            $rowStatus = strtolower($itemArray['status'] ?? 'absent');
                            $rowUserId = $itemArray['user_id'] ?? $itemArray['id'] ?? '';
                            $rowName = $itemArray['user_name'] ?? '';
                        @endphp

                        @if(!empty($itemArray))
                            <tr class="border-bottom border-light-subtle attendance-row"
                                data-status="{{ $rowStatus }}"
                                data-search="{{ strtolower($rowUserId . ' ' . $rowName) }}">
                                <td class="ps-4 text-secondary fw-medium">#{{ $rowUserId ?: '—' }}</td>
                                <td class="fw-semibold text-dark">{{ $rowName ?: '—' }}</td>
                                <td class="text-secondary">{{ $itemArray['check_in'] ?? '—' }}</td>
                                <td class="text-secondary">{{ $itemArray['check_out'] ?? '—' }}</td>
                                <td>
                                    @if($rowStatus === 'present')
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
                                    <button class="btn btn-sm btn-light rounded-circle p-2" data-bs-toggle="modal" data-bs-target="#inviteUserModalAttendance">
                                        <i class="ti ti-user-plus text-primary fs-5"></i>
                                    </button>
                                </td>

                                <td class="pe-4 text-end">
                                    <a href="{{ route('attendances.show', $itemArray['employee_id'] ?? ($rowUserId ?: 1)) }}" class="btn btn-light btn-sm rounded-2 fw-semibold px-2.5 py-1.5 d-inline-flex align-items-center gap-1 text-primary">
                                        <i class="ti ti-history fs-5"></i> History Logs
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @empty
                    @endforelse
                </tbody>
            </table>

            <!-- Empty state shown/hidden via JS when filters match nothing -->
            <div id="noResultsState" class="text-center py-5 d-none">
                <div class="empty-state p-4">
                    <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 text-secondary">
                        <i class="ti ti-calendar-off fs-1"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">No Matching Records</h6>
                    <p class="text-secondary fs-7 mb-0">Try a different search term or filter.</p>
                </div>
            </div>

            @if(empty($attendances))
                <div class="text-center py-5">
                    <div class="empty-state p-4">
                        <div class="bg-light rounded-circle d-inline-flex p-3 mb-3 text-secondary">
                            <i class="ti ti-calendar-off fs-1"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">No Attendance Records Found</h6>
                        <p class="text-secondary fs-7 mb-0">No attendance records found for today.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('attendanceSearchInput');
    const filterButtons = document.querySelectorAll('#statusFilterGroup button');
    const rows = document.querySelectorAll('#attendanceTableBody .attendance-row');
    const noResultsState = document.getElementById('noResultsState');

    let currentStatusFilter = 'all';

    function applyFilters() {
        const query = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        rows.forEach(function (row) {
            const matchesStatus = currentStatusFilter === 'all' || row.dataset.status === currentStatusFilter;
            const matchesSearch = query === '' || row.dataset.search.includes(query);
            const isVisible = matchesStatus && matchesSearch;

            row.classList.toggle('d-none', !isVisible);
            if (isVisible) visibleCount++;
        });

        noResultsState.classList.toggle('d-none', visibleCount !== 0 || rows.length === 0);
    }

    filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () {
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentStatusFilter = btn.dataset.filter;
            applyFilters();
        });
    });

    searchInput.addEventListener('input', applyFilters);
});
</script>
@include('invite-user-modal')

@endsection