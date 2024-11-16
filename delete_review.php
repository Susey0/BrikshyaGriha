// delete_review.php
<?php
if (isset($_GET['review_id'])) {
    $reviewId = $_GET['review_id'];

    // Check if the review belongs to the logged-in user
    $sql = "SELECT user_id FROM reviews WHERE id = $reviewId";
    $result = $conn->query($sql);
    $review = $result->fetch_assoc();

    if ($review && $review['user_id'] == $_SESSION['user_id']) {
        // Delete the review
        $sql = "DELETE FROM reviews WHERE id = $reviewId";
        $conn->query($sql);
        
        // Redirect to the product page or show success message
        header("Location: product_details.php?id={$productId}");
        exit();
    } else {
        echo "You cannot delete this review.";
        exit;
    }
}
?>