<?php
// update_status.php
session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Check if all required parameters are provided
if (!isset($_GET['book_id']) || !isset($_GET['user_id']) || !isset($_GET['status'])) {
    header('Location: ../bookshelf.php?message=missing_parameters');
    exit();
}

$bookId = intval($_GET['book_id']);
$userId = intval($_GET['user_id']);
$newStatus = htmlspecialchars($_GET['status']); // Sanitize status input

// Define the valid statuses
$validStatuses = ['Read', 'Currently Reading', 'Want to Read'];

// Check if the provided status is valid
if (!in_array($newStatus, $validStatuses)) {
    header('Location: ../bookshelf.php?message=invalid_status');
    exit();
}

// Update the book status in the reading list
$query = "UPDATE readinglist SET Status = ? WHERE BookID = ? AND UserID = ?";
$stmt = $pdo->prepare($query);
$result = $stmt->execute([$newStatus, $bookId, $userId]);

// Redirect to bookshelf page with appropriate message
if ($result) {
    header("Location: bookshelf.php?user_id=$userId&status=all&message=status_updated");
} else {
    header("Location: bookshelf.php?user_id=$userId&status=all&message=update_failed");
}
exit();
