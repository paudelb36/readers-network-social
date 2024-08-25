<?php
// handle_request.php

session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get data from request
$data = json_decode(file_get_contents('php://input'), true);
$userId = $data['userId'];
$action = $data['action']; // 'accept' or 'reject'

// Validate action
if (!in_array($action, ['accept', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

try {
    $pdo->beginTransaction();

    if ($action === 'accept') {
        // Add friend
        $stmt = $pdo->prepare("INSERT INTO Friends (UserID, FriendID) VALUES (:userId, :friendId), (:friendId, :userId)");
        $stmt->execute(['userId' => $_SESSION['user_id'], 'friendId' => $userId]);

        // Remove friend request
        $stmt = $pdo->prepare("DELETE FROM FriendRequests WHERE RequesterID = :userId AND RequestedID = :friendId");
        $stmt->execute(['userId' => $userId, 'friendId' => $_SESSION['user_id']]);
    } else if ($action === 'reject') {
        // Remove friend request
        $stmt = $pdo->prepare("DELETE FROM FriendRequests WHERE RequesterID = :userId AND RequestedID = :friendId");
        $stmt->execute(['userId' => $userId, 'friendId' => $_SESSION['user_id']]);
    }

    $pdo->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
