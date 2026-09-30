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

$error = "";

// ----- PROCESS LOGIN -----
if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Plain text password
    
    // Check if user exists with plain text password comparison
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        
        // Set ALL session variables
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'] ?? $user['username'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['username'] = $user['username'];
        
        // Close connection
        mysqli_close($conn);
        
        // Redirect to profile page
        header("Location: profile.php");
        exit();
    } else {
        $error = "❌ Invalid username or password!";
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
  <title>Golden Crust · Login</title>
  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet" />
  <!-- separate CSS file: login.css -->
  <link rel="stylesheet" href="css/login.css" />
</head>
<body>
  <!-- decorative circles -->
  <div class="bg-circle"></div>
  <div class="bg-circle"></div>
  <div class="bg-circle"></div>

  <div class="login-card">
    <!-- brand -->
    <div class="brand-icon">
      <i class="fas fa-crown"></i>
      <i class="fas fa-utensils"></i>
      <i class="fas fa-crown"></i>
    </div>
    <h2>Golden Crust Cakes</h2>
    <div class="subhead">
      <i class="fas fa-seedling"></i> user login · sign in <i class="fas fa-seedling"></i>
    </div>

    <!-- message block -->
    <?php if($error != "") { ?>
      <div class="msg msg-error">
        <i class="fas fa-circle-info"></i> <?php echo $error; ?>
      </div>
    <?php } else { ?>
      <div class="msg msg-info">
        <i class="fas fa-circle-info"></i> Welcome back! Please login.
      </div>
    <?php } ?>

    <!-- login form -->
    <form method="POST" action="login.php" id="loginForm">
      <div class="input-group">
        <i class="fas fa-user input-icon"></i>
        <input type="text" name="username" placeholder="Username" value="demo" required />
      </div>

      <div class="input-group password-group">
        <i class="fas fa-lock input-icon"></i>
        <input type="password" name="password" id="password" placeholder="Password" value="demo123" required />
        <button type="button" class="toggle-password" id="togglePassword" aria-label="Toggle password visibility">
          <i class="fas fa-eye-slash" id="toggleIcon"></i>
        </button>
      </div>

      <button type="submit" name="login" class="btn-submit">
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