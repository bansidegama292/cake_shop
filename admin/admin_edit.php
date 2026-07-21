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
$admin_data = null;

// Get admin ID from URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $admin_id = mysqli_real_escape_string($conn, $_GET['id']);
    
    $query = "SELECT * FROM admin WHERE id = '$admin_id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $admin_data = mysqli_fetch_assoc($result);
    } else {
        $error = "Admin not found!";
    }
} else {
    $error = "Invalid admin ID!";
}

// Handle Update - Plain Text Password (NO HASHING)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_admin'])) {
    $admin_id = mysqli_real_escape_string($conn, $_POST['admin_id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

    if (!empty($username)) {
        if (!empty($new_password)) {
            $update_query = "UPDATE admin SET 
                username = '$username',
                password = '$new_password'
                WHERE id = '$admin_id'";
        } else {
            $update_query = "UPDATE admin SET 
                username = '$username'
                WHERE id = '$admin_id'";
        }

        if (mysqli_query($conn, $update_query)) {
            header("Location: admin_list.php?msg=updated");
            exit();
        } else {
            $error = "Error updating admin: " . mysqli_error($conn);
        }
    } else {
        $error = "Please fill all required fields!";
    }
}

// If admin not found, redirect back
if (!$admin_data && !$error) {
    header("Location: admin_list.php?msg=not_found");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Admin - Golden Crust</title>
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

        /* Form Card - Baby Pink */
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

        /* Form Header - Baby Pink */
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

        /* Form Groups - Baby Pink */
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
            padding: 8px;
            border-radius: 8px;
        }

        .form-group .toggle-password:hover {
            color: #e8436e;
            background: rgba(232, 67, 110, 0.05);
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

        .form-help {
            font-size: 12px;
            color: #aaa;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .form-help i {
            color: #e8436e;
        }

        /* Info Box - Baby Pink */
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
            color: #1a0f17;
        }

        /* Buttons - Baby Pink */
        .btn-group {
            display: flex;
            gap: 14px;
            margin-top: 20px;
        }

        .btn-update {
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

        .btn-update:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 35px rgba(232, 67, 110, 0.35);
        }

        .btn-update:active {
            transform: scale(0.97);
        }

        .btn-update i {
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

            .btn-update, .btn-cancel {
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

    <!-- Only Form Card - No Top Header -->
    <div class="form-card">
        <div class="form-header">
            <div class="avatar">
                <i class="fas fa-user-edit"></i>
            </div>
            <h2><i class="fas fa-user-cog"></i> Edit Admin Account</h2>
            <p><i class="fas fa-crown"></i> Golden Crust Admin Panel</p>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <?php if($admin_data): ?>
            <form method="POST" action="">
                <input type="hidden" name="admin_id" value="<?= $admin_data['id'] ?>">
                
                <div class="form-group">
                    <label><i class="fas fa-user-tag"></i> Username <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <i class="fas fa-user-tag input-icon"></i>
                        <input type="text" name="username" value="<?= htmlspecialchars($admin_data['username']) ?>" required placeholder="Enter username">
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-key"></i> New Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key input-icon"></i>
                        <input type="password" name="new_password" id="new_password" placeholder="Leave blank to keep current password">
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                    <div class="form-help">
                        <i class="fas fa-info-circle"></i> Enter a new password only if you want to change it
                    </div>
                   
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <i class="fas fa-shield-alt"></i>
                    <span>You are editing admin: <strong><?= htmlspecialchars($admin_data['username']) ?></strong> (ID: <?= $admin_data['id'] ?>)</span>
                </div>

                <div class="btn-group">
                    <button type="submit" name="update_admin" class="btn-update">
                        <i class="fas fa-save"></i> Update Admin
                    </button>
                    <a href="admin_list.php" class="btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-error" style="margin-top: 10px;">
                <i class="fas fa-exclamation-circle"></i> Admin not found!
            </div>
            <div style="text-align: center; margin-top: 15px;">
                <a href="admin_list.php" class="btn-cancel" style="display: inline-flex; padding: 12px 30px; flex: none;">
                    <i class="fas fa-arrow-left"></i> Back to Admin List
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>

<script>
function togglePassword() {
    var password = document.getElementById("new_password");
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