<nav class="landing-navbar">
    <div class="container">
        <a href="{{ url('/') }}">
            <img class="landing-logo" src="{{ asset('storage/images/White Logo.png') }}"
                data-light-logo="{{ asset('storage/images/White Logo.png') }}"
                data-dark-logo="{{ asset('storage/images/Black Logo.png') }}" alt="Harvy Mance Films" height="38">
        </a>
        <ul class="nav-links">
            <li><a href="{{ url('/') }}#about">About</a></li>
            <li><a href="{{ url('/') }}#services">Services</a></li>
            <li><a href="{{ route('booking.form') }}" class="btn-nav-book">Book Now</a></li>
            <li><a href="{{ route('login') }}" class="btn-nav-login">Login</a></li>
        </ul>
        <button class="nav-hamburger" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>

<div class="nav-mobile-menu">
    <ul class="nav-mobile-links">
        <li><a href="{{ url('/') }}#about">About</a></li>
        <li><a href="{{ url('/') }}#services">Services</a></li>
        <li><a href="{{ route('booking.form') }}" class="btn-mobile-book">Book Now</a></li>
        <li><a href="{{ route('login') }}" class="btn-mobile-login">Login</a></li>
    </ul>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const navbar = document.querySelector('.landing-navbar');
        const hamburger = document.querySelector('.nav-hamburger');
        const mobileMenu = document.querySelector('.nav-mobile-menu');
        const logo = document.querySelector('.landing-logo');

        const updateNavbar = () => {
            const isScrolled = window.scrollY > 60;
            navbar.classList.toggle('scrolled', isScrolled);
            logo.src = isScrolled ? logo.dataset.darkLogo : logo.dataset.lightLogo;
        };

        window.addEventListener('scroll', updateNavbar, { passive: true });
        updateNavbar();

        const closeMenu = () => {
            hamburger.classList.remove('active');
            mobileMenu.classList.remove('open');
            document.body.classList.remove('menu-open');
        };

        hamburger.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.toggle('open');
            hamburger.classList.toggle('active', isOpen);
            document.body.classList.toggle('menu-open', isOpen);
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeMenu();
        });

        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMenu);
        });
    });
</script>