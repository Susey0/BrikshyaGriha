<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Handle form submission for changing password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Fetch the current password from the database
    $userResult = $conn->query("SELECT password FROM users WHERE id = $userId");
    $user = $userResult->fetch_assoc();

    // Verify the current password
    if (password_verify($currentPassword, $user['password'])) {
        // Validate the new password
        if (validatePassword($newPassword)) {
            // Check if new password and confirm password match
            if ($newPassword === $confirmPassword) {
                // Hash the new password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Update the password in the database
                $updateSql = "UPDATE users SET password='$hashedPassword' WHERE id=$userId";
                if ($conn->query($updateSql)) {
                    $successMessage = "Password changed successfully!";
                } else {
                    $errorMessage = "Error changing password: " . $conn->error;
                }
            } else {
                $errorMessage = "New password and confirm password do not match.";
            }
        } else {
            $errorMessage = "New password must be at least 8 characters long, contain uppercase letters, lowercase letters, numbers, and special characters.";
        }
    } else {
        $errorMessage = "Current password is incorrect.";
    }
}

// Function to validate password strength
function validatePassword($password) {
    // Check the password length
    if (strlen($password) < 8) {
        return false;
    }
    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }
    // Check for at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }
    // Check for at least one number
    if (!preg_match('/\d/', $password)) {
        return false;
    }
    // Check for at least one special character
    if (!preg_match('/[^\w]/', $password)) {
        return false;
    }
    return true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Brikshya Griha</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user_dashboard.css">
</head>
<body>

<div class="container my-5">
    <h2 class="text-center">Change Password</h2>
    
    <?php if (isset($successMessage)): ?>
        <div class="alert alert-success"><?php echo $successMessage; ?></div>
    <?php endif; ?>
    <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
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
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
