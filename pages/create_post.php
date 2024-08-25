<?php
// create_post.php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: login.php');
        exit();
    }

    $userId = $_SESSION['user_id'];
    $content = trim($_POST['post_content']); // Sanitize and trim the input to remove extra spaces
    $image = ''; // Initialize image path

    // Validate that the content is not empty
    if (empty($content)) {
        // If the content is empty, redirect back with an error message
        $_SESSION['error_message'] = "Post content cannot be empty.";
        header('Location: index.php');
        exit();
    }

    // Handle image upload
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['post_image']['tmp_name'];
        $imageName = basename($_FILES['post_image']['name']);
        $uploadDir = '../uploads/'; // Set the upload directory
        $imagePath = $uploadDir . uniqid() . '-' . $imageName; // Generate a unique filename

        // Move the uploaded file to the upload directory
        if (move_uploaded_file($imageTmpPath, $imagePath)) {
            $image = $imagePath; // Set the image path to store in the database
        } else {
            $_SESSION['error_message'] = "There was an error uploading the image.";
            header('Location: index.php');
            exit();
        }
    }

    // Insert the post into the Posts table
    $stmt = $pdo->prepare('INSERT INTO Posts (UserID, Content, CreatedAt, Image) VALUES (?, ?, NOW(), ?)');
    $stmt->execute([$userId, $content, $image]);

    header('Location: index.php');
    exit();
}
?>
