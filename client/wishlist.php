<?php
session_start();
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "golden_crust");
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

// If user not logged in
if ($user_id == 0) {
    echo json_encode(['success' => false, 'message' => 'Please login first', 'login' => true]);
    exit();
}

$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'add';

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit();
}

if ($action == 'add') {
    // Check if already in wishlist
    $stmt = mysqli_prepare($conn, "SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        // Remove from wishlist (toggle)
        $stmt2 = mysqli_prepare($conn, "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        mysqli_stmt_bind_param($stmt2, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
        
        echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist ❤️']);
    } else {
        // Add to wishlist
        $stmt2 = mysqli_prepare($conn, "INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt2, "ii", $user_id, $product_id);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
        
        echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Added to wishlist ❤️']);
    }
    mysqli_stmt_close($stmt);
    
} elseif ($action == 'remove') {
    // Remove from wishlist
    $stmt = mysqli_prepare($conn, "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    
    echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Removed from wishlist']);
}

mysqli_close($conn);
?>