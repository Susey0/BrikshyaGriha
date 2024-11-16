<?php
session_start();
include '../config.php'; // Include your database configuration

if (isset($_GET['id'])) {
    $messageId = $_GET['id'];

    // Delete the message
    $sqlDelete = "DELETE FROM contact_messages WHERE id = ?";
    $stmt = $conn->prepare($sqlDelete);
    $stmt->bind_param("i", $messageId);

    if ($stmt->execute()) {
        header('Location: contact_management.php?message_deleted=1');
    } else {
        header('Location: contact_management.php?error=1');
    }

    $stmt->close();
}
$conn->close();
?>
