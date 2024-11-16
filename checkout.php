<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch cart items for the user
$cartResult = $conn->query("SELECT c.*, p.name, p.image, p.price FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = $userId");

// Calculate the grand total
$grandTotal = 0;
if ($cartResult->num_rows > 0) {
    while ($item = $cartResult->fetch_assoc()) {
        $price = isset($item['price']) ? $item['price'] : 0;
        $quantity = isset($item['quantity']) ? $item['quantity'] : 0;
        $grandTotal += $price * $quantity;
    }
} else {
    echo "<script>alert('Your cart is empty. Please add items to your cart.'); window.location.href = 'shop.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email']; // Get email from the submitted form
    $payment_method = $_POST['payment_method'];

    $stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, email, phone, address, payment_method, total_amount, order_date) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("isssssd", $userId, $full_name, $email, $phone, $address, $payment_method, $grandTotal);

    if ($stmt->execute()) {
        $order_id = $stmt->insert_id;

        $cartResult->data_seek(0); // Reset result set pointer
        $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

        $updateStockStmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        while ($item = $cartResult->fetch_assoc()) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $itemStmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
            $itemStmt->execute();

            $updateStockStmt->bind_param("ii", $quantity, $product_id);
            $updateStockStmt->execute();
        }

        $itemStmt->close();
        $updateStockStmt->close();


        $conn->query("DELETE FROM cart WHERE user_id = $userId");

        echo "<script>alert('Order placed successfully!'); window.location.href = 'order_confirmation.php?order_id=" . $order_id . "';</script>";
    } else {
        echo "<script>alert('Error placing order: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Brikshya Griha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/checkout.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="images/logo.jpg" alt="Brikshya Griha Logo" width="30" height="30"> Brikshya Griha
        </a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="aboutus.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link" href="ContactUs/contactus.php">Contact Us</a></li>
            </ul>
        </div>
    </nav>

    <div class="container my-5">
        <h4>Checkout</h4>
        <div class="row">
            <div class="col-md-6">
                <h5>User Information</h5>
                <form action="checkout.php" method="POST" class="mt-4">
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="cod">Cash on Delivery (COD)</option>
                            <option value="esewa">eSewa</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Place Order</button>
                </form>
            </div>
            <div class="col-md-6">
                <h5>Your Cart</h5>
                <div class="cart-items">
                    <?php
                    // Reset the pointer to the result set
                    $cartResult->data_seek(0);
                    if ($cartResult->num_rows > 0) {
                        while ($item = $cartResult->fetch_assoc()) {
                            $price = isset($item['price']) ? $item['price'] : 0;
                            $quantity = isset($item['quantity']) ? $item['quantity'] : 0;
                            $total = $price * $quantity;
                            ?>
                            <div class="cart-item">
                                <div class="row">
                                    <div class="col-md-4">
                                        <img src="uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>"
                                            class="img-fluid">
                                    </div>
                                    <div class="col-md-8">
                                        <h5 class="product-name"><?php echo $item['name']; ?></h5>
                                        <p class="product-price">Rs <?php echo number_format($price, 2); ?></p>
                                        <p class="product-quantity">Quantity: <?php echo $quantity; ?></p>
                                        <p class="product-total">Total: Rs <?php echo number_format($total, 2); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p class='text-center'>Your cart is empty.</p>";
                    }
                    ?>
                </div>
                <h5 class="grand-total">Grand Total: Rs <?php echo number_format($grandTotal, 2); ?></h5>
            </div>
        </div>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>