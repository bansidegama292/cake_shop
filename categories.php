<?php
session_start();
$conn = mysqli_connect("localhost","root","","golden_crust");
?>

<!DOCTYPE html>
<html>
<head>
<title>Categories</title>
<link rel="stylesheet" href="style.css">

<style>
.cat-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
    padding:40px;
}

.cat-card{
    background:#fff;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
    transition:0.3s;
    text-align:center;
}

.cat-card:hover{
    transform:translateY(-10px);
}

.cat-card img{
    width:100%;
    height:200px;
    object-fit:cover;
}

.cat-card h3{
    padding:15px;
    color:#d63384;
}
</style>

</head>
<body>

<?php include("includes/navbar.php"); ?>

<h1 style="text-align:center;color:#d63384;margin-top:30px;">
    Shop By Categories
</h1>

<div class="cat-grid">

<?php
$cats = mysqli_query($conn,"SELECT DISTINCT category FROM cakes");

while($c = mysqli_fetch_assoc($cats)){
?>

<a href="cakes.php?category=<?php echo $c['category']; ?>" style="text-decoration:none;">

<div class="cat-card">

    <!-- optional image mapping -->
    <img src="images/<?php echo strtolower($c['category']); ?>.jpg">

    <h3><?php echo $c['category']; ?></h3>

</div>

</a>

<?php } ?>

</div>

</body>
</html>