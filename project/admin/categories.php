<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","golden_crust");

$msg = "";

if(isset($_POST['submit'])){

    $name = $_POST['name'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $category = $_POST['category'];

    // image upload
    $img = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $path = "uploads/";

    if(!is_dir($path)){
        mkdir($path, 0777, true);
    }

    move_uploaded_file($tmp, $path.$img);

    $sql = "INSERT INTO cakes 
    (name, price, image, stock, description, category)
    VALUES 
    ('$name', '$price', '$img', '$stock', '$description', '$category')";

    if(mysqli_query($conn,$sql)){
        $msg = "🍰 Cake Added Successfully!";
    } else {
        $msg = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Cake</title>

<style>

body{
    font-family:Segoe UI;
    background:linear-gradient(135deg,#ffe4ec,#ffd6e7,#ffb6c1);
}

.main{
    margin-left:90px;
    padding:30px;
}

.title{
    font-size:35px;
    font-weight:bold;
    color:#ff4d88;
    margin-bottom:20px;
}

.form-box{
    max-width:600px;
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

input, textarea, select{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:1px solid #ffd1dc;
    border-radius:10px;
    outline:none;
    font-size:14px;
}

button{
    width:100%;
    padding:12px;
    background:linear-gradient(135deg,#ff4d88,#ff85a2);
    color:white;
    border:none;
    border-radius:10px;
    font-size:16px;
    cursor:pointer;
}

.msg{
    margin-bottom:10px;
    font-weight:bold;
    color:green;
}

</style>

</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="main">

    <div class="title">➕ Add Cake</div>

    <?php if($msg != "") echo "<div class='msg'>$msg</div>"; ?>

    <div class="form-box">

        <form method="POST" enctype="multipart/form-data">

            <input type="text" name="name" placeholder="Cake Name" required>

            <input type="number" name="price" placeholder="Price" required>

            <!-- CATEGORY -->
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="Chocolate">Chocolate</option>
                <option value="Fruit">Fruit</option>
                <option value="Bundt">Bundt</option>
                <option value="Vanilla">Vanilla</option>
                <option value="Celebration">Celebration</option>
                <option value="Icecream">Icecream</option>
                <option value="Cupcake">Cupcake</option>
                <option value="Roll Cake">Roll Cake</option>
            </select>

            <input type="number" name="stock" placeholder="Stock Quantity">

            <textarea name="description" placeholder="Description"></textarea>

            <input type="file" name="image" required>

            <button type="submit" name="submit">➕ Add Cake</button>

        </form>

    </div>

</div>

</body>
</html>