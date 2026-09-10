<?php include 'includes/header.php'; ?>

<!-- ==========================================
   CSS INCLUDE - about.css
   ========================================== -->
   <link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/about.css">

<!-- ==========================================
   BACKGROUND EFFECT WITH UNIQUE ICONS & MOVEMENT
   ========================================== -->
<div class="about-bg">
    <!-- Floating Blobs -->
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>
    <div class="bg-blob blob-3"></div>
    <div class="bg-blob blob-4"></div>
    <div class="bg-blob blob-5"></div>
    
    <!-- Unique Background Icons -->
    <div class="bg-icon icon-1">🎂</div>
    <div class="bg-icon icon-2">🧁</div>
    <div class="bg-icon icon-3">🍰</div>
    <div class="bg-icon icon-4">🥧</div>
    <div class="bg-icon icon-5">🍩</div>
    <div class="bg-icon icon-6">🧇</div>
    <div class="bg-icon icon-7">🥮</div>
    <div class="bg-icon icon-8">🍪</div>
    <div class="bg-icon icon-9">🧁</div>
    <div class="bg-icon icon-10">🎂</div>
    <div class="bg-icon icon-11">🍰</div>
    <div class="bg-icon icon-12">🥧</div>
    
    <!-- Decorative Elements -->
    <div class="deco-line line-1"></div>
    <div class="deco-line line-2"></div>
    <div class="deco-dots dots-1"></div>
    <div class="deco-dots dots-2"></div>
</div>

<?php include 'includes/navbar.php'; ?>

<!-- ==========================================
   HERO SECTION
   ========================================== -->
<section class="kit-hero">

    <div class="kit-hero-text reveal">
        <span class="hero-badge">
            <i class="fas fa-heart" style="color:#ff6b9d;"></i> Made With Love
        </span>
        <h1>We Bake More Than <span class="gradient-text">Cakes</span></h1>
        <p>
            Golden Crust Bakery creates handcrafted cakes that turn every moment into a celebration.
            Each bite tells a story of passion, quality, and pure joy.
        </p>
        <div class="hero-buttons">
            <a href="cakes.php" class="btn-primary">
                <i class="fas fa-cake"></i> Explore Our Cakes
            </a>
            <a href="#story" class="btn-ghost">
                <i class="fas fa-play-circle"></i> Our Story
            </a>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <span class="stat-number">2,400+</span>
                <span class="stat-label">Cakes Baked</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number">4.9 ★</span>
                <span class="stat-label">Customer Rating</span>
            </div>
            <div class="hero-stat">
                <span class="stat-number">150+</span>
                <span class="stat-label">Custom Designs</span>
            </div>
        </div>
    </div>

    <div class="kit-hero-img reveal-right">
        <div class="hero-image-wrapper">
            <img src="images/bg/about.jpg" alt="Delicious Cake">
            <div class="floating-badge badge-1">
                <i class="fas fa-heart" style="color:#ff6b9d;"></i> Made with Love
            </div>
            <div class="floating-badge badge-2">
                <i class="fas fa-award" style="color:#ff6b9d;"></i> Premium Quality
            </div>
        </div>
    </div>

</section>

<!-- ==========================================
   ABOUT STORY
   ========================================== -->
<section class="kit-about" id="story">

    <div class="kit-img reveal-left">
        <div class="image-frame">
            <img src="images/bg/images (7).jpg" alt="Our Bakery">
            <div class="experience-badge">
                <span class="years">12+</span>
                <span class="label">Years of Excellence</span>
            </div>
        </div>
    </div>

    <div class="kit-text reveal">
        <span class="section-tag">About Us</span>
        <h2>Our Sweet Story</h2>
        <p class="lead">
            We started with a small passion for baking and turned it into a bakery loved by hundreds of families.
        </p>
        <p>
            Every cake is made fresh with premium ingredients and creative designs. 
            From birthdays to weddings, we pour our hearts into every creation.
        </p>

        <div class="kit-points">
            <div class="point-item">
                <i class="fas fa-check-circle" style="color:#ff6b9d;"></i>
                <div>
                    <strong>Fresh Ingredients</strong>
                    <span>Only the finest quality</span>
                </div>
            </div>
            <div class="point-item">
                <i class="fas fa-check-circle" style="color:#ff6b9d;"></i>
                <div>
                    <strong>Custom Designs</strong>
                    <span>Unique cakes for every occasion</span>
                </div>
            </div>
            <div class="point-item">
                <i class="fas fa-check-circle" style="color:#ff6b9d;"></i>
                <div>
                    <strong>Fast Delivery</strong>
                    <span>Fresh cakes delivered on time</span>
                </div>
            </div>
        </div>
        
        <a href="cakes.php" class="btn-primary">
            <i class="fas fa-arrow-right"></i> View Our Collection
        </a>
    </div>

</section>

<!-- ==========================================
   FEATURES
   ========================================== -->
<section class="kit-features">

    <div class="section-header reveal">
        <span class="section-tag">Why Choose Us</span>
        <h2>Baked With <span class="gradient-text">Perfection</span></h2>
        <p>Every cake is a masterpiece of taste and design</p>
    </div>

    <div class="features-grid">
        <div class="feature-card reveal-up">
            <div class="feature-icon">
                <i class="fas fa-crown"></i>
            </div>
            <h3>Premium Taste</h3>
            <p>Made with high-quality ingredients sourced from the best suppliers</p>
            <div class="feature-hover"></div>
        </div>

        <div class="feature-card reveal-up" style="animation-delay: 0.1s;">
            <div class="feature-icon">
                <i class="fas fa-palette"></i>
            </div>
            <h3>Creative Design</h3>
            <p>Unique cakes for every occasion with artistic flair and innovation</p>
            <div class="feature-hover"></div>
        </div>

        <div class="feature-card reveal-up" style="animation-delay: 0.2s;">
            <div class="feature-icon">
                <i class="fas fa-truck"></i>
            </div>
            <h3>On-Time Delivery</h3>
            <p>Fresh cakes delivered fast while maintaining quality and freshness</p>
            <div class="feature-hover"></div>
        </div>
    </div>

</section>

<!-- ==========================================
   JOURNEY / TIMELINE
   ========================================== -->
<section class="kit-journey">

    <div class="section-header reveal">
        <span class="section-tag">Our Timeline</span>
        <h2>Our Sweet <span class="gradient-text">Journey</span></h2>
        <p>Every milestone made with love and dedication</p>
    </div>

    <div class="kit-timeline">

        <div class="timeline-line"></div>

        <div class="kit-item reveal-left">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <span class="year">2020</span>
                <h3>Started Home Bakery</h3>
                <p>We began baking from home with passion and a dream.</p>
            </div>
        </div>

        <div class="kit-item reveal-right">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <span class="year">2021</span>
                <h3>First 100 Customers</h3>
                <p>We reached our first milestone with delighted customers.</p>
            </div>
        </div>

        <div class="kit-item reveal-left">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <span class="year">2023</span>
                <h3>Custom Cakes Added</h3>
                <p>We introduced designer cakes and custom creations.</p>
            </div>
        </div>

        <div class="kit-item reveal-right">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
                <span class="year">2026</span>
                <h3>Premium Brand</h3>
                <p>Now we are a trusted bakery brand loved by many.</p>
            </div>
        </div>

    </div>

</section>

<!-- ==========================================
   PREMIUM CTA
   ========================================== -->
<div class="kit-cta reveal">

    <div class="cta-inner">

        <div class="cta-content">
            <span class="cta-tag">✨ Ready to Celebrate?</span>
            <h2>Make Every Celebration <span class="gradient-text">Unforgettable</span></h2>

            <p>
                From birthdays to weddings — we craft cakes that tell your story 
                with sweetness, love, and perfection.
            </p>

            <div class="cta-highlight">
                <span><i class="fas fa-clock" style="color:#ff6b9d;"></i> Freshly Baked Daily</span>
                <span><i class="fas fa-truck" style="color:#ff6b9d;"></i> Fast Delivery</span>
                <span><i class="fas fa-paint-brush" style="color:#ff6b9d;"></i> Custom Designs</span>
            </div>

            <div class="cta-buttons">
                <a href="cakes.php" class="cta-btn primary">
                    <i class="fas fa-cake"></i> Order Your Cake
                </a>
                <a href="contact.php" class="cta-btn secondary">
                    <i class="fas fa-comment"></i> Talk to Us
                </a>
            </div>
        </div>

        <div class="cta-image">
            <img src="images/bg/images (1).jpg" alt="Celebration Cake">
        </div>

    </div>

</div>

<!-- ==========================================
   JAVASCRIPT
   ========================================== -->
<script>
// ========================================
// SCROLL REVEAL ANIMATIONS
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-up');
    
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));

    // Add smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>