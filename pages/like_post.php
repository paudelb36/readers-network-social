<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$unifiedPostId = $data['unifiedPostId'] ?? null;
$postType = $data['postType'] ?? null;
$like = $data['like'] ?? false;
$userId = $_SESSION['user_id'];

if ($postType === 'post') {
    $query = $like 
        ? "INSERT INTO Likes (UserID, UnifiedPostID) VALUES (?, ?)"
        : "DELETE FROM Likes WHERE UserID = ? AND UnifiedPostID = ?";
    $params = $like ? [$userId, $unifiedPostId] : [$userId, $unifiedPostId];
} elseif ($postType === 'review') {
    $query = $like 
        ? "INSERT INTO Likes (UserID, UnifiedPostID) VALUES (?, ?)"
        : "DELETE FROM Likes WHERE UserID = ? AND UnifiedPostID = ?";
    $params = $like ? [$userId, $unifiedPostId] : [$userId, $unifiedPostId];
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid post type']);
    exit;
}

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
