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

// Fetch all categories
$sqlCategories = 'SELECT * FROM categories';
$resultCategories = $conn->query($sqlCategories);

// Add a new category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];

    $sqlAddCategory = "INSERT INTO categories (name) VALUES ('$category_name')";

    if ($conn->query($sqlAddCategory) === TRUE) {
        $_SESSION['message'] = "Category added successfully!";

        header('Location: category_management.php'); // Redirect after successful addition
        exit();
    } else {
        echo "Error adding category: " . $conn->error;
    }
}

// Delete a category
if (isset($_GET['delete'])) {
    $categoryId = intval($_GET['delete']);
    $sqlDeleteCategory = "DELETE FROM categories WHERE id = $categoryId";
    if ($conn->query($sqlDeleteCategory) === TRUE) {
        $_SESSION['message'] = "Category deleted successfully!";

        header('Location: category_management.php'); // Redirect after successful deletion
        exit();
    } else {
        echo "Error deleting category: " . $conn->error;
    }
}

// Edit a category
if (isset($_GET['edit'])) {
    $categoryId = intval($_GET['edit']);
    $editCategory = $conn->query("SELECT * FROM categories WHERE id = $categoryId")->fetch_assoc();
}

// Update category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_category'])) {
    $categoryId = $_POST['category_id'];
    $category_name = $_POST['category_name'];

    $sqlUpdateCategory = "UPDATE categories SET name = '$category_name' WHERE id = $categoryId";

    if ($conn->query($sqlUpdateCategory) === TRUE) {
        $_SESSION['message'] = "Category updated successfully!";

        header('Location: category_management.php'); // Redirect after successful update
        exit();
    } else {
        echo "Error updating category: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/category_management.css">
    <style>
        .alert {
    padding: 15px;
    margin: 10px 0;
    border-radius: 5px;
    font-size: 16px;
}

.alert.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

    </style>
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
        <h1>Category Management</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert success">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Clear the message after displaying
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert error">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']); // Clear the error after displaying
                ?>
            </div>
        <?php endif; ?>


        <div class="add-category-form">
            <h3><?php echo isset($editCategory) ? 'Edit Category' : 'Add New Category'; ?></h3>
            <form action="category_management.php" method="POST">
                <input type="hidden" name="category_id"
                    value="<?php echo isset($editCategory) ? $editCategory['id'] : ''; ?>">
                <div class="form-group">
                    <label for="category_name">Category Name</label>
                    <input type="text" name="category_name"
                        value="<?php echo isset($editCategory) ? $editCategory['name'] : ''; ?>" required>
                </div>
                <button type="submit" name="<?php echo isset($editCategory) ? 'update_category' : 'add_category'; ?>">
                    <?php echo isset($editCategory) ? 'Update Category' : 'Add Category'; ?>
                </button>
            </form>
        </div>

        <div class="category-list">
            <h3>Category List</h3>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = $resultCategories->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $category['id']; ?></td>
                            <td><?php echo $category['name']; ?></td>
                            <td>
                                <a href="category_management.php?edit=<?php echo $category['id']; ?>"
                                    class="btn-edit">Edit</a>
                                <a href="category_management.php?delete=<?php echo $category['id']; ?>" class="btn-delete"
                                    onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
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