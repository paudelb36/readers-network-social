<?php
// includes/update_review.php

session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$reviewId = isset($_POST['review_id']) ? intval($_POST['review_id']) : 0;
$reviewText = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';

if ($reviewId === 0 || empty($reviewText)) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit();
}

try {
    // First, check if the review belongs to the current user
    $stmt = $pdo->prepare("SELECT UserID FROM Reviews WHERE ReviewID = ?");
    $stmt->execute([$reviewId]);
    $review = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$review) {
        echo json_encode(['success' => false, 'message' => 'Review not found']);
        exit();
    }

    if ($review['UserID'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'You are not authorized to edit this review']);
        exit();
    }

    // If authorized, proceed with the update
    $stmt = $pdo->prepare("UPDATE Reviews SET ReviewText = ? WHERE ReviewID = ?");
    $result = $stmt->execute([$reviewText, $reviewId]);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Review updated successfully']);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Failed to update review: ' . $errorInfo[2]]);
    }
} catch (PDOException $e) {
    error_log("Database error in update_review.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}