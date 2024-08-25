<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$unifiedPostId = $data['unifiedPostId'] ?? null;
$content = $data['content'] ?? null;
$userId = $_SESSION['user_id'];

if (empty($unifiedPostId) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$query = "INSERT INTO Comments (UnifiedPostID, UserID, Content) VALUES (?, ?, ?)";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute([$unifiedPostId, $userId, $content]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
