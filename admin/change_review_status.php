<?php
include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review_id'], $_POST['action'])) {
    $reviewId = $_POST['review_id'];
    $action = $_POST['action'];

    switch ($action) {
        case 'hide':
            $query = "UPDATE Reviews SET Status = 'hidden' WHERE ReviewID = ?";
            break;
        case 'unhide':
            $query = "UPDATE Reviews SET Status = 'visible' WHERE ReviewID = ?";
            break;
        case 'delete':
            $query = "DELETE FROM Reviews WHERE ReviewID = ?";
            break;
        default:
            header('Location: content_filtering.php');
            exit();
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([$reviewId]);

    header("Location: view_review.php?id=$reviewId");
    exit();
}
?>
