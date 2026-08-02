<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - HarvyMance Films</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client-login.css') }}">
</head>

<body>

    <main class="d-flex align-items-center" style="min-height: 100vh;">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-12 col-md-6 col-lg-5 col-xl-4">

                @if(session('success'))
                    <div class="alert alert-soft alert-soft-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-soft alert-soft-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <section class="surface-card">
                    <div class="text-center mb-4 login-header">
                        <h1 class="section-title fs-4">Change Password</h1>
                        @if($account->must_change_password)
                            <p class="section-sub">This is your first login. Please change your temporary password.</p>
                        @else
                            <p class="section-sub">Update your password to keep your account secure.</p>
                        @endif
                    </div>

                    <form method="POST" action="{{ route('client.change-password.post') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                       name="current_password" id="currentPw" placeholder="Enter your current password" required>
                                <button class="btn login-pw-toggle" type="button" data-target="currentPw" onclick="toggleField(this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                       name="new_password" id="new_password" placeholder="At least 6 characters" minlength="6" required>
                                <button class="btn login-pw-toggle" type="button" data-target="new_password" onclick="toggleField(this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="new_password_confirmation"
                                       id="new_password_confirmation" placeholder="Re-enter your new password" minlength="6" required>
                                <button class="btn login-pw-toggle" type="button" data-target="new_password_confirmation" onclick="toggleField(this)">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div id="pwMatchIndicator" class="form-text"></div>
                        </div>
                        <button type="submit" class="btn btn-primary-dark w-100">
                            Update Password
                        </button>
                    </form>

                    <p class="summary-note">
                        <a href="{{ route('client.dashboard') }}" class="text-decoration-none text-dark">
                            <i class="bi bi-arrow-left me-1"></i><strong>Back to Dashboard</strong>
                        </a>
                    </p>
                </section>
            </div>
        </div>
    </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
