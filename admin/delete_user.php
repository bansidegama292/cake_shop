<?php
session_start();

$conn = mysqli_connect("localhost","root","","golden_crust");

$id = $_GET['id'];

mysqli_query($conn,"DELETE FROM users WHERE id='$id'");

header("Location: users.php");
exit();
?>