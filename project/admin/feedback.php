<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","golden_crust");

// ===== DELETE FEEDBACK =====
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $query = "DELETE FROM feedback WHERE id = '$id'";
    if(mysqli_query($conn, $query)){
        echo "<script>
            alert('Feedback deleted successfully!');
            window.location='feedback.php';
        </script>";
        
    } else {
        echo "<script>
            alert('Error deleting feedback!');
            window.location='feedback.php';
        </script>";
    }
    exit();
}

// ===== DELETE ALL FEEDBACK =====
if(isset($_GET['delete_all'])){
    $query = "DELETE FROM feedback";
    if(mysqli_query($conn, $query)){
        echo "<script>
            alert('All feedback deleted successfully!');
            window.location='feedback.php';
        </script>";
    } else {
        echo "<script>
            alert('Error deleting feedback!');
            window.location='feedback.php';
        </script>";
    }
    exit();
}

// ===== GET ALL FEEDBACK =====
$query = "SELECT * FROM feedback ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
$total = mysqli_num_rows($result);

// ===== STATISTICS =====
$today_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM feedback WHERE DATE(created_at) = CURDATE()");
$today_count = mysqli_fetch_assoc($today_query)['total'] ?? 0;

$week_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM feedback WHERE WEEK(created_at) = WEEK(CURDATE())");
$week_count = mysqli_fetch_assoc($week_query)['total'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Feedback Management - Golden Crust Admin</title>
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

        /* ===== PAGE HEADER ===== */
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

        .btn-danger {
            padding: 12px 26px;
            background: transparent;
            color: #e8436e;
            border: 2px solid #e8436e;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .btn-danger:hover {
            background: #e8436e;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(232, 67, 110, 0.3);
        }

        /* ===== STATS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            padding: 20px 24px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.04);
            transition: 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(232, 67, 110, 0.08);
        }

        .stat-card .stat-number {
            font-size: 32px;
            font-weight: 800;
            color: #e8436e;
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: #6b4b5e;
            font-weight: 500;
            margin-top: 4px;
        }

        .stat-card .stat-icon {
            font-size: 24px;
            color: #e8436e;
            opacity: 0.3;
            margin-bottom: 4px;
        }

        /* ===== TABLE ===== */
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

        /* ===== SERIAL NUMBER ===== */
        .serial-number {
            font-weight: 700;
            color: #e8436e;
            font-size: 16px;
        }

        /* ===== FEEDBACK TEXT ===== */
        .feedback-text {
            max-width: 280px;
            font-size: 15px;
            color: #4a2c3f;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            cursor: pointer;
            transition: 0.3s;
        }

        .feedback-text:hover {
            -webkit-line-clamp: unset;
            background: rgba(232, 67, 110, 0.04);
            padding: 8px 12px;
            border-radius: 10px;
            max-width: 100%;
        }

        /* ===== USER NAME ===== */
        .user-name {
            font-weight: 600;
            color: #1a0f17;
            font-size: 16px;
        }

        .user-city {
            font-size: 13px;
            color: #6b4b5e;
        }

        .user-city i {
            color: #e8436e;
            margin-right: 3px;
        }

        .user-mobile {
            font-size: 13px;
            color: #6b4b5e;
        }

        .user-mobile i {
            color: #34d399;
            margin-right: 3px;
        }

        /* ===== EMAIL ===== */
        .email-link {
            font-size: 14px;
            color: #e8436e;
            text-decoration: none;
        }

        .email-link:hover {
            text-decoration: underline;
        }

        .email-link i {
            margin-right: 4px;
        }

        /* ===== DATE ===== */
        .date-text {
            font-size: 13px;
            color: #6b4b5e;
        }

        .time-text {
            font-size: 11px;
            color: #6b4b5e;
        }

        /* ===== ACTION BUTTONS ===== */
        .action-btns {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-delete {
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
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: #fff;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(238, 90, 36, 0.3);
        }

        .btn-view {
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
            background: rgba(232, 67, 110, 0.08);
            color: #e8436e;
        }

        .btn-view:hover {
            background: rgba(232, 67, 110, 0.15);
            transform: translateY(-2px);
        }

        /* ===== NO DATA ===== */
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

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .feedback-text {
                max-width: 150px;
                font-size: 13px;
            }
            
            table {
                font-size: 13px;
            }
            
            tbody td {
                padding: 12px 14px;
                font-size: 13px;
            }
            
            .btn-delete, .btn-view {
                padding: 6px 12px;
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-header {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-danger {
                justify-content: center;
                font-size: 14px;
                padding: 10px 20px;
            }

            .action-btns {
                flex-direction: column;
            }

            .btn-delete, .btn-view {
                justify-content: center;
            }
        }

        /* ===== VIEW MODAL ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: #fff;
            border-radius: 24px;
            padding: 35px;
            max-width: 600px;
            width: 100%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            animation: modalIn 0.3s ease;
        }

        @keyframes modalIn {
            from {
                transform: scale(0.9);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .modal-content .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgba(232, 67, 110, 0.06);
        }

        .modal-content .modal-header h3 {
            font-size: 24px;
            color: #1a0f17;
        }

        .modal-content .modal-header h3 i {
            color: #e8436e;
            margin-right: 10px;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            color: #b395a5;
            cursor: pointer;
            transition: 0.3s;
        }

        .modal-close:hover {
            color: #e8436e;
            transform: rotate(90deg);
        }

        .modal-content .modal-body .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid rgba(0,0,0,0.04);
        }

        .modal-content .modal-body .detail-row .label {
            font-weight: 600;
            color: #4a2c3f;
            min-width: 100px;
            font-size: 15px;
        }

        .modal-content .modal-body .detail-row .value {
            color: #1a0f17;
            font-size: 15px;
            word-break: break-word;
        }

        .modal-content .modal-body .detail-row .value .feedback-full {
            background: rgba(232, 67, 110, 0.04);
            padding: 15px;
            border-radius: 12px;
            line-height: 1.8;
            font-size: 16px;
            display: block;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<?php include "includes/sidebar.php"; ?>

<div class="main" id="main">

    <div class="page-header">
        <div class="title">
            <i class="fas fa-star"></i> Feedback Management
            <span class="badge"><?php echo $total; ?> Feedbacks</span>
        </div>
        <?php if($total > 0): ?>
            <a href="?delete_all=1" class="btn-danger" onclick="return confirm('Are you sure you want to delete ALL feedback?')">
                <i class="fas fa-trash-alt"></i> Delete All
            </a>
        <?php endif; ?>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-star"></i></div>
            <div class="stat-number"><?php echo $total; ?></div>
            <div class="stat-label">Total Feedbacks</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-number"><?php echo $today_count; ?></div>
            <div class="stat-label">Today's Feedbacks</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
            <div class="stat-number"><?php echo $week_count; ?></div>
            <div class="stat-label">This Week</div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th><i class="fas fa-user"></i> User</th>
                        <th><i class="fas fa-envelope"></i> Email</th>
                        <th><i class="fas fa-comment"></i> Feedback</th>
                        <th><i class="fas fa-calendar-alt"></i> Date</th>
                        <th><i class="fas fa-cog"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total > 0): ?>
                        <?php $sr = 1; while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td class="serial-number"><?php echo $sr++; ?></td>
                            <td>
                                <div>
                                    <div class="user-name"><?php echo htmlspecialchars($row['name']); ?></div>
                                    <?php if(!empty($row['city'])): ?>
                                        <div class="user-city"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['city']); ?></div>
                                    <?php endif; ?>
                                    <?php if(!empty($row['mno'])): ?>
                                        <div class="user-mobile"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($row['mno']); ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <a href="mailto:<?php echo $row['email']; ?>" class="email-link">
                                    <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($row['email']); ?>
                                </a>
                            </td>
                            <td>
                                <div class="feedback-text" title="Click to view full feedback">
                                    <?php echo htmlspecialchars($row['feedback']); ?>
                                </div>
                            </td>
                            <td>
                                <div class="date-text"><?php echo date('d M Y', strtotime($row['created_at'])); ?></div>
                                <div class="time-text"><?php echo date('h:i A', strtotime($row['created_at'])); ?></div>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn-view" onclick="viewFeedback(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>', '<?php echo addslashes($row['city']); ?>', '<?php echo addslashes($row['mno']); ?>', '<?php echo addslashes($row['email']); ?>', '<?php echo addslashes($row['feedback']); ?>', '<?php echo $row['created_at']; ?>')">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this feedback?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="no-data">
                                    <i class="fas fa-star"></i>
                                    <h3>No Feedback Yet</h3>
                                    <p>No feedback submitted by users yet.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ===== VIEW MODAL ===== -->
<div class="modal-overlay" id="viewModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-star"></i> Feedback Details</h3>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <div class="modal-body">
            <div class="detail-row">
                <span class="label"><i class="fas fa-user"></i> Name</span>
                <span class="value" id="modalName">-</span>
            </div>
            <div class="detail-row">
                <span class="label"><i class="fas fa-city"></i> City</span>
                <span class="value" id="modalCity">-</span>
            </div>
            <div class="detail-row">
                <span class="label"><i class="fas fa-phone"></i> Mobile</span>
                <span class="value" id="modalMno">-</span>
            </div>
            <div class="detail-row">
                <span class="label"><i class="fas fa-envelope"></i> Email</span>
                <span class="value" id="modalEmail">-</span>
            </div>
            <div class="detail-row">
                <span class="label"><i class="fas fa-calendar-alt"></i> Date</span>
                <span class="value" id="modalDate">-</span>
            </div>
            <div class="detail-row" style="border-bottom: none;">
                <span class="label"><i class="fas fa-comment"></i> Feedback</span>
                <span class="value" id="modalFeedback">-</span>
            </div>
        </div>
    </div>
</div>

<script>
function viewFeedback(id, name, city, mno, email, feedback, date) {
    document.getElementById('modalName').textContent = name || '-';
    document.getElementById('modalCity').textContent = city || '-';
    document.getElementById('modalMno').textContent = mno || '-';
    document.getElementById('modalEmail').textContent = email || '-';
    document.getElementById('modalDate').textContent = date ? new Date(date).toLocaleDateString('en-IN', { 
        day: 'numeric', 
        month: 'short', 
        year: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit' 
    }) : '-';
    
    const feedbackEl = document.getElementById('modalFeedback');
    feedbackEl.innerHTML = '<span class="feedback-full">' + feedback + '</span>';
    
    document.getElementById('viewModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('viewModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

document.getElementById('viewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModal();
    }
});

console.log('✅ Admin Feedback Management Loaded');
console.log('📊 Total Feedbacks: <?php echo $total; ?>');
</script>

</body>
</html>