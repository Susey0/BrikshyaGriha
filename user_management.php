<?php
session_start();
$host = 'localhost';
$dbname = 'brikshya_griha'; 
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all users
$users = [];
$sqlUsers = 'SELECT id, username, email, phone, role FROM users ORDER BY username ASC';
$resultUsers = $conn->query($sqlUsers);

if ($resultUsers) {
    while ($rowUser = $resultUsers->fetch_assoc()) {
        $users[] = $rowUser;
    }
}

// Delete user
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    
    $sqlDelete = "DELETE FROM users WHERE id = $user_id";
    if ($conn->query($sqlDelete) === TRUE) {
        $_SESSION['success_message'] = "User deleted successfully!";
        header('Location: user_management.php');
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="css/adminpanel.css">
    <link rel="stylesheet" href="css/user_management.css"> 
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
        <h1>User Management</h1>
        
        <!-- Display the success message -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert success">
                <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <h2>Existing Users</h2>
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['phone']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <form method="POST" action="user_management.php">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
