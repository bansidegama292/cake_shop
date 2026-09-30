<?php
$conn = mysqli_connect("localhost","root","","golden_crust");

$id = $_POST['id'];
$name = $_POST['name'];
$price = $_POST['price'];
$stock = $_POST['stock'];
$description = $_POST['description'];

$image_sql = "";

if(!empty($_FILES['image']['name'])){

    $img = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    move_uploaded_file($tmp,"uploads/".$img);

    $image_sql = ", image='$img'";
}

$sql = "UPDATE cakes SET 
        name='$name',
        price='$price',
        stock='$stock',
        description='$description'
        $image_sql
        WHERE id='$id'";

if(mysqli_query($conn,$sql)){
    echo "<script>
        alert('Updated Successfully');
        window.location='show_cakes.php';
    </script>";
}
?>