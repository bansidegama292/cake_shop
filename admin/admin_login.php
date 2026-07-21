<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "golden_crust");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (!empty($username) && !empty($password)) {
        $query = "SELECT * FROM admin WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $row['password']) || $password == $row['password']) {
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_username'] = $row['username'];
                $_SESSION['admin_fullname'] = $row['fullname'];
                $_SESSION['admin_email'] = $row['email'];
                $_SESSION['admin_phone'] = $row['phone'];
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Username not found!";
        }
    } else {
        $error = "Please enter username and password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Golden Crust</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Great+Vibes&family=Playfair+Display:ital,wght@0,400;0,700;1,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
            background: #fce4ec;
        }

        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            background: 
                radial-gradient(ellipse at 10% 20%, rgba(255, 182, 193, 0.5) 0%, transparent 50%),
                radial-gradient(ellipse at 90% 80%, rgba(255, 105, 135, 0.3) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 50%, rgba(255, 228, 235, 0.4) 0%, transparent 70%),
                linear-gradient(135deg, #fce4ec 0%, #f8bbd0 25%, #fce4ec 50%, #f8bbd0 75%, #fce4ec 100%);
        }

        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 182, 193, 0.3), transparent);
            animation: floatParticle 20s infinite ease-in-out;
        }

        .particle:nth-child(1) { width: 300px; height: 300px; top: -5%; left: -5%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 400px; height: 400px; bottom: -10%; right: -5%; animation-delay: -5s; }
        .particle:nth-child(3) { width: 200px; height: 200px; top: 50%; left: 50%; transform: translate(-50%, -50%); animation-delay: -10s; }
        .particle:nth-child(4) { width: 250px; height: 250px; top: 20%; right: 10%; animation-delay: -15s; }
        .particle:nth-child(5) { width: 180px; height: 180px; bottom: 20%; left: 10%; animation-delay: -7s; }

        @keyframes floatParticle {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.4; }
            25% { transform: translate(50px, -80px) scale(1.1); opacity: 0.7; }
            50% { transform: translate(-30px, 60px) scale(0.9); opacity: 0.3; }
            75% { transform: translate(80px, 40px) scale(1.2); opacity: 0.6; }
        }

        .floating-icon {
            position: fixed;
            font-size: 50px;
            opacity: 0.15;
            z-index: 1;
            animation: floatIcon 15s ease-in-out infinite;
            pointer-events: none;
        }

        .floating-icon:nth-child(6) { top: 8%; left: 5%; animation-delay: 0s; }
        .floating-icon:nth-child(7) { top: 75%; right: 5%; animation-delay: 3s; }
        .floating-icon:nth-child(8) { bottom: 10%; left: 8%; animation-delay: 6s; font-size: 40px; }
        .floating-icon:nth-child(9) { top: 45%; right: 3%; animation-delay: 1.5s; font-size: 35px; }
        .floating-icon:nth-child(10) { bottom: 35%; left: 2%; animation-delay: 4.5s; font-size: 45px; }

        @keyframes floatIcon {
            0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); }
            50% { transform: translateY(-30px) rotate(15deg) scale(1.1); }
        }

        /* ===== CARD WITH SHADOW EFFECT ===== */
        .login-container {
            background: #ffffff;
            border-radius: 40px;
            padding: 55px 50px 50px;
            max-width: 450px;
            width: 100%;
            position: relative;
            z-index: 2;
            animation: slideUp 1s cubic-bezier(0.22, 1, 0.36, 1);
            
            /* ===== MULTI-LAYER SHADOW EFFECT ===== */
            box-shadow: 
                0 2px 4px rgba(232, 67, 110, 0.04),
                0 8px 16px rgba(232, 67, 110, 0.06),
                0 16px 32px rgba(232, 67, 110, 0.08),
                0 32px 64px rgba(232, 67, 110, 0.10),
                0 64px 128px rgba(232, 67, 110, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            
            border: 1px solid rgba(255, 255, 255, 0.9);
            transition: box-shadow 0.4s ease, transform 0.4s ease;
        }

        .login-container:hover {
            box-shadow: 
                0 4px 8px rgba(232, 67, 110, 0.06),
                0 12px 24px rgba(232, 67, 110, 0.10),
                0 24px 48px rgba(232, 67, 110, 0.14),
                0 48px 96px rgba(232, 67, 110, 0.12),
                0 80px 160px rgba(232, 67, 110, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            transform: translateY(-4px);
        }

        /* ===== SOFT GLOW BORDER ===== */
        .login-container::before {
            content: '';
            position: absolute;
            top: -3px;
            left: -3px;
            right: -3px;
            bottom: -3px;
            border-radius: 43px;
            background: linear-gradient(135deg, 
                rgba(232, 67, 110, 0.15), 
                rgba(255, 182, 193, 0.30), 
                rgba(232, 67, 110, 0.15));
            z-index: -1;
            filter: blur(8px);
            opacity: 0.6;
            animation: borderGlow 4s ease-in-out infinite;
        }

        @keyframes borderGlow {
            0%, 100% { opacity: 0.4; }
            50% { opacity: 0.8; }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(60px) scale(0.95); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ===== BRAND SECTION ===== */
        .brand-section {
            text-align: center;
            margin-bottom: 25px;
        }

        /* ===== GOLDEN CRUST - STYLISH SCRIPT FONT ===== */
        .brand-section h1 {
            font-family: 'Great Vibes', 'Playfair Display', cursive;
            font-size: 40px;
            font-weight: 600;
            letter-spacing: 1px;
            
            /* ===== GRADIENT TEXT ===== */
            background: linear-gradient(135deg, #e8436e, #ff6b8a, #ff8a9f, #ff6b8a, #e8436e);
            background-size: 300% 300%;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradientMove 4s ease-in-out infinite;
            line-height: 1.2;
            
            /* ===== TEXT SHADOW EFFECT ===== */
            text-shadow: 
                0 2px 4px rgba(232, 67, 110, 0.10),
                0 4px 8px rgba(232, 67, 110, 0.08),
                0 8px 16px rgba(232, 67, 110, 0.06),
                0 16px 32px rgba(232, 67, 110, 0.04);
            
            /* ===== DROP SHADOW ===== */
            filter: drop-shadow(0 4px 20px rgba(232, 67, 110, 0.15))
                   drop-shadow(0 8px 40px rgba(232, 67, 110, 0.08));
            
            position: relative;
            display: inline-block;
        }

        /* ===== DECORATIVE UNDERLINE ===== */
        .brand-section h1::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 60%;
            height: 3px;
            background: linear-gradient(90deg, transparent, #e8436e, #ff8a9f, #e8436e, transparent);
            border-radius: 3px;
            animation: underlinePulse 3s ease-in-out infinite;
        }

        @keyframes underlinePulse {
            0%, 100% { width: 60%; opacity: 0.6; }
            50% { width: 80%; opacity: 1; }
        }

        /* ===== SUBTLE DECORATIVE DOT ===== */
        .brand-section h1::before {
            content: '✦';
            position: absolute;
            top: -10px;
            right: -30px;
            font-size: 16px;
            color: #e8436e;
            opacity: 0.3;
            font-family: 'Poppins', sans-serif;
            animation: sparkle 2s ease-in-out infinite;
        }

        @keyframes sparkle {
            0%, 100% { opacity: 0.3; transform: scale(1) rotate(0deg); }
            50% { opacity: 0.8; transform: scale(1.3) rotate(180deg); }
        }

        @keyframes gradientMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .brand-section .subtitle {
            color: #e8436e;
            font-size: 13px;
            font-weight: 600;
            margin-top: 8px;
            letter-spacing: 4px;
            text-transform: uppercase;
            opacity: 0.7;
            font-family: 'Poppins', sans-serif;
        }

        .brand-section .subtitle i {
            color: #ffd700;
            margin: 0 6px;
        }

        /* ===== DECORATIVE DIVIDER ===== */
        .brand-divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin: 8px 0 5px 0;
        }

        .brand-divider .line {
            width: 30px;
            height: 1px;
            background: linear-gradient(90deg, transparent, #e8436e);
            opacity: 0.3;
        }

        .brand-divider .line:last-child {
            background: linear-gradient(90deg, #e8436e, transparent);
        }

        .brand-divider .diamond {
            color: #e8436e;
            font-size: 8px;
            opacity: 0.4;
        }

        /* ===== FORM ===== */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #d45a7a;
            font-weight: 600;
            font-size: 12px;
            margin-bottom: 8px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .form-group label i {
            color: #e8436e;
            margin-right: 8px;
        }

        .form-group .input-wrapper {
            position: relative;
        }

        .form-group .input-wrapper .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #d45a7a;
            font-size: 18px;
            transition: all 0.4s ease;
            z-index: 1;
            opacity: 0.5;
        }

        .form-group input {
            width: 100%;
            padding: 16px 18px 16px 52px;
            border: 2px solid rgba(232, 67, 110, 0.12);
            border-radius: 18px;
            font-size: 15px;
            transition: all 0.4s ease;
            background: rgba(252, 228, 236, 0.3);
            color: #4a2a3a;
            
            /* ===== INPUT SHADOW ===== */
            box-shadow: 
                0 2px 4px rgba(232, 67, 110, 0.04),
                inset 0 1px 0 rgba(255, 255, 255, 0.6);
        }

        .form-group input::placeholder {
            color: #d4a0b0;
            font-weight: 300;
        }

        .form-group input:focus {
            border-color: #e8436e;
            outline: none;
            background: #ffffff;
            transform: translateY(-2px);
            
            /* ===== FOCUS SHADOW ===== */
            box-shadow: 
                0 4px 12px rgba(232, 67, 110, 0.10),
                0 8px 24px rgba(232, 67, 110, 0.06),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }

        .form-group input:focus ~ .input-icon,
        .form-group input:focus + .input-icon {
            color: #e8436e;
            transform: translateY(-50%) scale(1.1);
            opacity: 1;
        }

        /* ===== PASSWORD TOGGLE ICON ===== */
        .password-icon {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #d45a7a;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.4s ease;
            background: none;
            border: none;
            padding: 5px 8px;
            border-radius: 8px;
            opacity: 0.5;
        }

        .password-icon:hover {
            color: #e8436e;
            background: rgba(232, 67, 110, 0.06);
            opacity: 1;
        }

        /* ===== BUTTON ===== */
        .btn-login {
            background: linear-gradient(135deg, #e8436e, #ff6b8a, #ff8a9f, #e8436e);
            background-size: 200% 200%;
            color: #fff;
            border: none;
            padding: 18px;
            border-radius: 18px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s ease;
            width: 100%;
            
            /* ===== BUTTON SHADOW ===== */
            box-shadow: 
                0 4px 16px rgba(232, 67, 110, 0.25),
                0 8px 32px rgba(232, 67, 110, 0.15),
                0 16px 48px rgba(232, 67, 110, 0.10);
            
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            animation: buttonGradient 3s ease-in-out infinite;
        }

        @keyframes buttonGradient {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(135deg, transparent 30%, rgba(255, 255, 255, 0.2) 50%, transparent 70%);
            transform: rotate(45deg) translateX(-100%);
            transition: 0.8s;
        }

        .btn-login:hover::before {
            transform: rotate(45deg) translateX(100%);
        }

        .btn-login:hover {
            transform: translateY(-4px);
            
            /* ===== HOVER SHADOW ===== */
            box-shadow: 
                0 8px 32px rgba(232, 67, 110, 0.35),
                0 16px 56px rgba(232, 67, 110, 0.25),
                0 24px 80px rgba(232, 67, 110, 0.15);
        }

        .btn-login:active {
            transform: scale(0.97);
            box-shadow: 
                0 2px 8px rgba(232, 67, 110, 0.20);
        }

        .btn-login i {
            font-size: 18px;
        }

        /* ===== ALERT ===== */
        .alert {
            padding: 15px 20px;
            border-radius: 16px;
            margin-bottom: 20px;
            font-weight: 500;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: shake 0.5s ease;
            background: rgba(231, 76, 60, 0.06);
            border: 1px solid rgba(231, 76, 60, 0.10);
            color: #e8436e;
            
            /* ===== ALERT SHADOW ===== */
            box-shadow: 
                0 2px 8px rgba(231, 76, 60, 0.06);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        .alert i {
            font-size: 18px;
        }

        /* ===== FOOTER ===== */
        .login-footer {
            text-align: center;
            margin-top: 30px;
            color: #d4a0b0;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .login-footer i {
            color: #e8436e;
            animation: heartBeat 1.5s ease-in-out infinite;
            display: inline-block;
        }

        @keyframes heartBeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.3); }
        }

        .design-credit {
            text-align: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(232, 67, 110, 0.08);
            font-size: 11px;
            letter-spacing: 2px;
            color: #d4a0b0;
            font-weight: 300;
            text-transform: uppercase;
            transition: color 0.3s ease;
        }

        .design-credit:hover {
            color: #e8436e;
        }

        .design-credit .heart {
            color: #e8436e;
            display: inline-block;
            animation: heartBeat 1.5s ease-in-out infinite;
            margin: 0 4px;
            opacity: 0.5;
        }

        .design-credit .highlight {
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 40px 25px 35px;
                border-radius: 30px;
            }
            .brand-section h1 {
                font-size: 38px;
            }
            .brand-section h1::before {
                display: none;
            }
            .form-group input {
                padding: 14px 14px 14px 44px;
                font-size: 14px;
                border-radius: 14px;
            }
            .btn-login {
                padding: 16px;
                font-size: 16px;
                border-radius: 14px;
            }
            .floating-icon {
                display: none;
            }
            .design-credit {
                font-size: 9px;
                letter-spacing: 1.5px;
            }
        }

        @media (max-width: 380px) {
            .login-container {
                padding: 30px 18px 25px;
            }
            .brand-section h1 {
                font-size: 30px;
            }
        }
    </style>
</head>
<body>

<!-- ===== ANIMATED BACKGROUND ===== -->
<div class="bg-animation"></div>

<!-- ===== PARTICLES ===== -->
<div class="particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>

<!-- ===== FLOATING ICONS ===== -->
<div class="floating-icon">🍞</div>
<div class="floating-icon">🥐</div>
<div class="floating-icon">🧁</div>
<div class="floating-icon">🎂</div>
<div class="floating-icon">🍰</div>

<!-- ===== LOGIN CONTAINER ===== -->
<div class="login-container">
    <div class="brand-section">
        <h1>Golden Crust Cakes</h1>
        <div class="brand-divider">
            <span class="line"></span>
            <span class="diamond">◆</span>
            <span class="line"></span>
        </div>
        <p class="subtitle"><i class="fas fa-crown"></i> Admin Panel <i class="fas fa-crown"></i></p>
    </div>

    <?php if($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label><i class="fas fa-user"></i> Username</label>
            <div class="input-wrapper">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" placeholder="Enter username" required>
            </div>
        </div>

        <div class="form-group">
            <label><i class="fas fa-lock"></i> Password</label>
            <div class="input-wrapper">
                <i class="fas fa-key input-icon"></i>
                <input type="password" name="password" id="password" placeholder="Enter password" required>
                <button type="button" class="password-icon" onclick="togglePassword()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" name="login" class="btn-login">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
    </form>

    <div class="login-footer">
       <marquee behavior="smooth" direction="left"> &copy; <?= date('Y') ?> <i class="fas fa-heart"></i> Golden Crust Bakery-All Rights Reserved.</marquee>
    </div>

    
</div>

<script>
function togglePassword() {
    var password = document.getElementById("password");
    var icon = document.getElementById("toggleIcon");
    if (password.type === "password") {
        password.type = "text";
        icon.className = "fas fa-eye-slash";
    } else {
        password.type = "password";
        icon.className = "fas fa-eye";
    }
}

setTimeout(function() {
    const alert = document.querySelector('.alert');
    if (alert) {
        alert.style.transition = 'all 0.6s ease';
        alert.style.opacity = '0';
        alert.style.transform = 'translateY(-20px)';
        setTimeout(function() {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 600);
    }
}, 5000);
</script>

</body>
</html>