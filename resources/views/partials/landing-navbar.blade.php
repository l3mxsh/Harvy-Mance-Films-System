<nav class="landing-navbar">
    <div class="container">
        <a href="{{ url('/') }}">
            <img src="{{ asset('storage/images/Black Logo.png') }}" alt="Harvy Mance Films" height="50">
        </a>
        <ul class="nav-links">
            <li><a href="{{ url('/') }}#about">About</a></li>
            <li><a href="{{ url('/') }}#services">Services</a></li>
            <li><a href="{{ url('/') }}#portfolio">Portfolio</a></li>
            <li><a href="{{ route('booking.form') }}">Book Now</a></li>
            <li><a href="{{ route('login') }}" class="btn-nav-login">Login</a></li>
        </ul>
        <div class="d-md-none d-flex gap-2">
            <a href="{{ route('booking.form') }}"
                style="font-size:0.82rem;padding:0.45rem 1rem;background:#fff;color:#111;border-radius:999px;text-decoration:none;font-weight:600;">Book Now</a>
            <a href="{{ route('login') }}" class="btn-nav-login"
                style="font-size:0.82rem;padding:0.45rem 1rem;border-radius:999px;text-decoration:none;font-weight:600;">Login</a>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.querySelector('.landing-navbar');
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });
    });
</script>
