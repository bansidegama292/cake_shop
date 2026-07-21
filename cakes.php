<?php
session_start();

// Database connection
$conn = mysqli_connect("localhost", "root", "", "golden_crust");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// Get username for cart
$username = '';
if ($user_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT username FROM users WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($result);
    $username = $user_data['username'] ?? 'User';
    mysqli_stmt_close($stmt);
}

/* ================= ADD TO CART ================= */
if (isset($_POST['add_to_cart'])) {
    if ($user_id == 0) {
        echo "<script>alert('Please login first');window.location='login.php';</script>";
        exit();
    }

    $cake_id = isset($_POST['cake_id']) ? (int)$_POST['cake_id'] : 0;
    $qty = isset($_POST['qty']) ? max(1, (int)$_POST['qty']) : 1;

    if ($cake_id <= 0) {
        echo "<script>alert('Invalid product');window.location='cakes.php';</script>";
        exit();
    }

    // Get product details
    $stmt = mysqli_prepare($conn, "SELECT * FROM cakes WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $cake_id);
    mysqli_stmt_execute($stmt);
    $product_result = mysqli_stmt_get_result($stmt);
    $product = mysqli_fetch_assoc($product_result);
    mysqli_stmt_close($stmt);

    if ($product) {
        $itemname = mysqli_real_escape_string($conn, $product['itemname']);
        $price = (float)$product['price'];
        $total = $price * $qty;
        $img = $product['img'] ?? null;

        // Check if product already in cart
        $stmt = mysqli_prepare($conn, "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $user_id, $cake_id);
        mysqli_stmt_execute($stmt);
        $check_result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($check_result) > 0) {
            // Update existing cart item
            $existing = mysqli_fetch_assoc($check_result);
            $new_qty = $existing['quantity'] + $qty;
            $new_total = $price * $new_qty;
            
            $stmt2 = mysqli_prepare($conn, "UPDATE cart SET quantity = ?, total = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt2, "idi", $new_qty, $new_total, $existing['id']);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);
        } else {
            // Insert new cart item
            $stmt2 = mysqli_prepare($conn, "INSERT INTO cart (user_id, product_id, itemname, price, quantity, total, img, username) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "iisdiiss", $user_id, $cake_id, $itemname, $price, $qty, $total, $img, $username);
            mysqli_stmt_execute($stmt2);
            mysqli_stmt_close($stmt2);
        }
        mysqli_stmt_close($stmt);

        echo "<script>alert('Added to Cart 🛒');window.location='cakes.php';</script>";
        exit();
    }
}

/* ================= FILTER & SEARCH ================= */
$category = isset($_GET['category']) ? trim($_GET['category']) : "All";
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Whitelist allowed categories
$allowed_categories = ['All', 'Chocolate', 'Fruit', 'Bundt', 'Velvet', 'Celebration', 'Ice Cream', 'Cupcake', 'Roll', 'Pastry'];
$category = in_array($category, $allowed_categories) ? $category : "All";

// Build query
$sql = "SELECT * FROM cakes WHERE 1=1";
$params = [];
$types = "";

if ($category != "All") {
    $sql .= " AND categories = ?";
    $params[] = $category;
    $types .= "s";
}

if ($search != "") {
    $sql .= " AND itemname LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

$sql .= " ORDER BY id DESC";

// Execute query
$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

// ====== IMAGE FOLDER ======
$image_folder = "admin/uploads/";

// Get cart count for badge
$cart_count = 0;
if ($user_id > 0) {
    $stmt = mysqli_prepare($conn, "SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $count_result = mysqli_stmt_get_result($stmt);
    $count_row = mysqli_fetch_assoc($count_result);
    $cart_count = $count_row['total'] ?? 0;
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Cakes - Golden Crust</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">

<!-- ==========================================
   CSS INCLUDE - cakes.css
   ========================================== -->
   <link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="css/cakes.css">

</head>
<body>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<!-- ================= HERO ================= -->
<section class="hero-section">
    <h1> <span class="highlight">Our Delicious</span> Cakes</h1>
    <p><b>Fresh baked happiness for every celebration</b></p>
    
</section>

<!-- ================= FILTER BAR ================= -->
<div class="filter-wrapper">
    <div class="filter-bar">
        <a href="?category=All" class="filter-btn <?php echo $category=='All'?'active':''; ?>">
            <i class="fas fa-utensils"></i> All
        </a>
        <a href="?category=Chocolate" class="filter-btn <?php echo $category=='Chocolate'?'active':''; ?>">
            <i class="fas fa-chocolate-bar"></i> Chocolate
        </a>
        <a href="?category=Fruit" class="filter-btn <?php echo $category=='Fruit'?'active':''; ?>">
            <i class="fas fa-apple-alt"></i> Fruit
        </a>
        <a href="?category=Bundt" class="filter-btn <?php echo $category=='Bundt'?'active':''; ?>">
            <i class="fas fa-circle"></i> Bundt
        </a>
        <a href="?category=Velvet" class="filter-btn <?php echo $category=='Velvet'?'active':''; ?>">
            <i class="fas fa-heart"></i> Velvet
        </a>
        <a href="?category=Celebration" class="filter-btn <?php echo $category=='Celebration'?'active':''; ?>">
            <i class="fas fa-glass-cheers"></i> Celebration
        </a>
        <a href="?category=Ice Cream" class="filter-btn <?php echo $category=='Ice Cream'?'active':''; ?>">
            <i class="fas fa-ice-cream"></i> Ice Cream
        </a>
        <a href="?category=Cupcake" class="filter-btn <?php echo $category=='Cupcake'?'active':''; ?>">
            <i class="fas fa-muffin"></i> Cupcake
        </a>
        <a href="?category=Roll" class="filter-btn <?php echo $category=='Roll'?'active':''; ?>">
            <i class="fas fa-roll"></i> Roll
        </a>
        <a href="?category=Pastry" class="filter-btn <?php echo $category=='Pastry'?'active':''; ?>">
            <i class="fas fa-bread"></i> Pastry
        </a>
    </div>
</div>

<!-- ================= SECTION TITLE ================= -->
<div class="section-header">
    <h2>
        <span class="category-name"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></span> Cakes
        <span class="count">(<?php echo mysqli_num_rows($result); ?> items)</span>
    </h2>
</div>

<!-- ================= PRODUCTS ================= -->
<section class="cakes">
    <div class="cake-container">

    <?php 
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) { 
            $main_image = !empty($row['img']) ? htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8') : '';
            $main_image_path = !empty($main_image) ? htmlspecialchars($image_folder . $row['img'], ENT_QUOTES, 'UTF-8') : '';
            $itemname = htmlspecialchars($row['itemname'], ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
            $categories = htmlspecialchars($row['categories'], ENT_QUOTES, 'UTF-8');
            $price = number_format((float)$row['price'], 2);
            $stock = (int)$row['stock'];
    ?>

    <div class="cake-card" data-cake-id="<?php echo $row['id']; ?>">
        <!-- Category Badge -->
        <span class="category-badge">
            <i class="fas fa-tag"></i> <?php echo $categories; ?>
        </span>

        <!-- Wishlist Button -->
        <?php
        // Check if product is in wishlist
        $in_wishlist = false;
        if ($user_id > 0) {
            $stmt = mysqli_prepare($conn, "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $row['id']);
            mysqli_stmt_execute($stmt);
            $wish_result = mysqli_stmt_get_result($stmt);
            $in_wishlist = (mysqli_num_rows($wish_result) > 0);
            mysqli_stmt_close($stmt);
        }
        ?>

        <button class="wishlist-btn" onclick="toggleWishlist(<?php echo $row['id']; ?>, this)" data-product-id="<?php echo $row['id']; ?>">
            <i class="<?php echo $in_wishlist ? 'fas' : 'far'; ?> fa-heart" id="heart_<?php echo $row['id']; ?>"></i>
        </button>
        
        <!-- Image -->
        <div class="cake-image-wrapper" onclick="openModal('<?php echo $main_image_path; ?>', '<?php echo $itemname; ?>')">
            <?php if (!empty($main_image_path)): ?>
                <img src="<?php echo $main_image_path; ?>" 
                     alt="<?php echo $itemname; ?>"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="no-image-placeholder" style="display:none;">
                    <i class="fas fa-cake"></i>
                    <span>Image not found</span>
                </div>
                <div class="view-icon">
                    <i class="fas fa-expand"></i> View
                </div>
            <?php else: ?>
                <div class="no-image-placeholder">
                    <i class="fas fa-cake"></i>
                    <span><?php echo $itemname; ?></span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Content -->
        <div class="cake-content">
            <h3><?php echo $itemname; ?></h3>
            
            <p class="cake-description">
                <?php echo substr($description, 0, 80); ?>...
            </p>

            <div class="price-row">
                <span class="price">
                    <span class="currency">₹</span> <?php echo $price; ?>
                </span>
                <span class="stock-status <?php echo $stock > 0 ? 'in-stock' : 'out-stock'; ?>">
                    <?php if ($stock > 0): ?>
                        <i class="fas fa-check-circle"></i> In Stock
                    <?php else: ?>
                        <i class="fas fa-times-circle"></i> Out of Stock
                    <?php endif; ?>
                </span>
            </div>

            <?php if ($stock > 0): ?>
                <form method="POST" onsubmit="return validateQuantity(this)">
                    <input type="hidden" name="cake_id" value="<?php echo $row['id']; ?>">
                    
                    <div class="button-row">
                        <!-- Quantity Controls - LEFT -->
                        <div class="qty-control">
                            <button type="button" onclick="changeQty(this, -1)">−</button>
                            <input type="number" name="qty" value="1" min="1" max="<?php echo $stock; ?>" id="qty_<?php echo $row['id']; ?>">
                            <button type="button" onclick="changeQty(this, 1)">+</button>
                        </div>
                        
                        <!-- Add to Cart Button - RIGHT -->
                        <button type="submit" name="add_to_cart" class="add-to-cart">
                            <i class="fas fa-shopping-bag"></i> Add to Cart
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <button class="btn-disabled">
                    <i class="fas fa-ban"></i> Out of Stock
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php 
        } 
    } else { ?>
        <div class="no-products">
            <i class="fas fa-cake"></i>
            <h3>No cakes found</h3>
            <p>We couldn't find any cakes in the "<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>" category.</p>
            <a href="?category=All"><i class="fas fa-arrow-right"></i> View All Cakes</a>
        </div>
    <?php } ?>

    </div>
</section>

<!-- ================= IMAGE ZOOM MODAL ================= -->
<div class="image-modal" id="imageModal">
    <div class="image-modal-content">
        <button class="image-modal-close" onclick="closeModal()">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="main-image-container">
            <img id="modalImage" src="" alt="Full view">
        </div>
        
        <div class="image-modal-title" id="modalTitle">
            <i class="fas fa-cake"></i> <span id="titleText">Cake</span>
        </div>
    </div>
</div>

<!-- ================= JAVASCRIPT ================= -->
<script>
// ====== QUANTITY CONTROL ======
function changeQty(btn, val) {
    let input = btn.parentElement.querySelector('input[type="number"]');
    if (!input) return;
    
    let current = parseInt(input.value) || 1;
    let max = parseInt(input.max) || 99;
    let newVal = current + val;
    
    if (newVal < 1) newVal = 1;
    if (newVal > max) newVal = max;
    
    input.value = newVal;
}

// ====== VALIDATE QUANTITY BEFORE SUBMIT ======
function validateQuantity(form) {
    const input = form.querySelector('input[name="qty"]');
    const max = parseInt(input.max) || 1;
    const val = parseInt(input.value) || 1;
    
    if (val < 1) {
        alert('Quantity must be at least 1');
        input.value = 1;
        return false;
    }
    if (val > max) {
        alert('Quantity cannot exceed stock (' + max + ')');
        input.value = max;
        return false;
    }
    return true;
}

// ====== IMAGE ZOOM MODAL ======
function openModal(imagePath, title) {
    if (!imagePath || imagePath === '') {
        alert('No image available');
        return;
    }
    
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const titleText = document.getElementById('titleText');
    
    modalImg.src = imagePath;
    modalImg.alt = title || 'Cake';
    titleText.textContent = title || 'Cake';
    
    modalImg.onerror = function() {
        this.src = '';
        this.alt = 'Image not available';
        titleText.textContent = 'Image not available';
    };
    
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
}

// Close modal on background click
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// ====== KEYBOARD SUPPORT ======
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('imageModal');
    if (modal.classList.contains('show')) {
        if (e.key === 'Escape') closeModal();
    }
});

// ====== ADD TO CART ANIMATION ======
document.querySelectorAll('.add-to-cart').forEach(btn => {
    btn.addEventListener('click', function() {
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="fas fa-check"></i> ✓ Added';
        this.style.background = 'linear-gradient(135deg, #00b894, #00cec9)';
        this.style.pointerEvents = 'none';
        
        setTimeout(() => {
            this.innerHTML = originalText;
            this.style.background = '';
            this.style.pointerEvents = '';
        }, 2000);
    });
});

// ====== WISHLIST TOGGLE ======
function toggleWishlist(productId, element) {
    <?php if ($user_id == 0): ?>
        alert('Please login first to add to wishlist ❤️');
        window.location.href = 'login.php';
        return;
    <?php endif; ?>
    
    const heartIcon = element.querySelector('i');
    const isLiked = heartIcon.classList.contains('fas');
    
    // Optimistic UI update
    if (isLiked) {
        heartIcon.classList.remove('fas');
        heartIcon.classList.add('far');
    } else {
        heartIcon.classList.remove('far');
        heartIcon.classList.add('fas');
    }
    
    // Send AJAX request
    fetch('wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&action=' + (isLiked ? 'remove' : 'add')
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message);
            
            if (data.action === 'added') {
                heartIcon.classList.remove('far');
                heartIcon.classList.add('fas');
                element.style.color = '#d63384';
            } else {
                heartIcon.classList.remove('fas');
                heartIcon.classList.add('far');
                element.style.color = '#d63384';
            }
        } else {
            // Revert if error
            if (isLiked) {
                heartIcon.classList.remove('far');
                heartIcon.classList.add('fas');
            } else {
                heartIcon.classList.remove('fas');
                heartIcon.classList.add('far');
            }
            if (data.login) {
                alert('Please login first');
                window.location.href = 'login.php';
            } else {
                alert(data.message || 'Something went wrong');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert on error
        if (isLiked) {
            heartIcon.classList.remove('far');
            heartIcon.classList.add('fas');
        } else {
            heartIcon.classList.remove('fas');
            heartIcon.classList.add('far');
        }
        alert('Something went wrong. Please try again.');
    });
}

// ====== TOAST NOTIFICATION ======
function showToast(message) {
    let toast = document.getElementById('toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'toast';
        toast.style.cssText = `
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: rgba(214, 51, 132, 0.95);
            color: white;
            padding: 14px 30px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 10px 40px rgba(214, 51, 132, 0.3);
            z-index: 9999;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(10px);
            font-family: 'Poppins', sans-serif;
            pointer-events: none;
        `;
        document.body.appendChild(toast);
    }
    
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
<?php include 'includes/footer.php'; ?>