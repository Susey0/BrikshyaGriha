<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/SMTP.php';


// Connect to your database
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Check if the email exists in the database
    $query = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $query->bind_param("s", $email);
    $query->execute();
    $result = $query->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Generate a secure token
        $resetToken = bin2hex(random_bytes(50));
        $resetLink = "http://localhost/BrikshyaGriha/reset_password.php?token=" . $resetToken;

        // Store the token in the database
        $updateQuery = $conn->prepare("UPDATE users SET reset_token = ? WHERE email = ?");
        $updateQuery->bind_param("ss", $resetToken, $email);
        $updateQuery->execute();

        // Send email with PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'rewatistores@gmail.com'; // Your email
            $mail->Password = 'yjke mobl abnc ihhl'; // Your email password (App Password if using 2FA)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('rewatistores@gmail.com', 'Brikshya Griha');
            $mail->addAddress($email); // User's email

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "Click <a href='$resetLink'>here</a> to reset your password.";
            $mail->AltBody = "Click this link to reset your password: $resetLink";

            $mail->send();
            echo 'Reset link has been sent to your email.';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        echo "Email not found in the system.";
    }
}
?>
