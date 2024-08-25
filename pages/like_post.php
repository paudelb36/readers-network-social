<?php
session_start();
require_once '../includes/config.php'; // Ensure this path is correct for your setup

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$unifiedPostId = $data['unifiedPostId'] ?? null;
$postType = $data['postType'] ?? null;
$like = $data['like'] ?? false;
$userId = $_SESSION['user_id'];

if ($postType === 'post' || $postType === 'review') {
    $query = $like
        ? "INSERT INTO Likes (UnifiedPostID, UserID, CreatedAt) VALUES (?, ?, NOW())"
        : "DELETE FROM Likes WHERE UnifiedPostID = ? AND UserID = ?";
    $params = $like ? [$unifiedPostId, $userId] : [$unifiedPostId, $userId];
    
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid post type']);
}
?>
