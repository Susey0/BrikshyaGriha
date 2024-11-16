<?php
session_start();
$host = 'localhost';
$dbname = 'brikshya_griha'; // Update this with your database name
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$product_id = intval($_GET['id']); // Get product ID from URL

// Fetch product details for the given ID
$sqlProduct = "SELECT * FROM products WHERE id = $product_id";
$resultProduct = $conn->query($sqlProduct);

if ($resultProduct->num_rows == 0) {
    die("Product not found!");
}

$product = $resultProduct->fetch_assoc();

// Fetch categories for the dropdown
$sqlCategories = 'SELECT * FROM categories';
$resultCategories = $conn->query($sqlCategories);

// Update product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) { // Updated check here
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_description = $_POST['product_description'];
    $product_category = $_POST['product_category'];
    $product_stock = intval($_POST['product_stock']); // Get stock value
// Ensure stock is not negative
    if ($product_stock < 0) {
        $message = "Stock cannot be negative.";
    } else {

        // Handle image upload if a new one is provided
        $image = $product['image']; // Keep existing image by default
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $image = basename($_FILES['product_image']['name']);
            $targetDir = "uploads/";
            $targetFilePath = $targetDir . $image;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFilePath)) {
                // File uploaded successfully
            } else {
                echo "Error uploading image.";
                exit();
            }
        }


        // Use prepared statements to avoid SQL injection
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, description=?, category_id=?, stock=?, image=? WHERE id=?");
        $stmt->bind_param("sdssisi", $product_name, $product_price, $product_description, $product_category, $product_stock, $image, $product_id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Product updated successfully!";
            header('Location: product_management.php'); // Redirect to product management page
            exit();
        } else {
            echo "Error updating product: " . $stmt->error;
        }

        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">

    <link rel="stylesheet" href="css/edit_product.css"> <!-- Same CSS as the main content -->
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
                <i class="fas fa-list"></i> Category Management
            </a>
            <a href="product_management.php" class="active">
                <i class="fas fa-box"></i> Product Management
            </a>
            <a href="order_management.php">
                <i class="fas fa-shopping-cart"></i> Order Management
            </a>
            <a href="user_management.php">
                <i class="fas fa-users"></i> User Management
            </a>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <h1>Edit Product</h1>
        <div class="edit-product-form">
            <h3>Edit Product Details</h3>
            <form action="edit_product.php?id=<?php echo $product['id']; ?>" method="POST"
                enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" name="product_name" value="<?php echo $product['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="product_price">Price (in Rs)</label>
                    <input type="number" name="product_price" value="<?php echo $product['price']; ?>" step="0.01"
                        required>
                </div>
                <div class="form-group">
                    <label for="product_description">Description</label>
                    <textarea name="product_description" required><?php echo $product['description']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="product_category">Category</label>
                    <select name="product_category" required>
                        <option value="">Select a category</option>
                        <?php while ($category = $resultCategories->fetch_assoc()): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_stock">Stock</label>
                    <input type="number" name="product_stock" value="<?php echo $product['stock']; ?>" min="0" required>
                </div>
                <div class="form-group">
                    <label for="product_image">Current Image</label>
                    <?php if ($product['image']): ?>
                        <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"
                            width="100">
                    <?php endif; ?>
                    <label for="product_image">Change Image</label>
                    <input type="file" name="product_image" accept="image/*">
                </div>
                <button type="submit" name="update_product">Update Product</button>
            </form>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>