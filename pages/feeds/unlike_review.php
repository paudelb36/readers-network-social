<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reviewId = $_POST['review_id'];
    $userId = $_SESSION['UserID'];

    // Delete the like record for the user and review ID
    $query = "DELETE FROM Likes WHERE ReviewID = :reviewId AND UserID = :userId";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':reviewId', $reviewId, PDO::PARAM_INT);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to unlike review']);
    }
}
?>
