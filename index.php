<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!-- ==========================================
   CSS INCLUDE - home.css
   ========================================== -->
   <link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/home.css">

<!-- ==========================================
   BACKGROUND EFFECT WITH MOVEMENT
   ========================================== -->
<div class="page-bg">
    <!-- Floating Blobs -->
    <div class="bg-blob blob-1"></div>
    <div class="bg-blob blob-2"></div>
    <div class="bg-blob blob-3"></div>
    <div class="bg-blob blob-4"></div>
    <div class="bg-blob blob-5"></div>
    
    <!-- Floating Icons -->
    <div class="float-icon icon-1">🧁</div>
    <div class="float-icon icon-2">🎂</div>
    <div class="float-icon icon-3">🍰</div>
    <div class="float-icon icon-4">🧇</div>
    <div class="float-icon icon-5">🍩</div>
    <div class="float-icon icon-6">🥧</div>
    <div class="float-icon icon-7">🍪</div>
    <div class="float-icon icon-8">🧁</div>
    
    <!-- Sparkles -->
    <div class="sparkle s1"></div>
    <div class="sparkle s2"></div>
    <div class="sparkle s3"></div>
    <div class="sparkle s4"></div>
    <div class="sparkle s5"></div>
    <div class="sparkle s6"></div>
    <div class="sparkle s7"></div>
    <div class="sparkle s8"></div>
</div>

<!-- ==========================================
   HERO SECTION
   ========================================== -->
<section class="hero">

    <div class="hero-left">

        <span class="tag">
            <i class="fas fa-star" style="color:#ff6b9d;"></i> Premium Cake Shop
        </span>

        <h1>
            Crafting
            <span class="gradient-text">Sweet</span>
           Moments With <span class="gradient-text">Luxury Cakes</span> Made To Impress
        </h1>

        <p>
           Experience handcrafted cakes baked with premium ingredients, elegant designs, and unforgettable flavors for every celebration.
        </p>

        <div class="hero-buttons">
            <a href="cakes.php" class="btn-primary">
                <i class="fas fa-shopping-bag"></i> Order Now
            </a>

            <a href="about.php" class="btn-secondary">
                <i class="fas fa-play-circle"></i> Learn More
            </a>
        </div>

        <div class="stats">

            <div class="stat-box">
                <h2>500+</h2>
                <p>Cakes Sold</p>
            </div>

            <div class="stat-box">
                <h2>100+</h2>
                <p>Flavors</p>
            </div>

            <div class="stat-box">
                <h2>4.9★</h2>
                <p>Ratings</p>
            </div>

        </div>

    </div>

    <div class="hero-right">

        <div class="cake-circle"></div>
        <div class="hero-image-wrapper">
            <img src="images/bg/f1d5ac531ed27d1af48dd42742fe391c.jpg" alt="Chocolate Cake">
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
   FEATURED CAKES
   ========================================== -->
<section class="featured">

    <div class="section-title reveal">
        <span class="section-tag">Our Collection</span>
        <h2>special<span class="gradient-text">Cakes</span></h2>
        <p>Our most loved cakes</p>
    </div>

    <div class="cake-grid">

        <div class="cake-card reveal-up">
            <div class="cake-image-wrapper">
                <img src="images/bg/5084b7dbe586fa48b7c5c82735e83b70.jpg" alt="Chocolate Cake">
                <div class="cake-overlay">
                    <a href="#" class="btn-zoom" onclick="openZoom(this)">
                        <i class="fas fa-search-plus"></i> 
                    </a>
                </div>
                <div class="cake-badge">Best Seller</div>
            </div>
            <div class="cake-info">
                <h3>Chocolate Cake</h3>
                <div class="cake-meta">
                    <span class="price">₹859</span>
                    <span class="rating"><i class="fas fa-star" style="color:#ff6b9d;"></i> 4.8</span>
                </div>
            </div>
        </div>

        <div class="cake-card reveal-up" style="animation-delay: 0.1s;">
            <div class="cake-image-wrapper">
                <img src="images/bg/cb48295f523b50faddc4c42a6dfa1342.jpg" alt="Birthday Cake">
                <div class="cake-overlay">
                    <a href="#" class="btn-zoom" onclick="openZoom(this)">
                        <i class="fas fa-search-plus"></i> 
                    </a>
                </div>
                <div class="cake-badge">Premium</div>
            </div>
            <div class="cake-info">
                <h3>Birthday Cake</h3>
                <div class="cake-meta">
                    <span class="price">₹899</span>
                    <span class="rating"><i class="fas fa-star" style="color:#ff6b9d;"></i> 4.9</span>
                </div>
            </div>
        </div>

        <div class="cake-card reveal-up" style="animation-delay: 0.2s;">
            <div class="cake-image-wrapper">
                <img src="images/bg/ff332a26fc84e0ef10865014e060c424.jpg" alt="Strawberry Cake">
                <div class="cake-overlay">
                    <a href="#" class="btn-zoom" onclick="openZoom(this)">
                        <i class="fas fa-search-plus"></i> 
                    </a>
                </div>
                <div class="cake-badge">Popular</div>
            </div>
            <div class="cake-info">
                <h3>Floral Cake</h3>
                <div class="cake-meta">
                    <span class="price">₹799</span>
                    <span class="rating"><i class="fas fa-star" style="color:#ff6b9d;"></i> 4.7</span>
                </div>
            </div>
        </div>

        <div class="cake-card reveal-up" style="animation-delay: 0.3s;">
            <div class="cake-image-wrapper">
                <img src="images/bg/64d91df74967825d9261d2eccf5f3e9a.jpg" alt="Fruit Cake">
                <div class="cake-overlay">
                    <a href="#" class="btn-zoom" onclick="openZoom(this)">
                        <i class="fas fa-search-plus"></i> 
                    </a>
                </div>
                <div class="cake-badge">Fresh</div>
            </div>
            <div class="cake-info">
                <h3>Flower Cake</h3>
                <div class="cake-meta">
                    <span class="price">₹750</span>
                    <span class="rating"><i class="fas fa-star" style="color:#ff6b9d;"></i> 4.6</span>
                </div>
            </div>
        </div>

    </div>

</section>

<!-- ==========================================
   WHY US
   ========================================== -->
<section class="why-us">

    <div class="section-title reveal">
        <span class="section-tag">Why Choose Us</span>
        <h2>Why Choose <span class="gradient-text">Golden Crust</span>?</h2>
        <p>We bake with passion and deliver perfection</p>
    </div>

    <div class="why-grid">

        <div class="why-card reveal-up">
            <div class="why-icon">
                <i class="fa-solid fa-cake-candles"></i>
            </div>
            <h3>Fresh Cakes</h3>
            <p>Baked daily with premium ingredients.</p>
            <div class="card-shine"></div>
        </div>

        <div class="why-card reveal-up" style="animation-delay: 0.1s;">
            <div class="why-icon">
                <i class="fa-solid fa-heart"></i>
            </div>
            <h3>Made With Love</h3>
            <p>Every cake is handcrafted carefully.</p>
            <div class="card-shine"></div>
        </div>

        <div class="why-card reveal-up" style="animation-delay: 0.2s;">
            <div class="why-icon">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <h3>Fast Delivery</h3>
            <p>Quick and safe cake delivery.</p>
            <div class="card-shine"></div>
        </div>

        <div class="why-card reveal-up" style="animation-delay: 0.3s;">
            <div class="why-icon">
                <i class="fa-solid fa-star"></i>
            </div>
            <h3>Premium Quality</h3>
            <p>Rich taste and beautiful designs.</p>
            <div class="card-shine"></div>
        </div>

    </div>

</section>

<!-- ==========================================
   OUR TEAM SECTION
   ========================================== -->
<section class="our-team">

    <div class="section-title reveal">
        <span class="section-tag">Our Team</span>
        <h2>Meet The <span class="gradient-text">Bakers</span></h2>
        <p>The passionate people behind every delicious creation</p>
    </div>

    <div class="team-grid">

        <div class="team-card reveal-up">
            <div class="team-avatar" style="background:linear-gradient(135deg,#ff6b9d,#e8557a);">
                <span>R</span>
            </div>
            <h3>Sasiya Riddhi</h3>
            <p class="team-role">Head Baker & Founder</p>
            <p class="team-bio">20+ years of pastry magic. Loves vintage recipes and classic flavors.</p>
            <div class="team-social">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
            <div class="card-shine"></div>
            <div class="card-hover-line"></div>
        </div>

        <div class="team-card reveal-up" style="animation-delay: 0.1s;">
            <div class="team-avatar" style="background:linear-gradient(135deg,#ff9eb5,#ffb6c9);">
                <span>B</span>
            </div>
            <h3>Degama Bansi</h3>
            <p class="team-role">Cake Designer</p>
            <p class="team-bio">Former architect, now crafting edible masterpieces with artistic flair.</p>
            <div class="team-social">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
            <div class="card-shine"></div>
            <div class="card-hover-line"></div>
        </div>

        <div class="team-card reveal-up" style="animation-delay: 0.2s;">
            <div class="team-avatar" style="background:linear-gradient(135deg,#ffb0c3,#ffc1d0);">
                <span>R</span>
            </div>
            <h3>Rathod Riya</h3>
            <p class="team-role">Flavor Specialist</p>
            <p class="team-bio">Spice & citrus alchemist who creates unforgettable flavor combinations.</p>
            <div class="team-social">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
            <div class="card-shine"></div>
            <div class="card-hover-line"></div>
        </div>

    </div>

</section>

<!-- ==========================================
   CTA SECTION
   ========================================== -->
<section class="cta reveal">

    <div class="cta-content">
        <span class="cta-tag">🎉 Celebrate With Us</span>
        <h2>
            Ready To Make Your <span class="gradient-text">Special Moments</span> Memorable?
        </h2>
        <p>
            Order your favorite cake today and let us add sweetness to your celebration.
            Every cake tells a story of love and happiness.
        </p>
        <div class="cta-buttons">
            <a href="cakes.php" class="btn-primary">
                <i class="fas fa-cake"></i> Order Your Cake
            </a>
            <a href="contact.php" class="btn-secondary cta-ghost">
                <i class="fas fa-comment"></i> Get In Touch
            </a>
        </div>
    </div>

</section>

<!-- ==========================================
   ZOOM MODAL
   ========================================== -->
<div id="zoomModal" class="zoom-modal" onclick="closeZoom()">
    <span class="zoom-close">&times;</span>
    <img class="zoom-modal-content" id="zoomImage">
</div>

<!-- ==========================================
   JAVASCRIPT
   ========================================== -->
<script>
// ========================================
// SCROLL REVEAL ANIMATIONS
// ========================================
document.addEventListener('DOMContentLoaded', function() {
    const revealElements = document.querySelectorAll('.reveal, .reveal-up');
    
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
});

// ========================================
// ZOOM FUNCTIONALITY
// ========================================
function openZoom(element) {
    event.preventDefault();
    const card = element.closest('.cake-card');
    const img = card.querySelector('.cake-image-wrapper img');
    const modal = document.getElementById('zoomModal');
    const modalImg = document.getElementById('zoomImage');
    
    modal.style.display = 'block';
    modalImg.src = img.src;
    modalImg.alt = img.alt;
    document.body.style.overflow = 'hidden';
}

function closeZoom() {
    const modal = document.getElementById('zoomModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close zoom with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeZoom();
    }
});
</script>

<?php include 'includes/footer.php'; ?>