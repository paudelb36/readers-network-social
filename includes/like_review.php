<?php
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