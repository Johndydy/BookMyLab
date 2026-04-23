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
        * { margin: 0; padding: 0; }
        html, body { height: 100%; }
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
        .nav-link { color: rgba(255,255,255,0.9) !important; margin: 0 8px; transition: all 0.3s ease; }
        .nav-link:hover { color: var(--white) !important; }
        .main-content { flex: 1; padding: 30px 0; }
        .btn-primary {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            border: none; border-radius: var(--border-radius); font-weight: 600; transition: all 0.3s ease;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(26,46,74,0.3); }
        .btn-outline-primary { color: var(--dark-blue); border-color: var(--dark-blue); }
        .btn-outline-primary:hover { background-color: var(--dark-blue); border-color: var(--dark-blue); }
        .card {
            border: none; border-radius: var(--border-radius);
            box-shadow: 0 2px 12px rgba(0,0,0,0.08); transition: all 0.3s ease; margin-bottom: 20px;
        }
        .card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.12); transform: translateY(-2px); }
        .card-header {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            color: var(--white); border-radius: var(--border-radius) var(--border-radius) 0 0; font-weight: 600;
        }
        .alert { border-radius: var(--border-radius); border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .form-control, .form-select {
            border: 1px solid #ddd; border-radius: var(--border-radius);
            padding: 10px 15px; transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--dark-blue); box-shadow: 0 0 0 0.2rem rgba(26,46,74,0.25);
        }
        .table { background-color: var(--white); border-radius: var(--border-radius); overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .table thead { background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%); color: var(--white); }
        .table th { font-weight: 600; border: none; padding: 15px; }
        .table td { border-color: #f0f0f0; padding: 15px; vertical-align: middle; }
        footer {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            color: var(--white); text-align: center; padding: 20px; margin-top: auto; font-size: 0.9rem;
        }
        h1,h2,h3,h4,h5,h6 { color: var(--dark-blue); font-weight: 600; margin-bottom: 10px; }
        .page-title { font-size: 2rem; margin-bottom: 10px; }
        .page-subtitle { color: #6c757d; font-size: 1rem; margin-bottom: 30px; }
        .stat-card {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            color: var(--white); padding: 20px; border-radius: var(--border-radius); text-align: center; margin-bottom: 20px;
        }
        .stat-card .stat-value { font-size: 2.5rem; font-weight: 700; margin: 10px 0; }
        .stat-card .stat-label { font-size: 0.9rem; opacity: 0.9; }
        .pagination .page-link { border-radius: var(--border-radius); border: 1px solid #ddd; color: var(--dark-blue); transition: all 0.3s ease; }
        .pagination .page-link:hover { background-color: var(--dark-blue); border-color: var(--dark-blue); color: var(--white); }
        .pagination .page-item.active .page-link { background-color: var(--dark-blue); border-color: var(--dark-blue); }
        /* Google Setup Modal Styles */
        #googleSetupModal .modal-content {
            border: none;
            border-radius: 12px;
            padding: 20px;
        }

        #googleSetupModal .modal-header {
            border: none;
            text-align: center;
            display: block;
            padding: 0;
        }

        #googleSetupModal .modal-title {
            color: #1a2e4a;
            font-size: 1.5rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        #googleSetupModal .modal-subtitle {
            color: #6c757d;
            font-size: 0.85rem;
            line-height: 1.4;
            margin-bottom: 20px;
        }

        #googleSetupModal .user-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 12px;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        #googleSetupModal .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #1a2e4a;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            font-weight: bold;
            margin-right: 15px;
        }

        #googleSetupModal .user-info h5 {
            margin: 0;
            color: #1a2e4a;
            font-size: 1rem;
            font-weight: 700;
        }

        #googleSetupModal .user-info p {
            margin: 0;
            color: #6c757d;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        #googleSetupModal .form-label {
            color: #1a2e4a;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            margin-bottom: 8px;
        }

        #googleSetupModal .input-group-text {
            background-color: white;
            border-right: none;
            color: #6c757d;
        }

        #googleSetupModal .form-control {
            border-left: none;
            padding: 12px;
        }

        #googleSetupModal .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        #googleSetupModal .input-group:focus-within {
            border-radius: var(--border-radius);
            box-shadow: 0 0 0 0.2rem rgba(26, 46, 74, 0.1);
        }

        #googleSetupModal .field-hint {
            font-size: 0.75rem;
            color: #adb5bd;
            margin-top: 5px;
            text-transform: uppercase;
        }

        #googleSetupModal .btn-create {
            background-color: #ff9800;
            border: none;
            color: white;
            width: 100%;
            padding: 12px;
            font-weight: 800;
            font-size: 1rem;
            text-transform: uppercase;
            border-radius: 8px;
            margin-top: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        #googleSetupModal .btn-create:hover {
            background-color: #e68900;
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
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                        <li class="nav-item" style="position: relative;">
                            <a class="nav-link" href="{{ route('user.notifications.index') }}">
                                <i class="bi bi-bell"></i> Notifications
                                @php
                                    $unreadCount = \App\Models\Notification::where('user_id', auth()->user()->user_id)->where('is_read', false)->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="badge bg-danger" style="font-size: 0.7rem;">{{ $unreadCount }}</span>
                                @endif
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
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->full_name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.profile.edit') }}">
                                        <i class="bi bi-gear"></i> Account Settings
                                    </a>
                                </li>
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
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
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
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <footer>
        <p>&copy; {{ date('Y') }} School Laboratory Booking System. All rights reserved.</p>
    </footer>

    @auth
        @if(session('show_google_setup_modal') || !auth()->user()->username || !auth()->user()->profile_completed_at)
        <!-- Google Setup Modal -->
        <div class="modal fade" id="googleSetupModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content shadow-lg">
                    <div class="modal-header">
                        <h1 class="modal-title w-100">Complete Your Identity</h1>
                        <p class="modal-subtitle">Set a unique username and password to complete your account.<br>This will allow you to log in directly later.</p>
                    </div>
                    <div class="modal-body p-0">
                        <div class="user-card">
                            <div class="user-avatar">
                                {{ substr(auth()->user()->first_name, 0, 1) }}
                            </div>
                            <div class="user-info">
                                <h5>{{ auth()->user()->full_name }}</h5>
                                <p>Authenticated via Google</p>
                            </div>
                        </div>

                        <form action="{{ route('auth.google.complete-setup') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">School ID Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                                    <input type="text" name="school_id_number" class="form-control" placeholder="Enter your official ID number" required value="{{ old('school_id_number') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Create Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-at"></i></span>
                                    <input type="text" name="username" class="form-control" placeholder="Choose a unique username" required value="{{ old('username') }}">
                                </div>
                                <div class="field-hint">Lowercase letters, numbers, and underscores only</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Set Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Create a secure password" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Re-type password" required>
                                </div>
                            </div>

                            <button type="submit" class="btn-create">
                                <i class="bi bi-person-check-fill"></i> Complete Setup
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @auth
                @if(session('show_google_setup_modal') || !auth()->user()->username || !auth()->user()->profile_completed_at)
                    var googleSetupModal = new bootstrap.Modal(document.getElementById('googleSetupModal'));
                    googleSetupModal.show();
                @endif
            @endauth
        });
    </script>
    @yield('scripts')
</body>
</html>