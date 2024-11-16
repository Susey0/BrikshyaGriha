<?php
session_start();
include 'config.php';

// Display success and error messages
if (isset($_SESSION['success_message'])) {
    echo "<div class='success-message'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo "<div class='error-message'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['error'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error'] . "</div>";
    unset($_SESSION['error']);
}

// Handle adding items to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $query = "SELECT stock FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        $available_stock = $product['stock'];
        if ($quantity <= $available_stock) {
            $user_id = $_SESSION['user_id'];

            // Check if product already exists in the cart
            $cart_query = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($cart_query);
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $cart_result = $stmt->get_result();

            if ($cart_result->num_rows > 0) {
                // Update cart quantity
                $update_cart_query = "UPDATE cart SET quantity = quantity + ? WHERE user_id = ? AND product_id = ?";
                $stmt = $conn->prepare($update_cart_query);
                $stmt->bind_param("iii", $quantity, $user_id, $product_id);
                $stmt->execute();
            } else {
                // Insert product into cart
                $insert_cart_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($insert_cart_query);
                $stmt->bind_param("iii", $user_id, $product_id, $quantity);
                $stmt->execute();
            }

            // Update stock after adding to cart
            $new_stock = $available_stock - $quantity;
            $update_stock_query = "UPDATE products SET stock = ? WHERE id = ?";
            $stmt = $conn->prepare($update_stock_query);
            $stmt->bind_param("ii", $new_stock, $product_id);
            $stmt->execute();

            $_SESSION['success_message'] = "Product added to cart successfully!";
        } else {
            $_SESSION['error_message'] = "Sorry, only $available_stock items are available.";
        }
    } else {
        $_SESSION['error_message'] = "Product not found.";
    }
    header("Location: cart.php");
    exit;
}

// Cart class for fetching and managing items in the cart
class Cart {
    private $db;
    private $userId;

    public function __construct($db, $userId) {
        $this->db = $db;
        $this->userId = $userId;
    }

    // Fetch cart items
    public function getCartItems() {
        $query = "SELECT c.*, p.name, p.price, p.image, p.stock FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $this->userId);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Get total items in cart
    public function getTotalItems() {
        $query = "SELECT COUNT(DISTINCT product_id) AS total FROM cart WHERE user_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->bind_param('i', $this->userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'] ?? 0;
    }
}

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$userId = $_SESSION['user_id'];
$cart = new Cart($conn, $userId);

// Fetch cart items and total
$cartItems = $cart->getCartItems();
$cartCount = $cart->getTotalItems();
$totalAmount = 0; // Initialize total amount
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Brikshya Griha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .success-message {
            color: green;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }

        .error-message {
            color: red;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to logout?");
        }
    </script>

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="images/logo.jpg" alt="Brikshya Griha Logo" width="30" height="30"> Brikshya Griha
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="aboutus.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                <li class="nav-item"><a class="nav-link" href="ContactUs/contactus.php">Contact Us</a></li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        if ($cartCount > 0) {
                            echo "<span class='badge bg-danger'>$cartCount</span>";
                        }
                        ?>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a href="logout.php" onclick="return confirmLogout();" class="nav-link">Logout</a>
                    </li>

                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login-form.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="signup-form.php">Sign Up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container my-5">
        <h4>Your Cart</h4>
        <form id="cartForm" action="update_cart.php" method="POST">
            <div class="cart-items">
                <?php
                if ($cartItems->num_rows > 0) {
                    while ($item = $cartItems->fetch_assoc()) {
                        $quantity = isset($item['quantity']) ? $item['quantity'] : 0;
                        $availableStock = $item['stock'];

                        // Adjust quantity if stock is less
                        if ($quantity > $availableStock) {
                            $quantity = $availableStock;
                            $_SESSION['error_message'] = "Some items in your cart have limited stock.";
                        }

                        $total = $item['price'] * $quantity;
                        $totalAmount += $total;
                        ?>
                        <div class="cart-item">
                            <div class="row">
                                <div class="col-md-2">
                                    <img src="uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                </div>
                                <div class="col-md-6">
                                    <h5 class="product-name"><?php echo $item['name']; ?></h5>
                                    <p class="product-price">Rs <?php echo number_format($item['price'], 2); ?></p>
                                    <p class="product-stock">Stock: <?php echo $availableStock; ?></p>

                                    <input type="hidden" name="cart_ids[]" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="product_ids[]" value="<?php echo $item['product_id']; ?>">
                                    <input type="hidden" name="available_stock[]" value="<?php echo $availableStock; ?>">

                                    <button type="button" class="quantity-btn"
                                        onclick="updateQuantity(<?php echo $item['id']; ?>, 'increase', <?php echo $availableStock; ?>)">+</button>
                                    <span id="quantityDisplay_<?php echo $item['id']; ?>"
                                        class="product-quantity"><?php echo max(1, $quantity); ?></span>
                                    <button type="button" class="quantity-btn"
                                        onclick="updateQuantity(<?php echo $item['id']; ?>, 'decrease', <?php echo $availableStock; ?>)">-</button>

                                    <input type="hidden" id="finalQuantity_<?php echo $item['id']; ?>"
                                        name="final_quantities[<?php echo $item['id']; ?>]"
                                        value="<?php echo max(1, $quantity); ?>">
                                </div>
                                <div class="col-md-2">
                                    <p class="product-total">Total: Rs <?php echo number_format($total, 2); ?></p>
                                </div>
                                <div class="col-md-2">
                                    <a href="remove_from_cart.php?id=<?php echo $item['id']; ?>" class="btn-remove"
                                        onclick="return confirm('Are you sure you want to remove this item from your cart?');">Remove</a>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <p class="text-center">Your cart is empty.</p>
                    <?php
                }
                ?>
            </div>
            <div class="text-end">
                <h5>Total Amount: Rs <?php echo number_format($totalAmount, 2); ?></h5> <!-- Display Total Amount -->

                <button type="submit" class="update-btn">Update Cart</button>
                <a href="shop.php" class="btn btn-primary">Continue shopping</a>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        </form>

        <script>
            function updateQuantity(cartId, action, availableStock) {
                let quantityDisplay = document.getElementById("quantityDisplay_" + cartId);
                let finalQuantityInput = document.getElementById("finalQuantity_" + cartId);
                let currentQuantity = parseInt(quantityDisplay.textContent);

                if (action === 'increase'){

                if(currentQuantity < availableStock) {
                    currentQuantity++;
                    quantityDisplay.textContent = currentQuantity;
                    finalQuantityInput.value = currentQuantity;
                }else{
                    alert("You have reached the maximum available stock for this product.");

                }
                } else if (action === 'decrease' && currentQuantity > 1) {
                    currentQuantity--;
                    quantityDisplay.textContent = currentQuantity;
                    finalQuantityInput.value = currentQuantity;
                }
            }
        </script>
    </div>


    <footer style="background-color: #121212; color: #E0E0E0; padding: 20px 0;">
        <div class="container text-center">
            <img src="images/logo.jpg" alt="Brikshya Griha Logo" style="height: 50px; margin-bottom: 10px;">
            <h3 style="color: #5CD63A;">Brikshya Griha</h3>
            <p>Brikshya Griha is your go-to online store for a wide variety of plants and plant accessories. We are
                committed to providing high-quality products that nurture your green thumb.</p>

            <div class="quick-links" style="margin: 20px 0;">
                <h4 style="color: #FFC107;">Quick Links</h4>
                <a href="aboutus.php" class="footer-link">About Us</a>
                <a href="contactus.php" class="footer-link">Contact Us</a>
                <a href="terms.php" class="footer-link">Terms and Condition</a>
            </div>

            <div class="social-media" style="margin: 20px 0;">
                <h4 style="color: #FFC107;">Follow Us</h4>
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

            <p style="margin-top: 20px;">&copy; 2024 Brikshya Griha. All rights reserved.</p>
        </div>


    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>