<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — BookMyLab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a2e4a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .register-box {
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 480px;
        }
        .register-box h1 { color: #1a2e4a; font-weight: 700; font-size: 1.8rem; }
        .form-control:focus { border-color: #1a2e4a; box-shadow: 0 0 0 0.2rem rgba(26,46,74,0.25); }
        .btn-register {
            background-color: #1a2e4a;
            border-color: #1a2e4a;
            color: #fff;
            width: 100%;
            padding: 10px;
            font-weight: 600;
        }
        .btn-register:hover { background-color: #0f1f32; border-color: #0f1f32; color: #fff; }
        .form-label { color: #1a2e4a; font-weight: 500; }
        .error-text { color: #dc3545; font-size: 0.875rem; margin-top: 3px; }
    </style>
</head>
<body>
    <div class="register-box">
        <div class="text-center mb-4">
            <h1>BookMyLab</h1>
            <p class="text-muted">Create Your Account</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text"
                           class="form-control @error('first_name') is-invalid @enderror"
                           id="first_name" name="first_name"
                           value="{{ old('first_name') }}" required autofocus>
                    @error('first_name')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text"
                           class="form-control @error('last_name') is-invalid @enderror"
                           id="last_name" name="last_name"
                           value="{{ old('last_name') }}" required>
                    @error('last_name')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label for="school_email" class="form-label">School Email</label>
                <input type="email"
                       class="form-control @error('school_email') is-invalid @enderror"
                       id="school_email" name="school_email"
                       value="{{ old('school_email') }}" required>
                @error('school_email')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="school_id_number" class="form-label">School ID Number</label>
                <input type="text"
                       class="form-control @error('school_id_number') is-invalid @enderror"
                       id="school_id_number" name="school_id_number"
                       value="{{ old('school_id_number') }}" required>
                @error('school_id_number')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password"
                       class="form-control @error('password') is-invalid @enderror"
                       id="password" name="password" required>
                @error('password')
                    <div class="error-text">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password"
                       class="form-control"
                       id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-register">Create Account</button>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted mb-0">Already have an account? <a href="{{ route('login') }}" style="color: #1a2e4a; font-weight: 500;">Login here</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>