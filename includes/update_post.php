<?php
// includes/update_post.php

session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['review_id']) || !isset($_POST['review_text'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$reviewId = $_POST['review_id'];
$reviewText = $_POST['review_text'];
$userId = $_SESSION['user_id'];

try {
    // First, check if the review belongs to the current user
    $stmt = $pdo->prepare("SELECT UserID FROM Reviews WHERE ReviewID = ?");
    $stmt->execute([$reviewId]);
    $reviewUserId = $stmt->fetchColumn();

    if ($reviewUserId != $userId) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to update this review']);
        exit;
    }

    // If the review belongs to the user, proceed with update
    $stmt = $pdo->prepare("UPDATE Reviews SET ReviewText = ? WHERE ReviewID = ?");
    $stmt->execute([$reviewText, $reviewId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Review not found or no changes made']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}