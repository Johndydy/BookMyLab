<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookMyLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a2e4a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .welcome-box {
            background: #fff;
            padding: 50px 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.25);
            text-align: center;
            max-width: 440px;
            width: 100%;
        }
        h1 { color: #1a2e4a; font-weight: 700; }
        .btn-primary { background-color: #1a2e4a; border-color: #1a2e4a; font-weight: 600; padding: 10px 30px; }
        .btn-primary:hover { background-color: #0f1f32; border-color: #0f1f32; }
        .btn-outline-primary { color: #1a2e4a; border-color: #1a2e4a; font-weight: 600; padding: 10px 30px; }
        .btn-outline-primary:hover { background-color: #1a2e4a; color: #fff; }
    </style>
</head>
<body>
    <div class="welcome-box">
        <div class="mb-4">
            <i class="bi bi-flask" style="font-size: 3rem; color: #1a2e4a;"></i>
        </div>
        <h1 class="mb-2">BookMyLab</h1>
        <p class="text-muted mb-4">Book laboratory rooms and equipment with ease.</p>
        <div class="d-grid gap-3">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg">Login</a>
            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">Create Account</a>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>