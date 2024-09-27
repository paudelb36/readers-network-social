<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$reviewId = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
$opinionText = isset($_POST['opinion']) ? trim($_POST['opinion']) : '';
$opinionId = isset($_POST['opinion_id']) ? intval($_POST['opinion_id']) : 0; // Get opinion ID if editing

// Validate input
if ($reviewId === 0 || $rating === 0 || empty($opinionText)) {
    echo "Invalid input. Please provide all required information.<br>";
    echo "ReviewID: $reviewId, Rating: $rating, Opinion: $opinionText";
    exit();
}

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Fetch the BookID associated with the ReviewID
    $stmtBookId = $pdo->prepare('SELECT BookID FROM Reviews WHERE ReviewID = ?');
    $stmtBookId->execute([$reviewId]);
    $bookId = $stmtBookId->fetchColumn();

    if (!$bookId) {
        throw new Exception("Book not found for the given review.");
    }

    // Check if the user already has a rating for this book
    $stmtCheckRating = $pdo->prepare('SELECT Rating FROM Ratings WHERE UserID = ? AND BookID = ?');
    $stmtCheckRating->execute([$userId, $bookId]);
    $existingRating = $stmtCheckRating->fetchColumn();

    if ($existingRating !== false) {
        // Update the existing rating
        $stmtUpdateRating = $pdo->prepare('UPDATE Ratings SET Rating = ? WHERE UserID = ? AND BookID = ?');
        if (!$stmtUpdateRating->execute([$rating, $userId, $bookId])) {
            $errorInfo = $stmtUpdateRating->errorInfo();
            echo "Failed to update rating: " . implode(", ", $errorInfo) . "<br>";
            throw new Exception("Failed to update rating.");
        }
    } else {
        // Insert a new rating
        $stmtRating = $pdo->prepare('INSERT INTO Ratings (UserID, BookID, Rating) VALUES (?, ?, ?)');
        if (!$stmtRating->execute([$userId, $bookId, $rating])) {
            $errorInfo = $stmtRating->errorInfo();
            echo "Failed to insert rating: " . implode(", ", $errorInfo) . "<br>";
            throw new Exception("Failed to insert rating.");
        }
    }

    // Check if the user already has an opinion for this review
    $stmtCheckOpinion = $pdo->prepare('SELECT OpinionID FROM Opinions WHERE UserID = ? AND ReviewID = ?');
    $stmtCheckOpinion->execute([$userId, $reviewId]);
    $existingOpinion = $stmtCheckOpinion->fetchColumn();

    if ($existingOpinion !== false) {
        // Update the existing opinion
        $stmtUpdateOpinion = $pdo->prepare('UPDATE Opinions SET OpinionText = ? WHERE OpinionID = ?');
        if (!$stmtUpdateOpinion->execute([$opinionText, $existingOpinion])) {
            $errorInfo = $stmtUpdateOpinion->errorInfo();
            echo "Failed to update opinion: " . implode(", ", $errorInfo) . "<br>";
            throw new Exception("Failed to update opinion.");
        }
    } else {
        // Insert a new opinion
        $stmtOpinion = $pdo->prepare('INSERT INTO Opinions (UserID, ReviewID, OpinionText) VALUES (?, ?, ?)');
        if (!$stmtOpinion->execute([$userId, $reviewId, $opinionText])) {
            $errorInfo = $stmtOpinion->errorInfo();
            echo "Failed to insert opinion: " . implode(", ", $errorInfo) . "<br>";
            throw new Exception("Failed to insert opinion.");
        }
    }

    // Calculate the new average rating for the book
    $stmtAverage = $pdo->prepare('SELECT AVG(Rating) AS AverageRating FROM Ratings WHERE BookID = ?');
    $stmtAverage->execute([$bookId]);
    $averageRating = $stmtAverage->fetchColumn();

    if ($averageRating === false) {
        throw new Exception("Failed to calculate average rating.");
    }

    // Update the Books table with the new average rating
    $stmtUpdateBook = $pdo->prepare('UPDATE Books SET AverageRating = ? WHERE BookID = ?');
    if (!$stmtUpdateBook->execute([$averageRating, $bookId])) {
        $errorInfo = $stmtUpdateBook->errorInfo();
        echo "Failed to update average rating: " . implode(", ", $errorInfo) . "<br>";
        throw new Exception("Failed to update average rating.");
    }

    // Commit the transaction
    $pdo->commit();

    // Redirect to the review page with a success message
    $_SESSION['success'] = "Rating and opinion have been submitted successfully.";
    header('Location: view_review.php?review_id=' . $reviewId);
    exit();

} catch (Exception $e) {
    // Rollback in case of an error
    $pdo->rollBack();
    $_SESSION['error'] = "An error occurred: " . $e->getMessage();
    header('Location: view_review.php?review_id=' . $reviewId);
    exit();
}
?>
