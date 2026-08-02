@extends('layouts.app')
@section('title', 'Employee Attendance History')

@section('content')
<div class="container-fluid px-0">
    @php
        $historyUserName = $attendances['data'][0]['user_name'] ?? null;
    @endphp

    <!-- Back link -->
    <div class="mb-3">
        <a href="{{ route('attendances.index') }}" class="btn btn-light btn-sm rounded-2 text-secondary d-inline-flex align-items-center gap-1">
            <i class="ti ti-arrow-left"></i> Back to Overview
        </a>
    </div>

    <!-- Prominent Employee Identity Header -->
    <div class="d-flex align-items-center gap-3 p-3 p-md-4 mb-4 bg-white rounded-4 shadow-sm">
        <div class="avatar-initials rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0"
             style="width: 56px; height: 56px; font-size: 1.25rem;">
            {{ $historyUserName ? strtoupper(substr($historyUserName, 0, 1)) : '?' }}
        </div>
        <div class="min-w-0">
            <span class="fs-8 text-uppercase text-secondary fw-bold tracking-wider d-block mb-1">Attendance History</span>
            <h3 class="fw-extrabold mb-0 text-dark text-truncate">
                {{ $historyUserName ?? ('Employee #' . $id) }}
            </h3>
            <span class="fs-8 text-secondary">Employee ID #{{ $id }}</span>
        </div>
    </div>

    <!-- Main Data Section -->
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <!-- Filter Row -->
        <form method="GET" action="{{ route('attendances.show', $id) }}" class="card-header bg-white border-bottom border-light p-3 p-md-3.5 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <h5 class="fw-bold mb-0 text-dark">Logs History</h5>

            <div class="d-flex align-items-center gap-2 flex-nowrap attendance-controls">
                <!-- Date Range Filter Pills -->
                <div class="filter-pill-group" id="rangeFilterGroup">
                    <button type="button" class="filter-pill {{ !request('range') ? 'active' : '' }}" data-range="">All</button>
                    <button type="button" class="filter-pill {{ request('range') == 'today' ? 'active' : '' }}" data-range="today">Today</button>
                    <button type="button" class="filter-pill {{ request('range') == 'week' ? 'active' : '' }}" data-range="week">1 Week</button>
                    <button type="button" class="filter-pill {{ request('range') == 'month' ? 'active' : '' }}" data-range="month">1 Month</button>
                    <button type="button" class="filter-pill {{ request('range') == 'year' ? 'active' : '' }}" data-range="year">1 Year</button>
                </div>

                <input type="hidden" name="range" id="rangeInput" value="{{ request('range') }}">

                <!-- Search Box (right side) -->
                <div class="search-box-compact">
                    <i class="ti ti-search"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search date...">
                </div>

                @if(request()->filled('search') || request()->filled('range'))
                    <a href="{{ route('attendances.show', $id) }}" class="btn btn-sm btn-light text-secondary px-2 flex-shrink-0" title="Reset Filters">
                        <i class="ti ti-rotate-clockwise"></i>
                    </a>
                @endif
            </div>
        </form>

        <style>
            .attendance-controls { flex-shrink: 0; }

            .filter-pill-group {
                display: inline-flex;
                background: #f1f3f5;
                border-radius: 8px;
                padding: 3px;
                gap: 2px;
                flex-wrap: nowrap;
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

            .filter-pill:hover { color: #212529; }

            .filter-pill.active {
                background: #ffffff;
                color: var(--primary-color, #4f46e5);
                box-shadow: 0 1px 2px rgba(0,0,0,0.08);
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
                flex-shrink: 0;
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
                .attendance-controls { width: 100%; flex-wrap: wrap; }
                .filter-pill-group { overflow-x: auto; max-width: 100%; }
                .search-box-compact { width: 100%; }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const pills = document.querySelectorAll('#rangeFilterGroup .filter-pill');
                const rangeInput = document.getElementById('rangeInput');

                pills.forEach(function (pill) {
                    pill.addEventListener('click', function () {
                        pills.forEach(p => p.classList.remove('active'));
                        pill.classList.add('active');
                        rangeInput.value = pill.dataset.range;
                        pill.closest('form').submit();
                    });
                });
            });
        </script>

        <div class="table-responsive">
            <table class="table table-borderless table-hover align-middle mb-0" style="min-width: 600px;">
                <thead class="bg-light-subtle text-secondary fs-8 text-uppercase tracking-wider">
                    <tr>
                        <th class="ps-4 py-3">Attendance Date</th>
                        <th class="py-3">Check In</th>
                        <th class="py-3">Check Out</th>
                        <th class="py-3">Total Hours</th>
                        <th class="py-3">Remarks</th>
                        <th class="pe-4 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="fs-7">
                    @forelse($attendances['data'] ?? [] as $item)
                        @php
                            $itemArray = is_array($item) ? $item : (is_object($item) ? (array) $item : []);
                            $checkIns = $itemArray['check_ins'] ?? [];
                            $checkOuts = $itemArray['check_outs'] ?? [];
                        @endphp

                        @if(!empty($itemArray))
                            <tr class="border-bottom border-light-subtle align-top">
                                <td class="ps-4 text-secondary fw-medium py-3">
                                    {{ $itemArray['attendance_date'] ?? '—' }}
                                </td>
                                <td class="text-secondary py-3">
                                    @forelse($checkIns as $time)
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <i class="ti ti-login-2 text-success fs-6"></i> {{ $time }}
                                        </div>
                                    @empty
                                        —
                                    @endforelse
                                </td>
                                <td class="text-secondary py-3">
                                    @forelse($checkOuts as $time)
                                        <div class="d-flex align-items-center gap-1 mb-1">
                                            <i class="ti ti-logout-2 text-danger fs-6"></i> {{ $time }}
                                        </div>
                                    @empty
                                        —
                                    @endforelse
                                </td>
                                <td class="py-3">
                                    <span class="fw-semibold text-dark">{{ $itemArray['total_hours'] ?? '—' }}</span>
                                </td>
                                <td class="text-secondary py-3">
                                    {{ $itemArray['remarks'] ?? '—' }}
                                </td>
                                <td class="pe-4 py-3">
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
                            <td colspan="6" class="text-center py-5">
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

        <!-- Pagination Footer -->
        @if(isset($attendances['last_page']) && $attendances['last_page'] > 1)
            <div class="card-footer bg-white border-top border-light py-3">
                <div class="d-flex justify-content-center">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item {{ $attendances['current_page'] <= 1 ? 'disabled' : '' }}">
                            <a class="page-link"
                               href="{{ route('attendances.show', array_merge(['id' => $id], request()->except('page'), ['page' => $attendances['current_page'] - 1])) }}">
                                &laquo; Previous
                            </a>
                        </li>

                        @for ($page = 1; $page <= $attendances['last_page']; $page++)
                            <li class="page-item {{ $page == $attendances['current_page'] ? 'active' : '' }}">
                                <a class="page-link"
                                   href="{{ route('attendances.show', array_merge(['id' => $id], request()->except('page'), ['page' => $page])) }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endfor

                        <li class="page-item {{ $attendances['current_page'] >= $attendances['last_page'] ? 'disabled' : '' }}">
                            <a class="page-link"
                               href="{{ route('attendances.show', array_merge(['id' => $id], request()->except('page'), ['page' => $attendances['current_page'] + 1])) }}">
                                Next &raquo;
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection