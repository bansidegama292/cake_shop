<?php
session_start();
$conn = mysqli_connect("localhost","root","","golden_crust");

$user_id = $_SESSION['user_id'] ?? 0;

// If not logged in, redirect to login
if($user_id == 0){
    echo "<script>alert('Please login first');window.location='login.php';</script>";
    exit();
}

// Get username
$user_query = mysqli_query($conn, "SELECT username FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($user_query);
$username = $user_data['username'] ?? 'User';

/* ================= UPDATE CART QUANTITY ================= */
if(isset($_POST['update_cart'])){
    $cart_id = intval($_POST['cart_id']);
    $qty = intval($_POST['qty']);
    
    if($qty < 1) $qty = 1;
    
    $price_query = mysqli_query($conn, "SELECT price FROM cart WHERE id = '$cart_id' AND user_id = '$user_id'");
    $price_data = mysqli_fetch_assoc($price_query);
    
    if($price_data){
        $new_total = $price_data['price'] * $qty;
        mysqli_query($conn, "UPDATE cart SET quantity = '$qty', total = '$new_total' WHERE id = '$cart_id' AND user_id = '$user_id'");
    }
    
    header("Location: cart.php");
    exit();
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
$query = "SELECT * FROM cart WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

$grand_total = 0;
$total_items = 0;
$cart_items = [];

while($row = mysqli_fetch_assoc($result)){
    $cart_items[] = $row;
    $grand_total += $row['total'];
    $total_items += $row['quantity'];
}

// Delivery charges
$delivery_charge = ($grand_total > 0 && $grand_total < 500) ? 50 : 0;
$final_total = $grand_total + $delivery_charge;

// Get cart count for badge
$cart_count_query = mysqli_query($conn, "SELECT SUM(quantity) AS total FROM cart WHERE user_id = '$user_id'");
$cart_count = mysqli_fetch_assoc($cart_count_query)['total'] ?? 0;

/* ================= FETCH WISHLIST ITEMS ================= */
$wishlist_items = [];
$wishlist_count = 0;

$wishlist_query = "
    SELECT w.*, c.itemname, c.price, c.img, c.description, c.stock 
    FROM wishlist w 
    JOIN cakes c ON w.product_id = c.id 
    WHERE w.user_id = '$user_id'
    ORDER BY w.added_date DESC
";
$wishlist_result = mysqli_query($conn, $wishlist_query);

while($row = mysqli_fetch_assoc($wishlist_result)){
    $wishlist_items[] = $row;
}
$wishlist_count = count($wishlist_items);

/* ================= ADD FROM WISHLIST TO CART ================= */
if(isset($_GET['add_from_wishlist'])){
    $product_id = intval($_GET['add_from_wishlist']);
    $qty = intval($_GET['qty'] ?? 1);
    if($qty < 1) $qty = 1;
    
    $product_query = mysqli_query($conn, "SELECT * FROM cakes WHERE id = '$product_id'");
    $product = mysqli_fetch_assoc($product_query);
    
    if($product){
        $itemname = mysqli_real_escape_string($conn, $product['itemname']);
        $price = $product['price'];
        $total = $price * $qty;
        $img = $product['img'] ?? null;
        
        $check = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id='$user_id' AND product_id='$product_id'");
        
        if(mysqli_num_rows($check) > 0){
            $existing = mysqli_fetch_assoc($check);
            $new_qty = $existing['quantity'] + $qty;
            $new_total = $price * $new_qty;
            mysqli_query($conn, "UPDATE cart SET quantity='$new_qty', total='$new_total' WHERE id='{$existing['id']}'");
        }else{
            mysqli_query($conn, "INSERT INTO cart (user_id, product_id, itemname, price, quantity, total, img, username) 
                                 VALUES ('$user_id', '$product_id', '$itemname', '$price', '$qty', '$total', '$img', '$username')");
        }
        
        echo "<script>alert('Added to Cart 🛒');window.location='cart.php';</script>";
        exit();
    }
}

/* ================= REMOVE FROM WISHLIST ================= */
if(isset($_GET['remove_wishlist'])){
    $product_id = intval($_GET['remove_wishlist']);
    mysqli_query($conn, "DELETE FROM wishlist WHERE user_id='$user_id' AND product_id='$product_id'");
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart - Golden Crust</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- ==========================================
         CSS INCLUDE - cart.css
         ========================================== -->
         <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<?php include 'includes/navbar.php'; ?>

<!-- ================= CART PAGE ================= -->
<div class="cart-container">

    <div class="cart-header">
        <h1>🛒 Your Shopping Cart</h1>
        <p>Review your items and proceed to checkout</p>
        <div class="user-info">
            <i class="fas fa-user"></i> <?php echo htmlspecialchars($username); ?>
        </div>
    </div>

    <?php if(count($cart_items) > 0): ?>

        <!-- ================= CART TABLE ================= -->
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
                                <?php if(!empty($item['img'])): ?>
                                    <img src="admin/uploads/<?php echo $item['img']; ?>" alt="<?php echo htmlspecialchars($item['itemname']); ?>">
                                <?php else: ?>
                                    <div class="no-img">
                                        <i class="fas fa-cake"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="details">
                                    <div class="name"><?php echo htmlspecialchars($item['itemname']); ?></div>
                                    <div class="username">
                                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($item['username']); ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="item-price">₹<?php echo number_format($item['price'], 2); ?></td>
                        <td>
                            <form method="POST" style="display: flex; align-items: center; gap: 6px;">
                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                <div class="qty-control">
                                    <button type="button" onclick="changeQty(this, -1)">−</button>
                                    <input type="number" name="qty" value="<?php echo $item['quantity']; ?>" min="1">
                                    <button type="button" onclick="changeQty(this, 1)">+</button>
                                </div>
                                <button type="submit" name="update_cart" class="update-btn">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                        </td>
                        <td class="item-total">₹<?php echo number_format($item['total'], 2); ?></td>
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

        <!-- ================= CART SUMMARY ================= -->
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

        <!-- ================= EMPTY CART ================= -->
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

    <!-- ============================================================ -->
    <!-- ====== WISHLIST SECTION IN CART PAGE ====== -->
    <!-- ============================================================ -->

    <?php if(!empty($wishlist_items)): ?>
    <div class="wishlist-section">
        <h2><i class="fas fa-heart"></i> Your Wishlist (<?php echo $wishlist_count; ?>)</h2>
        <p class="wishlist-subtitle">Quickly add your favorite items to cart ❤️</p>
        
        <div class="wishlist-grid">
            <?php foreach($wishlist_items as $item): 
                $stock = (int)$item['stock'];
                $price = number_format((float)$item['price'], 2);
                $img = !empty($item['img']) ? 'admin/uploads/' . htmlspecialchars($item['img']) : '';
            ?>
                <div class="wishlist-item" data-product-id="<?php echo $item['product_id']; ?>">
                    <?php if(!empty($img) && file_exists($img)): ?>
                        <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($item['itemname']); ?>">
                    <?php else: ?>
                        <div class="wishlist-no-img">
                            <i class="fas fa-cake"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="wishlist-info">
                        <div class="w-name"><?php echo htmlspecialchars($item['itemname']); ?></div>
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

<script>
    // ====== QUANTITY CONTROL ======
    function changeQty(btn, val) {
        let input = btn.parentElement.querySelector('input[type="number"]');
        if (!input) return;
        
        let current = parseInt(input.value) || 1;
        let newVal = current + val;
        
        if (newVal < 1) newVal = 1;
        
        input.value = newVal;
        
        // Auto-submit the form
        let form = input.closest('form');
        if (form) {
            form.submit();
        }
    }

    document.querySelectorAll('.qty-control input').forEach(input => {
        input.addEventListener('wheel', (e) => e.preventDefault());
    });

    console.log('🛒 Cart Page Loaded');
    console.log('📦 Total Items: <?php echo $total_items; ?>');
    console.log('💰 Grand Total: ₹<?php echo number_format($grand_total, 2); ?>');
    console.log('❤️ Wishlist Items: <?php echo $wishlist_count; ?>');
</script>

</body>
</html>
<?php include 'includes/footer.php'; ?>