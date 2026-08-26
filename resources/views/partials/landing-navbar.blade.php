<nav class="landing-navbar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="{{ url('/') }}">
            <img class="landing-logo" src="{{ asset('storage/images/White Logo.png') }}"
                data-light-logo="{{ asset('storage/images/White Logo.png') }}"
                data-dark-logo="{{ asset('storage/images/Black Logo.png') }}" alt="Harvy Mance Films" height="44">
        </a>
        <ul class="nav-links d-none d-lg-flex align-items-center gap-2 list-unstyled m-0 p-0">
            <li><a href="{{ url('/') }}#about">About</a></li>
            <li><a href="{{ url('/') }}#services">Services</a></li>
            <li><a href="{{ route('booking.form') }}">Book Now</a></li>
            <li><a href="{{ route('client.login') }}" class="btn-nav-login">Login</a></li>
        </ul>
        <button class="nav-hamburger" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</nav>

<div class="nav-mobile-menu">
    <ul class="nav-mobile-links d-flex flex-column align-items-center justify-content-center list-unstyled">
        <li><a href="{{ url('/') }}#about" class="d-flex align-items-center justify-content-center">About</a></li>
        <li><a href="{{ url('/') }}#services" class="d-flex align-items-center justify-content-center">Services</a></li>
        <li><a href="{{ route('booking.form') }}"
                class="btn-mobile-book d-flex align-items-center justify-content-center">Book Now</a></li>
        <li><a href="{{ route('client.login') }}"
                class="btn-mobile-login d-flex align-items-center justify-content-center">Login</a></li>
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
            const useDarkLogo = isScrolled || mobileMenu.classList.contains('open');
            logo.src = useDarkLogo ? logo.dataset.darkLogo : logo.dataset.lightLogo;
        };

        window.addEventListener('scroll', updateNavbar, { passive: true });
        updateNavbar();

        const closeMenu = () => {
            hamburger.classList.remove('active');
            mobileMenu.classList.remove('open');
            navbar.classList.remove('menu-open');
            document.body.classList.remove('menu-open');
            updateNavbar();
        };

        hamburger.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.toggle('open');
            hamburger.classList.toggle('active', isOpen);
            navbar.classList.toggle('menu-open', isOpen);
            document.body.classList.toggle('menu-open', isOpen);
            updateNavbar();
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeMenu();
        });

        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', closeMenu);
        });
    });
</script>