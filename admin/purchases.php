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

// ===== UPDATE ORDER STATUS =====
if(isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_query = "UPDATE orders SET status = '$status' WHERE id = '$order_id'";
    if(mysqli_query($conn, $update_query)) {
        $msg = "Order status updated successfully!";
        $msg_type = "success";
    } else {
        $msg = "Error updating order status!";
        $msg_type = "error";
    }
}

// ===== DELETE ORDER =====
if(isset($_GET['delete'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // First delete order items
    mysqli_query($conn, "DELETE FROM order_items WHERE order_id = '$order_id'");
    
    // Then delete order
    $delete_query = "DELETE FROM orders WHERE id = '$order_id'";
    if(mysqli_query($conn, $delete_query)) {
        $msg = "Order deleted successfully!";
        $msg_type = "success";
    } else {
        $msg = "Error deleting order!";
        $msg_type = "error";
    }
}

// ===== FILTER ORDERS =====
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$date_filter = isset($_GET['date']) ? mysqli_real_escape_string($conn, $_GET['date']) : '';

$where_clause = "";
if(!empty($status_filter)) {
    $where_clause .= " WHERE status = '$status_filter'";
}
if(!empty($date_filter)) {
    if(!empty($where_clause)) {
        $where_clause .= " AND DATE(order_date) = '$date_filter'";
    } else {
        $where_clause .= " WHERE DATE(order_date) = '$date_filter'";
    }
}

// ===== GET ORDERS =====
$query = "SELECT o.*, 
          (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
          FROM orders o 
          $where_clause 
          ORDER BY o.id DESC";
$result = mysqli_query($conn, $query);
$total_orders = mysqli_num_rows($result);

// ===== GET ORDER STATISTICS =====
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
    SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
    SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped,
    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM orders";
$stats_result = mysqli_query($conn, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchases - Golden Crust Admin</title>
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

        /* ===== MAIN CONTENT - SAME AS CART PAGE ===== */
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

        /* ===== STATISTICS CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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

        .stat-card.pending .stat-number { color: #f39c12; }
        .stat-card.confirmed .stat-number { color: #3498db; }
        .stat-card.processing .stat-number { color: #9b59b6; }
        .stat-card.shipped .stat-number { color: #1abc9c; }
        .stat-card.delivered .stat-number { color: #27ae60; }
        .stat-card.cancelled .stat-number { color: #e74c3c; }

        /* ===== FILTERS ===== */
        .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 15px 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .filters select, .filters input {
            padding: 10px 16px;
            border: 2px solid rgba(200, 200, 200, 0.2);
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            background: rgba(255, 255, 255, 0.6);
            color: #333;
            transition: 0.3s;
        }

        .filters select:focus, .filters input:focus {
            border-color: #e8436e;
            outline: none;
            box-shadow: 0 0 0 4px rgba(232, 67, 110, 0.08);
        }

        .btn-filter {
            padding: 10px 24px;
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(232, 67, 110, 0.3);
        }

        .btn-reset {
            padding: 10px 24px;
            background: rgba(200, 200, 200, 0.2);
            color: #888;
            border: 2px solid rgba(200, 200, 200, 0.2);
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
        }

        .btn-reset:hover {
            background: rgba(200, 200, 200, 0.3);
            color: #555;
        }

        /* ===== TABLE ===== */
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

        .order-id {
            font-weight: 700;
            color: #e8436e;
            font-size: 16px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending { background: rgba(243, 156, 18, 0.12); color: #f39c12; }
        .status-confirmed { background: rgba(52, 152, 219, 0.12); color: #3498db; }
        .status-processing { background: rgba(155, 89, 182, 0.12); color: #9b59b6; }
        .status-shipped { background: rgba(26, 188, 156, 0.12); color: #1abc9c; }
        .status-delivered { background: rgba(46, 204, 113, 0.12); color: #27ae60; }
        .status-cancelled { background: rgba(231, 76, 60, 0.12); color: #e74c3c; }

        .customer-name {
            font-weight: 600;
            color: #1a0f17;
            font-size: 16px;
        }

        .customer-phone {
            font-size: 13px;
            color: #6b4b5e;
        }

        .customer-address {
            font-size: 12px;
            color: #6b4b5e;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .amount-text {
            font-weight: 700;
            color: #e8436e;
            font-size: 18px;
        }

        .date-text {
            font-size: 13px;
            color: #6b4b5e;
        }

        .time-text {
            font-size: 11px;
            color: #6b4b5e;
        }

        .items-count {
            font-weight: 600;
            color: #1a0f17;
            font-size: 16px;
        }

        /* ===== ACTIONS ===== */
        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .status-form {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        .status-form select {
            padding: 4px 10px;
            border: 2px solid rgba(200, 200, 200, 0.2);
            border-radius: 8px;
            font-size: 12px;
            font-family: 'Poppins', sans-serif;
            background: rgba(255, 255, 255, 0.6);
            color: #333;
        }

        .status-form select:focus {
            border-color: #e8436e;
            outline: none;
        }

        .btn-update-status {
            padding: 4px 12px;
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .btn-update-status:hover {
            transform: scale(1.05);
        }

        .btn-delete {
            padding: 6px 14px;
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

        /* ===== ALERT ===== */
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert i {
            font-size: 18px;
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

            .filters {
                flex-direction: column;
            }
            
            .filters select, .filters input, .btn-filter, .btn-reset {
                width: 100%;
            }

            table {
                font-size: 13px;
                min-width: 900px;
            }
            
            tbody td {
                padding: 12px 14px;
                font-size: 13px;
            }

            .status-form {
                flex-direction: column;
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

    <!-- ===== PAGE HEADER - NO DASHBOARD BUTTON ===== -->
    <div class="page-header">
        <div class="title">
            <i class="fas fa-shopping-cart"></i> Purchases
            <span class="badge"><?php echo $total_orders; ?> Orders</span>
        </div>
        <!-- Dashboard Button REMOVED -->
    </div>

    <!-- ===== STATISTICS ===== -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-box"></i></div>
            <div class="stat-number"><?php echo $stats['total'] ?? 0; ?></div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card pending">
            <div class="stat-icon"><i class="fas fa-clock"></i></div>
            <div class="stat-number"><?php echo $stats['pending'] ?? 0; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card confirmed">
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-number"><?php echo $stats['confirmed'] ?? 0; ?></div>
            <div class="stat-label">Confirmed</div>
        </div>
        <div class="stat-card processing">
            <div class="stat-icon"><i class="fas fa-cogs"></i></div>
            <div class="stat-number"><?php echo $stats['processing'] ?? 0; ?></div>
            <div class="stat-label">Processing</div>
        </div>
        <div class="stat-card shipped">
            <div class="stat-icon"><i class="fas fa-truck"></i></div>
            <div class="stat-number"><?php echo $stats['shipped'] ?? 0; ?></div>
            <div class="stat-label">Shipped</div>
        </div>
        <div class="stat-card delivered">
            <div class="stat-icon"><i class="fas fa-check-double"></i></div>
            <div class="stat-number"><?php echo $stats['delivered'] ?? 0; ?></div>
            <div class="stat-label">Delivered</div>
        </div>
        <div class="stat-card cancelled">
            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            <div class="stat-number"><?php echo $stats['cancelled'] ?? 0; ?></div>
            <div class="stat-label">Cancelled</div>
        </div>
    </div>

    <!-- ===== ALERT ===== -->
    <?php if(isset($msg)): ?>
        <div class="alert alert-<?php echo $msg_type; ?>" id="alertMsg">
            <i class="fas <?php echo $msg_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <!-- ===== FILTERS ===== -->
    <div class="filters">
        <form method="GET" action="" style="display: flex; gap: 12px; flex-wrap: wrap; width: 100%; align-items: center;">
            <select name="status">
                <option value="">All Status</option>
                <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="confirmed" <?php echo $status_filter == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                <option value="processing" <?php echo $status_filter == 'processing' ? 'selected' : ''; ?>>Processing</option>
                <option value="shipped" <?php echo $status_filter == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                <option value="delivered" <?php echo $status_filter == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                <option value="cancelled" <?php echo $status_filter == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            
            <input type="date" name="date" value="<?php echo $date_filter; ?>">
            
            <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="purchases.php" class="btn-reset">
                <i class="fas fa-times"></i> Reset
            </a>
        </form>
    </div>

    <!-- ===== TABLE ===== -->
    <div class="table-wrapper">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> Order ID</th>
                        <th><i class="fas fa-user"></i> Customer</th>
                        <th><i class="fas fa-phone"></i> Phone</th>
                        <th><i class="fas fa-calendar"></i> Date</th>
                        <th><i class="fas fa-rupee-sign"></i> Total</th>
                        <th><i class="fas fa-box"></i> Items</th>
                        <th><i class="fas fa-info-circle"></i> Status</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_orders > 0): ?>
                        <?php while($order = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="order-id">#<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                <td>
                                    <div class="customer-name"><?php echo htmlspecialchars($order['username'] ?? 'Guest'); ?></div>
                                    <div class="customer-address"><i class="fas fa-map-marker-alt" style="font-size: 11px;"></i> <?php echo htmlspecialchars(substr($order['address'] ?? 'N/A', 0, 30)) . (strlen($order['address'] ?? '') > 30 ? '...' : ''); ?></div>
                                </td>
                                <td>
                                    <div class="customer-phone"><?php echo htmlspecialchars($order['phone'] ?? 'N/A'); ?></div>
                                    <div class="customer-phone" style="font-size: 11px; color: #6b4b5e;">
                                        <i class="fas fa-credit-card"></i> <?php echo htmlspecialchars($order['payment_method'] ?? 'N/A'); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-text"><?php echo date('d M Y', strtotime($order['order_date'])); ?></div>
                                    <div class="time-text"><?php echo date('h:i A', strtotime($order['order_date'])); ?></div>
                                </td>
                                <td class="amount-text">₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><span class="items-count"><?php echo $order['item_count'] ?? 0; ?></span></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-btns">
                                        <!-- Status Update Form -->
                                        <form method="POST" class="status-form">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status">
                                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="confirmed" <?php echo $order['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn-update-status">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                        </form>
                                        <a href="?delete=<?php echo $order['id']; ?>" class="btn-delete" onclick="return confirm('Delete this order and all its items?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">
                                <div class="no-data">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h3>No Orders Found</h3>
                                    <p>No purchase orders available.</p>
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
// Auto hide alert message
setTimeout(function() {
    const msg = document.getElementById('alertMsg');
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

console.log('✅ Purchases Management Loaded');
console.log('📊 Total Orders: <?php echo $total_orders; ?>');
</script>

</body>
</html>