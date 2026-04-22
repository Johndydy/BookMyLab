<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Lab Booking System</title>
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
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 400px;
        }
        .login-box h1 { color: #1a2e4a; font-weight: 700; font-size: 1.8rem; }
        .form-control:focus { border-color: #1a2e4a; box-shadow: 0 0 0 0.2rem rgba(26,46,74,0.25); }
        .btn-login {
            background-color: #1a2e4a;
            border-color: #1a2e4a;
            color: #fff;
            width: 100%;
            padding: 10px;
            font-weight: 600;
        }
        .btn-login:hover { background-color: #0f1f32; border-color: #0f1f32; color: #fff; }
        .form-label { color: #1a2e4a; font-weight: 500; }
        .error-text { color: #dc3545; font-size: 0.875rem; margin-top: 3px; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="text-center mb-4">
            <h1>Lab Booking</h1>
            <p class="text-muted">Student & Faculty Portal</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="school_email" class="form-label">School Email</label>
                <input type="email"
                       class="form-control @error('school_email') is-invalid @enderror"
                       id="school_email"
                       name="school_email"
                       value="{{ old('school_email') }}"
                       required autofocus>
                @error('school_email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       id="password"
                       name="password"
                       required>
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label text-muted" for="remember">Remember me</label>
            </div>

            <button type="submit" class="btn btn-login">Login</button>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted mb-0">Don't have an account? <a href="{{ route('register') }}" style="color: #1a2e4a; font-weight: 500;">Register here</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>