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

$msg = "";
$msg_type = "";
$cake_data = null;

// ===== GET CAKE DATA =====
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $query = "SELECT * FROM cakes WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $cake_data = mysqli_fetch_assoc($result);
    } else {
        header("Location: show_cakes.php?msg=not_found");
        exit();
    }
} else {
    header("Location: show_cakes.php");
    exit();
}

// ===== UPDATE CAKE =====
if (isset($_POST['submit'])) {
    $itemname = mysqli_real_escape_string($conn, $_POST['itemname']);
    $categories = mysqli_real_escape_string($conn, $_POST['categories']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $id = intval($_POST['id']);

    // Check if new main image is uploaded
    if (!empty($_FILES['main_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        
        if (in_array($ext, $allowed)) {
            // Delete old main image
            if (!empty($cake_data['img'])) {
                $old_path = "uploads/" . $cake_data['img'];
                if (file_exists($old_path)) {
                    unlink($old_path);
                }
            }
            
            $mainImg = time() . "_main." . $ext;
            move_uploaded_file($_FILES['main_image']['tmp_name'], "uploads/" . $mainImg);
            
            // Update with new image
            $query = "UPDATE cakes SET 
                      itemname = '$itemname',
                      categories = '$categories',
                      price = '$price',
                      img = '$mainImg',
                      stock = '$stock',
                      description = '$description'
                      WHERE id = '$id'";
        } else {
            // Invalid image format - redirect with error
            header("Location: show_cakes.php?msg=image_error");
            exit();
        }
    } else {
        // Update without changing image
        $query = "UPDATE cakes SET 
                  itemname = '$itemname',
                  categories = '$categories',
                  price = '$price',
                  stock = '$stock',
                  description = '$description'
                  WHERE id = '$id'";
    }

    if (mysqli_query($conn, $query)) {
        // ✅ Redirect to show_cakes.php with success message
        header("Location: show_cakes.php?msg=updated");
        exit();
    } else {
        header("Location: show_cakes.php?msg=update_error");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Cake - Golden Crust Admin</title>
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

        .btn-back {
            display: inline-block;
            padding: 10px 22px;
            background: #6b4b5e;
            color: #fff;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .btn-back:hover {
            background: #4a2c3f;
            transform: translateY(-2px);
        }

        .btn-back i {
            margin-right: 8px;
        }

        .container {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 20px;
        }

        .form-box,
        .preview-box {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px 28px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 8px 30px rgba(232, 67, 110, 0.06);
            transition: 0.3s;
        }

        .form-box:hover,
        .preview-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 50px rgba(232, 67, 110, 0.08);
        }

        .form-box h2,
        .preview-box h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1a0f17;
            margin-bottom: 20px;
        }

        .form-box h2 i,
        .preview-box h2 i {
            color: #e8436e;
            margin-right: 10px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            color: #e8436e;
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-group label i {
            margin-right: 6px;
        }

        .form-group label .required {
            color: #ff4757;
            margin-left: 2px;
        }

        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #ffd1dc;
            border-radius: 14px;
            outline: none;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: 0.3s;
            background: rgba(255, 255, 255, 0.6);
            color: #1a0f17;
        }

        .form-control:focus {
            border-color: #e8436e;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(232, 67, 110, 0.08);
        }

        .form-control::placeholder {
            color: #b395a5;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b4b5e' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
            cursor: pointer;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .file-upload-wrapper {
            border: 2px dashed #ffd1dc;
            border-radius: 14px;
            padding: 35px 20px;
            text-align: center;
            transition: 0.3s;
            cursor: pointer;
            position: relative;
            background: rgba(255, 255, 255, 0.3);
        }

        .file-upload-wrapper:hover {
            border-color: #e8436e;
            background: rgba(232, 67, 110, 0.04);
        }

        .file-upload-wrapper i {
            font-size: 48px;
            color: #e8436e;
            opacity: 0.3;
            display: block;
            margin-bottom: 10px;
        }

        .file-upload-wrapper p {
            color: #6b4b5e;
            font-size: 15px;
        }

        .file-upload-wrapper .hint {
            font-size: 13px;
            color: #b395a5;
        }

        .file-upload-wrapper input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .image-preview {
            display: none;
            margin-top: 15px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 12px;
            border: 1px solid #ffd1dc;
        }

        .image-preview img {
            max-width: 150px;
            max-height: 150px;
            border-radius: 10px;
            object-fit: cover;
            display: block;
            margin: 0 auto;
        }

        .image-preview .file-name {
            text-align: center;
            font-size: 13px;
            color: #6b4b5e;
            margin-top: 5px;
        }

        .main-preview {
            width: 100%;
            height: 280px;
            object-fit: contain;
            border: 2px solid #ffd1dc;
            border-radius: 15px;
            display: block;
            background: #fff;
        }

        .btn-submit {
            width: 100%;
            margin-top: 20px;
            padding: 16px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #34d399, #059669);
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 6px 25px rgba(52, 211, 153, 0.25);
        }

        .btn-submit:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(52, 211, 153, 0.35);
        }

        .btn-submit i {
            margin-right: 10px;
        }

        .btn-cancel {
            width: 100%;
            padding: 14px;
            border: 2px solid #6b4b5e;
            border-radius: 14px;
            background: transparent;
            color: #6b4b5e;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-cancel:hover {
            background: #6b4b5e;
            color: #fff;
        }

        .btn-cancel i {
            margin-right: 8px;
        }

        .current-image {
            margin-top: 10px;
            text-align: center;
        }

        .current-image img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 10px;
            border: 2px solid #ffd1dc;
            object-fit: cover;
        }

        .current-image .label {
            display: block;
            font-size: 12px;
            color: #6b4b5e;
            margin-bottom: 5px;
        }

        .item-img-placeholder {
            display: none;
            width: 100%;
            height: 280px;
            border-radius: 15px;
            background: linear-gradient(135deg, #fce4ec, #f8bbd0);
            align-items: center;
            justify-content: center;
            color: #e8436e;
            font-size: 60px;
            border: 2px solid rgba(232, 67, 110, 0.10);
        }

        @media (max-width: 992px) {
            .container {
                grid-template-columns: 1fr;
            }
            
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

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-box, .preview-box {
                padding: 18px;
            }

            .page-header .title {
                font-size: 24px;
            }

            .btn-submit {
                font-size: 16px;
                padding: 14px;
            }
        }

        @media (max-width: 480px) {
            .form-box, .preview-box {
                padding: 12px;
            }

            .form-control {
                padding: 12px 14px;
                font-size: 14px;
            }

            .file-upload-wrapper {
                padding: 20px 15px;
            }

            .file-upload-wrapper i {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="main" id="main">

    <div class="page-header">
        <div class="title">
            <i class="fas fa-edit"></i> Edit Cake
        </div>
        <a href="show_cakes.php" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Cakes
        </a>
    </div>

    <?php if($cake_data): ?>
    <div class="container">

        <div class="form-box">
            <h2><i class="fas fa-pen"></i> Edit Product Details</h2>

            <form method="POST" enctype="multipart/form-data">

                <input type="hidden" name="id" value="<?php echo $cake_data['id']; ?>">

                <div class="form-group">
                    <label><i class="fas fa-cake"></i> Product Name <span class="required">*</span></label>
                    <input type="text" name="itemname" class="form-control" placeholder="Enter product name" value="<?php echo htmlspecialchars($cake_data['itemname']); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-rupee-sign"></i> Price <span class="required">*</span></label>
                        <input type="number" name="price" class="form-control" placeholder="0.00" step="0.01" value="<?php echo $cake_data['price']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-boxes"></i> Stock <span class="required">*</span></label>
                        <input type="number" name="stock" class="form-control" placeholder="Quantity" value="<?php echo $cake_data['stock']; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Category <span class="required">*</span></label>
                    <select name="categories" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="Chocolate" <?php echo ($cake_data['categories'] == 'Chocolate') ? 'selected' : ''; ?>>Chocolate</option>
                        <option value="Fruit" <?php echo ($cake_data['categories'] == 'Fruit') ? 'selected' : ''; ?>>Fruit</option>
                        <option value="Bundt" <?php echo ($cake_data['categories'] == 'Bundt') ? 'selected' : ''; ?>>Bundt</option>
                        <option value="Velvet" <?php echo ($cake_data['categories'] == 'Velvet') ? 'selected' : ''; ?>>Velvet</option>
                        <option value="Celebration" <?php echo ($cake_data['categories'] == 'Celebration') ? 'selected' : ''; ?>>Celebration</option>
                        <option value="Ice Cream" <?php echo ($cake_data['categories'] == 'Ice Cream') ? 'selected' : ''; ?>>Ice Cream</option>
                        <option value="Cupcake" <?php echo ($cake_data['categories'] == 'Cupcake') ? 'selected' : ''; ?>>Cupcake</option>
                        <option value="Roll" <?php echo ($cake_data['categories'] == 'Roll') ? 'selected' : ''; ?>>Roll</option>
                        <option value="Pastry" <?php echo ($cake_data['categories'] == 'Pastry') ? 'selected' : ''; ?>>Pastry</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description" class="form-control" placeholder="Enter product description"><?php echo htmlspecialchars($cake_data['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-image"></i> Main Image</label>
                    
                    <?php if(!empty($cake_data['img'])): ?>
                        <div class="current-image">
                            <span class="label">Current Image</span>
                            <img src="uploads/<?php echo $cake_data['img']; ?>" alt="Current Image">
                        </div>
                    <?php endif; ?>

                    <div class="file-upload-wrapper">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload new main image</p>
                        <span class="hint">JPG, PNG, GIF, WEBP (Leave empty to keep current)</span>
                        <input type="file" name="main_image" id="mainImage" accept="image/*">
                    </div>
                    <div class="image-preview" id="imagePreview">
                        <img id="previewImg" src="#" alt="Preview">
                        <div class="file-name" id="fileName"></div>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Update Cake
                </button>

                <a href="show_cakes.php" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </a>

            </form>
        </div>

        <!-- ===== PREVIEW BOX ===== -->
        <div class="preview-box">
            <h2><i class="fas fa-eye"></i> Preview</h2>
            
            <!-- Main Image Preview -->
            <img id="mainPreview" class="main-preview" src="<?php echo !empty($cake_data['img']) ? 'uploads/'.$cake_data['img'] : ''; ?>" 
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="item-img-placeholder" id="placeholderImg">
                <i class="fas fa-cake"></i>
            </div>
            
            <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.5); border-radius: 12px;">
                <p style="font-size: 14px; color: #6b4b5e; margin-bottom: 5px;">
                    <i class="fas fa-info-circle" style="color: #e8436e;"></i> Product Details
                </p>
                <p style="font-size: 16px; font-weight: 600; color: #1a0f17;" id="previewName">
                    <?php echo htmlspecialchars($cake_data['itemname']); ?>
                </p>
                <p style="font-size: 14px; color: #e8436e; font-weight: 600;" id="previewPrice">
                    ₹<?php echo number_format($cake_data['price'], 2); ?>
                </p>
                <p style="font-size: 13px; color: #6b4b5e;" id="previewCategory">
                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($cake_data['categories']); ?>
                </p>
                <p style="font-size: 13px; color: #6b4b5e;" id="previewStock">
                    <i class="fas fa-boxes"></i> Stock: <?php echo $cake_data['stock']; ?>
                </p>
            </div>
        </div>

    </div>
    <?php endif; ?>

</div>

<script>
// ===== MAIN IMAGE PREVIEW =====
document.getElementById("mainImage").addEventListener("change", function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const img = document.getElementById('previewImg');
    const fileName = document.getElementById('fileName');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            img.src = ev.target.result;
            preview.style.display = 'block';
            fileName.textContent = file.name;
            
            // Also show in main preview
            const mainPreview = document.getElementById("mainPreview");
            mainPreview.src = ev.target.result;
            mainPreview.style.display = "block";
            document.getElementById('placeholderImg').style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// ===== LIVE PREVIEW =====
document.querySelectorAll('input[name="itemname"], input[name="price"], select[name="categories"], input[name="stock"]').forEach(function(element) {
    element.addEventListener('input', function() {
        if (element.name === 'itemname') {
            document.getElementById('previewName').textContent = element.value || 'Product Name';
        }
        if (element.name === 'price') {
            document.getElementById('previewPrice').textContent = '₹' + (parseFloat(element.value) || 0).toFixed(2);
        }
        if (element.name === 'categories') {
            document.getElementById('previewCategory').innerHTML = '<i class="fas fa-tag"></i> ' + (element.value || 'Category');
        }
        if (element.name === 'stock') {
            document.getElementById('previewStock').innerHTML = '<i class="fas fa-boxes"></i> Stock: ' + (element.value || 0);
        }
    });
});

console.log('✅ Edit Cake Page Loaded');
</script>

</body>
</html>