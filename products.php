<?php
session_start();
include 'config.php';

// Get the selected category and search term from the request
$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : 0;
$searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Function to fetch all categories
function fetchCategories($conn)
{
    $categorySql = "SELECT * FROM categories"; // Assuming you have a category table
    return $conn->query($categorySql);
}

// Fetch categories
$categoriesResult = fetchCategories($conn);

// Function to fetch products based on selected category and search term
function fetchProducts($conn, $categoryId, $searchTerm)
{
    $productSql = "SELECT * FROM products WHERE name LIKE ?";
    $params = ["%$searchTerm%"];

    if ($categoryId > 0) {
        $productSql .= " AND category_id = ?";
        $params[] = $categoryId;
    }

    $stmt = $conn->prepare($productSql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Prepare the types string
    $types = 's'; // 's' for the search term
    if ($categoryId > 0) {
        $types .= 'i'; // Add 'i' for the integer category ID
    }

    // Bind parameters dynamically
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    return $stmt->get_result();
}

// Fetch products
$productsResult = fetchProducts($conn, $selectedCategory, $searchTerm);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/products.css">

</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="images/logo.jpg" alt="Brikshya Griha Logo" width="20" height="20"
                    class="d-inline-block align-text-top">
                Brikshya Griha
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="aboutus.php">About Us</a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="ContactUs/contactus.php">Contact Us</a></li>

                </ul>
                <form class="d-flex" method="GET" action="products.php">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search products..."
                        aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Products</h2>

        <!-- Category Filter -->
        <form method="GET" class="mb-3">
            <div class="form-row align-items-end">
                <div class="col-auto">
                    <label for="category">Category:</label>
                    <select name="category" id="category" class="form-control" onchange="this.form.submit()">
                        <option value="0">All Categories</option>
                        <?php while ($category = $categoriesResult->fetch_assoc()): ?>
                            <option value="<?= $category['id']; ?>" <?= ($category['id'] == $selectedCategory) ? 'selected' : ''; ?>><?= htmlspecialchars($category['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
        </form>

        <!-- Products Display -->
        <div class="row">
            <?php if ($productsResult->num_rows > 0): ?>
                <?php while ($product = $productsResult->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4 product-item" data-category="<?= $product['category_id']; ?>">
                        <div class="card">
                            <img src="uploads/<?= $product['image']; ?>" class="card-img-top product-img"
                                alt="<?= htmlspecialchars($product['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text">Rs <?= number_format($product['price'], 2); ?></p>
                                <a href="product_details.php?id=<?= $product['id']; ?>" class="btn btn-primary">View Details</a>
                                <a href="cart.php?id=<?= $product['id']; ?>" class="btn btn-success"
                                    onclick="checkLogin(event, '<?= $product['id']; ?>')">Add to Cart</a>
                                <script>
                                    function checkLogin(event, productId) {
                                        // Prevent the default action of the link
                                        event.preventDefault();

                                        // Check if the user is logged in (assuming you set $_SESSION['logged_in'])
                                        <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
                                            alert("You must log in first.");
                                            // Redirect to index page
                                            window.location.href = 'index.php';
                                        <?php else: ?>
                                            // If logged in, navigate to the cart page
                                            window.location.href = 'cart.php?id=' + productId;
                                        <?php endif; ?>
                                    }
                                </script>

                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No products found.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>