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

// Check if user is already a friend or request already exists
$query = "SELECT 1 FROM Friends WHERE (UserID = :userId AND FriendID = :currentUserId) OR (UserID = :currentUserId AND FriendID = :userId)";
$stmt = $pdo->prepare($query);
$stmt->execute(['userId' => $userId, 'currentUserId' => $_SESSION['user_id']]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Already friends']);
    exit();
}

$query = "SELECT 1 FROM FriendRequests WHERE RequesterID = :userId AND RequestedID = :currentUserId";
$stmt = $pdo->prepare($query);
$stmt->execute(['userId' => $userId, 'currentUserId' => $_SESSION['user_id']]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Friend request already sent']);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO FriendRequests (RequesterID, RequestedID, Status) VALUES (:userId, :currentUserId, 'Pending')");
    $stmt->execute(['userId' => $userId, 'currentUserId' => $_SESSION['user_id']]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
