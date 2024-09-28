<?php
// add_comment.php
require_once '../includes/config.php';

session_start();

if (isset($_POST['review_id'], $_POST['comment']) && isset($_SESSION['user_id'])) {
    $reviewId = $_POST['review_id'];
    $userId = $_SESSION['user_id'];
    $comment = trim($_POST['comment']);

    if (!empty($comment)) {
        // Insert comment into the Comments table
        $insertQuery = "INSERT INTO Comments (ReviewID, UserID, Content, CreatedAt) 
                        VALUES (:reviewId, :userId, :content, NOW())";
        $insertStmt = $pdo->prepare($insertQuery);
        $insertStmt->execute([
            ':reviewId' => $reviewId,
            ':userId' => $userId,
            ':content' => $comment
        ]);

        // Get the UserID of the review owner to send the notification
        $getOwnerQuery = "SELECT UserID FROM Reviews WHERE ReviewID = :reviewId";
        $ownerStmt = $pdo->prepare($getOwnerQuery);
        $ownerStmt->execute([':reviewId' => $reviewId]);
        $ownerId = $ownerStmt->fetchColumn();

        // Insert notification for the comment
        if ($ownerId && $ownerId != $userId) { // Check if the owner is not the commenter
            $notificationQuery = "INSERT INTO Notifications (ActorID, Type, Content, RecipientID) 
                                   VALUES (:actorId, 'comment', :content, :recipientId)";
            $notificationStmt = $pdo->prepare($notificationQuery);
            $notificationStmt->execute([
                ':actorId' => $userId,
                ':content' => $comment,
                ':recipientId' => $ownerId
            ]);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Comment cannot be empty']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Unauthorized or missing data']);
}
?>
