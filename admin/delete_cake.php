<?php
$conn = mysqli_connect("localhost","root","","golden_crust");

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM cake_images WHERE cake_id='$id'");
mysqli_query($conn,"DELETE FROM cakes WHERE id='$id'");

echo "<script>
alert('Deleted');
window.location='show_cakes.php';
</script>";
?>