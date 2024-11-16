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

// Fetch all orders
$orders = [];
$sqlOrders = 'SELECT id, full_name, email, phone, address, payment_method, total_amount, order_date, status 
              FROM orders 
              ORDER BY order_date DESC';
$resultOrders = $conn->query($sqlOrders);

if ($resultOrders) {
    while ($rowOrder = $resultOrders->fetch_assoc()) {
        // Fetch products associated with each order
        $orderId = $rowOrder['id'];
        $sqlProducts = 'SELECT p.name, p.image, oi.quantity, oi.price 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?';

    $stmt = $conn->prepare($sqlProducts);
    $stmt->bind_param('i', $orderId);  // Bind order_id as integer
    $stmt->execute();
    $resultProducts = $stmt->get_result();
        $products = [];
        if ($resultProducts) {
            while ($rowProduct = $resultProducts->fetch_assoc()) {
                $products[] = $rowProduct;
            }
        }

        // Add products to the order data
        $rowOrder['products'] = $products;
        $orders[] = $rowOrder;
        $stmt->close();
    }
}

// Update order status
if (isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['status'];

    $sqlUpdate = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param('si', $new_status, $order_id);  // Bind status as string and order_id as integer
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Order status updated successfully!";
        header('Location: order_management.php'); // Refresh page to reflect changes
        exit();
    } else {
        echo "Error updating status: " . $conn->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="css/order_management.css">
    <script>
        // Function to hide the alert message after a delay
        function hideAlert() {
            const alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.display = 'none'; // Hide the alert
                }, 5000); // Change this number to control how long the message is displayed (5000ms = 5 seconds)
            }
        }

        // Call the function on page load
        window.onload = hideAlert;

    </script>
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
        <h1>Order Management</h1>
        <!-- Display the success message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success">
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']); // Clear message after display
                ?>
            </div>
        <?php elseif (isset($_SESSION['error_message'])): ?>
            <div class="alert error">
                <?php
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']); // Clear message after display
                ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Payment Method</th>
                    <th>Total Amount (Rs.)</th>
                    <th>Order Date</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo $order['id']; ?></td>
                        <td><?php echo $order['full_name']; ?></td>
                        <td><?php echo $order['email']; ?></td>
                        <td><?php echo $order['phone']; ?></td>
                        <td><?php echo $order['address']; ?></td>
                        <td><?php echo $order['payment_method']; ?></td>
                        <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                        <td><?php echo $order['order_date']; ?></td>
                        <td>
                        <ul>
                                <?php foreach ($order['products'] as $product): ?>
                                    
                                    <li>
                                        <?php echo $product['name']; ?> (Qty: <?php echo $product['quantity']; ?>) - Rs. <?php echo number_format($product['price'], 2); ?>
                                        <br>
                                        <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="50">
                                    </li>
                                    
                                    <?php endforeach; ?>
                            </ul>
                        </td>
                        <td><?php echo $order['status']; ?></td>

                        <td>
                            <form method="POST" action="order_management.php">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="status">
                                    <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>
                                        Pending</option>
                                    <option value="Shipped" <?php echo ($order['status'] == 'Shipped') ? 'selected' : ''; ?>>
                                        Shipped</option>
                                    <option value="Delivered" <?php echo ($order['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                    <option value="Cancelled" <?php echo ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>

<?php
$conn->close();
?>