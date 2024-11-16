<?php
session_start();
include '../config.php'; // Include your database configuration

// Fetch all contact messages
$sqlContactMessages = 'SELECT * FROM contact_messages ORDER BY submitted_at DESC';
$resultContactMessages = $conn->query($sqlContactMessages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages</title>
    <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css"> 

    <link rel="stylesheet" href="../css/adminpanel.css">
    <script>
    function confirmLogout() {
        return confirm("Are you sure you want to logout?");
    }
</script>
  
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../images/admin.jpg" alt="Admin Logo">
            <span>Admin</span>
        </div>
        <div class="menu">
            <a href="../admin_panel.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="../category_management.php"><i class="fas fa-th-list"></i> Category Management</a>
            <a href="../product_management.php"><i class="fas fa-box"></i> Product Management</a>
            <a href="../order_management.php"><i class="fas fa-shopping-cart"></i> Order Management</a>
            <a href="../user_management.php"><i class="fas fa-users"></i> User Management</a>
            <a href="../sales_report.php"><i class="fas fa-chart-line"></i> Sales Report</a>
            <a href="../contact_management.php"><i class="fas fa-envelope"></i> Contact Messages</a>
            <a href="../logout.php"onclick="return confirmLogout();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <div class="main-content">
        <h1>Contact Messages</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $resultContactMessages->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['subject']; ?></td>
                    <td><?php echo $row['message']; ?></td>
                    <td>
                        <a href="reply.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Reply</a>
                        <a href="delete_contact.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this message?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
