<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harvy Mance Films</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>

<body>

    @include('partials.landing-navbar')

    <header class="landing-hero">
        <div class="hero-bg"></div>
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-eyebrow">Harvy Mance Films</div>
                <h1 class="hero-title">Every Moment<br>Deserves to Last Forever</h1>
                <p class="hero-desc">Cinematic storytelling for weddings, debuts, corporate events, and every milestone
                    worth remembering.</p>
                <div class="hero-actions">
                    <a href="{{ route('booking.form') }}" class="btn-hero-secondary">
                        Book Now <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="hero-scroll">
            <div class="scroll-line"></div>
            <span>Scroll</span>
        </div>
    </header>

    <section class="landing-section" id="about">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="about-img-wrap">
                        <img src="{{ asset('storage/images/2.jpg') }}" alt="Harvy Mance Films behind the scenes"
                            loading="lazy">
                        <div class="about-img-content about-overlay-mobile">
                            <div class="section-label">About Us</div>
                            <h2 class="about-img-title">Crafting Stories That Move People</h2>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 centered-copy">
                    <div class="about-desktop-heading">
                        <div class="section-label">About Us</div>
                        <h2 class="section-heading">Crafting Stories That Move People</h2>
                    </div>
                    <p class="section-body mb-4">
                        Harvy Mance Films is a professional film production team dedicated to capturing life's most
                        meaningful moments with cinematic precision and artistic vision. From intimate ceremonies to
                        grand celebrations, we bring your story to life.
                    </p>
                    <p class="section-body mb-4">
                        With a passion for visual storytelling and a commitment to quality, every project we take on is
                        treated with the care and creativity it deserves.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="landing-section" id="services">
        <div class="container">
            <div class="why-intro text-center mb-5">
                <div class="section-label justify-content-center">What We Do</div>
                <h2 class="section-heading">Our Services</h2>
                <p class="section-body mx-auto">Full-service film and video production tailored to your event and
                    vision.</p>
            </div>

            <div class="services-grid">
                <div class="service-card service-card-1">
                    <img src="{{ asset('storage/images/4.jpg') }}" alt="Wedding film showcase" loading="lazy">
                    <div class="about-img-content">
                        <h3 class="about-img-title">Wedding Films</h3>
                        <p class="about-img-desc">Cinematic coverage of your wedding day from ceremony to reception.</p>
                    </div>
                </div>
                <div class="service-card service-card-2">
                    <img src="{{ asset('storage/images/3.jpg') }}" alt="Debut and birthday celebration" loading="lazy">
                    <div class="about-img-content">
                        <h3 class="about-img-title">Debut & Birthdays</h3>
                        <p class="about-img-desc">Elegant documentation of milestone celebrations and special occasions.
                        </p>
                    </div>
                </div>
                <div class="service-card service-card-3">
                    <img src="{{ asset('storage/images/5.jpg') }}" alt="Corporate event production" loading="lazy">
                    <div class="about-img-content">
                        <h3 class="about-img-title">Corporate Events</h3>
                        <p class="about-img-desc">Professional video production for conferences, launches, and company
                            events.</p>
                    </div>
                </div>
                <div class="service-card service-card-4">
                    <img src="{{ asset('storage/images/6.jpg') }}" alt="Post production editing" loading="lazy">
                    <div class="about-img-content">
                        <h3 class="about-img-title">Post Production</h3>
                        <p class="about-img-desc">Expert editing, color grading, and delivery of your final film.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="landing-section" id="why">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 centered-copy">
                    <div class="section-label">Why Us</div>
                    <h2 class="section-heading">Why Choose Harvy Mance Films</h2>
                    <p class="section-body">We combine technical expertise with genuine passion to deliver films that
                        exceed expectations every time.</p>
                </div>
                <div class="col-lg-7">
                    <div class="why-grid">
                        <div class="why-item">
                            <div class="why-title">Cinematic Quality</div>
                            <p class="why-desc">Professional-grade equipment and techniques for a premium visual result.
                            </p>
                        </div>
                        <div class="why-item">
                            <div class="why-title">Experienced Team</div>
                            <p class="why-desc">A dedicated crew that works seamlessly to capture every important
                                moment.</p>
                        </div>
                        <div class="why-item">
                            <div class="why-title">On-Time Delivery</div>
                            <p class="why-desc">We respect your timeline and deliver your final film as promised.</p>
                        </div>
                        <div class="why-item">
                            <div class="why-title">Personal Approach</div>
                            <p class="why-desc">Every event is unique. We tailor our coverage to your story and vision.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready to Capture Your Story?</h2>
            <p class="cta-desc">Book your date today and let us create something beautiful together.</p>
            <a href="{{ route('booking.form') }}" class="btn-cta">
                Book Now <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>

    <footer class="landing-footer">
        <div class="container text-center">
            <small>&copy; {{ date('Y') }} Harvy Mance Films. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        let lastScrollY = window.scrollY;
        const navbar = document.querySelector('.landing-navbar');

        window.addEventListener('scroll', () => {
            const currentScrollY = window.scrollY;

            navbar.classList.toggle('scrolled', currentScrollY > 10);

            if (currentScrollY > lastScrollY && currentScrollY > 100) {
                navbar.classList.add('nav-hidden');
            } else {
                navbar.classList.remove('nav-hidden');
            }

            lastScrollY = currentScrollY;
        }, { passive: true });
    </script>
</body>

</html>