<?php
// includes/delete_book.php

session_start();
include 'config.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Check if the required parameters are set
if (isset($_GET['book_id']) && isset($_GET['user_id'])) {
    $bookId = intval($_GET['book_id']);
    $userId = intval($_GET['user_id']);

    // Ensure that the logged-in user is the one trying to delete the book
    if ($userId === $_SESSION['user_id']) {
        // Prepare and execute the delete query
        $query = "DELETE FROM readinglist WHERE BookID = ? AND UserID = ?";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $bookId, PDO::PARAM_INT);
        $stmt->bindParam(2, $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redirect back to bookshelf.php with a success message
            header('Location: ../bookshelf.php?user_id=' . $userId . '&status=all&message=Book deleted successfully');
            exit();
        } else {
            echo "Failed to delete the book.";
        }
    } else {
        echo "Unauthorized action.";
    }
} else {
    echo "Invalid request.";
}
?>
