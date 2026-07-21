<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Get cart count for badge
$conn = mysqli_connect("localhost","root","","golden_crust");
$cart_count_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM cart");
$cart_total = mysqli_fetch_assoc($cart_count_query);
$cart_count = $cart_total['total'] ?? 0;
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<div class="sidebar" id="sidebar">

    <!-- TOP -->
    <div class="top-section">
        <div class="toggle-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </div>
        <h2 class="logo-text">Golden Crust</h2>
    </div>

    <!-- MENU -->
    <ul>
        <li>
            <a href="dashboard.php" class="<?= ($currentPage=='dashboard.php') ? 'active' : '' ?>">
                <i class="fas fa-chart-pie icon"></i>
                <span class="text">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="add_cake.php" class="<?= ($currentPage=='add_cake.php') ? 'active' : '' ?>">
                <i class="fas fa-plus-circle icon"></i>
                <span class="text">Add Cake</span>
            </a>
        </li>
        <li>
            <a href="show_cakes.php" class="<?= ($currentPage=='show_cakes.php') ? 'active' : '' ?>">
                <i class="fas fa-cake-candles icon"></i>
                <span class="text">Show Cakes</span>
            </a>
        </li>
        <li>
            <a href="users.php" class="<?= ($currentPage=='users.php') ? 'active' : '' ?>">
                <i class="fas fa-users icon"></i>
                <span class="text">Users</span>
            </a>
        </li>
        
        <!-- ===== CART ===== -->
        <li>
            <a href="admin_cart.php" class="<?= ($currentPage=='admin_cart.php') ? 'active' : '' ?>">
                <i class="fas fa-cart-plus icon"></i>
                <span class="text">Cart</span>
                <?php if($cart_count > 0): ?>
                    <span class="badge"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>
        </li>
        
        <!-- ===== PURCHASES / ORDERS ===== -->
        <li>
            <a href="purchases.php" class="<?= ($currentPage=='purchases.php') ? 'active' : '' ?>">
                <i class="fas fa-shopping-cart icon"></i>
                <span class="text">Purchases</span>
            </a>
        </li>
        
        <!-- ===== ADMIN MANAGEMENT ===== -->
        <li>
            <a href="admin_list.php" class="<?= ($currentPage=='admin_list.php') ? 'active' : '' ?>">
                <i class="fas fa-user-shield icon"></i>
                <span class="text">Admins</span>
            </a>
        </li>
        
        <li>
            <a href="feedback.php" class="<?= ($currentPage=='feedback.php') ? 'active' : '' ?>">
                <i class="fas fa-star icon"></i>
                <span class="text">Feedback</span>
            </a>
        </li>
    </ul>

    <!-- LOGOUT -->
    <div class="logout-box">
        <a href="logout.php">
            <i class="fas fa-right-from-bracket icon"></i>
            <span class="text">Logout</span>
        </a>
    </div>

</div>

<style>
/* RESET */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

/* SIDEBAR - Baby Pink */
.sidebar{
    position:fixed;
    top:0;
    left:0;
    width:85px;
    height:100vh;
    background:rgba(252,228,236,.95);
    backdrop-filter:blur(15px);
    border-right:1px solid rgba(255,255,255,.4);
    box-shadow:5px 0 25px rgba(232,67,110,.12);
    overflow:hidden;
    transition:width 0.35s ease;
    z-index:9999;
    display:flex;
    flex-direction:column;
}

/* OPEN STATE */
.sidebar.active{
    width:260px;
}

/* TOP */
.top-section{
    height:80px;
    display:flex;
    align-items:center;
    padding:0 20px;
    border-bottom:1px solid rgba(255,255,255,.5);
    flex-shrink:0;
}

.toggle-btn{
    color:#e8436e;
    font-size:24px;
    cursor:pointer;
    min-width:35px;
    z-index:10;
    transition:transform 0.3s ease;
}

.toggle-btn:hover {
    transform: scale(1.1);
}

.logo-text{
    margin-left:15px;
    color:#e8436e;
    white-space:nowrap;
    display:none;
    font-size:22px;
    font-weight:700;
    opacity:0;
    transition:opacity 0.3s ease;
}

.sidebar.active .logo-text{
    display:block;
    opacity:1;
}

/* MENU */
.sidebar ul{
    list-style:none;
    padding:0 10px;
    flex:1;
    overflow-y:auto;
    margin-top:10px;
}

.sidebar ul li{
    margin-bottom:4px;
}

.sidebar ul li a{
    display:flex;
    align-items:center;
    color:#9c6b79;
    text-decoration:none;
    padding:12px 16px;
    transition:.25s;
    border-radius:15px;
    position:relative;
}

/* BADGE */
.sidebar ul li a .badge {
    margin-left:auto;
    background: #e8436e;
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    border-radius: 50%;
    display: none;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 10px rgba(232, 67, 110, 0.3);
}

.sidebar.active .badge {
    display: flex;
}

/* ACTIVE BADGE COLOR */
.sidebar ul li a.active .badge {
    background: #fff;
    color: #e8436e;
}

/* HOVER */
.sidebar ul li a:hover{
    background:rgba(232,67,110,.12);
    color:#e8436e;
}

.sidebar ul li a:hover .badge {
    background: #e8436e;
    color: #fff;
}

.sidebar ul li a.active:hover .badge {
    background: #fff;
    color: #e8436e;
}

/* ACTIVE PAGE HIGHLIGHT - Baby Pink */
.sidebar ul li a.active{
    background:linear-gradient(135deg,#e8436e,#ff6b8a);
    color:white;
    box-shadow:0 10px 20px rgba(232,67,110,.25);
}

/* ICON */
.icon{
    font-size:22px;
    min-width:30px;
    text-align:center;
}

/* TEXT */
.text{
    margin-left:18px;
    white-space:nowrap;
    display:none;
    opacity:0;
    transition:opacity 0.3s ease;
}

.sidebar.active .text{
    display:block;
    opacity:1;
}

/* LOGOUT - Baby Pink */
.logout-box{
    padding:15px 20px;
    border-top:1px solid rgba(255,255,255,.5);
    flex-shrink:0;
    background:rgba(255,255,255,0.3);
}

.logout-box a{
    display:flex;
    align-items:center;
    text-decoration:none;
    background:linear-gradient(135deg,#e8436e,#ff6b8a);
    color:white;
    padding:12px 18px;
    border-radius:15px;
    transition:transform 0.3s ease;
}

.logout-box a:hover {
    transform: translateX(5px);
}

.logout-box a .icon{
    color:white;
}

/* MAIN SHIFT FIX */
.main{
    margin-left:85px;
    transition:margin-left 0.35s ease;
    min-height:100vh;
    width: calc(100% - 85px);
}

.main.shift{
    margin-left:260px;
    width: calc(100% - 260px);
}

/* MOBILE */
@media(max-width:768px){
    .sidebar{ width:75px; }
    .sidebar.active{ width:230px; }

    .main{
        margin-left:75px;
        width: calc(100% - 75px);
    }
    
    .main.shift{
        margin-left:230px;
        width: calc(100% - 230px);
    }
}

/* SCROLLBAR - Baby Pink */
.sidebar ul::-webkit-scrollbar {
    width: 4px;
}

.sidebar ul::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar ul::-webkit-scrollbar-thumb {
    background: #e8436e;
    border-radius: 10px;
}
</style>

<script>
// ============================================================
// ===== SIDEBAR FUNCTIONALITY =====
// ============================================================

document.addEventListener("DOMContentLoaded", function() {

    const sidebar = document.getElementById("sidebar");
    const main = document.getElementById("main");

    if (!sidebar) {
        console.error("Sidebar element not found!");
        return;
    }

    let savedState = localStorage.getItem("sidebar_state");
    
    if (savedState === null) {
        savedState = "open";
        localStorage.setItem("sidebar_state", "open");
    }

    if (savedState === "closed") {
        sidebar.classList.remove("active");
        if (main) main.classList.remove("shift");
    } else {
        sidebar.classList.add("active");
        if (main) main.classList.add("shift");
    }
});

/* ===== TOGGLE SIDEBAR ===== */
function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const main = document.getElementById("main");

    if (!sidebar) return;

    sidebar.classList.toggle("active");
    if (main) main.classList.toggle("shift");

    if (sidebar.classList.contains("active")) {
        localStorage.setItem("sidebar_state", "open");
    } else {
        localStorage.setItem("sidebar_state", "closed");
    }
}

// ===== DEBUG FUNCTIONS =====
function checkSidebarState() {
    const sidebar = document.getElementById("sidebar");
    const state = localStorage.getItem("sidebar_state");
    console.log("=== SIDEBAR STATUS ===");
    console.log("Current State:", state);
    console.log("Sidebar has 'active' class:", sidebar ? sidebar.classList.contains("active") : "Not found");
    console.log("======================");
}
</script>