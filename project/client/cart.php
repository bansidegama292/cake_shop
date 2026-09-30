<?php
session_start();
$conn = mysqli_connect("localhost","root","","golden_crust");

if(!$conn)
{
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = $_SESSION['user_id'] ?? 0;

// If not logged in, redirect to login
if($user_id == 0){
    echo "<script>alert('Please login first');window.location='login.php';</script>";
    exit();
}

// Get username
$user_query = mysqli_query($conn, "SELECT username FROM users WHERE id = '$user_id'");
if($user_query && mysqli_num_rows($user_query) > 0) {
    $user_data = mysqli_fetch_assoc($user_query);
    $username = $user_data['username'] ?? 'User';
} else {
    $username = 'User';
}

/* ================= REMOVE FROM CART ================= */
if(isset($_GET['remove'])){
    $cart_id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'");
    header("Location: cart.php");
    exit();
}

/* ================= CLEAR CART ================= */
if(isset($_GET['clear'])){
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");
    header("Location: cart.php");
    exit();
}

/* ================= FETCH CART ITEMS ================= */
$cart_items = [];
$grand_total = 0;
$total_items = 0;

$query = "SELECT * FROM cart WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);

if($result) {
    while($row = mysqli_fetch_assoc($result)){
        $cart_items[] = $row;
        $grand_total += (float)$row['total'];
        $total_items += (int)$row['quantity'];
    }
}

// Delivery charges
$delivery_charge = ($grand_total > 0 && $grand_total < 500) ? 50 : 0;
$final_total = $grand_total + $delivery_charge;

// Get cart count for badge
$cart_count = 0;
$cart_count_query = mysqli_query($conn, "SELECT SUM(quantity) AS total FROM cart WHERE user_id = '$user_id'");
if($cart_count_query && mysqli_num_rows($cart_count_query) > 0) {
    $cart_count_data = mysqli_fetch_assoc($cart_count_query);
    $cart_count = (int)($cart_count_data['total'] ?? 0);
}

/* ================= FETCH WISHLIST ITEMS ================= */
$wishlist_items = [];
$wishlist_count = 0;

$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'wishlist'");
if($table_check && mysqli_num_rows($table_check) > 0) {
    $wishlist_query = "
        SELECT w.*, c.itemname, c.price, c.img, c.description, c.stock 
        FROM wishlist w 
        LEFT JOIN cakes c ON w.product_id = c.id 
        WHERE w.user_id = '$user_id'
    ";
    $wishlist_result = mysqli_query($conn, $wishlist_query);
    
    if($wishlist_result && mysqli_num_rows($wishlist_result) > 0) {
        while($row = mysqli_fetch_assoc($wishlist_result)){
            $wishlist_items[] = $row;
        }
    }
}
$wishlist_count = count($wishlist_items);

/* ================= ADD FROM WISHLIST TO CART ================= */
if(isset($_GET['add_from_wishlist'])){
    $product_id = intval($_GET['add_from_wishlist']);
    $qty = intval($_GET['qty'] ?? 1);
    if($qty < 1) $qty = 1;
    
    $product_query = mysqli_query($conn, "SELECT * FROM cakes WHERE id = '$product_id'");
    if($product_query && mysqli_num_rows($product_query) > 0) {
        $product = mysqli_fetch_assoc($product_query);
        
        if($product){
            $itemname = mysqli_real_escape_string($conn, $product['itemname']);
            $price = (float)$product['price'];
            $total = $price * $qty;
            $img = $product['img'] ?? null;
            
            $check = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
            
            if($check && mysqli_num_rows($check) > 0){
                $existing = mysqli_fetch_assoc($check);
                $new_qty = (int)$existing['quantity'] + $qty;
                $new_total = $price * $new_qty;
                mysqli_query($conn, "UPDATE cart SET quantity='$new_qty', total='$new_total' WHERE id='{$existing['id']}'");
            }else{
                $insert_query = "INSERT INTO cart (user_id, product_id, itemname, price, quantity, total, img, username) 
                                 VALUES ('$user_id', '$product_id', '$itemname', '$price', '$qty', '$total', '$img', '$username')";
                mysqli_query($conn, $insert_query);
            }
            
            echo "<script>alert('Added to Cart 🛒');window.location='cart.php';</script>";
            exit();
        }
    }
}

/* ================= REMOVE FROM WISHLIST ================= */
if(isset($_GET['remove_wishlist'])){
    $product_id = intval($_GET['remove_wishlist']);
    mysqli_query($conn, "DELETE FROM wishlist WHERE user_id='$user_id' AND product_id='$product_id'");
    header("Location: cart.php");
    exit();
}

/* ================= IMAGE PATH ================= */
$image_folder = "/Golden_Crust/project/admin/uploads/";
//$image_folder = "/golden_crust/project/uploads/project_image/";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart - Golden Crust</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="cart-container">

    <div class="cart-header">
        <h1>🛒 Your Shopping Cart</h1>
        <p>Review your items and proceed to checkout</p>
        <div class="user-info">
            <i class="fas fa-user"></i> <?php echo htmlspecialchars($username); ?>
        </div>
    </div>

    <?php if(count($cart_items) > 0): ?>

        <div class="cart-table">
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($cart_items as $item): ?>
                    <tr>
                        <td>
                            <div class="product-info">
                                <?php 
                                $img_path = !empty($item['img']) ? $image_folder . $item['img'] : '';
                                if(!empty($img_path) && file_exists($_SERVER['DOCUMENT_ROOT'] . $img_path)): 
                                ?>
                                    <img src="<?php echo $img_path; ?>" alt="<?php echo htmlspecialchars($item['itemname']); ?>">
                                <?php else: ?>
                                    <div class="no-img">
                                        <i class="fas fa-cake"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="details">
                                    <div class="name"><?php echo htmlspecialchars($item['itemname']); ?></div>
                                    <div class="username">
                                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($item['username'] ?? 'User'); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="item-price">₹<?php echo number_format((float)$item['price'], 2); ?></td>
                        <td>
                            <span class="qty-display"><?php echo $item['quantity']; ?></span>
                        </td>
                        <td class="item-total">₹<?php echo number_format((float)$item['total'], 2); ?></td>
                        <td>
                            <a href="?remove=<?php echo $item['id']; ?>" class="remove-btn" onclick="return confirm('Remove this item from cart?')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="cart-summary">
            <div class="left">
                <span class="total-label">Total Amount:</span>
                <span class="total-amount">₹<?php echo number_format($grand_total, 2); ?></span>
                <?php if($delivery_charge > 0): ?>
                    <span style="font-size: 13px; color: #e67e22; background: rgba(255,165,0,0.08); padding: 4px 12px; border-radius: 20px;">
                        <i class="fas fa-truck"></i> +₹<?php echo number_format($delivery_charge, 2); ?> delivery
                    </span>
                <?php endif; ?>
            </div>
            <div class="right">
                <a href="?clear=1" class="btn-danger" onclick="return confirm('Clear all items from cart?')">
                    <i class="fas fa-trash-alt"></i> Clear Cart
                </a>
                <a href="cakes.php" class="btn-secondary">
                    <i class="fas fa-shopping-bag"></i> Continue
                </a>
                <a href="checkout.php" class="btn-primary">
                    <i class="fas fa-credit-card"></i> Checkout
                </a>
            </div>
        </div>

    <?php else: ?>

        <div class="cart-table">
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your Cart is Empty!</h2>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="cakes.php" class="btn-primary">
                    <i class="fas fa-shopping-bag"></i> Start Shopping
                </a>
            </div>
        </div>

    <?php endif; ?>

    <!-- Wishlist Section -->
    <?php if(!empty($wishlist_items)): ?>
    <div class="wishlist-section">
        <h2><i class="fas fa-heart"></i> Your Wishlist (<?php echo $wishlist_count; ?>)</h2>
        <p class="wishlist-subtitle">Quickly add your favorite items to cart ❤️</p>
        
        <div class="wishlist-grid">
            <?php foreach($wishlist_items as $item): 
                $stock = (int)($item['stock'] ?? 0);
                $price = number_format((float)($item['price'] ?? 0), 2);
                $img = !empty($item['img']) ? $image_folder . htmlspecialchars($item['img']) : '';
            ?>
                <div class="wishlist-item" data-product-id="<?php echo $item['product_id']; ?>">
                    <?php if(!empty($img) && file_exists($_SERVER['DOCUMENT_ROOT'] . $img)): ?>
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($item['itemname']); ?>">
                    <?php else: ?>
                        <div class="wishlist-no-img">
                            <i class="fas fa-cake"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="wishlist-info">
                        <div class="w-name"><?php echo htmlspecialchars($item['itemname'] ?? 'Product'); ?></div>
                        <div class="w-price">₹<?php echo $price; ?></div>
                        <div class="w-stock <?php echo $stock > 0 ? 'in-stock' : 'out-stock'; ?>">
                            <?php echo $stock > 0 ? '✅ In Stock' : '❌ Out of Stock'; ?>
                        </div>
                    </div>
                    
                    <div class="w-actions">
                        <?php if($stock > 0): ?>
                            <a href="?add_from_wishlist=<?php echo $item['product_id']; ?>&qty=1" class="w-btn w-btn-add" onclick="return confirm('Add this item to cart?')">
                                <i class="fas fa-shopping-bag"></i> Add to Cart
                            </a>
                        <?php else: ?>
                            <span class="w-btn w-btn-out" style="cursor:not-allowed;opacity:0.6;">
                                <i class="fas fa-ban"></i> Out of Stock
                            </span>
                        <?php endif; ?>
                        <a href="?remove_wishlist=<?php echo $item['product_id']; ?>" class="w-btn w-btn-remove" onclick="return confirm('Remove from wishlist?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>