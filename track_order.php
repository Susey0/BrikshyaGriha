<?php
// Include database connection
include 'config.php'; // Make sure this file contains your database connection
session_start();

// Ensure user_id is set in the session
if (!isset($_SESSION['user_id'])) {
    echo "<h3>Please log in to track your order.</h3>";
    exit;
}
// Get the user ID from the session
$userId = $_SESSION['user_id'];
// Initialize variables
$orderStatus = '';
$trackingNumber = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $orderId = $_POST['order_id'];

    // Prepare and execute query to fetch order status
    $query = "SELECT status FROM orders WHERE id = ? AND user_id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $orderId, $userId);
    $stmt->execute();
    $stmt->bind_result($orderStatus);
    $stmt->fetch();

    // Check if order exists
echo "<div class='order-status-container'>"; // Add a container for centering
if ($orderStatus) {
    echo "<h3>Order Status for Order ID: " . htmlspecialchars($orderId) . "</h3>";
    echo "Status: " . htmlspecialchars($orderStatus);
    if ($trackingNumber) {
        echo "<br>Tracking Number: " . htmlspecialchars($trackingNumber);
    }
} else {
    echo "<h3>Order not found. Please check your Order ID.</h3>";
}
echo "</div>"; // Close the container

// Close the statement
$stmt->close(); // Close the prepared statement
}


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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">

    <link rel="stylesheet" href="css/track_order.css">
    <title>Track Your Order</title>
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

    <h2>Track Your Order</h2>
    <form action="track_order.php" method="POST">
        <label for="order_id">Enter your Order ID:</label>
        <input type="text" id="order_id" name="order_id" required>
        <button type="submit">Track Order</button>
    </form>
    <footer>
        <div class="container text-center">
            <img src="images/logo.jpg" alt="Brikshya Griha Logo" style="height: 50px; margin-bottom: 10px;">
            <h3>Brikshya Griha</h3>
            <p>Brikshya Griha is your go-to online store for a wide variety of plants and plant accessories. We are committed to providing high-quality products that nurture your green thumb.</p>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
