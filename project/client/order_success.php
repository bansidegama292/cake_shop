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
    // Get order details
    $stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    // Get order items
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

// If no order found, redirect
if (!$order) {
    echo "<script>alert('Order not found!');window.location='cakes.php';</script>";
    exit();
}

// Get user details
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
$user = null;
if ($user_id > 0) {
    
    $stmt = mysqli_prepare($conn, "SELECT username, email, mobileno FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $user_result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($user_result);
    mysqli_stmt_close($stmt);
}

// ====== CONTINUE SHOPPING ======
if (isset($_GET['continue_shopping'])) {
    $order_id = (int)$_GET['continue_shopping'];
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM order_items WHERE order_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $items_result = mysqli_stmt_get_result($stmt);
    
    $added_count = 0;
    while ($item = mysqli_fetch_assoc($items_result)) {
        $check_stmt = mysqli_prepare($conn, "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $item['product_id']);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if ($cart_item = mysqli_fetch_assoc($check_result)) {
            $new_qty = $cart_item['quantity'] + $item['quantity'];
            $update_stmt = mysqli_prepare($conn, "UPDATE cart SET quantity = ?, total = price * ? WHERE id = ?");
            mysqli_stmt_bind_param($update_stmt, "iii", $new_qty, $new_qty, $cart_item['id']);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        } else {
            $insert_stmt = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, itemname, price, quantity, total, img) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $total = $item['price'] * $item['quantity'];
            mysqli_stmt_bind_param($insert_stmt, "iisdiis", 
                $user_id,
                $item['product_id'],
                $item['itemname'],
                $item['price'],
                $item['quantity'],
                $total,
                $item['img']
            );
            mysqli_stmt_execute($insert_stmt);
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($check_stmt);
        $added_count++;
    }
    mysqli_stmt_close($stmt);
    
    if ($added_count > 0) {
        echo "<script>alert('🛒 " . $added_count . " item(s) added to cart successfully!');window.location='cart.php';</script>";
    }
    exit();
}


$delivery_date = date('l, F j, Y', strtotime($order['order_date'] . ' + 3 days'));
$delivery_estimate = date('M j', strtotime($order['order_date'] . ' + 2 days')) . ' - ' . date('M j', strtotime($order['order_date'] . ' + 4 days'));


$status_icons = [
    'Pending' => 'fa-clock',
    'Processing' => 'fa-spinner',
    'Shipped' => 'fa-truck',
    'Delivered' => 'fa-check-circle',
    'Cancelled' => 'fa-times-circle'
];


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
    <title>Order Success - Golden Crust</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/success.css">
</head>
<body>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="success-wrapper">
    <div class="success-container">
        
        <!-- ===== CONFETTI ANIMATION ===== -->
        <div class="confetti-container" id="confettiContainer"></div>
        
        <!-- ===== SUCCESS HEADER ===== -->
        <div class="success-header">
            <div class="success-icon-wrapper">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
            </div>
            <h1 class="success-title">Order Placed! 🎉</h1>
            <p class="success-subtitle">Thank you for your order! We're baking happiness just for you.</p>
        </div>
        
        <!-- ===== ORDER ID ===== -->
        <div class="order-id-section">
            <div class="order-id-card">
                <span class="order-id-label"><i class="fas fa-hashtag"></i> Order ID</span>
                <span class="order-id-number">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                <span class="order-id-date"><i class="far fa-calendar-alt"></i> <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></span>
            </div>
        </div>
        
        <!-- ===== ORDER STATUS & PROGRESS ===== -->
        <div class="status-progress-section">
            <div class="status-badge-container">
                <div class="status-badge <?php echo strtolower($order['status']); ?>">
                    <i class="fas <?php echo $status_icons[$order['status']] ?? 'fa-info-circle'; ?>"></i>
                    <?php echo $order['status']; ?>
                </div>
            </div>
            
            <div class="progress-steps">
                <div class="step <?php echo in_array($order['status'], ['Pending', 'Processing', 'Shipped', 'Delivered']) ? 'active' : ''; ?>">
                    <div class="step-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="step-label">Order Placed</div>
                    <div class="step-time"><?php echo date('h:i A', strtotime($order['order_date'])); ?></div>
                </div>
                <div class="step-line <?php echo in_array($order['status'], ['Processing', 'Shipped', 'Delivered']) ? 'active' : ''; ?>"></div>
                <div class="step <?php echo in_array($order['status'], ['Processing', 'Shipped', 'Delivered']) ? 'active' : ''; ?>">
                    <div class="step-icon"><i class="fas fa-cog"></i></div>
                    <div class="step-label">Processing</div>
                    <div class="step-time"><?php echo in_array($order['status'], ['Processing', 'Shipped', 'Delivered']) ? date('h:i A', strtotime($order['order_date'] . ' + 1 hour')) : 'Pending'; ?></div>
                </div>
                <div class="step-line <?php echo in_array($order['status'], ['Shipped', 'Delivered']) ? 'active' : ''; ?>"></div>
                <div class="step <?php echo in_array($order['status'], ['Shipped', 'Delivered']) ? 'active' : ''; ?>">
                    <div class="step-icon"><i class="fas fa-truck"></i></div>
                    <div class="step-label">Shipped</div>
                    <div class="step-time"><?php echo in_array($order['status'], ['Shipped', 'Delivered']) ? date('h:i A', strtotime($order['order_date'] . ' + 1 day')) : 'Pending'; ?></div>
                </div>
                <div class="step-line <?php echo $order['status'] == 'Delivered' ? 'active' : ''; ?>"></div>
                <div class="step <?php echo $order['status'] == 'Delivered' ? 'active' : ''; ?>">
                    <div class="step-icon"><i class="fas fa-home"></i></div>
                    <div class="step-label">Delivered</div>
                    <div class="step-time"><?php echo $order['status'] == 'Delivered' ? date('h:i A', strtotime($order['order_date'] . ' + 3 days')) : 'Pending'; ?></div>
                </div>
            </div>
        </div>
        
        <!-- ===== DELIVERY ESTIMATE ===== -->
        <div class="delivery-estimate-card">
            <div class="delivery-icon">
                <i class="fas fa-truck-fast"></i>
            </div>
            <div class="delivery-info">
                <h4>Expected Delivery</h4>
                <p class="delivery-date"><?php echo $delivery_date; ?></p>
                <span class="delivery-range"><i class="far fa-clock"></i> <?php echo $delivery_estimate; ?></span>
            </div>
            <div class="delivery-status">
                <span class="delivery-badge">On Time</span>
            </div>
        </div>
        
        <!-- ===== ORDER SUMMARY ===== -->
        <div class="order-summary-card">
            <div class="summary-header">
                <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                <span class="items-count"><?php echo count($order_items); ?> item(s)</span>
            </div>
            
            <div class="summary-body">
                <?php foreach ($order_items as $item): ?>
                <div class="summary-item">
                    <div class="item-image">
                        <?php if (!empty($item['img'])): ?>
                            <img src="/Golden_Crust/project/admin/uploads/<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['itemname']); ?>">
                        <?php else: ?>
                            <div class="item-placeholder"><i class="fas fa-cake"></i></div>
                        <?php endif; ?>
                    </div>
                    <div class="item-details">
                        <div class="item-name"><?php echo htmlspecialchars($item['itemname']); ?></div>
                        <div class="item-meta">
                            <span class="item-qty"><i class="fas fa-times"></i> <?php echo $item['quantity']; ?></span>
                            <span class="item-price">₹<?php echo number_format($item['price'], 2); ?></span>
                        </div>
                    </div>
                    <div class="item-total">₹<?php echo number_format($item['total'], 2); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="summary-footer">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Delivery Charges</span>
                    <span class="free"><i class="fas fa-gift"></i> FREE</span>
                </div>
                <div class="summary-row total">
                    <span>Total Amount</span>
                    <span class="total-amount">₹<?php echo number_format($order['total_amount'], 2); ?></span>
                </div>
            </div>
        </div>
        
        <!-- ===== DELIVERY & PAYMENT INFO ===== -->
        <div class="info-grid">
            <div class="info-card delivery-info-card">
                <div class="info-card-header">
                    <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <h4>Delivery Details</h4>
                </div>
                <div class="info-card-body">
                    <p class="info-address"><?php echo htmlspecialchars($order['address']); ?></p>
                    <div class="info-meta"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($order['phone']); ?></div>
                    <?php if ($user): ?>
                    <div class="info-meta"><i class="fas fa-user"></i> <?php echo htmlspecialchars($user['username']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($user['email'])): ?>
                    <div class="info-meta"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="info-card payment-info-card">
                <div class="info-card-header">
                    <div class="info-icon"><i class="fas fa-credit-card"></i></div>
                    <h4>Payment Details</h4>
                </div>
                <div class="info-card-body">
                    <div class="payment-method">
                        <i class="fas <?php echo $method_icons[$order['payment_method']] ?? 'fa-wallet'; ?>" style="color: <?php echo $method_colors[$order['payment_method']] ?? '#888'; ?>;"></i>
                        <span class="method-name"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                    </div>
                    <div class="payment-status <?php echo $order['payment_method'] == 'COD' ? 'pending' : 'paid'; ?>">
                        <?php if ($order['payment_method'] == 'COD'): ?>
                            <i class="fas fa-clock"></i> Pay on Delivery
                        <?php else: ?>
                            <i class="fas fa-check-circle"></i> Payment Successful
                        <?php endif; ?>
                    </div>
                    <div class="payment-amount">
                        <span>Amount Paid</span>
                        <strong>₹<?php echo number_format($order['total_amount'], 2); ?></strong>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ===== ACTION BUTTONS ===== -->
        <div class="action-buttons">
            <a href="?continue_shopping=<?php echo $order['id']; ?>" class="btn btn-success">
                <i class="fas fa-cart-plus"></i> Continue Shopping
            </a>
            <!-- ===== PRINT RECEIPT BUTTON ===== -->
            <button onclick="printReceipt()" class="btn btn-print">
                <i class="fas fa-print"></i> Print Receipt
            </button>
        </div>
        
        <!-- ===== TRUST BADGES ===== -->
        <div class="trust-badges">
            <div class="trust-item"><i class="fas fa-shield-alt"></i> Secure Payment</div>
            <div class="trust-item"><i class="fas fa-truck"></i> Fast Delivery</div>
            <div class="trust-item"><i class="fas fa-headset"></i> 24/7 Support</div>
            <div class="trust-item"><i class="fas fa-undo-alt"></i> Easy Returns</div>
        </div>
        
    </div>
</div>

<!-- ===== CONFETTI SCRIPT ===== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    createConfetti();
});

function createConfetti() {
    const container = document.getElementById('confettiContainer');
    const colors = ['#d63384', '#f57c00', '#1976d2', '#2e7d32', '#fbc02d', '#7b1fa2', '#dc3545', '#20c997'];
    const shapes = ['■', '●', '▲', '★', '♦', '♥'];
    
    for (let i = 0; i < 100; i++) {
        const confetti = document.createElement('div');
        confetti.className = 'confetti-piece';
        confetti.textContent = shapes[Math.floor(Math.random() * shapes.length)];
        confetti.style.left = Math.random() * 100 + '%';
        confetti.style.top = '-10%';
        confetti.style.color = colors[Math.floor(Math.random() * colors.length)];
        confetti.style.fontSize = (Math.random() * 20 + 10) + 'px';
        confetti.style.animationDelay = (Math.random() * 3) + 's';
        confetti.style.animationDuration = (Math.random() * 3 + 2) + 's';
        confetti.style.transform = 'rotate(' + (Math.random() * 360) + 'deg)';
        container.appendChild(confetti);
    }
}

// ===== PRINT RECEIPT FUNCTION =====
function printReceipt() {
    
    var orderId = <?php echo $order['id']; ?>;
    var printWindow = window.open('invoice.php?order_id=' + orderId, '_blank', 'width=800,height=600');
    printWindow.onload = function() {
        setTimeout(function() {
            printWindow.print();
        }, 1000);
    };
}
</script>


</body>
</html>