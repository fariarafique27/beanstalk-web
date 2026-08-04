<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Super Admin') - HRMS Command</title>

    <!-- Google Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.36.0/tabler-icons.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
            --topbar-height: 70px;
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --body-bg: #f8fafc;
        }

        body {
            background-color: var(--body-bg);
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }

        /* Desktop Sidebar Styles */
        @media (min-width: 992px) {
            .sidebar {
                width: var(--sidebar-width);
                height: calc(100vh - 32px);
                position: fixed;
                top: 16px;
                left: 16px;
                background: #0f172a;
                border-radius: 20px;
                color: #fff;
                z-index: 1030;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            }

            .main-wrapper {
                margin-left: calc(var(--sidebar-width) + 32px);
                padding-right: 16px;
                min-height: 100vh;
            }
        }

        /* Mobile / Tablet Offcanvas Adjustments */
        @media (max-width: 991.98px) {
            .sidebar {
                background: #0f172a;
                color: #fff;
            }

            .main-wrapper {
                margin-left: 0;
                padding-left: 12px;
                padding-right: 12px;
                min-height: 100vh;
            }
        }

        .sidebar .nav-link {
            color: #94a3b8;
            padding: 11px 18px;
            border-radius: 12px;
            margin: 4px 0;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.06);
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: var(--primary-color);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .topbar {
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .content-body {
            padding: 10px 0 40px 0;
        }

        /* Stat Card Fixes */
        .stat-card {
            border: 0 !important;
            border-top: 4px solid transparent !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card.card-accent-primary { border-top-color: var(--primary-color) !important; }
        .stat-card.card-accent-success { border-top-color: #10b981 !important; }
        .stat-card.card-accent-warning { border-top-color: #f59e0b !important; }
        .stat-card.card-accent-danger { border-top-color: #ef4444 !important; }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.08) !important;
        }

        .tracking-wider { letter-spacing: 0.05em; }
        .fs-7 { font-size: 0.875rem; }
        .fs-8 { font-size: 0.75rem; }
        
        .btn-primary { background-color: var(--primary-color); border-color: var(--primary-color); }
        .btn-primary:hover { background-color: var(--primary-hover); border-color: var(--primary-hover); }
        .bg-primary-subtle { background-color: #eef2ff !important; color: var(--primary-color) !important; }
        .bg-success-subtle { background-color: #ecfdf5 !important; color: #10b981 !important; }
        .bg-warning-subtle { background-color: #fffbeb !important; color: #f59e0b !important; }
    </style>
</head>
<body>

    @php
        // Pull user details and permissions safely from the Guzzle session
        $userData = session('user', []);
        $userRole = session('role') ?? $userData['role'] ?? 'admin';
        
        $sessionPerms = session('permissions', []);
        $userPerms = is_array($sessionPerms) && !empty($sessionPerms) 
            ? $sessionPerms 
            : ($userData['permissions'] ?? []);

           
    @endphp

    {{-- TEMPORARY DEBUG: Delete this after checking --}}
    <!-- <div style="background: yellow; color: black; padding: 10px; z-index: 9999; position: relative;">
        Role: {{ $userRole }} | Perms: {{ json_encode($userPerms) }}
    </div>   -->

    <!-- Desktop Sidebar -->
    <aside class="sidebar d-none d-lg-flex flex-column justify-content-between p-3">
        <div>
            <div class="px-3 py-3 d-flex align-items-center gap-2.5">
                <div class="bg-primary p-2 rounded-3 text-white d-flex align-items-center justify-content-center">
                    <i class="ti ti-layout-grid-add fs-4"></i>
                </div>
                <div>
                    <h6 class="fw-extrabold mb-0 text-white tracking-tight">HRMS Core</h6>
                    <span class="fs-8 text-white-50">Console</span>
                </div>
            </div>

            <hr class="border-secondary opacity-25 my-2">

            <nav class="mt-3">
                {{-- Link 1: Dashboard (Always visible) --}}
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ti ti-dashboard fs-5"></i> Dashboard
                </a>

                {{-- Link 2: Organizations (Super Admin only) --}}
                @if($userRole === 'super_admin'|| in_array('read_organizations', $userPerms) || in_array('manage_organizations', $userPerms))
                    <a href="{{ route('organizations.index') }}" class="nav-link {{ request()->routeIs('organizations.*') ? 'active' : '' }}">
                        <i class="ti ti-building fs-5"></i> Organizations
                    </a>
                @endif

              <!-- {{-- Link 3: Attendances --}}
                @if($userRole === 'super_admin' || in_array('attendances.view', $userPerms))
                    <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                        <i class="ti ti-clock-check fs-5"></i> Attendances
                    </a>
                @endif

                {{-- Link 4: Employees (Visible if permitted) --}}
                @if($userRole === 'super_admin'|| in_array('employees.manage', $userPerms))
                    <a href="#" class="nav-link">
                        <i class="ti ti-users fs-5"></i> Employees
                    </a>
                @endif

                {{-- Link 5: Device Settings (Visible if permitted) --}}
                @if($userRole === 'super_admin' || in_array('device.manage', $userPerms))
                    <a href="{{ route('settings.device.edit') }}" class="nav-link {{ request()->routeIs('settings.device.*') ? 'active' : '' }}">
                        <i class="ti ti-settings fs-5"></i> Device Settings
                    </a>
                @endif -->

                {{-- Link 3: Attendances --}}
                @if($userRole !== 'super_admin' && in_array('attendances.view', $userPerms))
                    <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                        <i class="ti ti-clock-check fs-5"></i> Attendances
                    </a>
                @endif

                {{-- Link 4: Employees (Visible if permitted) --}}
                @if($userRole !== 'super_admin' && in_array('employees.manage', $userPerms))
                    <a href="#" class="nav-link">
                        <i class="ti ti-users fs-5"></i> Employees
                    </a>
                @endif

                {{-- Link 5: Device Settings (Visible if permitted) --}}
                @if($userRole !== 'super_admin' && in_array('device.manage', $userPerms))
                    <a href="{{ route('settings.device.edit') }}" class="nav-link {{ request()->routeIs('settings.device.*') ? 'active' : '' }}">
                        <i class="ti ti-settings fs-5"></i> Device Settings
                    </a>
                @endif
            </nav>
        </div>

        <div>
            <a href="#" class="nav-link text-white-50">
                <i class="ti ti-settings fs-5"></i> Settings
            </a>
        </div>
    </aside>

    <!-- Mobile Offcanvas Sidebar Drawer -->
    <div class="offcanvas offcanvas-start sidebar w-75 d-lg-none" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header p-3 border-bottom border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-2.5">
                <div class="bg-primary p-2 rounded-3 text-white d-flex align-items-center justify-content-center">
                    <i class="ti ti-layout-grid-add fs-4"></i>
                </div>
                <h6 class="fw-extrabold mb-0 text-white tracking-tight" id="mobileSidebarLabel">HRMS Core</h6>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column justify-content-between p-3">
            <nav class="mt-2">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="ti ti-dashboard fs-5"></i> Overview
                </a>

                @if($userRole === 'super_admin' || in_array('read_organizations', $userPerms) || in_array('manage_organizations', $userPerms))
                    <a href="{{ route('organizations.index') }}" class="nav-link">
                        <i class="ti ti-building fs-5"></i> Organizations
                    </a>
                @endif
                <!-- @if($userRole === 'super_admin' || in_array('employees.manage', $userPerms))
                    <a href="#" class="nav-link">
                        <i class="ti ti-users fs-5"></i> Employees
                    </a>
                @endif

                @if($userRole === 'super_admin' || in_array('attendances.view', $userPerms))
                    <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                        <i class="ti ti-clock-check fs-5"></i> Attendances
                    </a>
                @endif
                @if($userRole === 'super_admin' || in_array('device.manage', $userPerms))
                    <a href="{{ route('settings.device.edit') }}" class="nav-link">
                        <i class="ti ti-settings fs-5"></i> Device Settings
                    </a>
               @endif -->

               @if($userRole !== 'super_admin' && in_array('employees.manage', $userPerms))
                    <a href="#" class="nav-link">
                        <i class="ti ti-users fs-5"></i> Employees
                    </a>
                @endif

                @if($userRole !== 'super_admin' && in_array('attendances.view', $userPerms))
                    <a href="{{ route('attendances.index') }}" class="nav-link {{ request()->routeIs('attendances.*') ? 'active' : '' }}">
                        <i class="ti ti-clock-check fs-5"></i> Attendances
                    </a>
                @endif

                @if($userRole !== 'super_admin' && in_array('device.manage', $userPerms))
                    <a href="{{ route('settings.device.edit') }}" class="nav-link">
                        <i class="ti ti-settings fs-5"></i> Device Settings
                    </a>
                @endif

            </nav>
            <div>
                <a href="#" class="nav-link text-white-50">
                    <i class="ti ti-settings fs-5"></i> Settings
                </a>
            </div>
        </div>
    </div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <header class="topbar">
            <div class="d-flex align-items-center gap-2">
                <!-- Mobile Toggle Button -->
                <button class="btn btn-light d-lg-none shadow-sm rounded-3 p-2 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    <i class="ti ti-menu-2 fs-4"></i>
                </button>
            </div>

            <div class="d-flex align-items-center gap-3 ms-auto">
                <div class="dropdown">
                    <button class="btn btn-white bg-white border-0 shadow-sm rounded-3 dropdown-toggle d-flex align-items-center gap-2.5 px-3 py-2" type="button" data-bs-toggle="dropdown">
                        <div class="avatar-initials rounded-circle bg-primary text-white fs-8 fw-bold d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                            SA
                        </div>
                        <span class="fw-semibold fs-7 text-dark d-none d-sm-inline">{{ session('user.name') ?? 'Admin User' }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 p-2 fs-7">
                        <li><span class="dropdown-item-text text-muted fs-8">{{ session('user.email') ?? 'admin@hrms.com' }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger rounded-2 d-flex align-items-center gap-2">
                                    <i class="ti ti-logout fs-5"></i> Sign Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <main class="content-body">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>