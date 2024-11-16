<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'config.php'; // Include your database connection

if (isset($_SESSION['login_error'])) {
    $loginError = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Clear the error after displaying it
} else {
    $loginError = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    //$username = $_POST['username'];

    // Query to find the user
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Login successful, redirect based on role
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];


            if ($user['role'] === 'admin') {
                header('Location: admin_panel.php'); // Redirect admin to admin panel
            } else {
                header('Location: shop.php'); // Redirect regular user to their dashboard
            }
            exit;
        } else {
            // Password is incorrect
            $_SESSION['login_error'] = "Incorrect password.";
            header('Location:'. $_SERVER['HTTP_REFERER']);
            exit;
        }
    } else {
        // Username not found
        $_SESSION['login_error'] = "Email not found.";
        header('Location:'. $_SERVER['HTTP_REFERER']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

</head>
<body>
    

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Login</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <?php if ($loginError): ?>
                <div class="alert alert-danger">
                    <?php echo $loginError; ?>
                </div>
            <?php endif; ?>
            <div class="modal-body">
                <div id="loginSuccessMessage" class="alert alert-success" style="display: none;"></div>

                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Email:</label>
                        <input type="text" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="toggleLoginPassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary">Login</button>
                    <div class="form-group text-center">
                        <p>Don't have an account? <a href="#" data-dismiss="modal" data-toggle="modal"
                                data-target="#signupModal">Signup</a></p>
                        <p><a href="#" data-toggle="modal" data-target="#forgotPasswordModal">Forgot Password?</a></p>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>