<?php
// like_review.php
require_once '../includes/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['user_id'];
$reviewId = $_POST['review_id'] ?? null;
$action = $_POST['action'] ?? 'like';

if (!$reviewId) {
    echo json_encode(['success' => false, 'message' => 'Review ID not provided']);
    exit;
}

try {
    $pdo->beginTransaction();

    if ($action === 'like') {
        $stmt = $pdo->prepare("INSERT IGNORE INTO Likes (UserID, ReviewID) VALUES (?, ?)");
        $stmt->execute([$userId, $reviewId]);

        // Get the UserID of the review owner to send the notification
        $getOwnerQuery = "SELECT UserID FROM Reviews WHERE ReviewID = :reviewId";
        $ownerStmt = $pdo->prepare($getOwnerQuery);
        $ownerStmt->execute([':reviewId' => $reviewId]);
        $ownerId = $ownerStmt->fetchColumn();

        // Insert notification for the like
        if ($ownerId && $ownerId != $userId) { // Check if the owner is not the liker
            $notificationQuery = "INSERT INTO Notifications (ActorID, Type, RecipientID) 
                                   VALUES (:actorId, 'reaction', :recipientId)";
            $notificationStmt = $pdo->prepare($notificationQuery);
            $notificationStmt->execute([
                ':actorId' => $userId,
                ':recipientId' => $ownerId
            ]);
        }
    } else {
        $stmt = $pdo->prepare("DELETE FROM Likes WHERE UserID = ? AND ReviewID = ?");
        $stmt->execute([$userId, $reviewId]);
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Likes WHERE ReviewID = ?");
    $stmt->execute([$reviewId]);
    $likeCount = $stmt->fetchColumn();

    $pdo->commit();

    echo json_encode(['success' => true, 'likeCount' => $likeCount]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
}
?>
