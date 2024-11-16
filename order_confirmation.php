<?php
session_start();
include 'config.php';
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('User not logged in'); window.location.href = 'index.php';</script>";
    exit;
}


// Check if the order ID is provided
if (!isset($_GET['order_id'])) {
    echo "<script>alert('Invalid order. Please try again.'); window.location.href = 'shop.php';</script>";
    exit;
}

$orderId = $_GET['order_id'];

// Check if the order has already been processed
if (isset($_SESSION['order_processed']) && $_SESSION['order_processed'] === $orderId) {
    // If the order has already been processed, redirect to avoid duplicate insertion
    echo "<script>alert('This order has already been processed.'); window.location.href = 'shop.php';</script>";
    exit;
}

// Fetch order details
$orderStmt = $conn->prepare("SELECT o.*, u.first_name, u.last_name FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$orderStmt->bind_param("i", $orderId);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

// Check if the order exists
if ($orderResult->num_rows === 0) {
    echo "<script>alert('Order not found. Please contact support.'); window.location.href = 'shop.php';</script>";
    exit;
}

$order = $orderResult->fetch_assoc();

// Fetch the ordered items
$orderItemsStmt = $conn->prepare("SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$orderItemsStmt->bind_param("i", $orderId);
$orderItemsStmt->execute();
$orderItemsResult = $orderItemsStmt->get_result();

// Check if order has items
if ($orderItemsResult->num_rows === 0) {
    echo "<script>alert('No items found in this order.'); window.location.href = 'shop.php';</script>";
    exit;
}

// Insert data into the sales table
$salesStmt = $conn->prepare("INSERT INTO sales (order_id, user_id, product_id, quantity, price, total_amount) VALUES (?, ?, ?, ?, ?, ?)");

// Insert each item into the sales table
while ($item = $orderItemsResult->fetch_assoc()) {
    $productId = $item['product_id']; // Product ID from order_items
    $quantity = $item['quantity']; // Quantity of the item
    $price = $item['price']; // Price of the item
    $totalAmount = $quantity * $price; // Calculate total amount

    // Bind parameters for sales
    $salesStmt->bind_param("iiiddd", $orderId, $order['user_id'], $productId, $quantity, $price, $totalAmount);

    // Execute the insert for each item
    if (!$salesStmt->execute()) {
        echo "<script>alert('Error inserting sales data: " . $salesStmt->error . "'); window.location.href = 'shop.php';</script>";
        exit;
    }
}

// Mark this order as processed to avoid duplicate insertion
$_SESSION['order_processed'] = $orderId;

$salesStmt->close();
$userId = $_SESSION['user_id'];

// Fetch user information
$userResult = $conn->query("SELECT * FROM users WHERE id = $userId");

if (!$userResult) {
    echo "Error fetching user information: " . $conn->error;
    exit;
}

$user = $userResult->fetch_assoc();
if (!$user) {
    echo "<h3>User not found.</h3>";
    exit;
}
// Fetch the ordered items again to display them
$orderItemsResult->data_seek(0); // Reset the result pointer to the beginning
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Brikshya Griha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/track_order.css">


    <script>
    function confirmLogout() {
        return confirm("Are you sure you want to logout?");
    }
    </script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="images/logo.jpg" alt="Brikshya Griha" width="30" height="30" class="d-inline-block align-text-top">
            Brikshya Griha
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" href="shop.php">
                        <i class="fas fa-store"></i> Shop
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="aboutus.php">
                        <i class="fas fa-info-circle"></i> About Us
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contactus.php">
                        <i class="fas fa-address-book"></i> Contact Us
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user['username']); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="track_order.php">Track Order</a></li> 
                    <li><a class = "dropdown-item" href="order_history.php">Order History</a></li>

                    <li><a class="dropdown-item" href="logout.php"onclick="return confirmLogout();">Sign Out</a></li>
                    </ul>
            </div>
        </div>
    </div>
</nav>


<div class="container my-5">
    <h4>Order Confirmation</h4>
    <div class="order-details mt-4">
        <h5>Thank you, <?php echo htmlspecialchars($order['first_name'] . ' ' . $order['last_name']); ?>!</h5>
        <p>Your order has been placed successfully.</p>
        <p>Order ID: <strong><?php echo htmlspecialchars($order['id']); ?></strong></p>
        <p>Order Date: <strong><?php echo htmlspecialchars(date('Y-m-d H:i:s', strtotime($order['order_date']))); ?></strong></p>
        <h6>Shipping Information</h6>
        <p><?php echo htmlspecialchars($order['full_name']); ?></p>
        <p><?php echo htmlspecialchars($order['address']); ?></p>
        <p>Phone: <strong><?php echo htmlspecialchars($order['phone']); ?></strong></p>
        <p>Email: <strong><?php echo htmlspecialchars($order['email']); ?></strong></p>
        <h6>Payment Method</h6>
        <p><?php echo htmlspecialchars(ucfirst($order['payment_method'])); ?></p>

        <h5 class="grand-total">Grand Total: Rs <?php echo number_format($order['total_amount'], 2); ?></h5>
    </div>

    <h5 class="mt-5">Ordered Items</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop through the order items and display them
            while ($item = $orderItemsResult->fetch_assoc()) {
                $itemTotal = $item['quantity'] * $item['price'];
                $imagePath = "uploads/" . htmlspecialchars($item['image']);
                echo "<tr>
                        <td>
                            <img src='" . $imagePath . "' alt='" . htmlspecialchars($item['name']) . "' width='50' height='50'>
                            " . htmlspecialchars($item['name']) . "
                        </td>
                        <td>" . htmlspecialchars($item['quantity']) . "</td>
                        <td>Rs " . number_format($item['price'], 2) . "</td>
                        <td>Rs " . number_format($itemTotal, 2) . "</td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<footer>
    <div class="container text-center">
        <h3>Brikshya Griha</h3>
        <p>Thank you for shopping with us!</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
