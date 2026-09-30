<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","golden_crust");

// Get all users ordered by newest first
$result = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");
$total_users = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Users Management - Golden Crust Admin</title>
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

        /* ===== MAIN CONTENT - SAME AS SIDEBAR ===== */
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
            font-size: 15px;
            min-width: 1200px;
        }

        thead {
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
        }

        thead th {
            color: #fff;
            padding: 16px 18px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        thead th i {
            margin-right: 6px;
            font-size: 14px;
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
            padding: 16px 18px;
            color: #2d1b24;
            vertical-align: middle;
            font-size: 15px;
        }

        /* ===== ID ===== */
        .id-cell {
            font-weight: 700;
            color: #e8436e;
            font-size: 16px;
        }

        /* ===== NAME ===== */
        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
        }

        .user-name {
            font-weight: 600;
            color: #1a0f17;
            font-size: 16px;
        }

        /* ===== ADDRESS ===== */
        .address-text {
            font-size: 14px;
            color: #4a2c3f;
            max-width: 150px;
            display: block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .address-text i {
            color: #e8436e;
            margin-right: 4px;
            font-size: 13px;
        }

        /* ===== CITY ===== */
        .city-text {
            font-size: 15px;
            color: #4a2c3f;
        }

        /* ===== PINCODE ===== */
        .pincode-text {
            font-size: 15px;
            color: #4a2c3f;
            font-weight: 500;
        }

        /* ===== STATE ===== */
        .state-text {
            font-size: 15px;
            color: #4a2c3f;
        }

        /* ===== COUNTRY ===== */
        .country-text {
            font-size: 15px;
            color: #4a2c3f;
        }

        /* ===== USERNAME ===== */
        .username-text {
            font-size: 15px;
            color: #e8436e;
            font-weight: 500;
        }

        .username-text i {
            font-size: 13px;
            margin-right: 4px;
        }

        /* ===== PASSWORD - SHOW PLAIN TEXT ===== */
        .password-text {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            font-weight: 600;
            color: #2d1b24;
            background: rgba(0, 0, 0, 0.04);
            padding: 4px 12px;
            border-radius: 6px;
            display: inline-block;
            letter-spacing: 0.5px;
        }

        /* ===== GENDER ===== */
        .gender-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .gender-male {
            background: rgba(41, 128, 185, 0.10);
            color: #2980b9;
        }

        .gender-female {
            background: rgba(232, 67, 110, 0.10);
            color: #e8436e;
        }

        .gender-other {
            background: rgba(142, 68, 173, 0.10);
            color: #8e44ad;
        }

        .gender-na {
            color: #b395a5;
            font-size: 14px;
        }

        /* ===== MOBILENO ===== */
        .mobile-link {
            color: #2d1b24;
            text-decoration: none;
            font-size: 15px;
        }

        .mobile-link i {
            color: #34d399;
            margin-right: 6px;
        }

        /* ===== EMAIL ===== */
        .email-link {
            color: #e8436e;
            text-decoration: none;
            font-size: 15px;
        }

        .email-link:hover {
            text-decoration: underline;
        }

        .email-link i {
            margin-right: 6px;
            font-size: 13px;
        }

        /* ===== DOB ===== */
        .dob-text {
            font-size: 15px;
            color: #4a2c3f;
        }

        .dob-text i {
            color: #e8436e;
            margin-right: 6px;
            font-size: 13px;
        }

        /* ===== CREATED AT ===== */
        .created-text {
            font-size: 14px;
            color: #6b4b5e;
        }

        .created-text i {
            color: #e8436e;
            margin-right: 6px;
        }

        /* ===== ACTION BUTTONS ===== */
        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: #fff;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
        }

        /* ===== NO DATA ===== */
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

        /* ===== RESPONSIVE ===== */
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
                padding: 70px 10px 10px 10px;
            }
            
            .main.shift {
                margin-left: 0;
            }

            .page-header .title {
                font-size: 24px;
            }
            
            table {
                font-size: 13px;
                min-width: 900px;
            }
            
            tbody td {
                padding: 12px 12px;
                font-size: 13px;
            }
            
            .user-name {
                font-size: 14px;
            }
            
            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 15px;
            }
            
            .btn-action {
                padding: 6px 12px;
                font-size: 12px;
            }
        }

        /* ===== STATS ===== */
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            padding: 16px 20px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            text-align: center;
        }

        .stat-card .stat-number {
            font-size: 28px;
            font-weight: 800;
            color: #e8436e;
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: #6b4b5e;
            font-weight: 500;
            margin-top: 4px;
        }
    </style>
</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="main" id="main">

    <!-- Page Header -->
    <div class="page-header">
        <div class="title">
            <i class="fas fa-users"></i> Users Management
            <span class="badge"><?php echo $total_users; ?> Users</span>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="stats-summary">
        <div class="stat-card">
            <div class="stat-number"><?php echo $total_users; ?></div>
            <div class="stat-label"><i class="fas fa-users"></i> Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">
                <?php 
                $male = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE gender = 'Male'");
                echo mysqli_fetch_assoc($male)['total'] ?? 0;
                ?>
            </div>
            <div class="stat-label"><i class="fas fa-mars" style="color:#2980b9;"></i> Male</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">
                <?php 
                $female = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE gender = 'Female'");
                echo mysqli_fetch_assoc($female)['total'] ?? 0;
                ?>
            </div>
            <div class="stat-label"><i class="fas fa-venus" style="color:#e8436e;"></i> Female</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">
                <?php 
                $today = mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE DATE(created_at) = CURDATE()");
                echo mysqli_fetch_assoc($today)['total'] ?? 0;
                ?>
            </div>
            <div class="stat-label"><i class="fas fa-calendar-day"></i> Today</div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-user"></i> Name</th>
                        <th><i class="fas fa-home"></i> Address</th>
                        <th><i class="fas fa-city"></i> City</th>
                        <th><i class="fas fa-map-pin"></i> Pincode</th>
                        <th><i class="fas fa-map"></i> State</th>
                        <th><i class="fas fa-globe"></i> Country</th>
                        <th><i class="fas fa-user-tag"></i> Username</th>
                        <th><i class="fas fa-lock"></i> Password</th>
                        <th><i class="fas fa-venus-mars"></i> Gender</th>
                        <th><i class="fas fa-phone"></i> Mobile</th>
                        <th><i class="fas fa-envelope"></i> Email</th>
                        <th><i class="fas fa-calendar-alt"></i> DOB</th>
                        <th><i class="fas fa-clock"></i> Created</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_users > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <!-- 1. ID -->
                            <td class="id-cell"><?php echo $row['id']; ?></td>

                            <!-- 2. Name -->
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($row['name'], 0, 1)); ?>
                                    </div>
                                    <span class="user-name"><?php echo htmlspecialchars($row['name']); ?></span>
                                </div>
                            </td>

                            <!-- 3. Address -->
                            <td>
                                <?php if(!empty($row['address'])): ?>
                                    <span class="address-text" title="<?php echo htmlspecialchars($row['address']); ?>">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars(substr($row['address'], 0, 30)); ?>
                                        <?php if(strlen($row['address']) > 30): ?>...<?php endif; ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #b395a5; font-size: 14px;">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- 4. City -->
                            <td>
                                <?php if(!empty($row['city'])): ?>
                                    <span class="city-text"><?php echo htmlspecialchars($row['city']); ?></span>
                                <?php else: ?>
                                    <span style="color: #b395a5; font-size: 14px;">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- 5. Pincode -->
                            <td>
                                <?php if(!empty($row['pincode'])): ?>
                                    <span class="pincode-text"><?php echo htmlspecialchars($row['pincode']); ?></span>
                                <?php else: ?>
                                    <span style="color: #b395a5; font-size: 14px;">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- 6. State -->
                            <td>
                                <?php if(!empty($row['state'])): ?>
                                    <span class="state-text"><?php echo htmlspecialchars($row['state']); ?></span>
                                <?php else: ?>
                                    <span style="color: #b395a5; font-size: 14px;">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- 7. Country -->
                            <td>
                                <?php if(!empty($row['country'])): ?>
                                    <span class="country-text"><?php echo htmlspecialchars($row['country']); ?></span>
                                <?php else: ?>
                                    <span style="color: #b395a5; font-size: 14px;">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- 8. Username -->
                            <td>
                                <?php if(!empty($row['username'])): ?>
                                    <span class="username-text">
                                        <i class="fas fa-at"></i> <?php echo htmlspecialchars($row['username']); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #b395a5; font-size: 14px;">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- 9. Password - SHOW PLAIN TEXT -->
                            <td>
                                <?php if(!empty($row['password'])): ?>
                                    <span class="password-text">
                                        <?php echo htmlspecialchars($row['password']); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #b395a5; font-size: 14px;">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- 10. Gender -->
                            <td>
                                <?php if(!empty($row['gender'])): ?>
                                    <span class="gender-badge gender-<?php echo strtolower($row['gender']); ?>">
                                        <?php if($row['gender'] == 'Male'): ?>
                                            <i class="fas fa-mars"></i>
                                        <?php elseif($row['gender'] == 'Female'): ?>
                                            <i class="fas fa-venus"></i>
                                        <?php else: ?>
                                            <i class="fas fa-genderless"></i>
                                        <?php endif; ?>
                                        <?php echo $row['gender']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="gender-na">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- 11. Mobileno -->
                            <td>
                                <?php if(!empty($row['mobileno'])): ?>
                                    <a href="tel:<?php echo $row['mobileno']; ?>" class="mobile-link">
                                        <i class="fas fa-phone"></i>
                                        <?php echo htmlspecialchars($row['mobileno']); ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color: #b395a5; font-size: 14px;">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- 12. Email -->
                            <td>
                                <a href="mailto:<?php echo $row['email']; ?>" class="email-link">
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </a>
                            </td>

                            <!-- 13. DOB -->
                            <td>
                                <?php if(!empty($row['dob'])): ?>
                                    <span class="dob-text">
                                        <i class="fas fa-calendar-alt"></i>
                                        <?php echo date('d M Y', strtotime($row['dob'])); ?>
                                    </span>
                                <?php else: ?>
                                    <span style="color: #b395a5; font-size: 14px;">—</span>
                                <?php endif; ?>
                            </td>

                            <!-- 14. Created At -->
                            <td>
                                <span class="created-text">
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('d M Y', strtotime($row['created_at'])); ?>
                                </span>
                            </td>

                            <!-- 15. Actions -->
                            <td>
                                <div class="action-btns">
                                    <a href="delete_user.php?id=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="15">
                                <div class="no-data">
                                    <i class="fas fa-users"></i>
                                    <h3>No Users Found</h3>
                                    <p>No users registered yet.</p>
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
console.log('✅ Users Management Page Loaded');
console.log('📊 Total Users: <?php echo $total_users; ?>');
</script>

</body>
</html>