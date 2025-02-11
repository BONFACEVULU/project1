<?php
require_once 'includes/header.php';
?>

<section class="hero-section" style="background: linear-gradient(to right, #000, #feb47b);">
    <div class="overlay"></div>
    <div class="container">
        <div class="row hero-content">
            <div class="col-md-8 text-white">
                <h1 data-aos="fade-up">Welcome to Our Dance Studio</h1>
                <p data-aos="fade-up" data-aos-delay="200">
                    Discover the joy of dance with over 400 weekly classes in 40+ styles
                </p>
                <a href="classes.php" class="btn btn-primary btn-lg" data-aos="fade-up" data-aos-delay="400">
                    Start Dancing Today
                </a>
            </div>
        </div>
    </div>
</section>

<section class="features-section py-5">
    <div class="container">
        <h2 class="text-center mb-5">Why Choose Us</h2>
        <div class="row">
            <div class="col-md-3 text-center mb-4" data-aos="fade-up">
                <div class="feature-box">
                    <i class="fas fa-users feature-icon"></i>
                    <h4>All Levels Welcome</h4>
                    <p>From beginners to professionals</p>
                </div>
            </div>
            <div class="col-md-3 text-center mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-box">
                    <i class="fas fa-music feature-icon"></i>
                    <h4>40+ Dance Styles</h4>
                    <p>Diverse range of classes</p>
                </div>
            </div>
            <div class="col-md-3 text-center mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-box">
                    <i class="fas fa-child feature-icon"></i>
                    <h4>Kids Classes</h4>
                    <p>Special programs for children</p>
                </div>
            </div>
            <div class="col-md-3 text-center mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="feature-box">
                    <i class="fas fa-calendar feature-icon"></i>
                    <h4>Flexible Schedule</h4>
                    <p>Classes 7 days a week</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section text-white text-center py-5">
    <div class="container">
        <h2 data-aos="fade-up">Ready to Start Your Dance Journey?</h2>
        <p data-aos="fade-up" data-aos-delay="200">
            Join our community of dancers and express yourself through movement
        </p>
        <a href="classes.php" class="btn btn-light btn-lg" data-aos="fade-up" data-aos-delay="400">
            Book Your First Class
        </a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
