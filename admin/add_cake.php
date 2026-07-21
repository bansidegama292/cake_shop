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

if (isset($_POST['submit'])) {

    $itemname = mysqli_real_escape_string($conn, $_POST['itemname']);
    $categories = mysqli_real_escape_string($conn, $_POST['categories']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $stock = mysqli_real_escape_string($conn, $_POST['stock']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $uploadPath = "uploads/";

    if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    /* =========================
       SINGLE IMAGE UPLOAD
    ========================== */

    $imageName = "";

    if (!empty($_FILES['cake_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['cake_image']['name'], PATHINFO_EXTENSION));
        $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
        
        if (in_array($ext, $allowed)) {
            $imageName = time() . "." . $ext;
            if (move_uploaded_file(
                $_FILES['cake_image']['tmp_name'],
                $uploadPath . $imageName
            )) {
                // Image uploaded successfully
            } else {
                $msg = "❌ Failed to upload image!";
                $msg_type = "error";
            }
        } else {
            $msg = "❌ Invalid image format! Only JPG, PNG, GIF, WEBP allowed.";
            $msg_type = "error";
        }
    } else {
        $msg = "⚠️ Please select an image!";
        $msg_type = "warning";
    }

    /* =========================
       INSERT CAKE
    ========================== */

    if (empty($msg) || $msg_type != "error") {
        $sql = "INSERT INTO cakes
        (itemname, categories, price, img, stock, description)
        VALUES
        ('$itemname', '$categories', '$price', '$imageName', '$stock', '$description')";

        if (mysqli_query($conn, $sql)) {
            $msg = "🍰 Cake Added Successfully!";
            $msg_type = "success";
            $_POST = array();
            $imageName = "";
        } else {
            $msg = "❌ " . mysqli_error($conn);
            $msg_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Product - Golden Crust Admin</title>
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

        /* ===== MAIN CONTENT ===== */
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
            font-size: 28px;
            font-weight: 700;
            color: #e8436e;
        }

        .page-header .title i {
            color: #e8436e;
            margin-right: 10px;
        }

        /* ===== FORM CONTAINER ===== */
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

        /* ===== FORM ELEMENTS ===== */
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

        /* ===== IMAGE UPLOAD ===== */
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

        /* ===== IMAGE PREVIEW ===== */
        .image-preview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            margin-top: 15px;
        }

        .image-preview-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #ffd1dc;
            aspect-ratio: 1;
            background: #fff;
        }

        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-preview-item .image-order {
            position: absolute;
            bottom: 5px;
            left: 5px;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
        }

        .no-images {
            color: #b395a5;
            text-align: center;
            padding: 30px 0;
            font-size: 14px;
        }

        /* ===== BUTTON ===== */
        .btn-submit {
            width: 100%;
            margin-top: 20px;
            padding: 16px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, #e8436e, #ff6b8a);
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 6px 25px rgba(232, 67, 110, 0.25);
        }

        .btn-submit:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(232, 67, 110, 0.35);
        }

        .btn-submit i {
            margin-right: 10px;
        }

        /* ===== MESSAGES ===== */
        .msg {
            padding: 14px 18px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 15px;
        }

        .msg-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .msg-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .msg-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }

        /* ===== SIDE PREVIEW ===== */
        .main-preview {
            width: 100%;
            height: 380px;
            object-fit: contain;
            border: 2px solid #ffd1dc;
            border-radius: 15px;
            display: none;
            background: #fff;
            padding: 10px;
        }

        .preview-placeholder {
            width: 100%;
            height: 380px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 2px dashed #ffd1dc;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.3);
            color: #b395a5;
        }

        .preview-placeholder i {
            font-size: 60px;
            color: #e8436e;
            opacity: 0.3;
            margin-bottom: 15px;
        }

        .preview-placeholder p {
            font-size: 16px;
        }

        /* ===== RESPONSIVE ===== */
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
                font-size: 22px;
            }

            .btn-submit {
                font-size: 16px;
                padding: 14px;
            }

            .main-preview {
                height: 250px;
            }

            .preview-placeholder {
                height: 250px;
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

            .main-preview {
                height: 200px;
            }

            .preview-placeholder {
                height: 200px;
            }
        }
    </style>
</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="main" id="main">

    <div class="page-header">
        <div class="title">
            <i class="fas fa-plus-circle"></i> Add Product
        </div>
    </div>

    <?php if($msg!=""){ ?>
    <div class="msg msg-<?php echo $msg_type; ?>" id="successMsg">
        <?php echo $msg; ?>
    </div>
    <?php } ?>

    <div class="container">

        <!-- ===== FORM BOX ===== -->
        <div class="form-box">
            <h2><i class="fas fa-pen"></i> Product Details</h2>

            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label><i class="fas fa-cake"></i> Product Name <span class="required">*</span></label>
                    <input type="text" name="itemname" class="form-control" placeholder="Enter product name" value="<?php echo isset($_POST['itemname']) ? htmlspecialchars($_POST['itemname']) : ''; ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-rupee-sign"></i> Price <span class="required">*</span></label>
                        <input type="number" name="price" class="form-control" placeholder="0.00" step="0.01" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-boxes"></i> Stock <span class="required">*</span></label>
                        <input type="number" name="stock" class="form-control" placeholder="Quantity" value="<?php echo isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Category <span class="required">*</span></label>
                    <select name="categories" class="form-control" required>
                        <option value="">Select Category</option>
                        <option value="Chocolate" <?php echo (isset($_POST['categories']) && $_POST['categories'] == 'Chocolate') ? 'selected' : ''; ?>>Chocolate</option>
                        <option value="Fruit" <?php echo (isset($_POST['categories']) && $_POST['categories'] == 'Fruit') ? 'selected' : ''; ?>>Fruit</option>
                        <option value="Bundt" <?php echo (isset($_POST['categories']) && $_POST['categories'] == 'Bundt') ? 'selected' : ''; ?>>Bundt</option>
                        <option value="Velvet" <?php echo (isset($_POST['categories']) && $_POST['categories'] == 'Velvet') ? 'selected' : ''; ?>>Velvet</option>
                        <option value="Celebration" <?php echo (isset($_POST['categories']) && $_POST['categories'] == 'Celebration') ? 'selected' : ''; ?>>Celebration</option>
                        <option value="Ice Cream" <?php echo (isset($_POST['categories']) && $_POST['categories'] == 'Ice Cream') ? 'selected' : ''; ?>>Ice Cream</option>
                        <option value="Cupcake" <?php echo (isset($_POST['categories']) && $_POST['categories'] == 'Cupcake') ? 'selected' : ''; ?>>Cupcake</option>
                        <option value="Roll" <?php echo (isset($_POST['categories']) && $_POST['categories'] == 'Roll') ? 'selected' : ''; ?>>Roll</option>
                        <option value="Pastry" <?php echo (isset($_POST['categories']) && $_POST['categories'] == 'Pastry') ? 'selected' : ''; ?>>Pastry</option>
                    </select>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description" class="form-control" placeholder="Enter product description"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>

                <!-- ===== SINGLE IMAGE UPLOAD ===== -->
                <div class="form-group">
                    <label><i class="fas fa-image"></i> Cake Image <span class="required">*</span></label>
                    <div class="file-upload-wrapper">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Click to upload cake image</p>
                        <span class="hint">JPG, PNG, GIF, WEBP (Max 5MB)</span>
                        <input type="file" name="cake_image" id="cakeImage" accept="image/*" required>
                    </div>
                </div>

                <button type="submit" name="submit" class="btn-submit">
                    <i class="fas fa-plus-circle"></i> Add Product
                </button>

            </form>
        </div>

        <!-- ===== PREVIEW BOX (SIDE) ===== -->
        <div class="preview-box">
            <h2><i class="fas fa-eye"></i> Preview</h2>
            
            <!-- Image Preview - Only in Side -->
            <div id="previewContainer">
                <div class="preview-placeholder" id="placeholder">
                    <i class="fas fa-image"></i>
                    <p>No image selected</p>
                    <span style="font-size: 13px; color: #b395a5;">Select an image from the form</span>
                </div>
                <img id="mainPreview" class="main-preview">
            </div>
            
            <div style="margin-top: 15px; text-align: center; font-size: 13px; color: #6b4b5e; background: #fce4ec; padding: 10px; border-radius: 10px;">
                <i class="fas fa-star" style="color: #ffd700;"></i> Image preview will appear here
            </div>
        </div>

    </div>
</div>

<script>
// ===== SINGLE IMAGE PREVIEW - SIDE ONLY =====
document.getElementById("cakeImage").addEventListener("change", function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('mainPreview');
    const placeholder = document.getElementById('placeholder');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Show image, hide placeholder
            preview.src = e.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else {
        // Show placeholder, hide image
        preview.style.display = 'none';
        placeholder.style.display = 'flex';
    }
});

// ===== AUTO-HIDE MESSAGES =====
setTimeout(function() {
    const msg = document.getElementById("successMsg");
    if (msg) {
        msg.style.transition = "opacity 0.5s ease";
        msg.style.opacity = "0";
        setTimeout(function() {
            if (msg.parentNode) {
                msg.remove();
            }
        }, 500);
    }
}, 4000);

console.log("✅ Add Cake page loaded");
console.log("ℹ️ Single image upload - Preview shows only in side box");
</script>

</body>
</html>