<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — BookMyLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Chelsea+Market&family=Roboto:wght@500&display=swap" rel="stylesheet">
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
        html, body { height: 100%; overflow: hidden; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--light-gray); }
        .wrapper { display: flex; height: 100vh; flex-direction: column; overflow: hidden; }
        .navbar {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 1rem 0;
            z-index: 1050;
        }
        .navbar-brand { 
            color: var(--white) !important; 
            font-family: "Chelsea Market", system-ui;
            font-weight: 400; 
            font-size: 1.3rem; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
        }
        .nav-link { color: rgba(255,255,255,0.9) !important; margin: 0 8px; transition: all 0.3s ease; }
        .nav-link:hover { color: var(--white) !important; }
        .container-wrapper { display: flex; flex: 1; overflow: hidden; }
        .sidebar {
            background: linear-gradient(180deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            width: 250px; padding-top: 10px; box-shadow: 2px 0 8px rgba(0,0,0,0.1);
            overflow-y: auto;
            z-index: 1000;
        }
        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar { width: 6px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.85) !important; padding: 14px 20px 14px 24px;
            margin-bottom: 4px; border-left: 4px solid transparent;
            text-decoration: none; transition: background-color 0.4s ease, color 0.4s ease, border-color 0.4s ease; display: flex; align-items: center; gap: 10px;
            position: relative; overflow: hidden;
            opacity: 0; /* For GSAP */
            transform: translateX(-20px); /* For GSAP */
        }
        .sidebar .nav-link i { font-size: 1.1rem; width: 20px; transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .sidebar .nav-link:hover { background-color: rgba(255,255,255,0.08); color: var(--white) !important; }
        .sidebar .nav-link:hover i { transform: scale(1.15); }
        .sidebar .nav-link.active { background-color: rgba(255,255,255,0.12); border-left-color: var(--accent); color: var(--white) !important; font-weight: 600; }
        .sidebar .nav-link::after { /* Apple-like active background glow */
            content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, rgba(52,152,219,0.1) 0%, transparent 100%);
            opacity: 0; transition: opacity 0.4s ease; pointer-events: none;
        }
        .sidebar .nav-link.active::after { opacity: 1; }
        .main-content { flex: 1; padding: 30px; overflow-y: auto; }
        .btn-primary {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--light-blue) 100%);
            border: none; border-radius: var(--border-radius); font-weight: 600; transition: all 0.3s ease;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(26,46,74,0.3); }
        .btn-approve {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: var(--white); border: none; border-radius: var(--border-radius); 
            font-weight: 600; transition: all 0.3s ease;
        }
        .btn-approve:hover { 
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); 
            color: var(--white); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(46,204,113,0.3); 
        }
        .btn-reject, .btn-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: var(--white); border: none; border-radius: var(--border-radius); 
            font-weight: 600; transition: all 0.3s ease;
        }
        .btn-reject:hover, .btn-danger:hover {
            background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
            color: var(--white); transform: translateY(-2px); box-shadow: 0 4px 12px rgba(231,76,60,0.3);
        }
        .btn-outline-primary { 
            color: var(--dark-blue); border-color: var(--dark-blue); font-weight: 600; 
            border-radius: var(--border-radius); transition: all 0.3s ease;
        }
        .btn-outline-primary:hover { 
            background: var(--dark-blue); border-color: var(--dark-blue); color: var(--white);
            transform: translateY(-2px); box-shadow: 0 4px 12px rgba(26,46,74,0.2);
        }
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
        h2, .page-title {
            font-family: "Roboto", sans-serif !important;
            font-weight: 500 !important;
            font-style: normal;
        }
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
            .navbar-brand { font-size: 1.1rem; }
            .sidebar { 
                position: fixed; top: 0; left: -280px; width: 280px; height: 100vh; 
                z-index: 1100; padding-top: 30px; border-radius: 0 20px 20px 0;
                box-shadow: 10px 0 30px rgba(0,0,0,0.3);
            }
            .main-content { padding: 20px 15px; }
            .sidebar-overlay {
                position: fixed; top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);
                z-index: 1090; display: none; opacity: 0;
            }
            .navbar-toggler { display: block !important; border: none; padding: 0; }
            .navbar-toggler:focus { box-shadow: none; }
        }
        @media (min-width: 769px) {
            .navbar-toggler { display: none !important; }
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
            <button class="navbar-toggler" type="button" id="sidebarToggle">
                <i class="bi bi-list text-white" style="font-size: 1.8rem;"></i>
            </button>
            <div class="collapse navbar-collapse d-none d-lg-block" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <!-- Navbar items removed for cleaner UI -->
                </ul>
            </div>
        </div>
    </nav>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="container-wrapper">
        <div class="sidebar" id="adminSidebar">
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
            
            <div class="mt-4 pt-4 border-top border-white border-opacity-10">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                        <i class="bi bi-box-arrow-right text-danger"></i> Logout
                    </button>
                </form>
            </div>
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
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", (event) => {
        // Desktop Staggered entrance
        if (window.innerWidth > 768) {
            gsap.to(".sidebar .nav-link", {
                opacity: 1, x: 0, duration: 0.8, stagger: 0.05, ease: "power3.out", delay: 0.1
            });
        } else {
            // Initialize mobile sidebar hidden state
            gsap.set("#adminSidebar", { x: 0 }); // reset initial CSS left
        }
        
        // Sidebar Toggle Logic
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        let isSidebarOpen = false;

        function toggleSidebar() {
            if (!isSidebarOpen) {
                // Open
                overlay.style.display = 'block';
                gsap.to(overlay, { opacity: 1, duration: 0.4 });
                gsap.to(sidebar, { left: 0, duration: 0.6, ease: "elastic.out(1, 0.75)" });
                gsap.to(".sidebar .nav-link", { 
                    opacity: 1, x: 0, duration: 0.4, stagger: 0.05, delay: 0.2, ease: "power2.out" 
                });
            } else {
                // Close
                gsap.to(overlay, { opacity: 0, duration: 0.4, onComplete: () => overlay.style.display = 'none' });
                gsap.to(sidebar, { left: -280, duration: 0.5, ease: "power3.inOut" });
                gsap.to(".sidebar .nav-link", { opacity: 0, x: -20, duration: 0.3 });
            }
            isSidebarOpen = !isSidebarOpen;
        }

        if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);

        // Add subtle hover animation for main content cards
        const cards = document.querySelectorAll('.card, .stat-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                gsap.to(card, { y: -4, duration: 0.3, ease: "power2.out" });
            });
            card.addEventListener('mouseleave', () => {
                gsap.to(card, { y: 0, duration: 0.3, ease: "power2.out" });
            });
        });
    });
</script>
@yield('scripts')
</body>
</html>