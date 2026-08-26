<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - HarvyMance Films</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="stylesheet" href="{{ asset('css/client-login.css') }}">
</head>

<body>

    {{-- ==================== NAVBAR ==================== --}}
    @include('partials.landing-navbar')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var logo = document.querySelector('.landing-logo');
            function forceDarkLogo() { if (logo) logo.src = logo.dataset.darkLogo; }
            forceDarkLogo();
            document.querySelector('.nav-hamburger')?.addEventListener('click', function () {
                setTimeout(forceDarkLogo, 0);
            });
            document.querySelectorAll('.nav-mobile-links a').forEach(function (link) {
                link.addEventListener('click', function () { setTimeout(forceDarkLogo, 0); });
            });
        });
    </script>

    <main class="container booking-main">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5 col-xl-4">

                @if(session('success'))
                    <div class="alert alert-soft alert-soft-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <section class="surface-card">
                    <div class="text-center mb-4">
                        <h1 class="section-title fs-4">Login</h1>
                    </div>

                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}"
                                   placeholder="name@example.com" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" placeholder="Enter your password" required>
                                <button class="btn login-pw-toggle" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="pwToggleIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary-dark w-100">
                            Log In
                        </button>
                    </form>
                </section>
            </div>
        </div>
    </main>

    {{-- ==================== FOOTER ==================== --}}
    @include('partials.auth-footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            var input = document.getElementById('password');
            var icon = document.getElementById('pwToggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    </script>
</body>

</html>
