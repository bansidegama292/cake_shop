<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "golden_crust");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

if ($user_id == 0) {
    echo "<script>alert('Please login first');window.location='login.php';</script>";
    exit();
}

// Get wishlist items
$stmt = mysqli_prepare($conn, "
    SELECT w.*, c.itemname, c.price, c.img, c.description, c.stock 
    FROM wishlist w 
    JOIN cakes c ON w.product_id = c.id 
    WHERE w.user_id = ?
    ORDER BY w.added_date DESC
");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// Get wishlist count for badge
$wishlist_count = 0;
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) AS total FROM wishlist WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$count_result = mysqli_stmt_get_result($stmt);
$count_row = mysqli_fetch_assoc($count_result);
$wishlist_count = $count_row['total'] ?? 0;
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Wishlist - Golden Crust</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

<!-- ==========================================
     CSS INCLUDE - wishlist.css
     ========================================== -->
     <link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/wishlist.css">

</head>
<body>

<!-- ====== NAVBAR ====== -->
<nav class="navbar">
    <a href="cakes.php" class="logo">🍰 Golden Crust</a>
    <ul class="nav-links">
        <li><a href="cakes.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="cart.php"><i class="fas fa-shopping-bag"></i> Cart</a></li>
        <li><a href="wishlist_page.php"><i class="fas fa-heart"></i> Wishlist 
            <?php if ($wishlist_count > 0): ?>
                <span class="badge"><?php echo $wishlist_count; ?></span>
            <?php endif; ?>
        </a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>

<!-- ====== WISHLIST PAGE ====== -->
<div class="wishlist-container">
    <h1><i class="fas fa-heart"></i> My Wishlist</h1>
    <p class="subtitle">Your favorite cakes saved for later ❤️</p>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="wishlist-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): 
                $stock = (int)$row['stock'];
                $price = number_format((float)$row['price'], 2);
                $img = !empty($row['img']) ? 'admin/uploads/' . htmlspecialchars($row['img']) : '';
            ?>
                <div class="wishlist-item" data-product-id="<?php echo $row['product_id']; ?>">
                    <div class="img-wrap">
                        <?php if (!empty($img)): ?>
                            <img src="<?php echo $img; ?>" alt="<?php echo htmlspecialchars($row['itemname']); ?>">
                        <?php else: ?>
                            <div style="display:flex;align-items:center;justify-content:center;height:200px;color:#d63384;font-size:40px;">
                                <i class="fas fa-cake"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="info">
                        <h3><?php echo htmlspecialchars($row['itemname']); ?></h3>
                        <p class="desc"><?php echo substr(htmlspecialchars($row['description'] ?? ''), 0, 60); ?>...</p>
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <span class="price">₹<?php echo $price; ?></span>
                            <span class="stock <?php echo $stock > 0 ? 'in-stock' : 'out-stock'; ?>">
                                <?php echo $stock > 0 ? '✅ In Stock' : '❌ Out of Stock'; ?>
                            </span>
                        </div>
                    </div>
                    <div class="actions">
                        <?php if ($stock > 0): ?>
                            <a href="add_to_cart.php?product_id=<?php echo $row['product_id']; ?>&qty=1" class="btn btn-add-cart">
                                <i class="fas fa-shopping-bag"></i> Add to Cart
                            </a>
                        <?php else: ?>
                            <span class="btn btn-out-of-stock">
                                <i class="fas fa-ban"></i> Out of Stock
                            </span>
                        <?php endif; ?>
                        <button class="btn btn-remove" onclick="removeFromWishlist(<?php echo $row['product_id']; ?>, this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-wishlist">
            <i class="far fa-heart"></i>
            <h3>Your wishlist is empty!</h3>
            <p>Start adding your favorite cakes ❤️</p>
            <a href="cakes.php"><i class="fas fa-shopping-bag"></i> Browse Cakes</a>
        </div>
    <?php endif; ?>
</div>

<!-- ====== TOAST ====== -->
<div id="toast"></div>

<!-- ====== JAVASCRIPT ====== -->
<script>
function removeFromWishlist(productId, element) {
    if (!confirm('Remove this item from wishlist?')) return;
    
    const item = element.closest('.wishlist-item');
    
    fetch('wishlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'product_id=' + productId + '&action=remove'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            item.style.transition = 'all 0.3s';
            item.style.transform = 'scale(0)';
            item.style.opacity = '0';
            setTimeout(() => {
                item.remove();
                // Check if wishlist is empty
                if (document.querySelectorAll('.wishlist-item').length === 0) {
                    location.reload();
                }
            }, 300);
            showToast(data.message || 'Removed from wishlist');
        } else {
            alert('Failed to remove from wishlist');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong. Please try again.');
    });
}

function showToast(message) {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.style.opacity = '1';
    toast.style.transform = 'translateX(-50%) translateY(0)';
    
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-50%) translateY(20px)';
    }, 2500);
}
</script>

</body>
</html>
<?php mysqli_close($conn); ?>