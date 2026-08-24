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

    {{-- ==================== NAVBAR ==================== --}}
    @include('partials.landing-navbar')

    {{-- ==================== HERO ==================== --}}
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
                    <a href="{{ route('booking.form') }}" class="btn-hero-primary">
                        Book Now <i class="bi bi-arrow-right"></i>
                    </a>
                    <a href="#portfolio" class="btn-hero-secondary">
                        View Our Work <i class="bi bi-play-circle"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="hero-scroll">
            <div class="scroll-line"></div>
            <span>Scroll</span>
        </div>
    </header>

    {{-- ==================== ABOUT ==================== --}}
    <section class="landing-section" id="about">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6">
                    <div class="about-img-wrap">
                        <img src="{{ asset('storage/images/2.jpg') }}" alt="Harvy Mance Films behind the scenes"
                            loading="lazy">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="section-label">About Us</div>
                    <h2 class="section-heading">Crafting Stories That Move People</h2>
                    <p class="section-body mb-4">
                        Harvy Mance Films is a professional film production team dedicated to capturing life's most
                        meaningful moments with cinematic precision and artistic vision. From intimate ceremonies to
                        grand celebrations, we bring your story to life.
                    </p>
                    <p class="section-body mb-4">
                        With a passion for visual storytelling and a commitment to quality, every project we take on is
                        treated with the care and creativity it deserves.
                    </p>
                    <a href="{{ route('booking.form') }}" class="btn btn-dark rounded-pill px-4">
                        Start Your Booking <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== SERVICES ==================== --}}
    <section class="landing-section services-bg" id="services">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-6">
                    <div class="section-label">What We Do</div>
                    <h2 class="section-heading">Our Services</h2>
                    <p class="section-body">Full-service film and video production tailored to your event and vision.
                    </p>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-card-body">
                            <div class="service-icon"><i class="bi bi-camera-video"></i></div>
                            <h3 class="service-card-title">Wedding Films</h3>
                            <p class="service-card-desc">Cinematic coverage of your wedding day from ceremony to
                                reception.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-card-body">
                            <div class="service-icon"><i class="bi bi-stars"></i></div>
                            <h3 class="service-card-title">Debut & Birthdays</h3>
                            <p class="service-card-desc">Elegant documentation of milestone celebrations and special
                                occasions.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-card-body">
                            <div class="service-icon"><i class="bi bi-briefcase"></i></div>
                            <h3 class="service-card-title">Corporate Events</h3>
                            <p class="service-card-desc">Professional video production for conferences, launches, and
                                company events.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card">
                        <div class="service-card-body">
                            <div class="service-icon"><i class="bi bi-film"></i></div>
                            <h3 class="service-card-title">Post Production</h3>
                            <p class="service-card-desc">Expert editing, color grading, and delivery of your final film.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== WHY CHOOSE US ==================== --}}
    <section class="landing-section" id="why">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5">
                    <div class="section-label">Why Us</div>
                    <h2 class="section-heading">Why Choose Harvy Mance Films</h2>
                    <p class="section-body">We combine technical expertise with genuine passion to deliver films that
                        exceed expectations every time.</p>
                </div>
                <div class="col-lg-7">
                    <div class="why-grid">
                        <div class="why-item">
                            <div class="why-icon"><i class="bi bi-camera-video-fill"></i></div>
                            <div class="why-title">Cinematic Quality</div>
                            <p class="why-desc">Professional-grade equipment and techniques for a premium visual result.
                            </p>
                        </div>
                        <div class="why-item">
                            <div class="why-icon"><i class="bi bi-people-fill"></i></div>
                            <div class="why-title">Experienced Team</div>
                            <p class="why-desc">A dedicated crew that works seamlessly to capture every important
                                moment.</p>
                        </div>
                        <div class="why-item">
                            <div class="why-icon"><i class="bi bi-clock-fill"></i></div>
                            <div class="why-title">On-Time Delivery</div>
                            <p class="why-desc">We respect your timeline and deliver your final film as promised.</p>
                        </div>
                        <div class="why-item">
                            <div class="why-icon"><i class="bi bi-heart-fill"></i></div>
                            <div class="why-title">Personal Approach</div>
                            <p class="why-desc">Every event is unique. We tailor our coverage to your story and vision.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== PORTFOLIO ==================== --}}
    <section class="landing-section portfolio-bg" id="portfolio">
        <div class="container">
            <div class="row mb-5">
                <div class="col-lg-6">
                    <div class="section-label">Featured Work</div>
                    <h2 class="section-heading">Our Portfolio</h2>
                    <p class="section-body">A glimpse of the stories we've had the privilege to tell.</p>
                </div>
            </div>
            <div class="bento-portfolio">
                <div class="portfolio-item bp-1">
                    <img src="{{ asset('storage/images/1.jpg') }}" alt="Featured work" loading="lazy">
                    <div class="portfolio-overlay"><span class="portfolio-tag">Wedding</span></div>
                </div>
                <div class="portfolio-item bp-2">
                    <img src="{{ asset('storage/images/3.jpg') }}" alt="Portfolio" loading="lazy">
                    <div class="portfolio-overlay"><span class="portfolio-tag">Debut</span></div>
                </div>
                <div class="portfolio-item bp-3">
                    <img src="{{ asset('storage/images/4.jpg') }}" alt="Portfolio" loading="lazy">
                    <div class="portfolio-overlay"><span class="portfolio-tag">Wedding</span></div>
                </div>
                <div class="portfolio-item bp-4">
                    <img src="{{ asset('storage/images/5.jpg') }}" alt="Portfolio" loading="lazy">
                    <div class="portfolio-overlay"><span class="portfolio-tag">Wedding</span></div>
                </div>
                <div class="portfolio-item bp-5">
                    <img src="{{ asset('storage/images/2.jpg') }}" alt="Portfolio" loading="lazy">
                    <div class="portfolio-overlay"><span class="portfolio-tag">Wedding</span></div>
                </div>
                <div class="portfolio-item bp-6">
                    <img src="{{ asset('storage/images/6.jpg') }}" alt="Portfolio" loading="lazy">
                    <div class="portfolio-overlay"><span class="portfolio-tag">Debut</span></div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== CTA ==================== --}}
    <section class="cta-section">
        <div class="container">
            <h2 class="cta-title">Ready to Capture Your Story?</h2>
            <p class="cta-desc">Book your date today and let us create something beautiful together.</p>
            <a href="{{ route('booking.form') }}" class="btn-cta">
                Book Now <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </section>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="landing-footer">
        <div class="container text-center">
            <small>&copy; {{ date('Y') }} Harvy Mance Films. All rights reserved.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>