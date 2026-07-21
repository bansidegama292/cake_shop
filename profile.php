<?php
session_start();

// ----- ERROR REPORTING -----
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ----- CHECK IF USER IS LOGGED IN -----
if(!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true){
    header("Location: login.php");
    exit();
}

// ----- DATABASE CONNECTION -----
$conn = mysqli_connect("localhost", "root", "", "golden_crust");

if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}

// ----- GET USER DATA -----
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0){
    header("Location: logout.php");
    exit();
}

$user = mysqli_fetch_assoc($result);

// ----- UPDATE PROFILE -----
$update_msg = "";
$update_msg_type = "";

if(isset($_POST['update_profile'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mobileno = mysqli_real_escape_string($conn, $_POST['mobileno']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $pincode = mysqli_real_escape_string($conn, $_POST['pincode']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);

    // Check if email already exists for other user
    $check_email = "SELECT id FROM users WHERE email = '$email' AND id != '$user_id'";
    $email_result = mysqli_query($conn, $check_email);

    if(mysqli_num_rows($email_result) > 0){
        $update_msg = "❌ Email already used by another user!";
        $update_msg_type = "error";
    } else {
        $update_sql = "UPDATE users SET 
            name = '$name',
            email = '$email',
            mobileno = '$mobileno',
            address = '$address',
            city = '$city',
            state = '$state',
            pincode = '$pincode',
            country = '$country',
            dob = '$dob',
            gender = '$gender',
            updated_at = NOW()
            WHERE id = '$user_id'";

        if(mysqli_query($conn, $update_sql)){
            $update_msg = "✅ Profile updated successfully!";
            $update_msg_type = "success";
            
            // Update session data
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            // Refresh user data
            $result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
            $user = mysqli_fetch_assoc($result);
        } else {
            $update_msg = "❌ Error updating profile: " . mysqli_error($conn);
            $update_msg_type = "error";
        }
    }
}

// ----- CHANGE PASSWORD -----
$pass_msg = "";
$pass_msg_type = "";

if(isset($_POST['change_password'])){
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check current password
    $password_correct = false;
    if(strpos($user['password'], '$2y$') === 0){
        if(password_verify($current_password, $user['password'])){
            $password_correct = true;
        }
    } else {
        if($current_password === $user['password']){
            $password_correct = true;
        }
    }

    if(!$password_correct){
        $pass_msg = "❌ Current password is incorrect!";
        $pass_msg_type = "error";
    } elseif(strlen($new_password) < 6){
        $pass_msg = "❌ New password must be at least 6 characters!";
        $pass_msg_type = "error";
    } elseif($new_password !== $confirm_password){
        $pass_msg = "❌ New passwords do not match!";
        $pass_msg_type = "error";
    } else {
        // Update password (store as plain text)
        $update_pass = "UPDATE users SET password = '$new_password', updated_at = NOW() WHERE id = '$user_id'";
        
        if(mysqli_query($conn, $update_pass)){
            $pass_msg = "✅ Password changed successfully!";
            $pass_msg_type = "success";
            
            // Refresh user data
            $result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
            $user = mysqli_fetch_assoc($result);
        } else {
            $pass_msg = "❌ Error changing password: " . mysqli_error($conn);
            $pass_msg_type = "error";
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Golden Crust</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;1,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- ==========================================
         CSS INCLUDE - profile.css
         ========================================== -->
         <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    
</head>
<body>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

    <!-- ===== MAIN CONTENT ===== -->
    <div class="container">

        <!-- ===== SIDEBAR ===== -->
        <div class="profile-sidebar">
            <div class="avatar">
                <?php 
                    $initial = strtoupper(substr($user['username'] ?? 'U', 0, 1));
                    echo $initial;
                ?>
            </div>
            <div>
                <h3><?php echo htmlspecialchars($user['name'] ?? $user['username']); ?></h3>
                <div class="username">@<?php echo htmlspecialchars($user['username']); ?></div>
                <div class="email"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></div>
                <div class="member-since">
                    <i class="fas fa-calendar-alt"></i> 
                    Member since <?php echo date('M d, Y', strtotime($user['created_at'] ?? date('Y-m-d'))); ?>
                </div>
            </div>
            
            <div class="stats">
                <div class="stat-item">
                    <div class="number">0</div>
                    <div class="label">Orders</div>
                </div>
                <div class="stat-item">
                    <div class="number">0</div>
                    <div class="label">Wishlist</div>
                </div>
            </div>

            <!-- ===== LOGOUT BUTTON AT BOTTOM ===== -->
            <div class="sidebar-logout">
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <!-- ===== PROFILE CONTENT ===== -->
        <div class="profile-content">

            <!-- ===== UPDATE PROFILE ===== -->
            <div class="profile-card">
                <h2><i class="fas fa-user-edit"></i> Edit Profile</h2>
                
                <?php if(isset($update_msg) && $update_msg != "") { ?>
                    <div class="msg msg-<?php echo $update_msg_type; ?>">
                        <i class="fas fa-circle-info"></i> <?php echo $update_msg; ?>
                    </div>
                <?php } ?>

                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Mobile Number</label>
                            <input type="text" name="mobileno" value="<?php echo htmlspecialchars($user['mobileno'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Date of Birth</label>
                            <input type="date" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-venus-mars"></i> Gender</label>
                            <select name="gender">
                                <option value="">Select Gender</option>
                                <option value="Male" <?php echo ($user['gender'] ?? '') == 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($user['gender'] ?? '') == 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($user['gender'] ?? '') == 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-home"></i> Address</label>
                        <textarea name="address" rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-city"></i> City</label>
                            <input type="text" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-map-marker-alt"></i> State</label>
                            <input type="text" name="state" value="<?php echo htmlspecialchars($user['state'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-mailbox"></i> Pincode</label>
                            <input type="text" name="pincode" value="<?php echo htmlspecialchars($user['pincode'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-globe"></i> Country</label>
                            <input type="text" name="country" value="<?php echo htmlspecialchars($user['country'] ?? ''); ?>">
                        </div>
                    </div>

                    <button type="submit" name="update_profile" class="btn btn-primary btn-block">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- ===== CHANGE PASSWORD ===== -->
            <div class="profile-card">
                <h2><i class="fas fa-key"></i> Change Password</h2>
                
                <?php if(isset($pass_msg) && $pass_msg != "") { ?>
                    <div class="msg msg-<?php echo $pass_msg_type; ?>">
                        <i class="fas fa-circle-info"></i> <?php echo $pass_msg; ?>
                    </div>
                <?php } ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Current Password</label>
                        <input type="password" name="current_password" placeholder="Enter current password" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-key"></i> New Password</label>
                            <input type="password" name="new_password" placeholder="Enter new password (min 6 characters)" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-check-circle"></i> Confirm New Password</label>
                            <input type="password" name="confirm_password" placeholder="Confirm new password" required>
                        </div>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-success btn-block">
                        <i class="fas fa-exchange-alt"></i> Change Password
                    </button>
                </form>
            </div>

        </div>
    </div>

    <script>
        // Auto-hide messages after 4 seconds
        setTimeout(function() {
            const msgs = document.querySelectorAll('.msg');
            msgs.forEach(function(msg) {
                msg.style.transition = 'opacity 0.5s ease';
                msg.style.opacity = '0';
                setTimeout(function() {
                    msg.remove();
                }, 500);
            });
        }, 4000);
    </script>

<?php include 'includes/footer.php'; ?>
</body>
</html>