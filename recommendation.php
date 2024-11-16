<?php
include 'config.php'; // Include your database connection
// recommendation.php

function getSimilarProducts($conn, $currentProductId, $description, $limit = 5) {
    $query = "SELECT * FROM products WHERE id != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $currentProductId);
    $stmt->execute();
    $result = $stmt->get_result();

    $similarProducts = [];
    while ($row = $result->fetch_assoc()) {
        $similarity = cosineSimilarity($description, $row['description']);
        $similarProducts[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'image' => $row['image'],
            'price' => $row['price'],
            'similarity' => $similarity
        ];
    }

    // Sort by similarity and get top results
    usort($similarProducts, function($a, $b) {
        return $b['similarity'] <=> $a['similarity'];
    });

    return array_slice($similarProducts, 0, $limit);
}

function cosineSimilarity($desc1, $desc2) {
    // Implement cosine similarity logic here (similar to previous example)
    $words1 = str_word_count(strtolower($desc1), 1);
    $words2 = str_word_count(strtolower($desc2), 1);

    $freq1 = array_count_values($words1);
    $freq2 = array_count_values($words2);

    $dotProduct = 0;
    $magnitude1 = 0;
    $magnitude2 = 0;

    foreach ($freq1 as $word => $count) {
        if (isset($freq2[$word])) {
            $dotProduct += $count * $freq2[$word];
        }
        $magnitude1 += $count ** 2;
    }

    foreach ($freq2 as $count) {
        $magnitude2 += $count ** 2;
    }

    $magnitude1 = sqrt($magnitude1);
    $magnitude2 = sqrt($magnitude2);

    return ($magnitude1 && $magnitude2) ? $dotProduct / ($magnitude1 * $magnitude2) : 0;
}
?>

          