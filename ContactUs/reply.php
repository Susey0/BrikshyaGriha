<?php
session_start();
include '../config.php'; // Include your database configuration

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/SMTP.php';


// Fetch the contact message
$messageId = $_GET['id'];
$sqlMessage = "SELECT * FROM contact_messages WHERE id = ?";
$stmt = $conn->prepare($sqlMessage);
$stmt->bind_param("i", $messageId);
$stmt->execute();
$result = $stmt->get_result();
$message = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $replyMessage = htmlspecialchars(trim($_POST['reply_message']));
    $userEmail = $message['email']; // User's email address

    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host = 'smtp.gmail.com';                   // Set the SMTP server to send through (replace with your SMTP server)
        $mail->SMTPAuth = true;                                   // Enable SMTP authentication
        $mail->Username = 'rewatistores@gmail.com';               // SMTP username (your email)
        $mail->Password = 'yjke mobl abnc ihhl';                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;       // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mail->Port = 587;                                   // TCP port to connect to (587 for TLS)

        //Recipients
        $mail->setFrom('rewatistores@gmail.com', 'Brikshya Griha'); // Your email and name
        $mail->addAddress($userEmail);                            // Add a recipient (the user's email)

        // Content
        $mail->isHTML(true);                                      // Set email format to HTML
        $mail->Subject = 'Reply to Your Message';
        $mail->Body = nl2br(htmlspecialchars($replyMessage)); // The reply message (formatted as HTML)
        $mail->AltBody = strip_tags($replyMessage);               // Alternative plain text body

        $mail->send();

        // Update the contact message record with the reply
        $sqlUpdate = "UPDATE contact_messages SET admin_reply = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("si", $replyMessage, $messageId);
        $stmtUpdate->execute();
        $stmtUpdate->close();

        // Set success message in session and redirect
        $_SESSION['success_message'] = 'Reply sent successfully!';
        header('Location: contact_management.php');                                           // Send the email
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"; // Error handling
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Message</title>
    <link rel="stylesheet" href="../fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="../css/adminpanel.css">
</head>

<body>
    <div class="sidebar">
        <div class="logo">
            <img src="../images/admin.jpg" alt="Admin Logo">
            <span>Admin</span>
        </div>
        <div class="menu">
            <a href="admin_panel.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="category_management.php"><i class="fas fa-th-list"></i> Category Management</a>
            <a href="product_management.php"><i class="fas fa-box"></i> Product Management</a>
            <a href="order_management.php"><i class="fas fa-shopping-cart"></i> Order Management</a>
            <a href="user_management.php"><i class="fas fa-users"></i> User Management</a>
            <a href="contact_management.php"><i class="fas fa-envelope"></i> Contact Messages</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="main-content">
        <h1>Reply to Message</h1>
        <form method="post" action="">
            <div>
                <label><strong>Name:</strong> <?php echo htmlspecialchars($message['name']); ?></label><br>
                <label><strong>Email:</strong> <?php echo htmlspecialchars($message['email']); ?></label><br>
                <label><strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?></label><br>
                <label><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($message['message'])); ?></label><br>
                <label for="reply_message">Your Reply:</label>
                <textarea id="reply_message" name="reply_message" rows="5" required></textarea><br>
                <button type="submit" class="btn btn-primary">Send Reply</button>
            </div>
        </form>
    </div>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>