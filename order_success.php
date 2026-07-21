<?php
session_start();
$order_id = $_GET['order_id'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
<title>Order Success - Golden Crust</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #fff5f7 0%, #ffe4ec 50%, #ffd6e4 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
}
.success-box {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(20px);
    border-radius: 30px;
    padding: 50px;
    text-align: center;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(214,51,132,0.15);
}
.success-box .icon {
    font-size: 80px;
    color: #00b894;
    margin-bottom: 20px;
}
.success-box h1 {
    font-size: 28px;
    color: #1a0f17;
}
.success-box p {
    color: #6b4b5e;
    margin: 10px 0 20px;
}
.success-box .order-id {
    background: rgba(214,51,132,0.08);
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 700;
    color: #d63384;
    display: inline-block;
}
.btn {
    display: inline-block;
    padding: 14px 35px;
    background: linear-gradient(135deg, #d63384, #e84393);
    color: white;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 700;
    margin-top: 20px;
    transition: 0.3s;
}
.btn:hover {
    transform: scale(1.05);
}
</style>
</head>
<body>
<div class="success-box">
    <div class="icon"><i class="fas fa-check-circle"></i></div>
    <h1>🎉 Order Placed Successfully!</h1>
    <p>Thank you for your order. We will deliver it soon.</p>
    <div class="order-id">Order #<?php echo $order_id; ?></div>
    <br>
    <a href="cakes.php" class="btn"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
</div>
</body>
</html>