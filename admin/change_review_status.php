<?php
// change_review_status.php
include('../includes/config.php');

// Set the time zone to Kathmandu
date_default_timezone_set('Asia/Kathmandu');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['action'])) {
    $reviewId = $_POST['review_id'];
    $action = $_POST['action'];

    switch ($action) {
        case 'hide':
            // Set review visibility to hidden
            $query = "UPDATE Reviews SET IsVisible = 0 WHERE ReviewID = ?";
            break;

        case 'unhide':
            // Set review visibility to visible
            $query = "UPDATE Reviews SET IsVisible = 1 WHERE ReviewID = ?";
            break;

        case 'delete':
            // Delete the review entirely
            $query = "DELETE FROM Reviews WHERE ReviewID = ?";
            break;

        default:
            header("Location: content_filtering.php");
            exit();
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([$reviewId]);
}

header("Location: content_filtering.php");
exit();
?>
