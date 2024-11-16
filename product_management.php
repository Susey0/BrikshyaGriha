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
$messageType = '';

// Fetch all products
$sqlProducts = 'SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC';

$resultProducts = $conn->query($sqlProducts);

// Fetch all categories
$sqlCategories = 'SELECT * FROM categories';
$resultCategories = $conn->query($sqlCategories);

// Add a new product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_description = $_POST['product_description'];
    $product_category = $_POST['product_category'];
    $product_stock = $_POST['product_stock'];

    // Ensure the stock is a non-negative integer
    if (!filter_var($product_stock, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]])) {
        echo "Error: Stock quantity must be a non-negative integer.";
        exit();
    }

    // Ensure the price is a positive integer
    if (!filter_var($product_price, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]])) {
        echo "Error: Price must be a positive integer.";
        exit();
    }
    // Check if the product already exists
    $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM products  WHERE name = ?");
    $stmtCheck->bind_param("s", $product_name);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count > 0) {
        $_SESSION['error_message'] = "Error: Product '$product_name' already exists.";
        header('Location: product_management.php');
        exit();
    }

    // Ensure the 'uploads/' directory exists
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Create directory if it doesn't exist
    }

    // Handle image upload
    $image = '';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $image = basename($_FILES['product_image']['name']);
        $targetFilePath = $targetDir . $image;

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetFilePath)) {
            // File uploaded successfully
        } else {
            echo "Error uploading image.";
            exit(); // Stop execution if file upload fails
        }
    }

    // Use prepared statements to avoid SQL injection and syntax errors
    $stmt = $conn->prepare("INSERT INTO products (name, price, description, category_id, image,stock) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdssss", $product_name, $product_price, $product_description, $product_category, $image,$product_stock);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Product added successfully!";
        header('Location: product_management.php'); // Redirect after adding
        exit();
    } else {
        echo "Error adding product: " . $stmt->error;
    }

    $stmt->close();
}

// Delete a product
if (isset($_GET['delete'])) {
    $productId = intval($_GET['delete']);
    $sqlDeleteProduct = "DELETE FROM products WHERE id = $productId";
    if ($conn->query($sqlDeleteProduct) === TRUE) {
        $_SESSION['success_message'] = "Product deleted successfully!";

    } else {
        echo "Error deleting product: " . $conn->error;

    }
    header('Location: product_management.php'); // Redirect after deletion

    exit();
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/product_management.css">
    <script>
        // Function to hide the alert message after a delay
        function hideAlert() {
            const alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(() => {
                    alert.style.display = 'none'; // Hide the alert
                }, 2000); // Change 5000 to the number of milliseconds you want the message to be displayed (5 seconds in this case)
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
        <h1>Product Management</h1>

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

        <div class="add-product-form">
            <h3>Add New Product</h3>
            <form action="product_management.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="product_name">Product Name</label>
                    <input type="text" name="product_name" required>
                </div>
                <div class="form-group">
                    <label for="product_price">Price (in Rs)</label>
                    <input type="number" name="product_price" min="1" step="1" required>
                </div>

                <div class="form-group">
                    <label for="product_description">Description</label>
                    <textarea name="product_description" required></textarea>
                </div>
                <div class="form-group">
                    <label for="product_stock">Stock Quantity</label>
                    <input type="number" name="product_stock" min="0" step="1" required>
                </div>

                <div class="form-group">
                    <label for="product_category">Category</label>
                    <select name="product_category" required>
                        <option value="">Select a category</option>
                        <?php while ($category = $resultCategories->fetch_assoc()): ?>
                            <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="product_image">Image</label>
                    <input type="file" name="product_image" accept="image/*" required>
                </div>
                <button type="submit" name="add_product">Add Product</button>
            </form>
        </div>

        <div class="product-list">
            <h3>Product List</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($product = $resultProducts->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $product['id']; ?></td>
                            <td><?php echo $product['name']; ?></td>
                            <td><?php echo 'Rs ' . number_format($product['price'], 2, '.', ''); ?></td>
                            <td><?php echo $product['description']; ?></td>
                            <td><?php echo $product['category_name'];  ?></td>
                            <td><?php echo $product['stock']; ?></td>
                            </td>

                            <td>
                                <?php if ($product['image']): ?>
                                    <img src="uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"
                                        width="50">
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn-edit">Edit</a>
                                <a href="product_management.php?delete=<?php echo $product['id']; ?>" class="btn-delete"
                                    onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<?php
$conn->close();
?>