<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "golden_crust");

$status = '';
$statusClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    
    $name = trim(htmlspecialchars(strip_tags($_POST['name'] ?? '')));
    $city = trim(htmlspecialchars(strip_tags($_POST['city'] ?? '')));
    $mno = trim(htmlspecialchars(strip_tags($_POST['mno'] ?? '')));
    $email = trim(htmlspecialchars(strip_tags($_POST['email'] ?? '')));
    $feedback = trim(htmlspecialchars(strip_tags($_POST['feedback'] ?? '')));

    if (strlen($name) < 2) {
        $status = '⚠️ Please enter your full name.';
        $statusClass = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = '⚠️ Please provide a valid email address.';
        $statusClass = 'error';
    } elseif (strlen($feedback) < 5) {
        $status = '⚠️ Feedback must be at least 5 characters.';
        $statusClass = 'error';
    } else {
        $name = mysqli_real_escape_string($conn, $name);
        $city = mysqli_real_escape_string($conn, $city);
        $mno = mysqli_real_escape_string($conn, $mno);
        $email = mysqli_real_escape_string($conn, $email);
        $feedback = mysqli_real_escape_string($conn, $feedback);
        
        $query = "INSERT INTO feedback (name, city, mno, email, feedback) 
                  VALUES ('$name', '$city', '$mno', '$email', '$feedback')";
        
        if(mysqli_query($conn, $query)){
            $status = '✅ Thank you, ' . $name . '! Your feedback was submitted successfully.';
            $statusClass = 'success';
            $_POST = array();
        } else {
            $status = '⚠️ Error: ' . mysqli_error($conn);
            $statusClass = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us | Golden Crust</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    
    <!-- ==========================================
         CSS INCLUDE - contact.css
         ========================================== -->
         <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/contact.css">
    
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<!-- ===== HERO ===== -->
<section class="hero">
    <i class="fa-solid fa-cake icon"></i>
    <i class="fa-solid fa-envelope icon"></i>
    <i class="fa-solid fa-phone icon"></i>
    <i class="fa-solid fa-comments icon"></i>
    <h1>Contact <span>Us</span></h1>
</section>

<!-- ===== CONTACT FORM + MAP ===== -->
<section class="contact-container">
    
    <!-- FORM -->
    <div class="contact-form">
        <h2><i class="fa-solid fa-paper-plane"></i> Send Message</h2>
        
        <form action="" method="post">
            <div class="input-box">
                <input type="text" name="name" placeholder="Your Full Name *" required
                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            <div class="input-box">
                <input type="email" name="email" placeholder="Email Address *" required
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="input-box">
                <input type="text" name="city" placeholder="Your City"
                       value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
            </div>
            <div class="input-box">
                <input type="tel" name="mno" placeholder="Mobile Number"
                       value="<?php echo isset($_POST['mno']) ? htmlspecialchars($_POST['mno']) : ''; ?>">
            </div>
            <div class="input-box">
                <textarea name="feedback" placeholder="Your Feedback / Message *" required><?php echo isset($_POST['feedback']) ? htmlspecialchars($_POST['feedback']) : ''; ?></textarea>
            </div>
            <button class="send-btn" type="submit" name="contact_submit">
                <i class="fa-solid fa-paper-plane"></i> Send Message
            </button>
        </form>

        <?php if($status): ?>
        <div class="status-msg <?php echo $statusClass; ?>">
            <i class="fas <?php echo $statusClass === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle'; ?>"></i>
            <?php echo $status; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- MAP -->
    <div class="map-box">
        <iframe src="https://maps.google.com/maps?q=Junagadh&t=&z=13&ie=UTF8&iwloc=&output=embed" 
                loading="lazy" 
                title="Golden Crust location map">
        </iframe>
    </div>
    
</section>

<!-- ===== CONTACT CARDS ===== -->
<section class="contact-cards">
    <div class="info-card">
        <i class="fa-solid fa-location-dot"></i>
        <h3>Our Bakery</h3>
        <p>Junagadh<br />Gujarat, India</p>
    </div>
    <div class="info-card">
        <i class="fa-solid fa-envelope"></i>
        <h3>Email</h3>
        <p>info@goldencrust.com</p>
    </div>
    <div class="info-card">
        <i class="fa-solid fa-phone"></i>
        <h3>Phone</h3>
        <p>+91 98765 43210</p>
    </div>
    <div class="info-card">
        <i class="fa-solid fa-clock"></i>
        <h3>Working Hours</h3>
        <p>Mon - Sat<br />09:00 AM – 08:00 PM</p>
    </div>
</section>

</body>
</html>

<?php include 'includes/footer.php'; ?>