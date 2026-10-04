<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "golden_crust");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$user_id = $_SESSION['user_id'] ?? 0;

if ($order_id == 0 || $user_id == 0) {
    echo "<p>Invalid request.</p>";
    exit();
}

// Get order details
$order_query = "SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id";
$order_res = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_res);

if (!$order) {
    echo "<p>Order not found.</p>";
    exit();
}

// Get order items
$items_query = "SELECT * FROM order_items WHERE order_id = $order_id";
$items_res = mysqli_query($conn, $items_query);

$image_folder = "/Golden_Crust/project/admin/uploads/";

// Output HTML
?>
<div class="order-summary-modal">
    <div class="summary-row">
        <span class="summary-label">Order ID:</span>
        <span class="summary-value">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Date:</span>
        <span class="summary-value"><?php echo date('d M Y, h:i A', strtotime($order['order_date'])); ?></span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Status:</span>
        <span class="summary-value status-<?php echo strtolower($order['status']); ?>"><?php echo $order['status']; ?></span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Payment Method:</span>
        <span class="summary-value"><?php echo $order['payment_method']; ?></span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Delivery Address:</span>
        <span class="summary-value"><?php echo htmlspecialchars($order['address']); ?></span>
    </div>
    <div class="summary-row">
        <span class="summary-label">Phone:</span>
        <span class="summary-value"><?php echo htmlspecialchars($order['phone']); ?></span>
    </div>
    <?php if(!empty($order['order_notes'])): ?>
        <div class="summary-row">
            <span class="summary-label">Notes:</span>
            <span class="summary-value"><?php echo htmlspecialchars($order['order_notes']); ?></span>
        </div>
    <?php endif; ?>
    <div class="summary-row total-row-modal">
        <span class="summary-label">Total Amount:</span>
        <span class="summary-value total-amount-modal">₹<?php echo number_format($order['total_amount'], 2); ?></span>
    </div>
</div>

<h3 class="items-title"><i class="fas fa-list"></i> Order Items</h3>
<div class="items-grid-modal">
    <?php while($item = mysqli_fetch_assoc($items_res)): 
        $img_path = !empty($item['img']) ? $image_folder . $item['img'] : '';
    ?>
        <div class="item-card-modal">
            <div class="item-image-modal">
                <?php if(!empty($img_path) && file_exists($_SERVER['DOCUMENT_ROOT'] . $img_path)): ?>
                    <img src="<?php echo $img_path; ?>" alt="<?php echo htmlspecialchars($item['itemname']); ?>">
                <?php else: ?>
                    <div class="item-placeholder-modal"><i class="fas fa-cake"></i></div>
                <?php endif; ?>
            </div>
            <div class="item-details-modal">
                <div class="item-name-modal"><?php echo htmlspecialchars($item['itemname']); ?></div>
                <div class="item-meta-modal">
                    <span>Qty: <?php echo $item['quantity']; ?></span>
                    <span>×</span>
                    <span>₹<?php echo number_format($item['price'], 2); ?></span>
                </div>
                <div class="item-total-modal">₹<?php echo number_format($item['total'], 2); ?></div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php
mysqli_close($conn);
?>