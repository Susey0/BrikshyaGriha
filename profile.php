<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Fetch user details from the database
$userId = $_SESSION['user_id'];
$userResult = $conn->query("SELECT * FROM users WHERE id = $userId");
$user = $userResult->fetch_assoc();

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = htmlspecialchars($_POST['first_name']);
    $lastName = htmlspecialchars($_POST['last_name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $address = htmlspecialchars($_POST['address']);

    // Update user information in the database
    $updateSql = "UPDATE users SET first_name='$firstName', last_name='$lastName', email='$email', phone='$phone', address='$address' WHERE id=$userId";
    if ($conn->query($updateSql)) {
        $successMessage = "Profile updated successfully!";
    } else {
        $errorMessage = "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Brikshya Griha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="fontawesome-free-6.6.0-web/css/all.min.css"> 

    <link rel="stylesheet" href="css/user_dashboard.css">
    <script>
    function confirmLogout() {
        return confirm("Are you sure you want to logout?");
    }
</script>

</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="images/logo.jpg" alt="Brikshya Griha" width="30" height="30" class="d-inline-block align-text-top">
            Brikshya Griha
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
        <div class="d-flex">
            
            <a class="nav-link" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                <li><a class="dropdown-item" href="logout.php"onclick="return confirmLogout();">Sign Out</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
    <h2 class="text-center">User Profile</h2>
    
    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>

    <hr>

    <h3>Change Password</h3>
    <form method="POST" action="change_password.php">
        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-warning">Change Password</button>
    </form>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center py-3">
    <p>&copy; 2024 Brikshya Griha. All rights reserved.</p>
    <div>
        <a href="#" class="text-white">Privacy Policy</a> |
        <a href="#" class="text-white">Terms of Service</a> |
        <a href="#" class="text-white">FAQs</a>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
