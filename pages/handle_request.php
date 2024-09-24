<?php
include '../includes/config.php'; // Include your database configuration
session_start(); // Start the session

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $requesterId = $_POST['requester_id'];
    $action = $_POST['action'];
    $userId = $_SESSION['user_id'];

    // Prepare the update statement
    $stmt = $pdo->prepare("UPDATE FriendRequests SET Status = ? WHERE RequesterID = ? AND RequestedID = ?");

    if ($action == 'accept') {
        // Update the request status to 'Accepted'
        $stmt->execute(['Accepted', $requesterId, $userId]);

        // Add a new friend relationship in both directions
        $friendStmt = $pdo->prepare("INSERT INTO Friends (UserID, FriendID) VALUES (?, ?), (?, ?)");
        $friendStmt->execute([$userId, $requesterId, $requesterId, $userId]);

    } elseif ($action == 'reject') {
        // Update the request status to 'Rejected'
        $stmt->execute(['Rejected', $requesterId, $userId]);
    }

    // Redirect back to the friend requests page
    header('Location: view_requests.php');
    exit();
} else {
    // If accessed directly without POST data, redirect to the friend requests page
    header('Location: view_requests.php');
    exit();
}
?>