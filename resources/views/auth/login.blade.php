<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BookMyLab</title>
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
            <h1>BookMyLab</h1>
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
                <label for="login" class="form-label">Email, Username, or Student ID</label>
                <input type="text"
                       class="form-control @error('login') is-invalid @enderror"
                       id="login"
                       name="login"
                       value="{{ old('login') }}"
                       placeholder="Enter your login identifier"
                       required autofocus>
                @error('login')
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

        <div class="position-relative my-4">
            <hr>
            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted" style="font-size: 0.8rem;">OR</span>
        </div>

        <a href="{{ route('auth.google') }}" class="btn btn-outline-dark w-100 d-flex align-items-center justify-content-center gap-2" style="padding: 10px; font-weight: 600;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
            </svg>
            Sign in with Google
        </a>

        <div class="text-center mt-4">
            <p class="text-muted mb-0">Don't have an account? <a href="{{ route('register') }}" style="color: #1a2e4a; font-weight: 500;">Register here</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>