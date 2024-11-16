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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); 
    exit();
}

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
// Fetch orders for the logged-in user
$sqlOrders = "
    SELECT o.id AS order_id, o.order_date, o.total_amount, o.status, 
           oi.quantity, p.name, p.price, p.image
    FROM orders o 
    JOIN order_items oi ON o.id = oi.order_id 
    JOIN products p ON oi.product_id = p.id 
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sqlOrders);
$stmt->bind_param("i", $userId);
$stmt->execute();
$resultOrders = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History</title>
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="css/order_history.css"> 
    <script>
    function confirmLogout() {
        return confirm("Are you sure you want to logout?");
    }
</script>


</head>

<body>

    <div class="navbar">
        <div class="logo">
            <img src="images/logo.jpg" alt="Brikshya Griha Logo"> <!-- Update the path to your logo -->
            <h1>Brikshya Griha</h1>
        </div>
        <div class="nav-links">
            <a href="shop.php">Shop</a>
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact Us</a>
        </div>
        <div class="dropdown">
            <button class="dropbtn">
                <i class="fas fa-user"></i> 
            </button>
            <div class="dropdown-content">
                <a href="profile.php">Profile</a>
                <a href="track_order.php">Track Order</a>
                <a href="order_history.php">Order History</a>
                <a href="logout.php"onclick="return confirmLogout();">Sign Out</a>
            </div>
        </div>
    </div>

    <div class="main-content">
        <h1>Your Order History</h1>

        <?php if ($resultOrders->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Items</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentOrderId = null;
                    while ($order = $resultOrders->fetch_assoc()):
                        // Start a new row for each order
                        if ($currentOrderId !== $order['order_id']):
                            if ($currentOrderId !== null): // Close previous order row
                                echo "</ul></td></tr>";
                            endif;
                            $currentOrderId = $order['order_id']; // Update current order ID
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($order['status']); ?></td>
                                <td>
                                    <div class="order-item">
                                        <img src="uploads/<?php echo htmlspecialchars($order['image']); ?>"
                                            alt="<?php echo htmlspecialchars($order['name']); ?>" style="width:50px; height:50px;">
                                        <?php echo htmlspecialchars($order['name']); ?> (Quantity:
                                        <?php echo htmlspecialchars($order['quantity']); ?>) - Rs.
                                        <?php echo number_format($order['price'], 2); ?>
                                    </div>


                                    <?php
                                    else: // Display additional items for the current order
                                        ?>
                                            <div class="order-item">
                                                <img src="uploads/<?php echo htmlspecialchars($order['image']); ?>"
                                                     alt="<?php echo htmlspecialchars($order['name']); ?>"
                                                     style="width:50px; height:50px;">
                                                <?php echo htmlspecialchars($order['name']); ?> (Quantity:
                                                <?php echo htmlspecialchars($order['quantity']); ?>) - Rs.
                                                <?php echo number_format($order['price'], 2); ?>
                                            </div>
                                        
                      

                                    <?php
                        endif; // End of else
                    endwhile; // End of while loop
                    ?>
                            </ul>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no orders yet.</p>
        <?php endif; ?>
    </div>

    <footer>
        <div class="container text-center">
            <img src="images/logo.jpg" alt="Brikshya Griha Logo" style="height: 50px; margin-bottom: 10px;">
            <h3>Brikshya Griha</h3>
            <p>Brikshya Griha is your go-to online store for a wide variety of plants and plant accessories. We are
                committed to providing high-quality products that nurture your green thumb.</p>

            <div class="quick-links">
                <h4>Quick Links</h4>
                <a href="aboutus.php">About Us</a>
                <a href="contactus.php">Contact Us</a>
                <a href="terms.php">Terms and Conditions</a>
            </div>

            <div class="social-media">
                <h4>Follow Us</h4>
                <a href="https://facebook.com" target="_blank" class="social-icon">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://instagram.com" target="_blank" class="social-icon">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://twitter.com" target="_blank" class="social-icon">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>

            <p>&copy; 2024 Brikshya Griha. All rights reserved.</p>
        </div>
    </footer>


</body>

</html>