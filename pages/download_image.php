<?php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the posted JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['imageUrl']) || !isset($data['title'])) {
        echo json_encode(['success' => false, 'message' => 'Missing image URL or title.']);
        exit();
    }

    $imageUrl = filter_var($data['imageUrl'], FILTER_VALIDATE_URL);
    if ($imageUrl === false) {
        echo json_encode(['success' => false, 'message' => 'Invalid image URL.']);
        exit();
    }

    $title = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['title']); // Sanitize title for filename
    $uploadDir = '../uploads/downloads/';
    $imageName = uniqid() . '-' . $title . '.jpg'; // Create a unique image name
    $imagePath = $uploadDir . $imageName;

    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Download the image
    $imageData = @file_get_contents($imageUrl);
    if ($imageData === false) {
        echo json_encode(['success' => false, 'message' => 'Could not download image.']);
        exit();
    }

    // Save the image to the server
    if (file_put_contents($imagePath, $imageData) === false) {
        echo json_encode(['success' => false, 'message' => 'Could not save image.']);
        exit();
    }

    echo json_encode(['success' => true, 'imagePath' => $imagePath]);
    exit();
}
?>