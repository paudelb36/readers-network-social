<?php
// includes/delete_post.php

session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$postId = $_POST['post_id'];
$userId = $_SESSION['user_id'];

try {
    // First, check if the post belongs to the current user
    $stmt = $pdo->prepare("SELECT UserID FROM Reviews WHERE ReviewID = ?");
    $stmt->execute([$postId]);
    $postUserId = $stmt->fetchColumn();

    if ($postUserId != $userId) {
        echo json_encode(['success' => false, 'message' => 'You do not have permission to delete this post']);
        exit;
    }

    // If the post belongs to the user, proceed with deletion
    $stmt = $pdo->prepare("DELETE FROM Reviews WHERE ReviewID = ?");
    $stmt->execute([$postId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Post not found or already deleted']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}