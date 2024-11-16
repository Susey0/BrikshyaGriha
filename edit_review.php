// edit_review.php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['review_id'])) {
    $reviewId = $_GET['review_id'];

    // Fetch review data from the database
    $sql = "SELECT * FROM reviews WHERE id = $reviewId AND user_id = {$_SESSION['user_id']}";
    $result = $conn->query($sql);
    $review = $result->fetch_assoc();

    if ($review) {
        $rating = $review['rating'];
        $reviewText = $review['comment'];
    } else {
        echo "Review not found or you don't have permission to edit this review.";
        exit;
    }
}

?>

<form action="edit_review.php" method="POST">
    <input type="hidden" name="review_id" value="<?= $reviewId; ?>">

    <!-- Star Rating -->
    <div class="rating">
        <span class="star" data-value="1">&#9733;</span>
        <span class="star" data-value="2">&#9733;</span>
        <span class="star" data-value="3">&#9733;</span>
        <span class="star" data-value="4">&#9733;</span>
        <span class="star" data-value="5">&#9733;</span>
    </div>

    <input type="hidden" name="rating" id="rating_value" value="<?= $rating; ?>" required>

    <!-- Review Text -->
    <div class="form-group">
        <textarea class="form-control" name="review" id="review" rows="4" required><?= htmlspecialchars($reviewText); ?></textarea>
    </div>

    <button type="submit" class="btn btn-primary">Update Review</button>
</form>

<script>
// Initialize the star rating based on the current review rating
highlightStars(<?= $rating; ?>);
</script>
