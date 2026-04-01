<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Lab Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-blue: #1a2e4a;
            --white: #ffffff;
        }

        body {
            background-color: var(--dark-blue);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .register-container {
            background-color: var(--white);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h1 {
            color: var(--dark-blue);
            font-weight: bold;
            margin-bottom: 10px;
        }

        .form-control:focus {
            border-color: var(--dark-blue);
            box-shadow: 0 0 0 0.2rem rgba(26, 46, 74, 0.25);
        }

        .btn-register {
            background-color: var(--dark-blue);
            border-color: var(--dark-blue);
            color: var(--white);
            width: 100%;
            padding: 10px;
            font-weight: bold;
            margin-top: 20px;
        }

        .btn-register:hover {
            background-color: #0f1f32;
            border-color: #0f1f32;
            color: var(--white);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: var(--dark-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            color: var(--dark-blue);
            font-weight: 500;
            margin-bottom: 5px;
        }

        .error-text {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 3px;
        }

        .alert {
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Lab Booking</h1>
            <p class="text-muted">Create Your Account</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Registration Failed:</strong>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                @error('name')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="school_email" class="form-label">School Email</label>
                <input type="email" class="form-control @error('school_email') is-invalid @enderror" id="school_email" name="school_email" value="{{ old('school_email') }}" required>
                @error('school_email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                @error('password_confirmation')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-register">Create Account</button>
        </form>

        <div class="login-link">
            <p class="text-muted mb-0">Already have an account? <a href="{{ route('login') }}">Login here</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
