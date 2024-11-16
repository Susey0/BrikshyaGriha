<?php
session_start();
include 'config.php';
if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_SESSION['error_message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error_message']); // Clear the error message after displaying ?>
<?php endif; 
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
// Initialize sortOrder with a default value
$sortOrder = isset($_GET['sort']) ? htmlspecialchars($_GET['sort']) : 'name';

$sql = "SELECT user_id, product_id, rating FROM reviews";
$result = $conn->query($sql);
$ratings = [];
while ($row = $result->fetch_assoc()) {
    $ratings[$row['user_id']][$row['product_id']] = $row['rating'];
}

$userId = $_SESSION['user_id'];
$cartQuery = $conn->query("SELECT COUNT(*) AS itemCount FROM cart WHERE user_id = $userId");
$cartData = $cartQuery->fetch_assoc();
$cart_count = $cartData['itemCount'];

// Handle adding item to cart
if (isset($_GET['add_to_cart'])) {
    $productId = intval($_GET['add_to_cart']);  
    $stockQuery = $conn->query("SELECT stock FROM products WHERE id = $productId");
    $productStock = $stockQuery->fetch_assoc();

    if ($productStock['stock'] > 0) {
        $checkQuery = $conn->query("SELECT * FROM cart WHERE user_id = $userId AND product_id = $productId");

        if ($checkQuery->num_rows > 0) {
            $cartItem = $checkQuery->fetch_assoc();
            $newQuantity = $cartItem['quantity'] + 1;

            if ($newQuantity <= $productStock['stock']) {
                $conn->query("UPDATE cart SET quantity = $newQuantity WHERE user_id = $userId AND product_id = $productId");
                $_SESSION['message'] = "Item quantity updated in the cart.";
            } else {
                $_SESSION['error_message'] = "Not enough stock available.";
            }
        } else {
            $_SESSION['cart'][] = $productId;  

            $conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($userId, $productId, 1)");
            $_SESSION['message'] = "Item added to cart successfully.";
        }
    } else {
        $_SESSION['error_message'] = "This product is out of stock.";
    }
}

$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : 0;
$searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

$categoriesResult = $conn->query("SELECT * FROM categories");

function fetchProducts($conn, $categoryId, $searchTerm)
{
    $productSql = "SELECT * FROM products WHERE name LIKE '%$searchTerm%'";
    if ($categoryId > 0) {
        $productSql .= " AND category_id = $categoryId";
    }

    return $conn->query($productSql);
}
//Algorithm



$productsResult = fetchProducts($conn, $selectedCategory, $searchTerm);
$products = [];
if ($productsResult->num_rows > 0) {
    while ($row = $productsResult->fetch_assoc()) {
        $products[] = $row; 
    }
}


$userId = $_SESSION['user_id'];

$userResult = $conn->query("SELECT * FROM users WHERE id = $userId");
$user = $userResult->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Brikshya Griha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/shop.css">
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">
    <script>
    function confirmLogout() {
        return confirm("Are you sure you want to logout?");
    }
</script>

</head>

<body>
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']);  ?>
    <?php endif; ?>

    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="images/logo.jpg" alt="Brikshya Griha" width="30" height="30"
                    class="d-inline-block align-text-top">
                Brikshya Griha
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="shop.php">
                            <i class="fas fa-store"></i> Home
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
            </div>

            <div class="d-flex align-items-center">
                <a class="nav-link" href="cart.php" style="position: relative;">
                    <i class="fas fa-shopping-cart"></i>
                    <?php
                    $userId = $_SESSION['user_id'];
                    $cartQuery = $conn->query("SELECT COUNT(*) AS itemCount FROM cart WHERE user_id = $userId");
                    $cartData = $cartQuery->fetch_assoc();
                    $cart_count = $cartData['itemCount'];
                    ?>
                    <span id="cart-count" class="badge bg-danger" style="position: absolute; top: -5px; right: -10px;">
                        <?php echo $cart_count; ?>
                    </span>
                </a>

                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user['username']); ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                    <li><a class="dropdown-item" href="track_order.php">Track Order</a></li>
                    <li><a class="dropdown-item" href="order_history.php">Order History</a></li>

                    <li><a class="dropdown-item" href="logout.php"onclick="return confirmLogout();" >Sign Out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="images/hero1.jpg" class="d-block w-100" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Welcome to Brikshya Griha</h5>
                    <p>Your one-stop shop for all plant and garden accessories.</p>
                    <a href="shop.php" class="btn btn-light">Shop Now</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/hero2.jpg" class="d-block w-100" alt="Second slide">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Explore Our Collection</h5>
                    <p>Discover beautiful plants and accessories.</p>
                    <a href="shop.php" class="btn btn-light">Shop Now</a>
                </div>
            </div>
            <div class="carousel-item">
                <img src="images/hero3.jpg" class="d-block w-100" alt="Third slide">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Join Our Community</h5>
                    <p>Share your gardening experiences with us!</p>
                    <a href="shop.php" class="btn btn-light">Shop Now</a>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="container my-5">
        <div class="row">
        <div class="container my-5">
    <div class="row">
        <!-- Sidebar for Categories -->
        <div class="col-md-3">
            <form class="d-flex mb-4" id="searchForm" onsubmit="searchProducts(event)">
                <input class="form-control me-2" type="search" placeholder="Search products..." aria-label="Search"
                    id="searchInput" value="<?php echo $searchTerm; ?>">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>

            <h4 class="mb-3">Categories</h4>
            <div class="list-group" id="category-list">
                <a href="#"
                    class="list-group-item list-group-item-action <?php echo ($selectedCategory == 0) ? 'active-category' : ''; ?>"
                    data-category="0" onclick="loadProducts(event, 0)">All Products</a>
                <?php while ($categoryItem = $categoriesResult->fetch_assoc()): ?>
                    <a href="#"
                        class="list-group-item list-group-item-action <?php echo ($selectedCategory == $categoryItem['id']) ? 'active-category' : ''; ?>"
                        data-category="<?php echo $categoryItem['id']; ?>"
                        onclick="loadProducts(event, <?php echo $categoryItem['id']; ?>)">
                        <?php echo htmlspecialchars($categoryItem['name']); ?>
                    </a>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Product Section -->
        <div class="col-md-9">
            <?php
            // Check if any products were found
            if ($productsResult->num_rows > 0) {
                ?>
                <div class="row">
                    <h4 class="mb-3">Available Products</h4>
                    <?php
                    // Display sorted products
                    if (!empty($products)) {
                        foreach ($products as $product) {
                            ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100"> <!-- Use h-100 to make cards equal height -->
                                    <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <div class="card-body d-flex flex-column"> <!-- Use flex to allow equal height -->
                                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                        <p class="card-text">
                                            Rs.<?php echo number_format(htmlspecialchars($product['price']), 2); ?></p>
                                        <div class="mt-auto"> <!-- Push buttons to the bottom -->
                                            <a href="shop.php?add_to_cart=<?php echo $product['id']; ?>"
                                                class="btn btn-primary">Add to Cart</a>
                                            <a href="product_details.php?id=<?php echo $product['id']; ?>"
                                                class="btn btn-secondary">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        // If the products array is empty
                        ?>
                        <div class="col-12">
                            <h4 class="mb-3">No products available</h4>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            } else {
                // If no products were found in the database
                ?>
                <div class="row">
                    <h4 class="mb-3">No products available</h4>
                </div>
                <?php
            }

            // Close the database connection
            $conn->close();
            ?>
        </div>
    </div>
</div>



                <!-- Bootstrap JS -->
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

                <script>
                    // Function to submit the sort form without scrolling
                    function submitSortForm(event) {
                        event.preventDefault(); // Prevent the default form submission

                        // Get the values from the form
                        const category = document.querySelector('input[name="category"]').value;
                        const search = document.querySelector('input[name="search"]').value;
                        const sort = document.querySelector('select[name="sort"]').value;

                        // Update the URL using pushState
                        const newUrl = `shop.php?category=${category}&search=${encodeURIComponent(search)}&sort=${sort}`;
                        history.pushState(null, '', newUrl);

                        // Reload the page to apply the new sort order
                        window.location.reload(); // Reload to apply the new filter
                    }

                    // Function to search products
                    function searchProducts(event) {
                        event.preventDefault();
                        const searchTerm = document.getElementById('searchInput').value;
                        const category = document.querySelector('input[name="category"]').value;
                        const sort = document.querySelector('select[name="sort"]').value;
                        const newUrl = `shop.php?search=${encodeURIComponent(searchTerm)}&category=${category}&sort=${sort}`;
                        history.pushState(null, '', newUrl);
                        window.location.reload(); // Reload to apply the new search
                    }

                    // Function to load products based on category
                    function loadProducts(event, categoryId) {
                        event.preventDefault();
                        const searchTerm = document.getElementById('searchInput').value;
                        const sort = document.querySelector('select[name="sort"]').value;
                        const newUrl = `shop.php?category=${categoryId}&search=${encodeURIComponent(searchTerm)}&sort=${sort}`;
                        history.pushState(null, '', newUrl);
                        window.location.reload(); // Reload to apply the new category
                    }
                </script>

</body>

</html>