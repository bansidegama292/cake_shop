<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","golden_crust");

// ===== DELETE CART ITEM =====
if(isset($_GET['delete'])){
    $cart_id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM cart WHERE id = '$cart_id'");
    echo "<script>alert('Cart item deleted successfully!');window.location='admin_cart.php';</script>";
}

// ===== CLEAR ALL CART =====
if(isset($_GET['clear_all'])){
    mysqli_query($conn, "DELETE FROM cart");
    echo "<script>alert('All cart items cleared!');window.location='admin_cart.php';</script>";
}

// ===== GET ALL CART ITEMS =====
$query = "
    SELECT 
        c.id,
        c.user_id,
        c.product_id,
        c.itemname,
        c.price,
        c.quantity,
        c.total,
        c.img,
        c.username,
        c.created_at,
        u.name AS user_fullname,
        u.email AS user_email,
        u.mobileno AS user_mobile
    FROM cart c
    LEFT JOIN users u ON c.user_id = u.id
    ORDER BY c.created_at DESC
";
$result = mysqli_query($conn, $query);

// ===== STATISTICS =====
$total_items_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM cart");
$total_items = mysqli_fetch_assoc($total_items_query)['total'] ?? 0;

$total_quantity_query = mysqli_query($conn, "SELECT SUM(quantity) AS total FROM cart");
$total_quantity = mysqli_fetch_assoc($total_quantity_query)['total'] ?? 0;

$total_amount_query = mysqli_query($conn, "SELECT SUM(total) AS total FROM cart");
$total_amount = mysqli_fetch_assoc($total_amount_query)['total'] ?? 0;

$unique_users_query = mysqli_query($conn, "SELECT COUNT(DISTINCT user_id) AS total FROM cart");
$unique_users = mysqli_fetch_assoc($unique_users_query)['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Cart Management - Golden Crust Admin</title>
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

        .btn-clear {
            padding: 12px 26px;
            background: transparent;
            color: #e8436e;
            border: 2px solid #e8436e;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .btn-clear:hover {
            background: #e8436e;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(232, 67, 110, 0.3);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 20px 24px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        }

        .stat-card .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #e8436e;
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: #6b4b5e;
            font-weight: 500;
            margin-top: 4px;
        }

        .stat-card .stat-icon {
            font-size: 24px;
            color: #e8436e;
            opacity: 0.3;
            margin-bottom: 4px;
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
            min-width: 1000px;
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

        /* ================================================================
           ITEM IMAGE - FIXED
           ================================================================ */
        .item-img {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(232, 67, 110, 0.10);
            background: #fce4ec;
            display: block;
        }

        .item-img-placeholder {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e8436e;
            font-size: 28px;
            border: 2px solid rgba(232, 67, 110, 0.10);
        }

        .serial-number {
            font-weight: 700;
            color: #e8436e;
            font-size: 16px;
        }

        .user-cell {
            display: flex;
            flex-direction: column;
        }

        .user-cell .user-name {
            font-weight: 600;
            color: #1a0f17;
            font-size: 16px;
        }

        .user-cell .user-username {
            font-size: 13px;
            color: #e8436e;
        }

        .user-cell .user-email {
            font-size: 12px;
            color: #6b4b5e;
        }

        .product-name {
            font-weight: 600;
            color: #1a0f17;
            font-size: 16px;
        }

        .product-id {
            font-size: 12px;
            color: #6b4b5e;
        }

        .price-text {
            font-weight: 600;
            color: #e8436e;
            font-size: 17px;
        }

        .total-text {
            font-weight: 700;
            color: #e8436e;
            font-size: 18px;
        }

        .qty-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            background: rgba(232, 67, 110, 0.08);
            color: #e8436e;
            font-weight: 700;
            font-size: 16px;
        }

        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-delete {
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
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: #fff;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
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

        .date-text {
            font-size: 13px;
            color: #6b4b5e;
        }

        .time-text {
            font-size: 11px;
            color: #6b4b5e;
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

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            table {
                font-size: 13px;
                min-width: 900px;
            }
            
            tbody td {
                padding: 12px 14px;
                font-size: 13px;
            }
            
            .item-img, .item-img-placeholder {
                width: 55px;
                height: 55px;
            }
            
            .btn-delete {
                padding: 6px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-clear {
                justify-content: center;
                font-size: 14px;
                padding: 10px 20px;
            }

            .action-btns {
                flex-direction: column;
            }

            .btn-delete {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="main" id="main">

    <div class="page-header">
        <div class="title">
            <i class="fas fa-shopping-cart"></i> Cart Management
            <span class="badge"><?php echo $total_items; ?> Items</span>
        </div>
        <?php if($total_items > 0): ?>
            <a href="?clear_all=1" class="btn-clear" onclick="return confirm('Are you sure you want to clear all cart items?')">
                <i class="fas fa-trash-alt"></i> Clear All Cart
            </a>
        <?php endif; ?>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
            <div class="stat-number"><?php echo $total_items; ?></div>
            <div class="stat-label">Total Items</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-boxes"></i></div>
            <div class="stat-number"><?php echo $total_quantity; ?></div>
            <div class="stat-label">Total Quantity</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-rupee-sign"></i></div>
            <div class="stat-number">₹<?php echo number_format($total_amount, 2); ?></div>
            <div class="stat-label">Total Amount</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-number"><?php echo $unique_users; ?></div>
            <div class="stat-label">Unique Users</div>
        </div>
    </div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th><i class="fas fa-image"></i> Image</th>
                        <th><i class="fas fa-box"></i> Product</th>
                        <th><i class="fas fa-user"></i> User</th>
                        <th><i class="fas fa-rupee-sign"></i> Price</th>
                        <th><i class="fas fa-sort-amount-up"></i> Qty</th>
                        <th><i class="fas fa-rupee-sign"></i> Total</th>
                        <th><i class="fas fa-calendar-alt"></i> Added</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php $sr = 1; while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="serial-number"><?php echo $sr++; ?></td>
                            <td>
                                <?php 
                                // ✅ FIX: Get the image path correctly
                                $image_path = '';
                                $image_exists = false;
                                
                                if(!empty($row['img'])){
                                    // Try multiple paths
                                    $paths_to_try = [
                                        'admin/uploads/' . $row['img'],
                                        '../admin/uploads/' . $row['img'],
                                        '../../admin/uploads/' . $row['img'],
                                        'uploads/' . $row['img'],
                                        '../uploads/' . $row['img'],
                                        '../../uploads/' . $row['img']
                                    ];
                                    
                                    foreach($paths_to_try as $test_path){
                                        if(file_exists($test_path)){
                                            $image_path = $test_path;
                                            $image_exists = true;
                                            break;
                                        }
                                    }
                                    
                                    // If file not found, try one more path
                                    if(!$image_exists){
                                        $image_path = 'uploads/' . $row['img'];
                                        if(file_exists($image_path)){
                                            $image_exists = true;
                                        }
                                    }
                                }
                                ?>
                                
                                <?php if(!empty($row['img']) && $image_exists): ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                         alt="<?php echo htmlspecialchars($row['itemname']); ?>" 
                                         class="item-img"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="item-img-placeholder" style="display:none;">
                                        <i class="fas fa-cake"></i>
                                    </div>
                                <?php elseif(!empty($row['img']) && !$image_exists): ?>
                                    <!-- Fallback: Try direct path -->
                                    <img src="uploads/<?php echo $row['img']; ?>" 
                                         alt="<?php echo htmlspecialchars($row['itemname']); ?>" 
                                         class="item-img"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="item-img-placeholder" style="display:none;">
                                        <i class="fas fa-cake"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="item-img-placeholder">
                                        <i class="fas fa-cake"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="product-name"><?php echo htmlspecialchars($row['itemname']); ?></div>
                                <div class="product-id">ID: #<?php echo $row['product_id']; ?></div>
                            </td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-name"><?php echo htmlspecialchars($row['user_fullname'] ?? $row['username']); ?></div>
                                    <div class="user-username">@<?php echo htmlspecialchars($row['username']); ?></div>
                                    <div class="user-email"><i class="fas fa-envelope" style="font-size: 11px;"></i> <?php echo htmlspecialchars($row['user_email'] ?? 'N/A'); ?></div>
                                </div>
                            </td>
                            <td class="price-text">₹<?php echo number_format($row['price'], 2); ?></td>
                            <td><span class="qty-badge"><?php echo $row['quantity']; ?></span></td>
                            <td class="total-text">₹<?php echo number_format($row['total'], 2); ?></td>
                            <td>
                                <div class="date-text"><?php echo date('d M Y', strtotime($row['created_at'])); ?></div>
                                <div class="time-text"><?php echo date('h:i A', strtotime($row['created_at'])); ?></div>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Delete this cart item?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="no-data">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h3>Cart is Empty</h3>
                                    <p>No items added to cart by any user yet.</p>
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
console.log('✅ Admin Cart Management Loaded');
console.log('📊 Total Items: <?php echo $total_items; ?>');
console.log('📊 Total Amount: ₹<?php echo number_format($total_amount, 2); ?>');
</script>

</body>
</html>