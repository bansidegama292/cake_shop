<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = $_GET['order_id'] ?? 0;

// Database connection
$conn = mysqli_connect("localhost", "root", "", "golden_crust");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if order exists and belongs to this user
if ($order_id > 0) {
    // First check if order exists and is pending
    $check_stmt = mysqli_prepare($conn, "SELECT id, status FROM orders WHERE id = ? AND user_id = ?");
    mysqli_stmt_bind_param($check_stmt, "ii", $order_id, $user_id);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    $order = mysqli_fetch_assoc($check_result);
    mysqli_stmt_close($check_stmt);
    
    if ($order) {
        // Check if order is pending (only pending orders can be cancelled)
        if (strtolower($order['status']) == 'pending') {
            
            $update_stmt = mysqli_prepare($conn, "UPDATE orders SET status = 'cancelled' WHERE id = ? AND user_id = ?");
            mysqli_stmt_bind_param($update_stmt, "ii", $order_id, $user_id);
            
            if (mysqli_stmt_execute($update_stmt)) {
                // Success - redirect with success message
                $_SESSION['success_message'] = "Order #" . str_pad($order_id, 6, '0', STR_PAD_LEFT) . " has been cancelled successfully.";
                mysqli_stmt_close($update_stmt);
                mysqli_close($conn);
                header("Location: orders.php");
                exit;
            } else {
                $error = "Failed to cancel order. Please try again.";
            }
            mysqli_stmt_close($update_stmt);
        } else {
            $error = "This order cannot be cancelled because it is already " . $order['status'] . ".";
        }
    } else {
        $error = "Order not found or you don't have permission to cancel it.";
    }
} else {
    $error = "Invalid order ID.";
}

mysqli_close($conn);

// If there's an error, redirect back with error message
if (isset($error)) {
    $_SESSION['error_message'] = $error;
    header("Location: orders.php");
    exit;
}
?>