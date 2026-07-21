<?php
session_start();

// ----- ERROR REPORTING -----
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ----- DATABASE CONNECTION -----
$conn = mysqli_connect("localhost", "root", "", "golden_crust");

if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}

$msg = "";
$msg_type = "";

// ----- LOGIN PROCESSING -----
if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Check if username exists in users table
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);

    if(!$result){
        die("Query failed: " . mysqli_error($conn));
    }

    if(mysqli_num_rows($result) == 1){
        $row = mysqli_fetch_assoc($result);
        
        // ----- SUPPORT BOTH HASHED AND PLAIN TEXT PASSWORDS -----
        $password_correct = false;
        
        // Check if password is hashed (starts with $2y$)
        if(strpos($row['password'], '$2y$') === 0){
            // Hashed password - use password_verify
            if(password_verify($password, $row['password'])){
                $password_correct = true;
            }
        } else {
            // Plain text password - direct comparison
            if($password === $row['password']){
                $password_correct = true;
            }
        }

        if($password_correct){
            // ----- LOGIN SUCCESS -----
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_name'] = $row['name'] ?? $row['username'];
            $_SESSION['user_email'] = $row['email'] ?? '';
            $_SESSION['user_logged_in'] = true;

            // Redirect to home page
            header("Location: index.php");
            exit();
        } else {
            $msg = "❌ Wrong password! Please try again.";
            $msg_type = "error";
        }
    } else {
        $msg = "❌ Username not found! Please check your username.";
        $msg_type = "error";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Golden Crust · User Login</title>
    
    <!-- Google Fonts + Font Awesome 6 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">
    
    <!-- ==========================================
         CSS INCLUDE - login.css
         ========================================== -->
    <link rel="stylesheet" href="css/login.css">
    
</head>
<body>

    <!-- Background decorative circles -->
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>

    <div class="login-card">

        <!-- Brand -->
        <div class="brand-icon">
            <i class="fas fa-crown"></i>
            <i class="fas fa-utensils"></i>
            <i class="fas fa-crown"></i>
        </div>
        <h2>Golden Crust Cakes</h2>
        <div class="subhead">
            <i class="fas fa-seedling"></i> user login · sign in <i class="fas fa-seedling"></i>
        </div>

        <!-- PHP Message -->
        <?php if (isset($msg) && $msg != "") { ?>
            <div class="msg msg-<?php echo $msg_type; ?>">
                <i class="fas fa-circle-info"></i> <?= $msg ?>
            </div>
        <?php } ?>

        <!-- Login Form -->
        <form method="POST" action="" id="loginForm">
            <input type="hidden" name="login" value="1">

            <div class="input-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" placeholder="Username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>

            <div class="input-group password-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <button type="button" class="toggle-password" id="togglePassword" aria-label="Toggle password visibility">
                    <i class="fas fa-eye-slash" id="toggleIcon"></i>
                </button>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-arrow-right-to-bracket"></i> Log In
            </button>
        </form>

        <div class="register-link">
            <i class="fas fa-user-plus"></i> Don't have an account?
            <a href="register.php"><i class="fas fa-chevron-right" style="font-size:0.6rem;"></i> Register</a>
        </div>

        <div class="golden-dots">◆ ◆ ◆ ◆ ◆</div>
    </div>

    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const toggleBtn = document.getElementById('togglePassword');
            const toggleIcon = document.getElementById('toggleIcon');

            if (passwordInput && toggleBtn && toggleIcon) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const currentType = passwordInput.getAttribute('type');
                    
                    if (currentType === 'password') {
                        passwordInput.setAttribute('type', 'text');
                        toggleIcon.classList.remove('fa-eye-slash');
                        toggleIcon.classList.add('fa-eye');
                    } else {
                        passwordInput.setAttribute('type', 'password');
                        toggleIcon.classList.remove('fa-eye');
                        toggleIcon.classList.add('fa-eye-slash');
                    }
                });

                toggleBtn.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                });
            }
        });
    </script>

</body>
</html>