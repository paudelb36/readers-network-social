<?php
// send_request.php

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

// Current user ID
$currentUserId = $_SESSION['user_id'];

// Check if user is already a friend
$query = "SELECT 1 FROM Friends WHERE (UserID = :userId AND FriendID = :currentUserId) OR (UserID = :currentUserId AND FriendID = :userId)";
$stmt = $pdo->prepare($query);
$stmt->execute(['userId' => $userId, 'currentUserId' => $currentUserId]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Already friends']);
    exit();
}

// Check if a friend request already exists
$query = "SELECT 1 FROM FriendRequests WHERE RequesterID = :currentUserId AND RequestedID = :userId";
$stmt = $pdo->prepare($query);
$stmt->execute(['currentUserId' => $currentUserId, 'userId' => $userId]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Friend request already sent']);
    exit();
}

// Check if the user is sending a request to themselves
if ($currentUserId == $userId) {
    echo json_encode(['success' => false, 'message' => 'You cannot send a friend request to yourself']);
    exit();
}

try {
    // Insert the new friend request
    $stmt = $pdo->prepare("INSERT INTO FriendRequests (RequesterID, RequestedID, Status) VALUES (:requesterId, :requestedId, 'Pending')");
    $stmt->execute(['requesterId' => $currentUserId, 'requestedId' => $userId]);

    // Insert notification
    $notificationContent = htmlspecialchars($_SESSION['username']) . " sent you a friend request.";
    $notificationStmt = $pdo->prepare("INSERT INTO Notifications (Content, IsRead, CreatedAt, ActorID, Type, RecipientID) VALUES (:content, 0, NOW(), :actorId, 'friend_request', :recipientId)");
    $notificationStmt->execute([
        'content' => $notificationContent,
        'actorId' => $currentUserId,
        'recipientId' => $userId
    ]);

    echo json_encode(['success' => true, 'message' => 'Friend request sent successfully']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error sending friend request: ' . $e->getMessage()]);
}

?>
