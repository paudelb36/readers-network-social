<?php
// change_report_status.php
include('../includes/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id']) && isset($_POST['status'])) {
    $reportId = $_POST['report_id'];
    $newStatus = $_POST['status'];

    $updateQuery = "UPDATE Reports SET Status = ? WHERE ReportID = ?";
    $updateStmt = $pdo->prepare($updateQuery);
    $updateStmt->execute([$newStatus, $reportId]);

    // Check if we're coming from view_user.php
    if (isset($_POST['user_id'])) {
        header("Location: view_user.php?id=" . $_POST['user_id']);
    } else {
        header("Location: reports.php");
    }
    exit();
} else {
    // If accessed directly without proper POST data, redirect to reports page
    header("Location: reports.php");
    exit();
}
?>