<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "golden_crust");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$order = null;
$order_items = [];

if ($order_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if ($order) {
        $stmt = mysqli_prepare($conn, "SELECT * FROM order_items WHERE order_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $items_result = mysqli_stmt_get_result($stmt);
        while ($item = mysqli_fetch_assoc($items_result)) {
            $order_items[] = $item;
        }
        mysqli_stmt_close($stmt);
    }
}

if (!$order) {
    echo "<script>alert('Order not found!');window.close();</script>";
    exit();
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$user = null;
if ($user_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT username, email, phone FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $user_result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($user_result);
    mysqli_stmt_close($stmt);
}

$method_icons = [
    'COD' => 'fa-money-bill-wave',
    'Online' => 'fa-credit-card',
    'UPI' => 'fa-mobile-alt'
];
$method_colors = [
    'COD' => '#f57c00',
    'Online' => '#1976d2',
    'UPI' => '#7b1fa2'
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Invoice #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/invoice.css">
</head>
<body>

<div class="invoice-print-wrapper">
    <!-- Header -->
    <div class="invoice-print-header">
        <div class="brand">
            <h1><i class="fas fa-cake"></i> Golden Crust Cake's</h1>
            
        </div>
        <div class="invoice-id">
            <h2>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></h2>
            <span><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></span>
        </div>
    </div>
    
    <!-- Body -->
    <div class="invoice-print-body">
        <!-- Info -->
        <div class="print-info-grid">
            <div>
                <h4><i class="fas fa-user"></i> Billed To</h4>
                <p><?php echo htmlspecialchars($user['username'] ?? 'Guest'); ?></p>
                <p><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
                <p>📞 <?php echo htmlspecialchars($order['phone']); ?></p>
            </div>
            <div>
                <h4><i class="fas fa-truck"></i> Delivery Address</h4>
                <p><?php echo htmlspecialchars($order['address']); ?></p>
                <p>Status: <span class="badge <?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span></p>
            </div>
        </div>
        
        <!-- Items -->
        <table class="print-items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['itemname']); ?></td>
                    <td style="text-align:center;"><?php echo $item['quantity']; ?></td>
                    <td style="text-align:right;">₹<?php echo number_format($item['price'], 2); ?></td>
                    <td style="text-align:right;color:#d63384;font-weight:600;">₹<?php echo number_format($item['total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <!-- Totals -->
        <div class="print-totals">
            <div class="total-row">
                <span>Subtotal</span>
                <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
            <div class="total-row">
                <span>Delivery Charges</span>
                <span class="free">FREE</span>
            </div>
            <div class="total-row grand">
                <span>Grand Total</span>
                <span class="amount">₹<?php echo number_format($order['total_amount'], 2); ?></span>
            </div>
            
            <div class="payment-row">
                <div>
                    <i class="fas <?php echo $method_icons[$order['payment_method']] ?? 'fa-wallet'; ?>"></i>
                    <?php echo htmlspecialchars($order['payment_method']); ?>
                </div>
                <div class="status <?php echo $order['payment_method'] == 'COD' ? 'pending' : 'paid'; ?>">
                    <?php if ($order['payment_method'] == 'COD'): ?>
                        <i class="fas fa-clock"></i> Pending Payment
                    <?php else: ?>
                        <i class="fas fa-check-circle"></i> Payment Received
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($order['order_notes'])): ?>
            <div class="notes">
                <span>Notes:</span> <?php echo htmlspecialchars($order['order_notes']); ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="print-footer">
            <p class="thanks">🎂 Thank you for your order!</p>
            <p>For queries: support@goldencrust.com | +91 98765 43210</p>
        </div>
    </div>
</div>

<!-- ===== AUTO PRINT SCRIPT ===== -->
<script>
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 800);
    };
</script>

</body>
</html>