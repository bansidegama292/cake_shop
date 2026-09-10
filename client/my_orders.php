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

$sql = "SELECT username FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// ----- IMAGE PATH -----
$image_folder = "/golden_crust_cake_shop/project/uploads/project_image/";

// ====== PAGINATION ======
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$count_query = "SELECT COUNT(*) as total FROM orders WHERE user_id = $user_id";
$count_result = mysqli_query($conn, $count_query);
$total_orders = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_orders / $limit);

$order_query = "SELECT * FROM orders WHERE user_id = $user_id ORDER BY id DESC LIMIT $limit OFFSET $offset";
$orders_result = mysqli_query($conn, $order_query);

// ====== CONTINUE ORDER ======
if (isset($_GET['continue_order'])) {
    $order_id = (int)$_GET['continue_order'];
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
    echo "<script>alert('🛒 " . $added_count . " item(s) added to cart successfully!');window.location='cart.php';</script>";
    exit();
}

// ====== CANCEL ORDER ======
if (isset($_GET['cancel_order'])) {
    $order_id = (int)$_GET['cancel_order'];
    $stmt1 = mysqli_prepare($conn, "DELETE FROM order_items WHERE order_id = ?");
    mysqli_stmt_bind_param($stmt1, "i", $order_id);
    mysqli_stmt_execute($stmt1);
    mysqli_stmt_close($stmt1);
    $stmt2 = mysqli_prepare($conn, "DELETE FROM orders WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($stmt2, "ii", $order_id, $user_id);
    mysqli_stmt_execute($stmt2);
    mysqli_stmt_close($stmt2);
    echo "<script>alert('❌ Order cancelled and removed successfully!');window.location='my_orders.php';</script>";
    exit();
}

// ====== DO NOT CLOSE CONNECTION HERE ======
// mysqli_close($conn);   <-- REMOVE THIS LINE

?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders - Golden Crust</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/my_orders.css">
</head>
<body>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<div class="orders-container">
    <!-- Header -->
    <div class="orders-header">
        <h1><i class="fas fa-shopping-bag"></i> My Orders</h1>
        <div class="header-right">
            <p class="welcome-text">Welcome, <strong><?php echo htmlspecialchars($user['username'] ?? 'Guest'); ?></strong>!</p>
            <a href="cakes.php" class="btn btn-shop">
                <i class="fas fa-shopping-bag"></i> Shop More
            </a>
        </div>
    </div>

    <!-- Orders List -->
    <div class="orders-list">
        <?php if (mysqli_num_rows($orders_result) > 0): ?>
            <?php while ($order = mysqli_fetch_assoc($orders_result)): 
                // Fetch items for this order – connection is still open
                $items_query = "SELECT * FROM order_items WHERE order_id = " . $order['id'];
                $items_res = mysqli_query($conn, $items_query);
                $items = [];
                while ($item = mysqli_fetch_assoc($items_res)) {
                    $items[] = $item;
                }
                $item_count = count($items);
                $total_qty = array_sum(array_column($items, 'quantity'));
            ?>
                <div class="order-card <?php echo strtolower($order['status']); ?>">
                    <div class="order-header">
                        <div class="order-id">
                            <span class="order-number">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                            <span class="order-date">
                                <i class="far fa-calendar-alt"></i> 
                                <?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?>
                            </span>
                        </div>
                        <div class="order-status">
                            <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                <?php 
                                $status_icons = [
                                    'Pending' => '🛒',
                                    'Processing' => '⚙️',
                                    'Shipped' => '📦',
                                    'Delivered' => '✅',
                                    'Cancelled' => '❌'
                                ];
                                echo ($status_icons[$order['status']] ?? '📋') . ' ' . $order['status'];
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="order-body">
                        <div class="order-items-summary">
                            <i class="fas fa-cake"></i> 
                            <?php echo $total_qty; ?> item(s)
                        </div>
                        <div class="order-total">
                            <i class="fas fa-rupee-sign"></i> ₹<?php echo number_format($order['total_amount'], 2); ?>
                        </div>
                        <div class="order-payment">
                            <i class="fas <?php 
                                if($order['payment_method'] == 'COD') echo 'fa-money-bill-wave';
                                elseif($order['payment_method'] == 'Online') echo 'fa-credit-card';
                                else echo 'fa-mobile-alt';
                            ?>"></i>
                            <?php echo $order['payment_method']; ?>
                            <?php if($order['payment_method'] == 'COD'): ?>
                                <span class="payment-status pending">(Pending)</span>
                            <?php else: ?>
                                <span class="payment-status paid">(Paid)</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Item Thumbnails -->
                    <?php if($item_count > 0): ?>
                        <div class="order-item-images">
                            <?php 
                            $display_count = 0;
                            foreach($items as $item): 
                                if($display_count >= 4) break;
                                $img_path = !empty($item['img']) ? $image_folder . $item['img'] : '';
                            ?>
                                <div class="item-thumb">
                                    <?php if(!empty($img_path) && file_exists($_SERVER['DOCUMENT_ROOT'] . $img_path)): ?>
                                        <img src="<?php echo $img_path; ?>" alt="<?php echo htmlspecialchars($item['itemname']); ?>">
                                    <?php else: ?>
                                        <div class="thumb-placeholder"><i class="fas fa-cake"></i></div>
                                    <?php endif; ?>
                                </div>
                            <?php 
                                $display_count++;
                            endforeach; 
                            if($item_count > 4): ?>
                                <div class="item-thumb more">+<?php echo $item_count - 4; ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="order-actions">
                        <button class="btn btn-view" onclick="openOrderModal(<?php echo $order['id']; ?>)">
                            <i class="fas fa-eye"></i> View Details
                        </button>
                        <?php if ($order['status'] != 'Cancelled' && $order['status'] != 'Delivered'): ?>
                            <a href="?continue_order=<?php echo $order['id']; ?>" class="btn btn-continue" onclick="return confirm('🛒 Add all items from this order to cart?')">
                                <i class="fas fa-cart-plus"></i> Add Items
                            </a>
                            <a href="?cancel_order=<?php echo $order['id']; ?>" class="btn btn-cancel" onclick="return confirmCancel()">
                                <i class="fas fa-trash-alt"></i> Cancel Order
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>" class="page-link"><i class="fas fa-chevron-left"></i> Previous</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="page-link">Next <i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="empty-orders">
                <i class="fas fa-shopping-bag" style="font-size: 60px; color: #d0c8c0;"></i>
                <h3>No orders found!</h3>
                <p>Start shopping to see your orders here.</p>
                <a href="cakes.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal -->
<div id="orderModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-shopping-bag"></i> Order Details</h2>
            <button class="modal-close" onclick="closeOrderModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeOrderModal()">Close</button>
        </div>
    </div>
</div>

<script>
function confirmCancel() {
    return confirm('⚠️ Are you sure you want to cancel this order?\n\nThis action cannot be undone!');
}

function openOrderModal(orderId) {
    fetch('get_order_details.php?order_id=' + orderId)
        .then(response => response.text())
        .then(data => {
            document.getElementById('modalBody').innerHTML = data;
            document.getElementById('orderModal').style.display = 'flex';
        })
        .catch(error => alert('Error loading order details.'));
}

function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
}

document.addEventListener('keydown', function(e) {
    if(e.key === 'Escape') closeOrderModal();
});
document.getElementById('orderModal').addEventListener('click', function(e) {
    if(e.target === this) closeOrderModal();
});
</script>

<?php include 'includes/footer.php'; ?>

<?php
// Close connection at the very end (optional)
mysqli_close($conn);
?>
</body>
</html>