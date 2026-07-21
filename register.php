<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "golden_crust";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variable
$msg = "";

// Check if form is submitted
if (isset($_POST['register']) && $_POST['register'] == '1') {
    // Get form data and sanitize
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']); // Plain text
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $mobileno = mysqli_real_escape_string($conn, $_POST['mobileno']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);

    // Check if username already exists
    $check_sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $msg = "Username or email already exists!";
    } else {
        // ============================================
        // STORE PASSWORD AS PLAIN TEXT
        // ============================================
        $plain_password = $password; // No hashing
        
        $sql = "INSERT INTO users (name, address, city, pincode, state, country, username, password, gender, mobileno, email, dob, created_at) 
                VALUES ('$name', '$address', '$city', '$pincode', '$state', '$country', '$username', '$plain_password', '$gender', '$mobileno', '$email', '$dob', NOW())";

        if ($conn->query($sql) === TRUE) {
            $msg = "Registration successful! Welcome to Golden Crust! 🎉";
        } else {
            $msg = "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Golden Crust · Register</title>
    
    <!-- Google Fonts + Font Awesome 6 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- ==========================================
         CSS INCLUDE - register.css
         ========================================== -->
    <link rel="stylesheet" href="css/register.css">
    
    <style>
        /* ===== PASSWORD TOGGLE STYLES ===== */
       
        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 55px;
        }

        /* Toggle Button */
        .toggle-password {
            position: absolute;
            right: 30px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.25);
            cursor: pointer;
            font-size: 18px;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
        }

        .toggle-password:hover {
            color: #e8436e;
            background: rgba(232, 67, 110, 0.08);
        }

        .toggle-password:active {
            transform: translateY(-50%) scale(0.9);
        }

        .input-group {
            position: relative;
        }

        .input-group .fa-eye,
        .input-group .fa-eye-slash {
            font-size: 16px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 480px) {
            .toggle-password {
                right: 10px;
                font-size: 16px;
                padding: 3px 6px;
            }
        }
    </style>
</head>
<body>

    <!-- Background decorative circles -->
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>

    <div class="register-card">

        <!-- Brand -->
        <div class="brand-icon">
            <i class="fas fa-crown"></i>
            <i class="fas fa-utensils"></i>
            <i class="fas fa-crown"></i>
        </div>
        <h2>Golden Crust</h2>
        <div class="subhead">
            <i class="fas fa-seedling"></i> artisan bakery · register <i class="fas fa-seedling"></i>
        </div>

        <!-- PHP Message -->
        <?php if (isset($msg) && $msg != "") { 
            $msg_class = (strpos($msg, 'successful') !== false) ? 'success' : 'error';
        ?>
            <div class="msg <?= $msg_class ?>">
                <i class="fas fa-circle-info"></i> <?= $msg ?>
            </div>
        <?php } ?>

        <!-- Form -->
        <form method="POST">
            <input type="hidden" name="register" value="1">

            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="name" placeholder="Full name" required>
            </div>

            <div class="input-group">
                <i class="fas fa-home"></i>
                <input type="text" name="address" placeholder="Street address" required>
            </div>

            <div class="row-duo">
                <div class="input-group">
                    <i class="fas fa-city"></i>
                    <input type="text" name="city" placeholder="City" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-map-pin"></i>
                    <input type="text" name="pincode" placeholder="Pincode" required>
                </div>
            </div>

            <div class="row-duo">
                <div class="input-group">
                    <i class="fas fa-flag"></i>
                    <input type="text" name="state" placeholder="State" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-globe"></i>
                    <input type="text" name="country" placeholder="Country" required>
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-user-tag"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <div class="password-wrapper">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <button type="button" class="toggle-password" onclick="togglePassword()">
                        <i class="fas fa-eye-slash" id="toggleIcon"></i>  <!-- Default: Slash Eye (Password Hidden) -->
                    </button>
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-venus-mars"></i>
                <select name="gender" required>
                    <option value="">Select gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="input-group">
                <i class="fas fa-phone-alt"></i>
                <input type="text" name="mobileno" placeholder="Mobile number" required>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email address" required>
            </div>

            <div class="input-group">
                <i class="fas fa-cake-candles"></i>
                <input type="date" name="dob" required>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-bread-slice"></i> Create account
            </button>
        </form>

        <div class="login-link">
            <i class="fas fa-arrow-right-to-bracket"></i> Already a member?
            <a href="login.php"><i class="fas fa-chevron-right" style="font-size:0.6rem;"></i> Sign in</a>
        </div>

        <div class="golden-dots">◆ ◆ ◆ ◆ ◆</div>
    </div>
<script>
    function togglePassword() {
        var password = document.getElementById('password');
        var icon = document.getElementById('toggleIcon');
        
        if (password.type === 'password') {
            password.type = 'text';
            icon.className = 'fas fa-eye';         // Password show → Open Eye
        } else {
            password.type = 'password';
            icon.className = 'fas fa-eye-slash';   // Password hide → Slash Eye
        }
    }
</script>

</body>
</html>