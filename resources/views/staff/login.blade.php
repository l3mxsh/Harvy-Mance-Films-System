<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - HarvyMance Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body class="bg-white d-flex align-items-center justify-content-center min-vh-100">
    <div class="card border-dark" style="width: 100%; max-width: 400px;">
        <div class="card-header bg-dark text-white text-center fw-bold">
            <i class="bi bi-person-badge me-1"></i> Staff Login
        </div>
        <div class="card-body">
            @if(session('error'))
                <div class="alert alert-danger py-2">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('staff.login.post') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control border-dark @error('email') is-invalid @enderror"
                           id="email" name="email" value="{{ old('email') }}" placeholder="name@example.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control border-dark @error('password') is-invalid @enderror"
                           id="password" name="password" placeholder="Enter password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-dark w-100">Login</button>
            </form>
        </div>
    </div>
</body>

</html>
