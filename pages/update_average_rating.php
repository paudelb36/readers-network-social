<?php
// update_average_rating.php

// Include your database connection file
require_once '../includes/config.php';

/**
 * Calculate the average rating and total reviews for a given book and update the Books table.
 *
 * @param int $bookId The ID of the book.
 * @param PDO $pdo The PDO database connection.
 * @return array
 */
function updateAverageRating($bookId, $pdo) {
    try {
        // Step 1: Calculate the Average Rating
        $queryAverageRating = "SELECT AVG(Rating) AS AverageRating, COUNT(Rating) AS TotalReviews FROM Ratings WHERE BookID = ?";
        $stmtAverageRating = $pdo->prepare($queryAverageRating);
        $stmtAverageRating->bindParam(1, $bookId, PDO::PARAM_INT);
        $stmtAverageRating->execute();
        
        // Fetch the average rating and total reviews
        $result = $stmtAverageRating->fetch(PDO::FETCH_ASSOC);
        
        // Check if there are ratings
        if ($result['AverageRating'] !== null) {
            $averageRating = round($result['AverageRating'], 2); // Round to two decimal places
            $totalReviews = $result['TotalReviews'];
            
            // Step 2: Update the Books Table with the Average Rating and Total Reviews
            $queryUpdate = "UPDATE Books SET AverageRating = :averageRating, TotalReviews = :totalReviews WHERE BookID = :bookId";
            $stmtUpdate = $pdo->prepare($queryUpdate);
            $stmtUpdate->bindParam(':averageRating', $averageRating, PDO::PARAM_STR);
            $stmtUpdate->bindParam(':totalReviews', $totalReviews, PDO::PARAM_INT);
            $stmtUpdate->bindParam(':bookId', $bookId, PDO::PARAM_INT);
            $stmtUpdate->execute();

            return ["success" => true, "averageRating" => $averageRating, "totalReviews" => $totalReviews];
        } else {
            return ["success" => false, "message" => "No ratings found for Book ID $bookId."];
        }
    } catch (PDOException $e) {
        return ["success" => false, "message" => "Error: " . $e->getMessage()];
    }
}

// Usage example: Call the function with a specific book ID
if (isset($_GET['book_id'])) {
    $bookId = (int)$_GET['book_id'];
    $response = updateAverageRating($bookId, $pdo);
    echo json_encode($response);
} else {
    echo json_encode(["success" => false, "message" => "No Book ID provided."]);
}
?>
