<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Monitoring Login - HarvyMance Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/client-login.css') }}">
</head>

<body class="client-login-body">
    <div class="client-login-card">
        <div class="client-login-header">
            <h3><i class="bi bi-camera-video me-2"></i>HarvyMance Films</h3>
            <p>Booking Monitoring Login</p>
        </div>
        <div class="client-login-body">
            @if(session('success'))
                <div class="alert alert-success py-2">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('customer.login.post') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Control Number</label>
                    <input type="text" class="form-control @error('control_number') is-invalid @enderror"
                           name="control_number" value="{{ old('control_number') }}"
                           placeholder="e.g. BKG-2026-00001" required autofocus>
                    @error('control_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" id="password" placeholder="Enter your password" required>
                        <button class="btn btn-outline-secondary" type="button" onclick="togglePw()">
                            <i class="bi bi-eye" id="pwToggleIcon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-client-login">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Log In
                </button>
            </form>

            <div class="client-login-footer">
                <a href="{{ url('/') }}">
                    <i class="bi bi-arrow-left me-1"></i> Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePw() {
            var input = document.getElementById('password');
            var icon = document.getElementById('pwToggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>

</html>
