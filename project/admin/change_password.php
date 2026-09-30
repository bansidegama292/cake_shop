<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","golden_crust");

$admin_id = $_SESSION['admin_id'];
$error = '';
$success = '';

if(isset($_POST['change_password'])){
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    
    $query = "SELECT password FROM admin WHERE id = '$admin_id'";
    $result = mysqli_query($conn, $query);
    $admin = mysqli_fetch_assoc($result);
    
    if(empty($current_password) || empty($new_password) || empty($confirm_password)){
        $error = "All fields are required!";
    } elseif($current_password !== $admin['password']){
        $error = "Current password is incorrect!";
    } elseif(strlen($new_password) < 6){
        $error = "New password must be at least 6 characters long!";
    } elseif($new_password !== $confirm_password){
        $error = "New password and confirm password do not match!";
    } else {
        $update_query = "UPDATE admin SET password = '$new_password' WHERE id = '$admin_id'";
        if(mysqli_query($conn, $update_query)){
            $success = "Password changed successfully! 🎉";
        } else {
            $error = "Error changing password: " . mysqli_error($conn);
        }
    }
}

// Get admin details for header
$query = "SELECT * FROM admin WHERE id = '$admin_id'";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Golden Crust Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #fff0f5, #ffd6e7, #ffb6c1, #ffc0cb, #ffe4ec);
            min-height: 100vh;
            overflow-x: hidden;
        }

        ::-webkit-scrollbar {
            width: 10px;
        }
        ::-webkit-scrollbar-track {
            background: #fce4ec;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #ff4d88, #ff82aa);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #e63e73, #ff4d88);
        }

        .main {
            margin-left: 85px;
            width: calc(100% - 85px);
            padding: 30px 35px;
            transition: 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            min-height: 100vh;
        }

        .main.shift {
            margin-left: 260px;
            width: calc(100% - 260px);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 15px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            padding: 20px 30px;
            border-radius: 30px;
            box-shadow: 0 10px 40px rgba(255, 77, 136, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .header-left {
            display: flex;
            flex-direction: column;
        }

        .title {
            font-size: 38px;
            font-weight: 900;
            background: linear-gradient(135deg, #ff4d88, #ff6b9d, #e63e73);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: #888;
            font-size: 15px;
            margin-top: 2px;
            font-weight: 500;
            letter-spacing: 0.3px;
        }

        .subtitle i {
            color: #ff4d88;
            margin-right: 8px;
        }

        /* ===== SIMPLE PROFILE DISPLAY (NO TOGGLE) ===== */
        .profile-display {
            display: flex;
            align-items: center;
            gap: 14px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 77, 136, 0.15);
            padding: 8px 20px 8px 8px;
            border-radius: 50px;
            box-shadow: 0 8px 25px rgba(255, 77, 136, 0.1);
        }

        .profile-display .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ff4d88, #ff82aa);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 4px 15px rgba(255, 77, 136, 0.3);
            flex-shrink: 0;
        }

        .profile-display .profile-text {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            line-height: 1.2;
        }

        .profile-display .profile-text .name {
            font-size: 14px;
            font-weight: 700;
            color: #333;
        }

        .profile-display .profile-text .role {
            font-size: 10px;
            color: #ff4d88;
            font-weight: 500;
        }

        .password-wrapper {
            display: flex;
            gap: 30px;
            align-items: flex-start;
            max-width: 650px;
            margin: 0 auto;
        }

        .password-card {
            flex: 1;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(8px);
            border-radius: 28px;
            padding: 40px;
            box-shadow: 0 8px 30px rgba(255, 77, 136, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .password-card .card-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .password-card .card-header .lock-icon {
            font-size: 60px;
            background: linear-gradient(135deg, #ff4d88, #ff82aa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            display: block;
        }

        .password-card .card-header h2 {
            color: #ff4d88;
            font-size: 24px;
            font-weight: 700;
        }

        .password-card .card-header p {
            color: #888;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            color: #555;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .form-group label i {
            color: #ff4d88;
            margin-right: 8px;
        }

        .form-group .input-wrapper {
            position: relative;
        }

        .form-group .input-wrapper .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #ff4d88;
            font-size: 18px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 18px 14px 50px;
            border: 2px solid rgba(255, 77, 136, 0.12);
            border-radius: 14px;
            font-size: 15px;
            transition: 0.3s;
            background: rgba(255, 255, 255, 0.6);
            color: #333;
        }

        .form-group input:focus {
            border-color: #ff4d88;
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 77, 136, 0.08);
            background: white;
        }

        .form-group input::placeholder {
            color: #bbb;
        }

        .form-group .toggle-password {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #aaa;
            cursor: pointer;
            font-size: 18px;
            transition: 0.3s;
        }

        .form-group .toggle-password:hover {
            color: #ff4d88;
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            border-radius: 4px;
            background: #eee;
            overflow: hidden;
            transition: 0.3s;
        }

        .password-strength .strength-bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            transition: 0.3s;
        }

        .strength-text {
            font-size: 12px;
            margin-top: 4px;
            color: #999;
        }

        .btn-change {
            background: linear-gradient(135deg, #ff4d88, #ff82aa);
            color: white;
            border: none;
            padding: 16px 35px;
            border-radius: 16px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
            box-shadow: 0 8px 25px rgba(255, 77, 136, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 10px;
        }

        .btn-change:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(255, 77, 136, 0.35);
        }

        .btn-change:active {
            transform: scale(0.98);
        }

        .btn-change i {
            font-size: 18px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 14px;
            margin-bottom: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: rgba(46, 204, 113, 0.12);
            border: 1px solid rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }

        .alert-error {
            background: rgba(231, 76, 60, 0.12);
            border: 1px solid rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }

        .alert i {
            font-size: 20px;
        }

        /* ===== BACK TO DASHBOARD (LEFT SIDE) ===== */
        .back-to-dashboard {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 15px 12px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(8px);
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 30px rgba(255, 77, 136, 0.06);
            transition: 0.3s;
            text-decoration: none;
            min-width: 60px;
        }

        .back-to-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(255, 77, 136, 0.15);
            border-color: rgba(255, 77, 136, 0.2);
        }

        .back-to-dashboard i {
            font-size: 28px;
            color: #ff4d88;
            transition: 0.3s;
        }

        .back-to-dashboard:hover i {
            transform: translateX(-5px);
        }

        .back-to-dashboard span {
            font-size: 11px;
            color: #888;
            font-weight: 500;
            text-align: center;
            writing-mode: vertical-lr;
            letter-spacing: 2px;
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                width: 100%;
                padding: 15px;
            }
            .main.shift {
                margin-left: 0;
                width: 100%;
            }
            .header {
                padding: 15px 20px;
                flex-direction: column;
                align-items: flex-start;
            }
            .title {
                font-size: 28px;
            }
            .password-card {
                padding: 25px 20px;
            }
            .password-wrapper {
                flex-direction: column;
                align-items: stretch;
            }
            .back-to-dashboard {
                flex-direction: row;
                padding: 12px 20px;
                min-width: auto;
                justify-content: center;
                order: -1;
            }
            .back-to-dashboard span {
                writing-mode: horizontal-tb;
                letter-spacing: 1px;
            }
            .back-to-dashboard i {
                font-size: 20px;
            }
            .profile-display {
                padding: 5px 14px 5px 5px;
            }
            .profile-display .avatar {
                width: 34px;
                height: 34px;
                font-size: 14px;
            }
            .profile-display .profile-text .name {
                font-size: 12px;
            }
            .profile-display .profile-text .role {
                font-size: 9px;
            }
        }

        @media (max-width: 500px) {
            .profile-display .profile-text .name {
                font-size: 11px;
            }
            .profile-display .profile-text .role {
                font-size: 8px;
            }
            .profile-display {
                padding: 4px 10px 4px 4px;
            }
            .profile-display .avatar {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }
            .back-to-dashboard {
                padding: 10px 16px;
            }
            .back-to-dashboard i {
                font-size: 18px;
            }
            .back-to-dashboard span {
                font-size: 10px;
            }
        }
    </style>
</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="main" id="main">

    <div class="header">
        <div class="header-left">
            <h1 class="title">🔒 Change Password</h1>
            <p class="subtitle"><i class="fas fa-key"></i> Update your password to keep your account secure</p>
        </div>

        <!-- ===== SIMPLE PROFILE DISPLAY (NO TOGGLE) ===== -->
        <div class="profile-display">
            <span class="avatar"><?= strtoupper(substr($admin['fullname'] ?? 'A', 0, 1)) ?></span>
            <div class="profile-text">
                <span class="name"><?= htmlspecialchars($admin['fullname'] ?? 'Administrator') ?></span>
                <span class="role">Super Admin</span>
            </div>
        </div>
    </div>

    <!-- ===== PASSWORD WITH BACK BUTTON ===== -->
    <div class="password-wrapper">
        <!-- Back to Dashboard (Left Side) -->
        <a href="dashboard.php" class="back-to-dashboard">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Dashboard</span>
        </a>

        <!-- Password Card -->
        <div class="password-card">
            <div class="card-header">
                <span class="lock-icon"><i class="fas fa-lock"></i></span>
                <h2>Change Password</h2>
                <p>Enter your current password and choose a new one</p>
            </div>

            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-key"></i> Current Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="current_password" id="current_password" placeholder="Enter your current password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('current_password', 'toggleIcon1')">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-key"></i> New Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" name="new_password" id="new_password" placeholder="Enter new password (min 6 characters)" required onkeyup="checkStrength(this.value)">
                        <button type="button" class="toggle-password" onclick="togglePassword('new_password', 'toggleIcon2')">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="strength-text" id="strengthText">Enter at least 6 characters</div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-check-circle"></i> Confirm New Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-check-circle input-icon"></i>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm your new password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password', 'toggleIcon3')">
                            <i class="fas fa-eye" id="toggleIcon3"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="change_password" class="btn-change">
                    <i class="fas fa-save"></i> Change Password
                </button>
            </form>
        </div>
    </div>

</div>

<script>
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

    function checkStrength(password) {
        var strengthBar = document.getElementById('strengthBar');
        var strengthText = document.getElementById('strengthText');
        var strength = 0;
        
        if(password.length >= 6) strength += 1;
        if(password.length >= 10) strength += 1;
        if(/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 1;
        if(/[0-9]/.test(password)) strength += 1;
        if(/[^a-zA-Z0-9]/.test(password)) strength += 1;
        
        var percentage = (strength / 5) * 100;
        strengthBar.style.width = percentage + '%';
        
        if(password.length === 0) {
            strengthBar.style.background = '#eee';
            strengthText.textContent = 'Enter at least 6 characters';
            strengthText.style.color = '#999';
        } else if(strength <= 1) {
            strengthBar.style.background = '#e74c3c';
            strengthText.textContent = 'Weak password';
            strengthText.style.color = '#e74c3c';
        } else if(strength <= 2) {
            strengthBar.style.background = '#f39c12';
            strengthText.textContent = 'Fair password';
            strengthText.style.color = '#f39c12';
        } else if(strength <= 3) {
            strengthBar.style.background = '#3498db';
            strengthText.textContent = 'Good password';
            strengthText.style.color = '#3498db';
        } else if(strength <= 4) {
            strengthBar.style.background = '#2ecc71';
            strengthText.textContent = 'Strong password';
            strengthText.style.color = '#2ecc71';
        } else {
            strengthBar.style.background = '#27ae60';
            strengthText.textContent = 'Very Strong password';
            strengthText.style.color = '#27ae60';
        }
    }

    // Auto hide success/error messages after 5 seconds
    setTimeout(function() {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.style.display = 'none';
            }, 500);
        });
    }, 5000);
</script>

</body>
</html>