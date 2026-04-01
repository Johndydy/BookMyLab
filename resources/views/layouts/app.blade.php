<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lab Booking System')</title>
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

        * {
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 1rem 0;
        }

        .navbar-brand {
            color: var(--white) !important;
            font-weight: 700;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-brand i {
            font-size: 1.8rem;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            margin: 0 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--white) !important;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--accent);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .container-fluid {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            padding: 30px 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(26, 46, 74, 0.3);
        }

        .btn-outline-primary {
            color: var(--dark-blue);
            border-color: var(--dark-blue);
        }

        .btn-outline-primary:hover {
            background-color: var(--dark-blue);
            border-color: var(--dark-blue);
        }

        .btn-sm {
            border-radius: 6px;
        }

        .badge {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            color: var(--white);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            font-weight: 600;
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .form-control,
        .form-select {
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--dark-blue);
            box-shadow: 0 0 0 0.2rem rgba(26, 46, 74, 0.25);
        }

        .table {
            background-color: var(--white);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .table thead {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            color: var(--white);
        }

        .table th {
            font-weight: 600;
            border: none;
            padding: 15px;
        }

        .table td {
            border-color: #f0f0f0;
            padding: 15px;
            vertical-align: middle;
        }

        .table-striped tbody tr:hover {
            background-color: #f8f9fa;
        }

        footer {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            color: var(--white);
            text-align: center;
            padding: 20px;
            margin-top: auto;
            font-size: 0.9rem;
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--dark-blue);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .page-title {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .page-subtitle {
            color: #6c757d;
            font-size: 1rem;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            color: var(--white);
            padding: 20px;
            border-radius: var(--border-radius);
            text-align: center;
            margin-bottom: 20px;
        }

        .stat-card .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .stat-card .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .pagination {
            margin-top: 20px;
        }

        .pagination .page-link {
            border-radius: var(--border-radius);
            border: 1px solid #ddd;
            color: var(--dark-blue);
            transition: all 0.3s ease;
        }

        .pagination .page-link:hover {
            background-color: var(--dark-blue);
            border-color: var(--dark-blue);
            color: var(--white);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--dark-blue);
            border-color: var(--dark-blue);
        }

        @media (max-width: 768px) {
            .page-title {
                font-size: 1.5rem;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }

            .nav-link {
                margin: 0 5px;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('user.dashboard') }}">
                <i class="bi bi-flask"></i> Lab Booking
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.dashboard') }}">
                                <i class="bi bi-house-fill"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.bookings.index') }}">
                                <i class="bi bi-calendar-check"></i> My Bookings
                            </a>
                        </li>
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="bi bi-gear-fill"></i> Admin Panel
                                </a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ route('user.dashboard') }}">Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid main-content">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="bi bi-exclamation-circle"></i> Validation Error:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="container">
            @yield('content')
        </div>
    </div>

    <footer>
        <p>&copy; 2026 School Laboratory Booking System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
