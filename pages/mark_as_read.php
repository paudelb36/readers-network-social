<?php
include '../includes/config.php';
session_start();

$userId = $_SESSION['user_id'];

// Mark all notifications as read for the logged-in user
$stmt = $pdo->prepare("UPDATE Notifications SET IsRead = 1 WHERE RecipientID = :userId");
$stmt->execute(['userId' => $userId]);

header('Location: notification.php');
exit;
