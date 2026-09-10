<?php
session_start();

// Database Connection
$conn = mysqli_connect("localhost", "root", "", "golden_crust");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}

// User Login Check
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;


// ==========================
// ADD TO CART
// ==========================
if(isset($_POST['add_to_cart'])){

    if($user_id == 0){
        echo "<script>
        alert('Please Login First!');
        window.location='login.php';
        </script>";
        exit();
    }

    $product_id = intval($_POST['product_id']);
    $qty = intval($_POST['qty']);

    if($qty <= 0){
        $qty = 1;
    }

    // Product already exists?
    $check = mysqli_query($conn,
    "SELECT * FROM cart
    WHERE user_id='$user_id'
    AND product_id='$product_id'");

    if(mysqli_num_rows($check)>0){

        mysqli_query($conn,
        "UPDATE cart
        SET qty = qty + $qty
        WHERE user_id='$user_id'
        AND product_id='$product_id'");

    }else{

        mysqli_query($conn,
        "INSERT INTO cart(user_id,product_id,qty)
        VALUES('$user_id','$product_id','$qty')");
    }

    echo "<script>alert('Cake Added To Cart');</script>";
}



// ==========================
// CATEGORY FILTER
// ==========================

$category = "All";

if(isset($_GET['category'])){
    $category = mysqli_real_escape_string($conn,$_GET['category']);
}



// ==========================
// SEARCH
// ==========================

$search = "";

if(isset($_GET['search'])){
    $search = mysqli_real_escape_string($conn,$_GET['search']);
}



// ==========================
// PRODUCT QUERY
// ==========================

$sql = "SELECT * FROM products WHERE 1";

if($category!="All"){
    $sql .= " AND category='$category'";
}

if(!empty($search)){
    $sql .= " AND name LIKE '%$search%'";
}

$sql .= " ORDER BY id DESC";

$result = mysqli_query($conn,$sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta
name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Our Cakes</title>

<link
href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
rel="stylesheet">

<link
rel="stylesheet"
href="style.css">
</head>

<body>

<!--================ NAVBAR ================-->

<header class="navbar">

    <div class="logo">
        🎂 Golden Crust
    </div>

    <nav>

        <a href="index.php">Home</a>

        <a href="about.php">About</a>

        <a href="cakes.php" class="active">Cakes</a>

        <a href="contact.php">Contact</a>

        <a href="cart.php">
            🛒 Cart
        </a>

    </nav>

</header>


<!--================ HERO SECTION ================-->

<section class="hero">

    <h1>Our Delicious Cakes</h1>

    <p>
        Freshly baked cakes for every celebration.
        Choose your favourite flavour and order instantly.
    </p>

</section>



<!--================ SEARCH BAR ================-->

<section class="search-section">

<form method="GET">

<input
type="text"
name="search"
placeholder="Search delicious cakes..."
value="<?php echo htmlspecialchars($search); ?>">

<?php
if($category!="All"){
?>
<input
type="hidden"
name="category"
value="<?php echo $category; ?>">
<?php
}
?>

<button type="submit">
Search
</button>

</form>

</section>



<!--================ CATEGORY BUTTONS ================-->

<section class="category-section">

<a href="cakes.php"
class="<?php if($category=="All") echo 'active'; ?>">
All
</a>

<a href="?category=Chocolate"
class="<?php if($category=="Chocolate") echo 'active'; ?>">
Chocolate
</a>

<a href="?category=Fruit"
class="<?php if($category=="Fruit") echo 'active'; ?>">
Fruit
</a>

<a href="?category=Bundt"
class="<?php if($category=="Bundt") echo 'active'; ?>">
Bundt
</a>

<a href="?category=Velvet"
class="<?php if($category=="Velvet") echo 'active'; ?>">
Velvet
</a>

<a href="?category=Celebration"
class="<?php if($category=="Celebration") echo 'active'; ?>">
Celebration
</a>

</section>



<!--================ PRODUCTS ================-->

<section class="cakes">

<div class="cake-container">

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

?>
<div class="cake-card">

    <div class="cake-image">

        <?php
        if(!empty($row['image'])){
        ?>

        <img src="<?php echo $row['image']; ?>"
             alt="<?php echo $row['name']; ?>">

        <?php
        }else{
        ?>

        <img src="images/no-image.png" alt="No Image">

        <?php
        }
        ?>

    </div>

    <div class="cake-content">

        <span class="category">
            <?php echo $row['category']; ?>
        </span>

        <h2>
            <?php echo $row['name']; ?>
        </h2>

        <p class="description">
            <?php echo substr($row['description'],0,120); ?>
        </p>

        <div class="price">
            ₹<?php echo number_format($row['price'],2); ?>
        </div>

        <div class="stock">

            <?php

            if($row['stock']>0){

                echo "<span class='in-stock'>In Stock : ".$row['stock']."</span>";

            }else{

                echo "<span class='out-stock'>Out Of Stock</span>";

            }

            ?>

        </div>


        <?php

        if($row['stock']>0){

        ?>

        <form method="POST">

            <input
            type="hidden"
            name="product_id"
            value="<?php echo $row['id']; ?>">


            <div class="qty-box">

                <button
                type="button"
                onclick="decreaseQty(this)">
                -
                </button>

                <input
                type="number"
                name="qty"
                value="1"
                min="1"
                max="<?php echo $row['stock']; ?>">

                <button
                type="button"
                onclick="increaseQty(this)">
                +
                </button>

            </div>


            <button
            type="submit"
            name="add_to_cart"
            class="cart-btn">

                🛒 Add To Cart

            </button>

        </form>

        <?php

        }else{

        ?>

        <button
        class="disabled-btn"
        disabled>

            Out Of Stock

        </button>

        <?php

        }

        ?>

    </div>

</div>

<?php

}

}else{

?>

<div class="no-product">

<h2>No Cakes Found.</h2>

</div>

<?php

}

?>

</div>

</section>
<script>

function increaseQty(button){

    let input = button.parentElement.querySelector("input");

    let max = parseInt(input.max);

    let value = parseInt(input.value);

    if(value < max){

        input.value = value + 1;

    }

}



function decreaseQty(button){

    let input = button.parentElement.querySelector("input");

    let value = parseInt(input.value);

    if(value > 1){

        input.value = value - 1;

    }

}

</script>

</body>
</html>