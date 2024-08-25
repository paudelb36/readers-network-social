<?php
require_once '../includes/config.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

$postId = $_POST['post_id'] ?? null;
$reviewId = $_POST['review_id'] ?? null;

if (!$postId && !$reviewId) {
    echo json_encode(['success' => false, 'error' => 'No post or review ID provided']);
    exit();
}

$table = $postId ? 'Posts' : 'Reviews';
$id = $postId ?: $reviewId;

try {
    $query = "
        SELECT c.CommentID, c.UserID, c.Content AS CommentText, c.CreatedAt, u.Username, u.ProfilePicture
        FROM Comments c
        JOIN Users u ON c.UserID = u.UserID
        WHERE " . ($table === 'Posts' ? 'c.PostID' : 'c.ReviewID') . " = :id
        ORDER BY CreatedAt DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'comments' => $comments]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
