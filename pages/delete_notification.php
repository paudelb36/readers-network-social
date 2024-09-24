<?php
include '../includes/config.php';
session_start();

$notificationId = $_POST['notification_id'];

// Delete the notification
$stmt = $pdo->prepare("DELETE FROM Notifications WHERE NotificationID = :notificationId AND RecipientID = :userId");
$stmt->execute(['notificationId' => $notificationId, 'userId' => $_SESSION['user_id']]);

header('Location: notification.php');
exit;
