<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "golden_crust");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// If user is not logged in, redirect to login
if ($user_id == 0) {
    echo "<script>alert('Please login first');window.location='login.php';</script>";
    exit();
}

// Get user details

$stmt = mysqli_prepare($conn, "SELECT username, email, mobileno, address FROM users WHERE id = ?");

if (!$stmt) {
    die("SQL Error (User Query): " . mysqli_error($conn)); 
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user_result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($user_result);
mysqli_stmt_close($stmt);

// Get cart items
$stmt = mysqli_prepare($conn, "SELECT * FROM cart WHERE user_id = ?");

if (!$stmt) {
    die("SQL Error (Cart Query): " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$cart_result = mysqli_stmt_get_result($stmt);
$cart_items = [];
$total_amount = 0;

while ($row = mysqli_fetch_assoc($cart_result)) {
    $cart_items[] = $row;
    $total_amount += (float)$row['total'];
}
mysqli_stmt_close($stmt);

// If cart is empty, redirect to cakes page
if (empty($cart_items)) {
    echo "<script>alert('Your cart is empty!');window.location='cakes.php';</script>";
    exit();
}

/* ================= IMAGE PATH ================= */
$image_folder = "/golden_crust/project/uploads/project_image/";

// ====== PROCESS CHECKOUT ======
if (isset($_POST['place_order'])) {
    // Get form data
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? $user['address'] ?? '');
    
    
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? $user['mobileno'] ?? '');
    
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? 'COD');
    $order_notes = mysqli_real_escape_string($conn, $_POST['order_notes'] ?? '');
    
    // Validate
    if (empty($address) || empty($phone)) {
        $error = "Please fill in all required fields!";
    } else {
        // ---------- PAYMENT METHOD SPECIFIC LOGIC ----------
        $payment_status = 'Pending';
        $payment_message = '';
        
        switch($payment_method) {
            case 'COD':
                $payment_status = 'Pending';
                $payment_message = 'Cash on Delivery - Pay when you receive';
                break;
            case 'Online':
                $payment_status = 'Paid';
                $payment_message = 'Online Payment Successful';
                break;
            case 'UPI':
                $payment_status = 'Paid';
                $payment_message = 'UPI Payment Successful';
                break;
            default:
                $payment_status = 'Pending';
                $payment_message = 'Payment method selected';
        }
        
        // Insert order into orders table
        $order_date = date('Y-m-d H:i:s');
        $status = $payment_status; 
        
        $stmt = mysqli_prepare($conn, "INSERT INTO orders (user_id, username, total_amount, address, phone, payment_method, order_notes, order_date, status, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        
        mysqli_stmt_bind_param($stmt, "isdsssssss", 
            $user_id,            // i - Integer
            $user['username'],   // s - String
            $total_amount,       // d - Double/Decimal
            $address,            // s - String
            $phone,              // s - String
            $payment_method,     // s - String
            $order_notes,        // s - String
            $order_date,         // s - String
            $status,             // s - String
            $payment_status      // s - String (Newly added)
        );
        
        if (mysqli_stmt_execute($stmt)) {
            $order_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            // Insert order items
            foreach ($cart_items as $item) {
                $stmt2 = mysqli_prepare($conn, "INSERT INTO order_items (order_id, product_id, itemname, price, quantity, total, img) VALUES (?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt2, "iisdiss", 
                    $order_id,
                    $item['product_id'],
                    $item['itemname'],
                    $item['price'],
                    $item['quantity'],
                    $item['total'],
                    $item['img']
                );
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);
            }
            
            // Clear cart
            $stmt3 = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt3, "i", $user_id);
            mysqli_stmt_execute($stmt3);
            mysqli_stmt_close($stmt3);
            
            // Different messages based on payment method
            $success_message = '';
            switch($payment_method) {
                case 'COD':
                    $success_message = "Order placed successfully! 🎉 Pay ₹" . number_format($total_amount, 2) . " when you receive your order. Order ID: #" . $order_id;
                    break;
                case 'Online':
                    $success_message = "Payment successful! 💳 Order confirmed. Order ID: #" . $order_id;
                    break;
                case 'UPI':
                    $success_message = "UPI payment successful! 📱 Order confirmed. Order ID: #" . $order_id;
                    break;
            }
            
            echo "<script>alert('" . $success_message . "');window.location='order_success.php?order_id=" . $order_id . "';</script>";
            exit();
        } else {
            $error = "Failed to place order. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout - Golden Crust</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="checkout-container">
    <h1><i class="fas fa-shopping-bag"></i> Checkout</h1>
    <p class="subtitle">Review your order and complete your purchase</p>

    <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <!-- Order Summary -->
    <div class="order-summary">
        <h3><i class="fas fa-list"></i> Order Summary</h3>
        
        <?php foreach ($cart_items as $item): ?>
            <div class="order-item">
                <div class="item-info">
                    <?php 
                    $img_path = !empty($item['img']) ? $image_folder . $item['img'] : '';
                    if(!empty($img_path) && file_exists($_SERVER['DOCUMENT_ROOT'] . $img_path)): 
                    ?>
                        <img src="<?php echo $img_path; ?>" alt="<?php echo htmlspecialchars($item['itemname']); ?>" class="item-img">
                    <?php else: ?>
                        <div class="item-img-placeholder">
                            <i class="fas fa-cake"></i>
                        </div>
                    <?php endif; ?>
                    <div>
                        <div class="item-name"><?php echo htmlspecialchars($item['itemname']); ?></div>
                        <div class="item-qty">Qty: <?php echo $item['quantity']; ?> × ₹<?php echo number_format($item['price'], 2); ?></div>
                    </div>
                </div>
                <div class="item-total">₹<?php echo number_format($item['total'], 2); ?></div>
            </div>
        <?php endforeach; ?>
        
        <div class="total-row">
            <span>Total Amount</span>
            <span class="total-amount">₹<?php echo number_format($total_amount, 2); ?></span>
        </div>
    </div>

    <!-- Checkout Form -->
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" readonly disabled>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly disabled>
            </div>
        </div>

        <div class="form-group">
            <label>Delivery Address <span class="required">*</span></label>
            <textarea name="address" required placeholder="Enter your full delivery address"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label>Phone Number <span class="required">*</span></label>
            <!-- FIX: $user['phone'] ne badle $user['mobileno'] karyu chhe -->
            <input type="tel" name="phone" required placeholder="Enter your phone number" value="<?php echo htmlspecialchars($user['mobileno'] ?? ''); ?>">
        </div>

        <div class="form-group">
            <label>Payment Method <span class="required">*</span></label>
            <div class="payment-options">
                <div class="payment-option">
                    <input type="radio" name="payment_method" id="cod" value="COD" checked>
                    <label for="cod"><i class="fas fa-money-bill-wave"></i> Cash on Delivery</label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="payment_method" id="online" value="Online">
                    <label for="online"><i class="fas fa-credit-card"></i> Online Payment</label>
                </div>
                <div class="payment-option">
                    <input type="radio" name="payment_method" id="upi" value="UPI">
                    <label for="upi"><i class="fas fa-mobile-alt"></i> UPI</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Order Notes (Optional)</label>
            <textarea name="order_notes" placeholder="Any special instructions..."></textarea>
        </div>

        <div class="button-row">
            <a href="cart.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Cart
            </a>
            
            <button type="submit" name="place_order" class="btn btn-success">
                <i class="fas fa-check-circle"></i> Place Order
            </button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>