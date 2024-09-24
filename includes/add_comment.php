<?php
require_once '../includes/config.php';

session_start();

if (isset($_POST['review_id'], $_POST['comment']) && isset($_SESSION['user_id'])) {
    $reviewId = $_POST['review_id'];
    $userId = $_SESSION['user_id'];
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
        $insertQuery = "INSERT INTO Comments (ReviewID, UserID, Content, CreatedAt) 
                        VALUES (:reviewId, :userId, :content, NOW())";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([
            ':reviewId' => $reviewId,
            ':userId' => $userId,
            ':content' => $comment
        ]);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Unauthorized or missing data']);
}
?>
