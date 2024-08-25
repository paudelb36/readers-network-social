<?php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: login.php');
        exit();
    }

    $userId = $_SESSION['user_id'];
    $reviewText = trim($_POST['review_text']);
    $bookTitle = trim($_POST['book_title']);
    $author = trim($_POST['book_author']);
    $isbn = trim($_POST['book_isbn']);
    $publicationYear = trim($_POST['book_year']);
    $genre = trim($_POST['book_genre']);
    $description = trim($_POST['description']);
    $rating = trim($_POST['rating']);
    $image = ''; // Initialize image path

    // Basic validation to ensure required fields are not empty
    if (empty($reviewText) || empty($bookTitle) || empty($author) || empty($rating)) {
        $_SESSION['error_message'] = 'Please fill in all required fields.';
        header('Location: create_review.php');
        exit();
    }

    // Handle image upload
    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['book_image']['tmp_name'];
        $imageName = basename($_FILES['book_image']['name']);
        $uploadDir = '../uploads/reviews/'; // Set the upload directory
        $imagePath = $uploadDir . uniqid() . '-' . $imageName; // Generate a unique filename

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the upload directory
        if (move_uploaded_file($imageTmpPath, $imagePath)) {
            $image = $imagePath; // Set the image path to store in the database
        } else {
            $_SESSION['error_message'] = "There was an error uploading the image.";
            header('Location: create_review.php');
            exit();
        }
    }

    // Insert the review into the Reviews table
    try {
        $stmt = $pdo->prepare('INSERT INTO reviews (UserID, ReviewText, Title, Author, ISBN, PublicationYear, Genre, Description, Rating, CreatedAt, Image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)');
        $stmt->execute([$userId, $reviewText, $bookTitle, $author, $isbn, $publicationYear, $genre, $description, $rating, $image]);
        header('Location: index.php');
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
        header('Location: create_review.php');
        exit();
    }
}
?>
