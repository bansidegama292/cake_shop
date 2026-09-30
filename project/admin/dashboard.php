<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: admin_login.php");
    exit();
}

$conn = mysqli_connect("localhost","root","","golden_crust");

$totalCakes = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM cakes"));

$totalUsers = 0;
$userQuery = mysqli_query($conn,"SHOW TABLES LIKE 'users'");
if(mysqli_num_rows($userQuery)>0){
    $totalUsers = mysqli_num_rows(mysqli_query($conn,"SELECT * FROM users"));
}

$totalOrders = 25;
$totalRevenue = 12000;

$monthlyData = [45,70,60,110,90,130];
$weeklyData  = [12,19,8,15];
$revenueTrend = [2000, 3500, 2800, 4200, 5000, 6500];

$topProducts = ["Black Forest","Chocolate Cake","Strawberry","Cheese Cake","Vanilla"];
$downProducts = ["Dry Cake","Old Cake","Mini Cake","Plain Cake","Sugar Less"];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Golden Crust Dashboard</title>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* RESET */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Inter, sans-serif;
}

/* BODY - Baby Pink */
body{
    background: linear-gradient(135deg,#fce4ec,#f8bbd0,#fce4ec,#f8bbd0);
    min-height: 100vh;
}

/* WRAPPER */
.wrapper{
    display:flex;
}

/* SIDEBAR WIDTH FIX */
.sidebar{
    width:85px;
}

/* MAIN IMPORTANT FIX */
.main{
    margin-left:85px;
    padding:25px;
    transition:0.35s ease;
    width:100%;
}

/* 🔥 SHIFT CLASS (MAIN FIX) */
.main.shift{
    margin-left:260px;
}

/* TOP - Baby Pink */
.top-banner{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:20px 28px;
    border-radius:18px;
    background: linear-gradient(135deg,#e8436e,#ff6b8a,#ff8a9f);
    color:white;
    margin-bottom:25px;
    box-shadow: 0 4px 20px rgba(232, 67, 110, 0.2);
}

.top-banner h2 {
    font-weight: 700;
    font-size: 24px;
}

.top-banner p {
    opacity: 0.85;
    font-size: 14px;
}

.top-banner div:last-child {
    background: rgba(255,255,255,0.2);
    padding: 8px 18px;
    border-radius: 30px;
    font-size: 14px;
    font-weight: 500;
}

/* CARDS - Baby Pink */
.cards{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:18px;
    margin-bottom:25px;
}

.card{
    background: rgba(255,255,255,0.9);
    padding:22px 20px;
    border-radius:18px;
    transition:0.3s;
    border: 1px solid rgba(232, 67, 110, 0.08);
    box-shadow: 0 2px 12px rgba(232, 67, 110, 0.06);
    color: #4a2a3a;
}

.card:hover{
    transform:translateY(-8px);
    background: linear-gradient(135deg, #e8436e, #ff6b8a);
    color:white;
    box-shadow: 0 8px 30px rgba(232, 67, 110, 0.25);
}

.card h2{
    font-size: 32px;
    margin-top: 6px;
    font-weight: 700;
}

.card:hover h2 {
    color: white;
}

/* ===== CHARTS GRID - 2x2 WITH GAP ===== */
.charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 25px;
}

/* BOX - Chart Card */
.box{
    background: rgba(255,255,255,0.92);
    border-radius:18px;
    padding:20px 22px;
    height:330px;
    display:flex;
    flex-direction:column;
    border: 1px solid rgba(232, 67, 110, 0.06);
    box-shadow: 0 2px 12px rgba(232, 67, 110, 0.05);
    transition: 0.3s;
}

.box:hover {
    box-shadow: 0 4px 20px rgba(232, 67, 110, 0.08);
}

.box h3 {
    color: #4a2a3a;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 12px;
    flex-shrink: 0;
}

/* CANVAS FIX */
.box canvas{
    flex:1;
    width:100% !important;
    height:100% !important;
}

/* PRODUCTS */
.products-row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
    margin-top:5px;
}

.product-box{
    background: rgba(255,255,255,0.92);
    padding:20px;
    border-radius:18px;
    border: 1px solid rgba(232, 67, 110, 0.06);
    box-shadow: 0 2px 12px rgba(232, 67, 110, 0.05);
    transition: 0.3s;
}

.product-box:hover {
    box-shadow: 0 4px 20px rgba(232, 67, 110, 0.08);
}

.product-box h3 {
    color: #4a2a3a;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 12px;
}

/* LIST - Baby Pink */
.list li{
    list-style:none;
    padding:12px 16px;
    margin:6px 0;
    border-radius:12px;
    background: rgba(252, 228, 236, 0.4);
    border-left:4px solid #e8436e;
    transition:0.3s;
    color: #4a2a3a;
    font-weight: 500;
    font-size: 14px;
}

.list li:hover{
    transform:translateX(6px);
    background: linear-gradient(135deg, #e8436e, #ff6b8a);
    color:white;
    border-left-color: white;
}

/* ===== RESPONSIVE ===== */
@media(max-width:992px){
    .charts-grid {
        grid-template-columns: 1fr 1fr;
        gap: 18px;
    }
    
    .cards {
        grid-template-columns: repeat(2,1fr);
    }
}

@media(max-width:768px){
    .charts-grid {
        grid-template-columns: 1fr;
        gap: 18px;
    }
    
    .main {
        padding: 15px;
        margin-left: 0;
    }
    
    .main.shift {
        margin-left: 0;
    }
    
    .cards {
        grid-template-columns: repeat(2,1fr);
        gap: 12px;
    }
    
    .products-row {
        grid-template-columns: 1fr;
    }
    
    .top-banner {
        flex-direction: column;
        text-align: center;
        gap: 10px;
        padding: 20px;
    }
    
    .top-banner div:last-child {
        margin-top: 5px;
    }
    
    .box {
        height: 280px;
        padding: 15px;
    }
}

@media(max-width:500px){
    .cards {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .card {
        padding: 16px;
    }
    
    .card h2 {
        font-size: 26px;
    }
    
    .box {
        height: 250px;
        padding: 12px;
    }
    
    .box h3 {
        font-size: 14px;
    }
}
</style>
</head>

<body>

<div class="wrapper">

<?php include "includes/sidebar.php"; ?>

<div class="main" id="main">

<!-- TOP -->
<div class="top-banner">
    <div>
        <h2>🌸 Welcome</h2>
        <p>Golden Crust Dashboard</p>
    </div>
    <div><?= date('d M Y') ?></div>
</div>

<!-- CARDS -->
<div class="cards">
    <div class="card">🎂 Cakes <h2><?= $totalCakes ?></h2></div>
    <div class="card">👥 Users <h2><?= $totalUsers ?></h2></div>
    <div class="card">🛒 Orders <h2><?= $totalOrders ?></h2></div>
    <div class="card">💰 Revenue <h2>₹<?= $totalRevenue ?></h2></div>
</div>

<!-- ===== CHARTS GRID - 2x2 WITH DIFFERENT CHART TYPES ===== -->
<div class="charts-grid">

    <!-- Chart 1: Monthly Growth - BAR CHART -->
    <div class="box">
        <h3>📈 Monthly Growth <span style="font-size:12px;font-weight:400;color:#999;">(Bar Chart)</span></h3>
        <canvas id="month"></canvas>
    </div>

    <!-- Chart 2: Weekly Report - LINE CHART -->
    <div class="box">
        <h3>📊 Weekly Report <span style="font-size:12px;font-weight:400;color:#999;">(Line Chart)</span></h3>
        <canvas id="week"></canvas>
    </div>

    <!-- Chart 3: Overview - DOUGHNUT CHART -->
    <div class="box">
        <h3>📊 Overview <span style="font-size:12px;font-weight:400;color:#999;">(Doughnut Chart)</span></h3>
        <canvas id="pie"></canvas>
    </div>

    <!-- Chart 4: Revenue Trend - AREA CHART (Line with fill) -->
    <div class="box">
        <h3>💰 Revenue Trend <span style="font-size:12px;font-weight:400;color:#999;">(Area Chart)</span></h3>
        <canvas id="revenue"></canvas>
    </div>

</div>

<!-- PRODUCTS -->
<div class="products-row">

<div class="product-box">
    <h3>🔥 Top Products</h3>
    <ul class="list">
        <?php foreach($topProducts as $p): ?>
            <li>⭐ <?= $p ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<div class="product-box">
    <h3>📉 Low Products</h3>
    <ul class="list">
        <?php foreach($downProducts as $p): ?>
            <li><?= $p ?></li>
        <?php endforeach; ?>
    </ul>
</div>

</div>

</div>
</div>

<script>

/* ============================================
   CHART 1: BAR CHART - Monthly Growth
   ============================================ */
new Chart(document.getElementById('month'),{
    type:'bar',
    data:{
        labels:['Jan','Feb','Mar','Apr','May','Jun'],
        datasets:[{
            label:'Monthly Growth',
            data:<?= json_encode($monthlyData) ?>,
            backgroundColor:['#e8436e','#ff6b8a','#ff8a9f','#e8436e','#ff6b8a','#ff8a9f'],
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{display:false}
        },
        scales:{
            y:{
                beginAtZero:true,
                grid:{color:'rgba(232,67,110,0.06)'}
            },
            x:{
                grid:{display:false}
            }
        }
    }
});

/* ============================================
   CHART 2: LINE CHART - Weekly Report
   ============================================ */
new Chart(document.getElementById('week'),{
    type:'line',
    data:{
        labels:['W1','W2','W3','W4'],
        datasets:[{
            label:'Weekly Report',
            data:<?= json_encode($weeklyData) ?>,
            borderColor:'#e8436e',
            backgroundColor:'rgba(232,67,110,0.08)',
            fill:false,
            tension:0.4,
            pointBackgroundColor:'#e8436e',
            pointBorderColor:'#ffffff',
            pointBorderWidth:2,
            pointRadius:6
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{display:false}
        },
        scales:{
            y:{
                beginAtZero:true,
                grid:{color:'rgba(232,67,110,0.06)'}
            },
            x:{
                grid:{display:false}
            }
        }
    }
});

/* ============================================
   CHART 3: DOUGHNUT CHART - Overview
   ============================================ */
new Chart(document.getElementById('pie'),{
    type:'doughnut',
    data:{
        labels:['Cakes','Users','Orders'],
        datasets:[{
            data:[<?= $totalCakes ?>,<?= $totalUsers ?>,<?= $totalOrders ?>],
            backgroundColor:['#e8436e','#ff6b8a','#ffd1dc'],
            borderColor: ['#ffffff', '#ffffff', '#ffffff'],
            borderWidth: 3
        }]
    },
    options:{
        cutout:'65%',
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{
                position:'bottom',
                labels:{
                    usePointStyle:true,
                    padding:12,
                    font:{size:11,weight:'500'}
                }
            }
        }
    }
});

/* ============================================
   CHART 4: AREA CHART - Revenue Trend (Line with Fill)
   ============================================ */
new Chart(document.getElementById('revenue'),{
    type:'line',
    data:{
        labels:['Jan','Feb','Mar','Apr','May','Jun'],
        datasets:[{
            label:'Revenue Trend',
            data:<?= json_encode($revenueTrend) ?>,
            borderColor:'#e8436e',
            backgroundColor:'rgba(232,67,110,0.25)',
            fill:true,
            tension:0.4,
            pointBackgroundColor:'#ff6b8a',
            pointBorderColor:'#ffffff',
            pointBorderWidth:2,
            pointRadius:5
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{display:false}
        },
        scales:{
            y:{
                beginAtZero:true,
                grid:{color:'rgba(232,67,110,0.06)'}
            },
            x:{
                grid:{display:false}
            }
        }
    }
});

</script>

</body>
</html>