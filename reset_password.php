<?php
// Connect to your database
require 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $query = $conn->prepare("SELECT * FROM users WHERE reset_token = ?");
    $query->bind_param("s", $token);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // If the form is submitted to reset the password
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
            $email = $user['email'];

            // Update the password in the database
            $updateQuery = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL WHERE email = ?");
            $updateQuery->bind_param("ss", $newPassword, $email);
            $updateQuery->execute();

            echo "<p>Your password has been reset successfully!</p>";
            echo '<div class="link"><a href="index.php">Go back to the Home Page</a></div>'; // Link to index page
            exit; // Stop further execution
        }

        // Display the password reset form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
            <link rel="stylesheet" href="css/resetpassword.css">
        </head>
        <body>
        <div class="container">
            <h2>Reset Password</h2>
            <form action="reset_password.php?token=<?php echo $token; ?>" method="POST">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" required>
                <button type="submit">Reset Password</button>
            </form>
            <div class="link">
                <a href="index.php">Go back to the Home Page</a>
            </div> <!-- Link to index page -->
        </div>
        </body>
        </html>
        
        <?php
    } else {
        echo "Invalid token!";
    }
} else {
    echo "No token provided!";
}
?>
