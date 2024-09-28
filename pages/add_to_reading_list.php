<?php
require_once '../includes/config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => "You must be logged in to manage your reading list."]);
    exit();
}

$bookId = isset($_POST['book_id']) ? intval($_POST['book_id']) : null;
$status = isset($_POST['status']) ? $_POST['status'] : null;
$userId = $_SESSION['user_id'];

if ($bookId && $status) {
    try {
        $pdo->beginTransaction();

        $checkSql = "SELECT Status FROM readinglist WHERE UserID = :user_id AND BookID = :book_id";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':user_id' => $userId, ':book_id' => $bookId]);
        $existingStatus = $checkStmt->fetchColumn();

        if ($existingStatus) {
            if ($existingStatus === $status) {
                $message = "This book is already in your list.";
            } else {
                $updateSql = "UPDATE readinglist SET Status = :status WHERE UserID = :user_id AND BookID = :book_id";
                $updateStmt = $pdo->prepare($updateSql);
                $updateStmt->execute([':status' => $status, ':user_id' => $userId, ':book_id' => $bookId]);
                $message = "Book status updated to '{$status}'.";
            }
        } else {
            $insertSql = "INSERT INTO readinglist (UserID, BookID, Status) VALUES (:user_id, :book_id, :status)";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([':user_id' => $userId, ':book_id' => $bookId, ':status' => $status]);
            $message = "Book added to your list.";
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => $message]);
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error managing reading list: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => "Error managing reading list."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Invalid book or status."]);
}
?>