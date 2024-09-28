<?php
// includes/get_review.php

session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !$_SESSION['logged_in']) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

if (!isset($_GET['review_id'])) {
    echo json_encode(['error' => 'Review ID not provided']);
    exit();
}

$reviewId = intval($_GET['review_id']);

try {
    $stmt = $pdo->prepare("SELECT ReviewID, ReviewText, UserID FROM Reviews WHERE ReviewID = ?");
    $stmt->execute([$reviewId]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($review) {
        // Check if the current user is the author of the review
        if ($review['UserID'] != $_SESSION['user_id']) {
            echo json_encode(['error' => 'You are not authorized to edit this review']);
            exit();
        }
        
        echo json_encode([
            'ReviewID' => $review['ReviewID'],
            'ReviewText' => $review['ReviewText']
        ]);
    } else {
        echo json_encode(['error' => 'Review not found']);
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error occurred']);
}