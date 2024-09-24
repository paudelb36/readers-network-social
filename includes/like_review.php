<?php
require_once '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];
$reviewId = $_POST['review_id'];

// Check if the user already liked the post
$stmt = $pdo->prepare('SELECT * FROM likes WHERE UserID = ? AND ReviewID = ?');
$stmt->execute([$userId, $reviewId]);
$like = $stmt->fetch();

if ($like) {
    // Unlike the post
    $stmt = $pdo->prepare('DELETE FROM likes WHERE UserID = ? AND ReviewID = ?');
    $stmt->execute([$userId, $reviewId]);
    echo json_encode(['success' => true, 'message' => 'Like removed']);
} else {
    // Like the post
    $stmt = $pdo->prepare('INSERT INTO likes (UserID, ReviewID, CreatedAt) VALUES (?, ?, NOW())');
    $stmt->execute([$userId, $reviewId]);
    echo json_encode(['success' => true, 'message' => 'Post liked']);
}
?>
