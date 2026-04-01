<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Lab Booking System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-blue: #1a2e4a;
            --white: #ffffff;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }

        .navbar {
            background-color: var(--dark-blue);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: var(--white) !important;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .nav-link {
            color: var(--white) !important;
            margin-left: 20px;
        }

        .nav-link:hover {
            opacity: 0.8;
        }

        .sidebar {
            background-color: var(--dark-blue);
            min-height: 100vh;
            padding-top: 20px;
        }

        .sidebar .nav-link {
            color: var(--white) !important;
            padding: 12px 20px;
            margin-bottom: 5px;
            border-left: 4px solid transparent;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            border-left-color: var(--white);
        }

        .btn-primary {
            background-color: var(--dark-blue);
            border-color: var(--dark-blue);
        }

        .btn-primary:hover {
            background-color: #0f1f32;
            border-color: #0f1f32;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-approved {
            background-color: #28a745;
        }

        .badge-rejected {
            background-color: #dc3545;
        }

        .badge-cancelled {
            background-color: #6c757d;
        }

        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: var(--dark-blue);
            color: var(--white);
        }

        .alert {
            border-radius: 4px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--dark-blue);
            box-shadow: 0 0 0 0.2rem rgba(26, 46, 74, 0.25);
        }

        .table {
            background-color: var(--white);
        }

        .table th {
            background-color: var(--dark-blue);
            color: var(--white);
            border: none;
        }

        footer {
            background-color: var(--dark-blue);
            color: var(--white);
            text-align: center;
            padding: 20px;
            margin-top: 40px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('user.dashboard') }}">Lab Booking</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.bookings.index') }}">My Bookings</a>
                        </li>
                        <li class="nav-item">
                            <span class="nav-link">{{ auth()->user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link" style="text-decoration: none; color: white;">Logout</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Errors:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <footer>
        <p>&copy; 2026 School Laboratory Booking System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
