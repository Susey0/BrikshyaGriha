<?php
session_start();
include '../config.php'; // Include your database configuration

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect the form data
    $name = htmlspecialchars(trim($_POST['name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $subject = htmlspecialchars(trim($_POST['subject']));
    $message = htmlspecialchars(trim($_POST['message']));

    // Validation
    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($subject)) {
        $errors[] = "Subject is required.";
    }

    if (empty($message)) {
        $errors[] = "Message is required.";
    }

    // If there are validation errors, redirect back with error messages
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: contactus.php');
        exit();
    }

    // Prepare to insert into the database
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to contactus.php with success parameter
        header('Location: contactus.php?success=1');
    } else {
        // Handle the error (e.g., log it, display a message, etc.)
        $_SESSION['errors'] = ["Error saving message. Please try again later."];
        header('Location: contactus.php');
    }

    // Close the statement
    $stmt->close();
    // Close the connection
    $conn->close();
    exit();
}
?>
