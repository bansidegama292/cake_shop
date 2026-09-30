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

// ===== DELETE ADMIN =====
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    
    if($id == $_SESSION['admin_id']){
        header("Location: admin_list.php?msg=self_error");
        exit();
    }
    
    $query = "DELETE FROM admin WHERE id = '$id'";
    if(mysqli_query($conn, $query)){
        header("Location: admin_list.php?msg=deleted");
    } else {
        header("Location: admin_list.php?msg=error");
    }
    exit();
}

// ===== GET ALL ADMINS =====
$query = "SELECT id, username, password FROM admin ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$total_admins = mysqli_num_rows($result);

// ===== GET MESSAGE =====
$msg = '';
$msg_type = '';
if(isset($_GET['msg'])){
    if($_GET['msg'] == 'deleted'){
        $msg = 'Admin deleted successfully!';
        $msg_type = 'success';
    } elseif($_GET['msg'] == 'self_error'){
        $msg = 'You cannot delete your own account!';
        $msg_type = 'error';
    } elseif($_GET['msg'] == 'error'){
        $msg = 'Error deleting admin!';
        $msg_type = 'error';
    } elseif($_GET['msg'] == 'not_found'){
        $msg = 'Admin not found!';
        $msg_type = 'error';
    } elseif($_GET['msg'] == 'added'){
        $msg = 'Admin added successfully!';
        $msg_type = 'success';
    } elseif($_GET['msg'] == 'updated'){
        $msg = 'Admin updated successfully!';
        $msg_type = 'success';
    } elseif($_GET['msg'] == 'update_error'){
        $msg = 'Error updating admin!';
        $msg_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Management - Golden Crust</title>
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
        }

        .main {
            margin-left: 85px;
            padding: 30px;
            transition: margin-left 0.35s ease;
            min-height: 100vh;
        }

        .main.shift {
            margin-left: 260px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .page-header .title {
            font-size: 32px;
            font-weight: 800;
            color: #1a0f17;
        }

        .page-header .title i {
            color: #e8436e;
            margin-right: 10px;
        }

        .page-header .title .badge {
            font-size: 16px;
            background: rgba(232, 67, 110, 0.12);
            color: #e8436e;
            padding: 6px 18px;
            border-radius: 30px;
            font-weight: 600;
            margin-left: 10px;
        }

        .btn-add {
            padding: 12px 26px;
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(232, 67, 110, 0.25);
            font-family: 'Poppins', sans-serif;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(232, 67, 110, 0.4);
        }

        .table-wrapper {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 5px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .table-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            min-width: 700px;
        }

        thead {
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
        }

        thead th {
            color: #fff;
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        thead th i {
            margin-right: 6px;
            font-size: 13px;
        }

        tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            transition: 0.3s;
        }

        tbody tr:hover {
            background: rgba(232, 67, 110, 0.03);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody td {
            padding: 14px 16px;
            color: #2d1b24;
            vertical-align: middle;
            font-size: 14px;
        }

        .id-cell {
            font-weight: 700;
            color: #e8436e;
            font-size: 16px;
        }

        .username-cell {
            font-weight: 500;
            color: #1a0f17;
            font-size: 15px;
        }

        .username-cell i {
            color: #e8436e;
            margin-right: 6px;
        }

        /* ===== PASSWORD - ALWAYS SHOW ===== */
        .password-display {
            font-size: 14px;
            color: #1a0f17;
            font-weight: 500;
            background: #fce4ec;
            padding: 4px 14px;
            border-radius: 6px;
            font-family: monospace;
            display: inline-block;
            letter-spacing: 1px;
        }

        .password-display i {
            color: #e8436e;
            margin-right: 6px;
            font-size: 13px;
        }

        .action-btns {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 6px 14px;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: linear-gradient(135deg, #34d399, #059669);
            color: #fff;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 211, 153, 0.3);
        }

        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: #fff;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
        }

        .btn-delete.disabled {
            background: #b395a5;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .btn-delete.disabled:hover {
            transform: none;
            box-shadow: none;
        }

        .you-badge {
            display: inline-block;
            background: #e8436e;
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px;
            margin-left: 8px;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
        }

        .no-data i {
            font-size: 70px;
            color: #e8436e;
            opacity: 0.2;
            display: block;
            margin-bottom: 20px;
        }

        .no-data h3 {
            font-size: 26px;
            color: #1a0f17;
            margin-bottom: 8px;
        }

        .no-data p {
            font-size: 16px;
            color: #6b4b5e;
        }

        .msg {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 15px;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .msg-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .msg-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .msg i {
            margin-right: 10px;
        }

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

            .page-header .title {
                font-size: 24px;
            }

            table {
                font-size: 12px;
                min-width: 600px;
            }
            
            tbody td {
                padding: 10px 12px;
                font-size: 12px;
            }
            
            .btn-action {
                padding: 5px 10px;
                font-size: 11px;
            }

            .password-display {
                font-size: 12px;
                padding: 3px 10px;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-add {
                justify-content: center;
                font-size: 14px;
                padding: 10px 20px;
            }

            .action-btns {
                flex-direction: column;
            }

            .btn-action {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="main" id="main">

    <?php if(!empty($msg)): ?>
        <div class="msg msg-<?php echo $msg_type; ?>" id="successMsg">
            <i class="fas <?php echo $msg_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div class="title">
            <i class="fas fa-user-shield"></i> Admin Management
            <span class="badge"><?php echo $total_admins; ?> Admins</span>
        </div>
        <a href="admin_register.php" class="btn-add">
            <i class="fas fa-user-plus"></i> Add New Admin
        </a>
    </div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-user-tag"></i> Username</th>
                        <th><i class="fas fa-lock"></i> Password</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_admins > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): 
                            $original_password = htmlspecialchars($row['password']);
                        ?>
                        <tr>
                            <td class="id-cell"><?php echo $row['id']; ?></td>
                            <td>
                                <span class="username-cell">
                                    <i class="fas fa-user"></i> 
                                    <?php echo htmlspecialchars($row['username']); ?>
                                    <?php if($row['id'] == $_SESSION['admin_id']): ?>
                                        <span class="you-badge">You</span>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <!-- Password - Always Show -->
                                <span class="password-display">
                                    <i class="fas fa-key"></i> 
                                    <?php echo $original_password; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="admin_edit.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <?php if($row['id'] == $_SESSION['admin_id']): ?>
                                        <a href="#" class="btn-action btn-delete disabled" onclick="return false;">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    <?php else: ?>
                                        <a href="?delete=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this admin?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="no-data">
                                    <i class="fas fa-user-shield"></i>
                                    <h3>No Admins Found</h3>
                                    <p>Start by adding your first admin!</p>
                                    <a href="admin_register.php" class="btn-add" style="display: inline-block; margin-top: 10px;">
                                        <i class="fas fa-user-plus"></i> Add Admin
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
setTimeout(function() {
    const msg = document.getElementById('successMsg');
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
}, 3000);
</script>

</body>
</html>