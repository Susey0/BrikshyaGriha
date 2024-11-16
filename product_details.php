<?php
include 'config.php';
include 'recommendation.php';
session_start();

// Handle login error message
$loginError = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']); // Clear the error after displaying it

// Product Class
class Product
{
    private $conn;
    private $id;

    public function __construct($conn, $id)
    {
        $this->conn = $conn;
        $this->id = intval($id);
    }

    public function fetchProduct()
    {
        $productSql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->conn->prepare($productSql);
        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}

// Review Class
class Review
{
    private $conn;
    private $productId;

    public function __construct($conn, $productId)
    {
        $this->conn = $conn;
        $this->productId = $productId;
    }

    public function fetchReviews()
    {
        $reviewSql = "SELECT reviews.comment, reviews.rating, users.username, reviews.created_at 
                      FROM reviews 
                      JOIN users ON reviews.user_id = users.id 
                      WHERE product_id = ? ORDER BY reviews.created_at DESC";
        $stmt = $this->conn->prepare($reviewSql);
        $stmt->bind_param('i', $this->productId);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function submitReview($userId, $comment, $rating)
    {
        $insertSql = "INSERT INTO reviews (user_id, product_id, comment, rating, created_at) 
                      VALUES (?, ?, ?, ?, NOW())";
        $stmt = $this->conn->prepare($insertSql);
        $stmt->bind_param('iisi', $userId, $this->productId, $comment, $rating);
        $stmt->execute();
    }
}

// Cart Class
class Cart
{
    private $conn;
    private $userId;
    private $productId;

    public function __construct($conn, $userId, $productId = null)
    {
        $this->conn = $conn;
        $this->userId = $userId;
        $this->productId = $productId;
    }

    public function getTotalCartItems()
    {
        $sql = "SELECT COUNT(DISTINCT product_id) as total_items FROM cart WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param('i', $this->userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return isset($row['total_items']) ? $row['total_items'] : 0;
    }

    public function addToCart()
    {
        // Check the current stock of the product
        $stockSql = "SELECT stock FROM products WHERE id = ?";
        $stockStmt = $this->conn->prepare($stockSql);
        $stockStmt->bind_param('i', $this->productId);
        $stockStmt->execute();
        $stockResult = $stockStmt->get_result();
        $product = $stockResult->fetch_assoc();

        // Check if the product exists and has stock
        if ($product && $product['stock'] > 0) {
            // Check if the product is already in the cart
            $checkSql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt = $this->conn->prepare($checkSql);
            $stmt->bind_param('ii', $this->userId, $this->productId);
            $stmt->execute();
            $existingItem = $stmt->get_result()->fetch_assoc();

            if ($existingItem) {
                $newQuantity = $existingItem['quantity'] + 1;
                $updateSql = "UPDATE cart SET quantity = ? WHERE id = ?";
                $updateStmt = $this->conn->prepare($updateSql);
                $updateStmt->bind_param('ii', $newQuantity, $existingItem['id']);
                $updateStmt->execute();

                $_SESSION['cart_items'][$this->productId] = $newQuantity;
                $_SESSION['success_message'] = "Product quantity updated in cart.";
            } else {
                // If it doesn't exist, add a new cart item
                $insertSql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
                $insertStmt = $this->conn->prepare($insertSql);
                $insertStmt->bind_param('ii', $this->userId, $this->productId);
                $insertStmt->execute();

                $_SESSION['cart_items'][$this->productId] = 1;
            }
            $_SESSION['total_cart_items'] = isset($_SESSION['total_cart_items']) ? $_SESSION['total_cart_items'] + 1 : 1;
            $_SESSION['success_message'] = "Product added to cart successfully!";
        } else {
            $_SESSION['error_message'] = "Sorry, this product is out of stock.";
        }
    }
}

// Get the product ID from the URL
$productId = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch product details
$productObj = new Product($conn, $productId);
$product = $productObj->fetchProduct();

// Redirect if the product doesn't exist
if (!$product) {
    header("Location: shop.php");
    exit;
}

// Fetch product reviews
$reviewObj = new Review($conn, $productId);
$reviews = $reviewObj->fetchReviews();

// Handle add to cart logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['user_id'])) {
        $cartObj = new Cart($conn, $_SESSION['user_id'], $product['id']);
        $cartObj->addToCart();

        header("Location: product_details.php?id=" . $productId);
        exit;
    } else {
        // Display error message if not logged in
        // $_SESSION['error_message'] = "You need to be logged in to add items to your cart.";
        // header("Location: index.php");
        // exit;
    }
}
$productId = $_GET['id'];
$recommendedProducts = getSimilarProducts($conn, $productId, $product['description']);



?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']); ?> - Product Details</title>
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="css/products_details.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function addToCart() {

            // Check if user is logged in
            <?php if (!isset($_SESSION['user_id'])): ?>
                alert("You must log in first to add items to the cart.");
            <?php else: ?>
                // If logged in, submit the form
                document.getElementById('addToCartForm').submit();
            <?php endif; ?>
        }
    </script>
    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to logout?");
        }
    </script>

</head>

<body>
    <!-- Success or error messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" role="alert"><?= $_SESSION['success_message']; ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert"><?= $_SESSION['error_message']; ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>



    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">
            <img src="images/logo.jpg" alt="Brikshya Griha Logo" width="30" height="30"> Brikshya Griha
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="index.php" onclick="return confirmLeave()">Home</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="goToShop()">Shop</a>
                </li>
                <li class="nav-item"><a class="nav-link" href="aboutus.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="contactus.php">Contact Us</a></li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="cart.php">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        // Initialize Cart class and get total cart items
                        if (isset($_SESSION['user_id'])) {
                            $cart = new Cart($conn, $_SESSION['user_id']);
                            $cartCount = $cart->getTotalCartItems();
                        } else {
                            $cartCount = 0;
                        }

                        // Display cart count if greater than 0
                        if ($cartCount > 0) {
                            echo "<span class='badge badge-danger'>$cartCount</span>";
                        }
                        ?>
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a href="logout.php" onclick="return confirmLogout();" class="nav-link">Logout</a>

                    <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="#" id="loginLink" data-toggle="modal"
                            data-target="#loginModal">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="#" id="signupLink" data-toggle="modal"
                            data-target="#signupModal">Signup</a></li>

                </ul>
            <?php endif; ?>
            </ul>
        </div>
        <script>
            function confirmLeave() {
                // Show the confirmation dialog
                var isConfirmed = confirm("Do you want to leave the site and log out?");

                if (isConfirmed) {
                    window.location.href = "logout.php"; // You can add any logout logic here if needed
                    return true; // Allow the navigation
                } else {
                    // If the user clicks "Cancel", stop the navigation
                    return false;
                }
            }
        </script>
    </nav>
    <script>
        function goToShop() {
            <?php if (isset($_SESSION['user_id'])): ?>
                window.location.href = 'shop.php'; // Redirect to shop.php if logged in
            <?php else: ?>
                alert("You must log in to visit the shop page."); // Show alert if not logged in
            <?php endif; ?>
        }
    </script>
    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- Display error message if present -->
                <?php if ($loginError): ?>
                    <div class="alert alert-danger">
                        <?php echo $loginError; ?>
                    </div>
                <?php endif; ?>
                <div class="modal-body">
                    <div id="loginSuccessMessage" class="alert alert-success" style="display: none;"></div>

                    <form action="login.php" method="POST">
                        <div class="form-group">
                            <label for="username">Email:</label>
                            <input type="text" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="toggleLoginPassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                        </div>

                        <button type="submit" class="btn btn-primary">Login</button>
                        <div class="form-group text-center">
                            <p>Don't have an account? <a href="#" data-dismiss="modal" data-toggle="modal"
                                    data-target="#signupModal">Signup</a></p>
                            <p><a href="#" data-toggle="modal" data-target="#forgotPasswordModal">Forgot Password?</a>
                            </p>

                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        window.onload = function () {
            if ("<?php echo $loginError; ?>") {
                $('#loginModal').modal('show');
            }
        };
        $(document).on('click', '[data-toggle="modal"]', function (event) {
            event.preventDefault(); // Prevent default anchor click behavior
            var targetModal = $(this).data('target');
            $(targetModal).modal('show'); // Show the target modal
        });
    </script>

    <!-- Forgot Password Modal -->
    <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="forgotPasswordForm" action="forgot_password.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="forgotPasswordModalLabel">Forgot Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="fpSuccessMessage" class="alert alert-success" style="display: none;"></div>
                        <div id="fpErrorMessage" class="alert alert-danger" style="display: none;"></div>

                        <div class="form-group">
                            <label for="fpEmail">Email address</label>
                            <input type="email" class="form-control" id="fpEmail" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Send Reset Link</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function handleShopClick(categoryIndex) {
            if (!isLoggedIn) {
                // Show the login modal if not logged in
                $('#loginModal').modal('show');
                // Add a message to the login modal to inform the user that they need to login to shop
                document.getElementById('loginSuccessMessage').textContent = 'You must login first to shop.';
                document.getElementById('loginSuccessMessage').style.display = 'block';
                // Clear any existing error messages
                document.getElementById('loginError').innerHTML = '';
            } else {
                // Redirect to the shopping page or category page
                window.location.href = 'shop.php?category=' + categoryIndex;
            }
        }
    </script>


    <!-- Register Modal -->
    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="signupForm" action="signup.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalLabel">Sign Up</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="successMessage" class="alert alert-success" style="display: none;"></div>

                        <div class="form-group">
                            <label for="registerFirstName">First Name</label>
                            <input type="text" class="form-control" id="registerFirstName" name="first_name" required>
                            <div class="error" id="errorFirstName"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerLastName">Last Name</label>
                            <input type="text" class="form-control" id="registerLastName" name="last_name" required>
                            <div class="error" id="errorLastName"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerUsername">Username</label>
                            <input type="text" class="form-control" id="registerUsername" name="username" required>
                            <div class="error" id="errorUsername"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerEmail">Email address</label>
                            <input type="email" class="form-control" id="registerEmail" name="email" required>
                            <div class="error" id="errorEmail"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerPassword">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="registerPassword" name="password"
                                    required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="toggleSignupPassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="error" id="errorPassword"></div>
                            <small class="form-text text-muted">
                                Password must be at least 8 characters long, contain one uppercase letter, one lowercase
                                letter, one number, and one special character.
                            </small>
                        </div>
                        <div class="form-group">
                            <label for="registerConfirmPassword">Confirm Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="registerConfirmPassword"
                                    name="confirm_password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="error" id="errorConfirmPassword"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerPhone">Phone Number</label>
                            <input type="tel" class="form-control" id="registerPhone" name="phone" required>
                            <div class="error" id="errorPhone"></div>
                        </div>
                        <div class="form-group">
                            <label for="registerAddress">Address</label>
                            <input type="text" class="form-control" id="registerAddress" name="address" required>
                            <div class="error" id="errorAddress"></div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="termsCheck" name="terms" required>
                                <label class="form-check-label" for="termsCheck">
                                    I agree to the <a href="terms.php">terms and conditions</a>.
                                </label>
                                <div class="error" id="errorTerms"></div>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <p>Already have an account? <a href="#" data-dismiss="modal" data-toggle="modal"
                                    data-target="#loginModal">Login</a></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Sign Up</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('toggleLoginPassword').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const passwordIcon = this.querySelector('i');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        });

        document.getElementById('toggleSignupPassword').addEventListener('click', function () {
            const signupPasswordInput = document.getElementById('registerPassword');
            const signupPasswordIcon = this.querySelector('i');

            if (signupPasswordInput.type === 'password') {
                signupPasswordInput.type = 'text';
                signupPasswordIcon.classList.remove('fa-eye');
                signupPasswordIcon.classList.add('fa-eye-slash');
            } else {
                signupPasswordInput.type = 'password';
                signupPasswordIcon.classList.remove('fa-eye-slash');
                signupPasswordIcon.classList.add('fa-eye');
            }
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
            const confirmPasswordInput = document.getElementById('registerConfirmPassword');
            const confirmPasswordIcon = this.querySelector('i');

            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                confirmPasswordIcon.classList.remove('fa-eye');
                confirmPasswordIcon.classList.add('fa-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                confirmPasswordIcon.classList.remove('fa-eye-slash');
                confirmPasswordIcon.classList.add('fa-eye');
            }
        });
    </script>


    <script>
        // Password validation function for individual rules
        function validatePassword(password) {
            const errors = [];
            if (password.length < 8) {
                errors.push("Password must be at least 8 characters long.");
            }
            if (!/[A-Z]/.test(password)) {
                errors.push("Password must contain at least one uppercase letter.");
            }
            if (!/[a-z]/.test(password)) {
                errors.push("Password must contain at least one lowercase letter.");
            }
            if (!/\d/.test(password)) {
                errors.push("Password must contain at least one number.");
            }
            if (!/[@$!%*?&]/.test(password)) {
                errors.push("Password must contain at least one special character.");
            }
            return errors;
        }

        // Form validation on submit
        document.getElementById('signupForm').addEventListener('submit', function (e) {
            // Get form elements
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('registerConfirmPassword').value;
            const errorPassword = document.getElementById('errorPassword');
            const errorConfirmPassword = document.getElementById('errorConfirmPassword');

            // Clear previous error messages
            errorPassword.innerHTML = '';
            errorConfirmPassword.textContent = '';

            let valid = true;

            // Validate password
            const passwordErrors = validatePassword(password);
            if (passwordErrors.length > 0) {
                valid = false;
                // Display password errors
                errorPassword.innerHTML = passwordErrors.map(err => `<p>${err}</p>`).join('');
            }

            // Check if passwords match
            if (password !== confirmPassword) {
                errorConfirmPassword.textContent = 'Passwords do not match.';
                valid = false;
            }

            // Prevent form submission if validation fails
            if (!valid) {
                e.preventDefault();
            }
        });
    </script>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('signupForm');

            // Check for success message
            const successMessage = '<?php echo isset($_SESSION["success_message"]) ? $_SESSION["success_message"] : ""; ?>';
            if (successMessage) {
                // Display the success message in the login modal
                document.getElementById('loginSuccessMessage').textContent = successMessage;
                document.getElementById('loginSuccessMessage').style.display = 'block';

                // Show the login modal
                $('#loginModal').modal('show');

                // Clear the session variable
                <?php unset($_SESSION['success_message']); ?>
            }

            form.addEventListener('submit', function (event) {
                // Clear previous error messages
                document.querySelectorAll('.error').forEach(el => el.textContent = '');

                const errors = validateForm(); // Get validation errors

                if (errors.length > 0) {
                    // Display errors
                    errors.forEach(error => {
                        const errorElement = document.getElementById(`error${error.field}`);
                        if (errorElement) {
                            errorElement.textContent = error.message;
                        }
                    });

                    // Prevent form submission and keep the modal open
                    event.preventDefault();
                    $('#signupModal').modal('show'); // Show the modal if it closes
                } else {
                    // If no errors, submit the form
                    form.submit();
                }
            });

            function validateForm() {
                const errors = [];

                // First Name
                const firstName = document.getElementById('registerFirstName').value.trim();
                if (!firstName) {
                    errors.push({ field: 'FirstName', message: 'First Name is required.' });
                }

                // Last Name
                const lastName = document.getElementById('registerLastName').value.trim();
                if (!lastName) {
                    errors.push({ field: 'LastName', message: 'Last Name is required.' });
                }

                // Username
                const username = document.getElementById('registerUsername').value.trim();
                if (!username) {
                    errors.push({ field: 'Username', message: 'Username is required.' });
                }

                // Email
                const email = document.getElementById('registerEmail').value.trim();
                if (!email || !/\S+@\S+\.\S+/.test(email)) {
                    errors.push({ field: 'Email', message: 'Valid email is required.' });
                }

                // Password
                const password = document.getElementById('registerPassword').value;
                const passwordErrors = validatePassword(password);
                if (passwordErrors.length > 0) {
                    passwordErrors.forEach(err => {
                        errors.push({ field: 'Password', message: err });
                    });
                }


                // Confirm Password
                const confirmPassword = document.getElementById('registerConfirmPassword').value;
                if (password !== confirmPassword) {
                    errors.push({ field: 'ConfirmPassword', message: 'Passwords do not match.' });
                }

                // Phone
                const phone = document.getElementById('registerPhone').value.trim();
                if (!phone) {
                    errors.push({ field: 'Phone', message: 'Phone Number is required.' });
                }

                // Address
                const address = document.getElementById('registerAddress').value.trim();
                if (!address) {
                    errors.push({ field: 'Address', message: 'Address is required.' });
                }

                // Terms
                const terms = document.getElementById('termsCheck').checked;
                if (!terms) {
                    errors.push({ field: 'Terms', message: 'You must agree to the terms and conditions.' });
                }

                return errors;
            }
        });

    </script>

    <!-- Main content -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <img src="uploads/<?= htmlspecialchars($product['image']); ?>" class="img-fluid"
                    alt="<?= htmlspecialchars($product['name']); ?>">
            </div>
            <div class="col-md-6">
                <h2><?= htmlspecialchars($product['name']); ?></h2>
                <div class="product-info mt-3">
                    <h3>Price: Rs <?= number_format($product['price'], 2); ?></h3>
                    <p>Stock: <?php echo $product['stock']; ?> available</p>
                    <p><strong>Description:</strong></p>
                    <p><?= nl2br(htmlspecialchars($product['description'])); ?></p>
                    <form id="addToCartForm" method="POST" action="">
                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-primary" onclick="addToCart()">Add to
                            Cart</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recommended Products Section -->
<h4 class="mt-4">You Might Also Like</h4>
<div class="row">
    <?php if (!empty($recommendedProducts)): ?>
        <?php foreach ($recommendedProducts as $recommendedProduct): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <!-- Product Image -->
                    <img src="uploads/<?php echo htmlspecialchars($recommendedProduct['image'] ?? 'default.jpg'); ?>"
                         class="card-img-top" alt="<?php echo htmlspecialchars($recommendedProduct['name'] ?? 'Product Image'); ?>">
                    
                    <div class="card-body d-flex flex-column">
                        <!-- Product Name -->
                        <h5 class="card-title"><?php echo htmlspecialchars($recommendedProduct['name'] ?? 'Unknown Product'); ?></h5>
                        
                        <!-- Product Price with Enhanced Error Handling -->
                        <p class="card-text">
                            <?php 
                                if (isset($recommendedProduct['price']) && is_numeric($recommendedProduct['price'])) {
                                    // If price is a valid number, format and display it
                                    echo "Rs. " . number_format((float) $recommendedProduct['price'], 2);
                                } else {
                                    // If price is missing or invalid, display 'N/A'
                                    echo "Price: N/A";
                                }
                            ?>
                        </p>
                        
                        <div class="mt-auto">
                            <!-- Add to Cart Button -->
                            <a href="shop.php?add_to_cart=<?php echo $recommendedProduct['id']; ?>"
                               class="btn btn-primary">Add to Cart</a>
                            
                            <!-- View Details Button -->
                            <a href="product_details.php?id=<?php echo $recommendedProduct['id']; ?>"
                               class="btn btn-secondary">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No similar products found.</p>
    <?php endif; ?>
</div>




        <!-- Review Section -->
        <div class="row mt-5">
            <div class="col-12">
                <h4>Reviews</h4>
                <?php if (isset($_SESSION['user_id']) && (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin')): ?>
                    <form action="submit_review.php" method="POST" class="mb-4">
                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                        <div class="form-group">
                            <label for="review">Your Review:</label>
                            <textarea class="form-control" name="review" id="review" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="rating">Rating:</label>
                            <select class="form-control" name="rating" id="rating" required>
                                <option value="5">5 - Excellent</option>
                                <option value="4">4 - Very Good</option>
                                <option value="3">3 - Average</option>
                                <option value="2">2 - Poor</option>
                                <option value="1">1 - Terrible</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                <?php else: ?>
                    <p>You need to <a href="index.php">login</a> to leave a review.</p>
                <?php endif; ?>

                <!-- Display Reviews -->
                <div class="mt-4">
                    <?php if ($reviews->num_rows > 0): ?>
                        <h5>Comments</h5>
                        <ul class="list-unstyled">
                            <?php while ($review = $reviews->fetch_assoc()): ?>
                                <li class="mb-3">
                                    <strong><?= htmlspecialchars($review['username']); ?>:</strong>
                                    <span><?= htmlspecialchars($review['comment']); ?></span>
                                    <div>
                                        <span>Rating:
                                            <?= str_repeat('★', $review['rating']); ?>
                                            <?= str_repeat('☆', 5 - $review['rating']); ?></span>
                                    </div>
                                    <small>Posted on: <?= htmlspecialchars($review['created_at']); ?></small>
                                </li>
                                <hr>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>No reviews yet. Be the first to leave a review!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
</body>

</html>