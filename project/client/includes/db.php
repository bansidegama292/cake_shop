<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "golden_crust"
);

if(!$conn){
    die("Database Connection Failed");
}

?>