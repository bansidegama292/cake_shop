<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Golden Crust - Bakery</title>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<!-- FOOTER -->
<footer class="footer">

    <div class="footer-container">

        <!-- About -->
        <div class="footer-box">
            <h3>Golden Crust</h3>

            <p>
                Freshly baked cakes crafted with love,
                premium ingredients and unforgettable taste.
            </p>
        </div>

        <!-- Quick Links -->
        <div class="footer-box">
            <h3>Quick Links</h3>

            <a href="index.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="cakes.php">Cakes</a>
            <a href="contact.php">Contact Us</a>
        </div>

        <!-- Categories -->
        <div class="footer-box">
            <h3>Categories</h3>

            <a href="cakes.php">Velvet Cakes</a>
            <a href="cakes.php">Cup Cakes</a>
            <a href="cakes.php">Chocolate Cakes</a>
            <a href="cakes.php">Celebration Cakes</a>
        </div>

        <!-- Contact -->
        <div class="footer-box">
            <h3>Contact Info</h3>

            <p>
                <i class="fas fa-location-dot"></i>
                Gujarat, India
            </p>

            <p>
                <i class="fas fa-phone"></i>
                +91 98765 43210
            </p>

            <p>
                <i class="fas fa-envelope"></i>
                info@goldencrust.com
            </p>
        </div>

    </div>

    <!-- SOCIAL ICONS -->
    <div class="social-links">

        <a href="#" title="Facebook">
            <i class="fab fa-facebook-f"></i>
        </a>

        <a href="#" title="Instagram">
            <i class="fab fa-instagram"></i>
        </a>

        <a href="#" title="WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>

        <a href="#" title="YouTube">
            <i class="fab fa-youtube"></i>
        </a>

    </div>

    <!-- COPYRIGHT -->
    <div class="footer-bottom">
        <p>
            © 2026 <span>Golden Crust</span> | All Rights Reserved.
        </p>
    </div>

</footer>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const elements = document.querySelectorAll(
        ".reveal, .reveal-left, .reveal-right, .reveal-up"
    );

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add("active");
            }
        });
    }, {
        threshold: 0.15
    });

    elements.forEach(el => {
        observer.observe(el);
    });

});
</script>

</body>
</html>