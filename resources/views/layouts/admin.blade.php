<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — BookMyLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --dark-blue: #1a2e4a;
            --light-blue: #2d4a73;
            --accent: #3498db;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --border-radius: 8px;
        }
        * { margin: 0; padding: 0; }
        html, body { height: 100%; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light-gray); }
        .wrapper { display: flex; min-height: 100vh; flex-direction: column; }
        .navbar {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 1rem 0;
        }
        .navbar-brand { color: var(--white) !important; font-weight: 700; font-size: 1.3rem; display: flex; align-items: center; gap: 8px; }
        .nav-link { color: rgba(255,255,255,0.9) !important; margin: 0 8px; transition: all 0.3s ease; }
        .nav-link:hover { color: var(--white) !important; }
        .container-wrapper { display: flex; flex: 1; }
        .sidebar {
            background: linear-gradient(180deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            width: 250px; padding-top: 20px; box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.9) !important; padding: 14px 20px 14px 24px;
            margin-bottom: 4px; border-left: 4px solid transparent;
            text-decoration: none; transition: all 0.3s ease; display: flex; align-items: center; gap: 10px;
        }
        .sidebar .nav-link i { font-size: 1.1rem; width: 20px; }
        .sidebar .nav-link:hover { background-color: rgba(255,255,255,0.1); border-left-color: var(--accent); color: var(--white) !important; }
        .sidebar .nav-link.active { background-color: rgba(255,255,255,0.15); border-left-color: var(--accent); color: var(--white) !important; font-weight: 600; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .btn-primary {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            border: none; border-radius: var(--border-radius); font-weight: 600; transition: all 0.3s ease;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(26,46,74,0.3); }
        .card { border: none; border-radius: var(--border-radius); box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: all 0.3s ease; margin-bottom: 20px; }
        .card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.12); transform: translateY(-2px); }
        .card-header {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            color: var(--white); border-radius: var(--border-radius) var(--border-radius) 0 0; font-weight: 600;
        }
        .card-header h1, .card-header h2, .card-header h3, .card-header h4, .card-header h5, .card-header h6 { color: var(--white); }
        .alert { border-radius: var(--border-radius); border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .form-control, .form-select {
            border: 1px solid #ddd; border-radius: var(--border-radius); padding: 10px 15px; transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus { border-color: var(--dark-blue); box-shadow: 0 0 0 0.2rem rgba(26,46,74,0.25); }
        .table { background-color: var(--white); border-radius: var(--border-radius); overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .table thead { background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%); color: var(--white); }
        .table th { font-weight: 600; border: none; padding: 15px; }
        .table td { border-color: #f0f0f0; padding: 15px; vertical-align: middle; }
        footer {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            color: var(--white); text-align: center; padding: 20px; font-size: 0.9rem;
        }
        h1,h2,h3,h4,h5,h6 { color: var(--dark-blue); font-weight: 600; margin-bottom: 10px; }
        .page-title { font-size: 2rem; margin-bottom: 10px; }
        .page-subtitle { color: #6c757d; font-size: 1rem; margin-bottom: 30px; }
        .stat-card {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            color: var(--white); padding: 20px; border-radius: var(--border-radius);
            text-align: center; margin-bottom: 20px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .stat-card .stat-number { font-size: 2.5rem; font-weight: 700; margin: 10px 0; }
        .stat-card .stat-label { font-size: 0.9rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 0.5px; }
        .pagination .page-link { border-radius: var(--border-radius); border: 1px solid #ddd; color: var(--dark-blue); transition: all 0.3s ease; }
        .pagination .page-link:hover { background-color: var(--dark-blue); border-color: var(--dark-blue); color: var(--white); }
        .pagination .page-item.active .page-link { background-color: var(--dark-blue); border-color: var(--dark-blue); }
        /* Status Badges */
        .status-badge { padding: 5px 10px; font-weight: 600; font-size: 0.85rem; letter-spacing: 0.5px; }
        .status-pending { background-color: rgba(52, 152, 219, 0.15); color: var(--accent); border: 1px solid rgba(52, 152, 219, 0.5); }
        .status-approved { background-color: rgba(45, 74, 115, 0.15); color: var(--light-blue); border: 1px solid rgba(45, 74, 115, 0.5); }
        .status-rejected { background-color: rgba(26, 46, 74, 0.15); color: var(--dark-blue); border: 1px solid rgba(26, 46, 74, 0.5); }
        .status-cancelled { background-color: #f8f9fa; color: #6c757d; border: 1px solid #dee2e6; }
        @media (max-width: 768px) {
            .sidebar { width: 100%; padding-top: 0; }
            .container-wrapper { flex-direction: column; }
            .main-content { padding: 15px; }
        }
    </style>
    @yield('styles')
</head>
<body>
<div class="wrapper">
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="bi bi-shield-lock"></i> BookMyLab — Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.dashboard') }}">
                            <i class="bi bi-arrow-left"></i> Back to User
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            @if(auth()->user()->avatar)
                                <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->full_name }}" style="width: 24px; height: 24px; border-radius: 50%; margin-right: 5px; object-fit: cover;">
                            @else
                                <i class="bi bi-person-circle"></i>
                            @endif
                            {{ auth()->user()->full_name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-wrapper">
        <div class="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.bookings.index') }}" class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event"></i> Bookings
            </a>
            <a href="{{ route('admin.laboratories.index') }}" class="nav-link {{ request()->routeIs('admin.laboratories.*') ? 'active' : '' }}">
                <i class="bi bi-building"></i> Laboratories
            </a>
            <a href="{{ route('admin.equipment.index') }}" class="nav-link {{ request()->routeIs('admin.equipment.*') ? 'active' : '' }}">
                <i class="bi bi-tools"></i> Equipment
            </a>
            <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i> Departments
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Users
            </a>
            <a href="{{ route('admin.maintenance_logs.index') }}" class="nav-link {{ request()->routeIs('admin.maintenance_logs.*') ? 'active' : '' }}">
                <i class="bi bi-wrench"></i> Maintenance Logs
            </a>
            <a href="{{ route('admin.equipment_logs.index') }}" class="nav-link {{ request()->routeIs('admin.equipment_logs.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Equipment Logs
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up"></i> Reports
            </a>
        </div>

        <div class="main-content">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong><i class="bi bi-exclamation-circle"></i> Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle"></i> {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} School Laboratory Booking System. All rights reserved.</p>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>