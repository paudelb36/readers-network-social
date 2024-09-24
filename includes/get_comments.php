<?php
require_once '../includes/config.php';

if (!isset($_GET['review_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$reviewId = $_GET['review_id'];

$getComments = $pdo->prepare("
    SELECT c.CommentID, c.Content, c.CreatedAt, u.Username
    FROM Comments c
    JOIN Users u ON c.UserID = u.UserID
    WHERE c.ReviewID = ?
    ORDER BY c.CreatedAt DESC
");
$getComments->execute([$reviewId]);
$comments = $getComments->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'comments' => $comments]);
?>
