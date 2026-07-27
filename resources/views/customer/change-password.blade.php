<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - HarvyMance Films</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/client-login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client-dashboard.css') }}">
</head>

<body class="client-dashboard-body">
    <div class="client-topnav">
        <a href="{{ route('customer.dashboard') }}" class="brand">
            <i class="bi bi-camera-video me-2"></i>HarvyMance Films
        </a>
        <span class="small opacity-75">Booking Monitoring</span>
    </div>

    <div class="container py-4">
        <div class="client-pw-card card border-0 shadow">
            <div class="card-header">
                <h6 class="mb-0 fw-bold"><i class="bi bi-key me-2"></i>Change Password</h6>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success py-2">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger py-2">{{ session('error') }}</div>
                @endif

                <p class="text-muted small mb-3">
                    @if($account->must_change_password)
                        <i class="bi bi-info-circle me-1"></i>
                        This is your first login. Please change your temporary password.
                    @else
                        Update your password to keep your account secure.
                    @endif
                </p>

                <form method="POST" action="{{ route('customer.change-password.post') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                   name="current_password" id="currentPw" required>
                            <button class="btn btn-outline-secondary" type="button" data-target="currentPw" onclick="toggleField(this)">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                   name="new_password" id="new_password" minlength="6" required>
                            <button class="btn btn-outline-secondary" type="button" data-target="new_password" onclick="toggleField(this)">
                                <i class="bi bi-eye"></i>
                            </button>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Confirm New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="new_password_confirmation"
                                   id="new_password_confirmation" minlength="6" required>
                            <button class="btn btn-outline-secondary" type="button" data-target="new_password_confirmation" onclick="toggleField(this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div id="pwMatchIndicator" class="form-text"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('customer.dashboard') }}" class="text-muted text-decoration-none small">
                            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                        </a>
                        <button type="submit" class="btn btn-dark">
                            <i class="bi bi-check-lg me-1"></i> Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/client-dashboard.js') }}"></script>
    <script>
        function toggleField(btn) {
            var target = document.getElementById(btn.dataset.target);
            var icon = btn.querySelector('i');
            if (target.type === 'password') {
                target.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                target.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }

        var newPw = document.getElementById('new_password');
        var confirmPw = document.getElementById('new_password_confirmation');
        var indicator = document.getElementById('pwMatchIndicator');
        function checkMatch() {
            if (!confirmPw.value) { indicator.textContent = ''; return; }
            if (newPw.value === confirmPw.value) {
                indicator.textContent = 'Passwords match';
                indicator.className = 'form-text text-success';
            } else {
                indicator.textContent = 'Passwords do not match';
                indicator.className = 'form-text text-danger';
            }
        }
        if (newPw) newPw.addEventListener('input', checkMatch);
        if (confirmPw) confirmPw.addEventListener('input', checkMatch);
    </script>
</body>

</html>
