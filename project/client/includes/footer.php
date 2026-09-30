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
            <a href="cakes.php">Cakes</a>
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact Us</a>
        </div>

        <!-- Categories -->
        <div class="footer-box">
            <h3>Categories</h3>

            <a href="#">Birthday Cakes</a>
            <a href="#">Wedding Cakes</a>
            <a href="#">Chocolate Cakes</a>
            <a href="#">Designer Cakes</a>
        </div>

        <!-- Contact -->
        <div class="footer-box">
            <h3>Contact Info</h3>

            <p>
                <i class="fas fa-location-dot"></i>
                Shop No.1, Shubham Palace, Zanzarda Rd, near Gayatri School, Junagadh, Gujarat 362001
            </p>

            <p>
                <i class="fas fa-phone"></i>
                +91 74923 54963
            </p>

            <p>
                <i class="fas fa-envelope"></i>
                goldencrust@gmail.com
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