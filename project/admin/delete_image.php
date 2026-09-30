<?php
$conn = mysqli_connect("localhost","root","","golden_crust");

$id = $_GET['id'];      // image id
$cake = $_GET['cake'];  // cake id

// get image name first
$res = mysqli_query($conn,"SELECT image FROM cake_images WHERE id='$id'");
$row = mysqli_fetch_assoc($res);

// delete file from folder
unlink("uploads/".$row['image']);

// delete only ONE image from DB
mysqli_query($conn,"DELETE FROM cake_images WHERE id='$id'");

echo "<script>
alert('Image Deleted Successfully');
window.location='show_cakes.php';
</script>";
?>