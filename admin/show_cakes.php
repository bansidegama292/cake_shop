<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "golden_crust");

if (!$conn) {
    die("Database Connection Failed : " . mysqli_connect_error());
}

// ===== DELETE CAKE =====
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Get image name to delete file
    $img_query = mysqli_query($conn, "SELECT img FROM cakes WHERE id = '$id'");
    $img_row = mysqli_fetch_assoc($img_query);
    
    if ($img_row && !empty($img_row['img'])) {
        $file_path = "uploads/" . $img_row['img'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    // Delete sub images
    $sub_query = mysqli_query($conn, "SELECT image FROM cake_images WHERE cake_id = '$id'");
    while ($sub_row = mysqli_fetch_assoc($sub_query)) {
        if (!empty($sub_row['image'])) {
            $file_path = "uploads/" . $sub_row['image'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
    
    // Delete from database
    mysqli_query($conn, "DELETE FROM cake_images WHERE cake_id = '$id'");
    mysqli_query($conn, "DELETE FROM cakes WHERE id = '$id'");
    
    echo "<script>alert('Cake deleted successfully!');window.location='show_cakes.php';</script>";
}

// ===== GET ALL CAKES =====
$query = "SELECT * FROM cakes ORDER BY id DESC";

$result = mysqli_query($conn, $query);
$total_cakes = mysqli_num_rows($result);

// ===== GET MESSAGE =====
$msg = '';
$msg_type = '';
if(isset($_GET['msg'])){
    if($_GET['msg'] == 'updated'){
        $msg = '🍰 Cake updated successfully!';
        $msg_type = 'success';
    } elseif($_GET['msg'] == 'image_error'){
        $msg = '❌ Invalid image format! Only JPG, PNG, GIF, WEBP allowed.';
        $msg_type = 'error';
    } elseif($_GET['msg'] == 'update_error'){
        $msg = '❌ Error updating cake!';
        $msg_type = 'error';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Show Cakes - Golden Crust Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0, #fce4ec);
            min-height: 100vh;
        }

        /* ===== MAIN CONTENT - SAME AS SIDEBAR ===== */
        .main {
            margin-left: 85px;
            padding: 30px;
            transition: margin-left 0.35s ease;
            min-height: 100vh;
        }

        .main.shift {
            margin-left: 260px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .page-header .title {
            font-size: 32px;
            font-weight: 800;
            color: #1a0f17;
        }

        .page-header .title i {
            color: #e8436e;
            margin-right: 10px;
        }

        .page-header .title .badge {
            font-size: 16px;
            background: rgba(232, 67, 110, 0.12);
            color: #e8436e;
            padding: 6px 18px;
            border-radius: 30px;
            font-weight: 600;
            margin-left: 10px;
        }

        .btn-add {
            padding: 12px 26px;
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
            color: #fff;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(232, 67, 110, 0.25);
            font-family: 'Poppins', sans-serif;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(232, 67, 110, 0.4);
        }

        .table-wrapper {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 5px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }

        .table-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 15px;
            min-width: 800px;
        }

        thead {
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
        }

        thead th {
            color: #fff;
            padding: 16px 18px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.3px;
            white-space: nowrap;
        }

        thead th i {
            margin-right: 6px;
            font-size: 14px;
        }

        tbody tr {
            border-bottom: 1px solid rgba(0, 0, 0, 0.04);
            transition: 0.3s;
        }

        tbody tr:hover {
            background: rgba(232, 67, 110, 0.03);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody td {
            padding: 16px 18px;
            color: #2d1b24;
            vertical-align: middle;
            font-size: 15px;
        }

        /* ===== ITEM IMAGE ===== */
        .item-img {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(232, 67, 110, 0.10);
            background: #f8e8ef;
        }

        .item-img-placeholder {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e8436e;
            font-size: 28px;
            border: 2px solid rgba(232, 67, 110, 0.10);
        }

        .category-badge {
            display: inline-block;
            padding: 5px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background: rgba(232, 67, 110, 0.08);
            color: #e8436e;
        }

        .price-text {
            font-weight: 700;
            color: #e8436e;
            font-size: 18px;
        }

        .stock-badge {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .stock-in {
            background: rgba(46, 213, 115, 0.12);
            color: #1f8b4c;
        }

        .stock-out {
            background: rgba(255, 71, 87, 0.12);
            color: #b33c4a;
        }

        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: linear-gradient(135deg, #34d399, #059669);
            color: #fff;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(52, 211, 153, 0.3);
        }

        .btn-delete {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: #fff;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
        }

        /* ===== SERIAL NUMBER ===== */
        .serial-number {
            font-weight: 700;
            color: #e8436e;
            font-size: 16px;
        }

        /* ===== PRODUCT NAME ===== */
        .product-name {
            font-weight: 600;
            color: #1a0f17;
            font-size: 16px;
        }

        .product-desc {
            font-size: 13px;
            color: #6b4b5e;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
        }

        .no-data i {
            font-size: 70px;
            color: #e8436e;
            opacity: 0.2;
            display: block;
            margin-bottom: 20px;
        }

        .no-data h3 {
            font-size: 26px;
            color: #1a0f17;
            margin-bottom: 8px;
        }

        .no-data p {
            font-size: 16px;
            color: #6b4b5e;
        }

        .no-data .btn-add {
            display: inline-block;
            margin-top: 10px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            .main {
                margin-left: 75px;
                padding: 20px;
            }
            
            .main.shift {
                margin-left: 240px;
            }
        }

        @media (max-width: 768px) {
            .main {
                margin-left: 0;
                padding: 70px 15px 15px 15px;
            }
            
            .main.shift {
                margin-left: 0;
            }

            .page-header .title {
                font-size: 24px;
            }

            table {
                font-size: 13px;
                min-width: 700px;
            }

            tbody td {
                padding: 12px 12px;
                font-size: 13px;
            }

            .item-img, .item-img-placeholder {
                width: 50px;
                height: 50px;
            }
            
            .btn-action {
                padding: 6px 12px;
                font-size: 12px;
            }
            
            .price-text {
                font-size: 15px;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-add {
                justify-content: center;
                font-size: 14px;
                padding: 10px 20px;
            }

            .action-btns {
                flex-direction: column;
            }

            .btn-action {
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="main" id="main">

    <div class="page-header">
        <div class="title">
            <i class="fas fa-cake-candles"></i> Show Cakes
            <span class="badge"><?php echo $total_cakes; ?> Cakes</span>
        </div>
        <a href="add_cake.php" class="btn-add">
            <i class="fas fa-plus-circle"></i> Add New Cake
        </a>
    </div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th><i class="fas fa-image"></i> Image</th>
                        <th><i class="fas fa-cake"></i> Name</th>
                        <th><i class="fas fa-tag"></i> Category</th>
                        <th><i class="fas fa-rupee-sign"></i> Price</th>
                        <th><i class="fas fa-boxes"></i> Stock</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_cakes > 0): ?>
                        <?php $sr = 1; while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="serial-number"><?php echo $sr++; ?></td>
                            <td>
                                <?php if(!empty($row['img'])): ?>
                                    <img src="uploads/<?php echo $row['img']; ?>" alt="<?php echo $row['itemname']; ?>" class="item-img">
                                <?php else: ?>
                                    <div class="item-img-placeholder">
                                        <i class="fas fa-cake"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="product-name"><?php echo htmlspecialchars($row['itemname']); ?></div>
                                <?php if(!empty($row['description'])): ?>
                                    <div class="product-desc"><?php echo substr(htmlspecialchars($row['description']), 0, 40); ?>...</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="category-badge">
                                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($row['categories']); ?>
                                </span>
                            </td>
                            <td class="price-text">₹<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <span class="stock-badge <?php echo $row['stock'] > 0 ? 'stock-in' : 'stock-out'; ?>">
                                    <?php if($row['stock'] > 0): ?>
                                        <i class="fas fa-check-circle"></i> <?php echo $row['stock']; ?>
                                    <?php else: ?>
                                        <i class="fas fa-times-circle"></i> Out of Stock
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit_cake.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this cake?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="no-data">
                                    <i class="fas fa-cake"></i>
                                    <h3>No Cakes Found</h3>
                                    <p>Start by adding your first cake!</p>
                                    <a href="add_cake.php" class="btn-add" style="display: inline-block; margin-top: 10px;">
                                        <i class="fas fa-plus-circle"></i> Add Cake
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
console.log('✅ Show Cakes Page Loaded');
console.log('📊 Total Cakes: <?php echo $total_cakes; ?>');
</script>

</body>
</html>