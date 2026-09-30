<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","golden_crust");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = '';

// Handle Add Admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Validation
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Please fill all fields!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } else {
        // Check if username already exists
        $check_query = "SELECT id FROM admin WHERE username = '$username'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "Username already exists! Please choose a different username.";
        } else {
            // ✅ STORE PASSWORD IN PLAIN TEXT (NO HASHING)
            $insert_query = "INSERT INTO admin (username, password) VALUES ('$username', '$password')";
            
            if (mysqli_query($conn, $insert_query)) {
                header("Location: admin_list.php?msg=added");
                exit();
            } else {
                $error = "Error adding admin: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Admin - Golden Crust</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0, #fce4ec);
            min-height: 100vh;
            padding: 30px 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Floating Decorations */
        .floating-decor {
            position: fixed;
            font-size: 50px;
            opacity: 0.08;
            z-index: 0;
            animation: float 10s ease-in-out infinite;
            pointer-events: none;
        }

        .floating-decor:nth-child(1) { top: 5%; left: 3%; animation-delay: 0s; }
        .floating-decor:nth-child(2) { top: 80%; right: 3%; animation-delay: 2s; }
        .floating-decor:nth-child(3) { bottom: 10%; left: 8%; animation-delay: 4s; }
        .floating-decor:nth-child(4) { top: 40%; right: 5%; animation-delay: 1s; font-size: 35px; }
        .floating-decor:nth-child(5) { bottom: 40%; left: 2%; animation-delay: 3s; font-size: 40px; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-25px) rotate(8deg); }
        }

        .main {
            margin-left: 85px;
            padding: 30px;
            transition: margin-left 0.35s ease;
            min-height: 100vh;
            max-width: 900px;
            position: relative;
            z-index: 1;
        }

        .main.shift {
            margin-left: 260px;
        }

        /* Form Card */
        .form-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            border-radius: 30px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(232, 67, 110, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.7);
            animation: slideUp 0.6s ease;
            max-width: 700px;
            margin: 0 auto;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form Header */
        .form-header {
            text-align: center;
            margin-bottom: 35px;
            padding-bottom: 25px;
            border-bottom: 2px solid rgba(232, 67, 110, 0.08);
        }

        .form-header .avatar {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 8px 30px rgba(232, 67, 110, 0.25);
            transition: transform 0.3s ease;
        }

        .form-header .avatar:hover {
            transform: scale(1.05) rotate(-5deg);
        }

        .form-header .avatar i {
            font-size: 40px;
            color: #fff;
        }

        .form-header h2 {
            font-size: 26px;
            font-weight: 700;
            color: #1a0f17;
        }

        .form-header h2 i {
            color: #e8436e;
            margin-right: 10px;
        }

        .form-header p {
            color: #888;
            font-size: 14px;
            font-weight: 500;
            margin-top: 5px;
        }

        .form-header p i {
            color: #ffd700;
        }

        /* Alert Messages */
        .alert {
            padding: 15px 20px;
            border-radius: 14px;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-8px); }
            75% { transform: translateX(8px); }
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.08);
            color: #e74c3c;
            border: 1px solid rgba(231, 76, 60, 0.15);
        }

        .alert i {
            font-size: 20px;
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #555;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-group label i {
            color: #e8436e;
            margin-right: 8px;
        }

        .form-group label .required {
            color: #e74c3c;
            margin-left: 3px;
        }

        .form-group .input-wrapper {
            position: relative;
        }

        .form-group .input-wrapper .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #e8436e;
            font-size: 18px;
            transition: 0.3s;
            z-index: 1;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px 14px 50px;
            border: 2px solid rgba(232, 67, 110, 0.12);
            border-radius: 16px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.6);
            color: #333;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus {
            border-color: #e8436e;
            outline: none;
            box-shadow: 0 0 0 5px rgba(232, 67, 110, 0.08);
            background: white;
            transform: translateY(-1px);
        }

        .form-group input::placeholder {
            color: #bbb;
        }

        .form-group input[type="text"] {
            letter-spacing: 0.5px;
        }

        /* ===== PASSWORD TOGGLE BUTTON ===== */
        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #aaa;
            cursor: pointer;
            font-size: 18px;
            padding: 8px 10px;
            border-radius: 8px;
            transition: all 0.3s ease;
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: #e8436e;
            background: rgba(232, 67, 110, 0.06);
        }

        .toggle-password:active {
            transform: translateY(-50%) scale(0.9);
        }

        /* Password Requirements */
        .password-requirements {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
        }

        .password-requirements .req {
            font-size: 12px;
            color: #888;
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(232, 67, 110, 0.04);
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid rgba(232, 67, 110, 0.06);
        }

        .password-requirements .req i {
            font-size: 12px;
            color: #e8436e;
        }

        /* Warning Text */
        .warning-text {
            color: #e74c3c;
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 6px;
            background: rgba(231, 76, 60, 0.04);
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid rgba(231, 76, 60, 0.08);
        }

        .warning-text i {
            font-size: 14px;
        }

        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, rgba(232, 67, 110, 0.04), rgba(255, 107, 138, 0.04));
            border: 1px solid rgba(232, 67, 110, 0.10);
            border-radius: 14px;
            padding: 16px 20px;
            margin: 5px 0 10px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: 0.3s;
        }

        .info-box:hover {
            background: linear-gradient(135deg, rgba(232, 67, 110, 0.06), rgba(255, 107, 138, 0.06));
        }

        .info-box i {
            color: #e8436e;
            font-size: 20px;
        }

        .info-box span {
            color: #666;
            font-size: 13px;
            font-weight: 500;
        }

        .info-box span strong {
            color: #e74c3c;
        }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 14px;
            margin-top: 20px;
        }

        .btn-submit {
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
            color: #fff;
            border: none;
            padding: 16px 35px;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            box-shadow: 0 6px 25px rgba(232, 67, 110, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 35px rgba(232, 67, 110, 0.35);
        }

        .btn-submit:active {
            transform: scale(0.97);
        }

        .btn-submit i {
            font-size: 18px;
        }

        .btn-cancel {
            background: rgba(200, 200, 200, 0.15);
            color: #888;
            border: 2px solid rgba(200, 200, 200, 0.20);
            padding: 16px 35px;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-cancel:hover {
            background: rgba(200, 200, 200, 0.25);
            border-color: rgba(200, 200, 200, 0.40);
            color: #555;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        .btn-cancel i {
            font-size: 16px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .main {
                margin-left: 75px;
                padding: 20px;
            }
            
            .main.shift {
                margin-left: 240px;
            }
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 70px 15px 15px 15px;
            }
            
            .main.shift {
                margin-left: 0;
            }

            .form-card {
                padding: 25px 20px;
                border-radius: 24px;
            }

            .form-header .avatar {
                width: 70px;
                height: 70px;
            }

            .form-header .avatar i {
                font-size: 32px;
            }

            .form-header h2 {
                font-size: 22px;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn-submit, .btn-cancel {
                padding: 14px 20px;
                font-size: 15px;
            }

            .floating-decor {
                display: none;
            }
        }

        @media (max-width: 480px) {
            .form-card {
                padding: 20px 15px;
            }

            .form-header .avatar {
                width: 60px;
                height: 60px;
            }

            .form-header .avatar i {
                font-size: 28px;
            }

            .form-header h2 {
                font-size: 20px;
            }

            .form-group input {
                padding: 12px 14px 12px 44px;
                font-size: 14px;
            }

            .password-requirements {
                flex-direction: column;
                gap: 4px;
            }

            .toggle-password {
                font-size: 16px;
                padding: 6px 8px;
                right: 10px;
            }
        }
    </style>
</head>
<body>

<!-- Floating Decorations -->
<div class="floating-decor">🍞</div>
<div class="floating-decor">🥐</div>
<div class="floating-decor">🧁</div>
<div class="floating-decor">🎂</div>
<div class="floating-decor">🍰</div>

<?php include "includes/sidebar.php"; ?>

<div class="main" id="main">

    <div class="form-card">
        <div class="form-header">
            <div class="avatar">
                <i class="fas fa-user-plus"></i>
            </div>
            <h2><i class="fas fa-user-cog"></i> Add Admin Account</h2>
            <p><i class="fas fa-crown"></i> Golden Crust Admin Panel</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label><i class="fas fa-user-tag"></i> Username <span class="required">*</span></label>
                <div class="input-wrapper">
                    <i class="fas fa-user-tag input-icon"></i>
                    <input type="text" name="username" id="username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>" required placeholder="Enter username">
                </div>
            </div>

            <div class="form-group">
                <label><i class="fas fa-key"></i> Password <span class="required">*</span></label>
                <div class="input-wrapper">
                    <i class="fas fa-key input-icon"></i>
                    <input type="password" name="password" id="password" required placeholder="Enter password" value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>">
                    <button type="button" class="toggle-password" onclick="togglePassword('password', 'toggleIcon1')">
                        <i class="fas fa-eye" id="toggleIcon1"></i>
                    </button>
                </div>
                <div class="password-requirements">
                    <span class="req"><i class="fas fa-circle"></i> Minimum 6 characters</span>
                    <span class="req"><i class="fas fa-circle"></i> Use strong password</span>
                   
                </div>
               
            </div>

            <div class="form-group">
                <label><i class="fas fa-check-circle"></i> Confirm Password <span class="required">*</span></label>
                <div class="input-wrapper">
                    <i class="fas fa-check-circle input-icon"></i>
                    <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm password" value="<?= isset($_POST['confirm_password']) ? htmlspecialchars($_POST['confirm_password']) : '' ?>">
                    <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', 'toggleIcon2')">
                        <i class="fas fa-eye" id="toggleIcon2"></i>
                    </button>
                </div>
            </div>

            

            <div class="btn-group">
                <button type="submit" name="add_admin" class="btn-submit">
                    <i class="fas fa-user-plus"></i> Add Admin
                </button>
                <a href="admin_list.php" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>

</div>

<script>
// ===== PASSWORD TOGGLE FUNCTION =====
function togglePassword(inputId, iconId) {
    var input = document.getElementById(inputId);
    var icon = document.getElementById(iconId);
    
    if (input.type === "password") {
        input.type = "text";
        icon.className = "fas fa-eye-slash";
    } else {
        input.type = "password";
        icon.className = "fas fa-eye";
    }
}

// ===== AUTO-HIDE ERROR MESSAGE =====
setTimeout(function() {
    const msg = document.querySelector('.alert');
    if (msg) {
        msg.style.transition = 'all 0.5s ease';
        msg.style.opacity = '0';
        msg.style.transform = 'translateY(-20px)';
        setTimeout(function() {
            if (msg.parentNode) {
                msg.remove();
            }
        }, 500);
    }
}, 5000);
</script>

</body>
</html>