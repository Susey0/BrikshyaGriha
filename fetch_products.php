<?php
session_start();
include 'config.php';

// Get the selected category and search term from the request
$selectedCategory = isset($_GET['category']) ? intval($_GET['category']) : 0;
$searchTerm = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Function to fetch products based on selected category and search term
function fetchProducts($conn, $categoryId, $searchTerm) {
    $productSql = "SELECT * FROM products WHERE name LIKE '%$searchTerm%'";
    if ($categoryId > 0) {
        $productSql .= " AND category_id = $categoryId";
    }
    return $conn->query($productSql);
}

// Fetch products based on the selected category and search term
if ($selectedCategory > 0) {
    $productsResult = fetchProducts($conn, $selectedCategory, $searchTerm);
} else {
    $productsResult = fetchProducts($conn, 0, $searchTerm);
}

// Get the category name
$categoryName = '';
if ($selectedCategory > 0) {
    $categoryResult = $conn->query("SELECT name FROM categories WHERE id = $selectedCategory");
    if ($categoryResult->num_rows > 0) {
        $category = $categoryResult->fetch_assoc();
        $categoryName = $category['name'];
    }
}

// Generate HTML for the products
$output = '';
if ($productsResult->num_rows > 0) {
    while ($product = $productsResult->fetch_assoc()) {
        $output .= '<div class="col-md-4 mb-4 product-item" data-category="' . $product['category_id'] . '">';
        $output .= '<div class="card">';
        $output .= '<img src="uploads/' . $product['image'] . '" class="card-img-top product-img" alt="' . htmlspecialchars($product['name']) . '">';
        $output .= '<div class="card-body">';
        $output .= '<h5 class="card-title">' . htmlspecialchars($product['name']) . '</h5>';
        $output .= '<p class="card-text">Rs ' . number_format($product['price'], 2) . '</p>';
        $output .= '<a href="product_details.php?id=' . $product['id'] . '" class="btn btn-primary">View Details</a>';
        $output .= '<a href="add_to_cart.php?id=' . $product['id'] . '" class="btn btn-success">Add to Cart</a>';
        $output .= '</div></div></div>';
    }
} else {
    $output = '<p>No products found.</p>';
}
echo $output;