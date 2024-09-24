<?php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user is logged in
    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        header('Location: login.php');
        exit();
    }

    // Retrieve user ID from session
    $userId = $_SESSION['user_id'];
    
    // Retrieve and trim input data
    $reviewText = trim($_POST['review_text']);
    $bookTitle = trim($_POST['book_title']);
    $author = trim($_POST['book_author']);
    $isbn = trim($_POST['book_isbn']);
    $publicationYear = trim($_POST['book_year']);
    $genre = trim($_POST['book_genre']);
    $rating = trim($_POST['rating']);
    $description = trim($_POST['description']); // Assuming this is passed from the form
    $image = null; // Initialize image variable

    // Validate required fields
    if (empty($reviewText) || empty($bookTitle) || empty($author) || empty($rating)) {
        $_SESSION['error_message'] = 'Please fill in all required fields.';
        header('Location: create_review.php');
        exit();
    }

    // Handle uploaded image if exists
    if (isset($_FILES['book_image_upload']) && $_FILES['book_image_upload']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['book_image_upload']['tmp_name'];
        $imageName = basename($_FILES['book_image_upload']['name']);
        $uploadDir = '../uploads/reviews/';
        $imagePath = $uploadDir . uniqid() . '-' . $imageName;

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file
        if (move_uploaded_file($imageTmpPath, $imagePath)) {
            $image = $imagePath; // Use the uploaded image
        } else {
            $_SESSION['error_message'] = "There was an error uploading the image.";
            header('Location: create_review.php');
            exit();
        }
    }

    // Check for the downloaded image path from the AJAX submission
    if (empty($image) && !empty(trim($_POST['downloaded_image']))) {
        $image = trim($_POST['downloaded_image']);
    }

    // Validate the image path if it's provided
    if (!empty($image) && !file_exists($image)) {
        $_SESSION['error_message'] = 'The image file does not exist.';
        header('Location: create_review.php');
        exit();
    }

    try {
        // Begin transaction
        $pdo->beginTransaction();

        // Insert into the Reviews table
        $stmt = $pdo->prepare('INSERT INTO Reviews (UserID, ReviewText, Title, Author, ISBN, PublicationYear, Genre, Description, Rating, CreatedAt, Image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)');
        $stmt->execute([$userId, $reviewText, $bookTitle, $author, $isbn, $publicationYear, $genre, $description, $rating, $image]);

        // Commit the transaction
        $pdo->commit();

        // Redirect to the index page with a success message
        $_SESSION['success_message'] = 'Review submitted successfully!';
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        // Roll back the transaction in case of an error
        $pdo->rollBack();
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
        header('Location: create_review.php');
        exit();
    }
}
?>
