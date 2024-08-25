<?php
include '../includes/config.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['book_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$userID = $_SESSION['user_id'];
$bookID = $_POST['book_id'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT * FROM readinglist WHERE UserID = ? AND BookID = ?");
    $stmt->execute([$userID, $bookID]);
    $existingEntry = $stmt->fetch();

    if ($existingEntry) {
        $stmt = $pdo->prepare("DELETE FROM readinglist WHERE UserID = ? AND BookID = ?");
        $stmt->execute([$userID, $bookID]);
        $message = "Book removed from your reading list";
    } else {
        $stmt = $pdo->prepare("INSERT INTO readinglist (UserID, BookID, Status, DateAdded) VALUES (?, ?, 'Want to Read', NOW())");
        $stmt->execute([$userID, $bookID]);
        $message = "Book added to your reading list";
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => $message]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}