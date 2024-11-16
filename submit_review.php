<?php
include 'config.php';
session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0; // Get the user ID from session
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $review = isset($_POST['review']) ? trim($_POST['review']) : '';
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;

    // Validate that the user is logged in and input fields are not empty
    if ($userId > 0 && !empty($review) && $rating > 0 && $productId > 0) {
        // Prepare the SQL query to insert the review
        $sql = "INSERT INTO reviews (user_id, product_id, comment, rating, created_at) VALUES (?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('iisi', $userId, $productId, $review, $rating);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                // Set the success message in session and redirect back to the product page
                $_SESSION['success_message'] = "Review submitted successfully!";
                header("Location: product_details.php?id=$productId");
                exit();
            } else {
                $_SESSION['error_message'] = "Failed to submit your review. Please try again.";
            }
        } else {
            $_SESSION['error_message'] = "Failed to prepare the review submission.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid form submission. Please fill out all fields.";
    }
} else {
    $_SESSION['error_message'] = "Invalid request method.";
}

// Redirect back to the product page if an error occurs
header("Location: product_details.php?id=$productId");
exit();
?>
