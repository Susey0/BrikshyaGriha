<?php
session_start();
$host = 'localhost';
$dbname = 'brikshya_griha';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch statistics
$totalUsers = 0;
$totalProducts = 0;
$totalOrders = 0;
$recentOrders = [];

// Total users
$sqlTotalUsers = 'SELECT COUNT(*) AS total_users FROM users';
$resultTotalUsers = $conn->query($sqlTotalUsers);
if ($resultTotalUsers) {
    $rowTotalUsers = $resultTotalUsers->fetch_assoc();
    $totalUsers = $rowTotalUsers['total_users'];
}

// Total products
$sqlTotalProducts = 'SELECT COUNT(*) AS total_products FROM products';
$resultTotalProducts = $conn->query($sqlTotalProducts);
if ($resultTotalProducts) {
    $rowTotalProducts = $resultTotalProducts->fetch_assoc();
    $totalProducts = $rowTotalProducts['total_products'];
}

// Total orders
$sqlTotalOrders = 'SELECT COUNT(*) AS total_orders FROM orders';
$resultTotalOrders = $conn->query($sqlTotalOrders);
if ($resultTotalOrders) {
    $rowTotalOrders = $resultTotalOrders->fetch_assoc();
    $totalOrders = $rowTotalOrders['total_orders'];
}

// Recent orders
$sqlRecentOrders = 'SELECT orders.id, users.username, orders.order_date, orders.total_amount 
                    FROM orders JOIN users ON orders.user_id = users.id 
                    ORDER BY orders.order_date DESC LIMIT 5';
$resultRecentOrders = $conn->query($sqlRecentOrders);
if ($resultRecentOrders) {
    while ($rowRecentOrders = $resultRecentOrders->fetch_assoc()) {
        $recentOrders[] = $rowRecentOrders;
    }
}
// Fetch products with stock below their low stock threshold
$sqlLowStock = "SELECT name, stock
                FROM products 
                WHERE stock < 5";

$resultLowStock = $conn->query($sqlLowStock);

$lowStockProducts = [];
if ($resultLowStock) {
    while ($rowLowStock = $resultLowStock->fetch_assoc()) {
        $lowStockProducts[] = $rowLowStock;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/adminpanel.css">
    <script>
    function confirmLogout() {
        return confirm("Are you sure you want to logout?");
    }
</script>

</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="images/admin.jpg" alt="Admin Logo">
            <span>Admin</span>
        </div>
        <div class="menu">
            <a href="admin_panel.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="category_management.php">
                <i class="fas fa-th-list"></i> Category Management
            </a>
            <a href="product_management.php">
                <i class="fas fa-box"></i> Product Management
            </a>
            <a href="order_management.php">
                <i class="fas fa-shopping-cart"></i> Order Management
            </a>
            <a href="user_management.php">
                <i class="fas fa-users"></i> User Management
            </a>
            <a href="sales_report.php">
                <i class="fas fa-chart-line"></i> Sales Report
            </a>
            <a href="ContactUs/contact_management.php">
                <i class="fas fa-envelope"></i> Contact Messages
            </a>

            <a href="logout.php"onclick="return confirmLogout();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <h1>Dashboard</h1>
        <div class="low-stock-alert">
            <?php if (count($lowStockProducts) > 0): ?>
                <div class="alert alert-warning">
                    <h3>Low Stock Alert</h3>
                    <p>The following products have less than 5 in stock:</p>
                    <ul>
                        <?php foreach ($lowStockProducts as $product): ?>
                            <li><?php echo $product['name']; ?> - 
                                Stock: <?php echo $product['stock']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php else: ?>
                <div class="alert alert-success">
                    <p>All products are well-stocked.</p>
                </div>
            <?php endif; ?>
        </div>


        <div class="dashboard-content">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-info">
                    <p>Total Users</p>
                    <h3><?php echo $totalUsers; ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-box"></i>
                <div class="stat-info">
                    <p>Total Products</p>
                    <h3><?php echo $totalProducts; ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <i class="fas fa-shopping-cart"></i>
                <div class="stat-info">
                    <p>Total Orders</p>
                    <h3><?php echo $totalOrders; ?></h3>
                </div>
            </div>
        </div>

        <div class="recent-orders">
            <h3>Recent Orders</h3>
            <ul>
                <?php foreach ($recentOrders as $order): ?>
                    <li>
                        <p><strong>Order ID:</strong> <?php echo $order['id']; ?></p>
                        <p><strong>Username:</strong> <?php echo $order['username']; ?></p>
                        <p><strong>Order Date:</strong> <?php echo $order['order_date']; ?></p>
                        <p><strong>Total Amount:</strong> Rs.<?php echo number_format($order['total_amount'], 2); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>