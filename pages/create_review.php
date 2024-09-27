<?php
require_once '../includes/config.php';
session_start();

// Updated function to send notifications to friends
function notifyFriends($pdo, $userId, $reviewId, $bookTitle) {
    // Get friends of the user
    $friendsQuery = "SELECT FriendID FROM Friends WHERE UserID = ?";
    $friendsStmt = $pdo->prepare($friendsQuery);
    $friendsStmt->execute([$userId]);
    $friends = $friendsStmt->fetchAll(PDO::FETCH_COLUMN);

    // Prepare notification insertion statement
    $notifyStmt = $pdo->prepare("INSERT INTO Notifications (Content, IsRead, CreatedAt, ActorID, Type, RecipientID) VALUES (?, 0, NOW(), ?, 'review', ?)");

    // Send notification to each friend
    foreach ($friends as $friendId) {
        $content = "Your friend has posted a new review for the book '$bookTitle'.";
        $notifyStmt->execute([$content, $userId, $friendId]);
    }
}


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
    $description = trim($_POST['description']); // Assuming this is passed from the form
    $image = null; // Initialize image variable

    // Validate required fields
    if (empty($reviewText) || empty($bookTitle) || empty($author)) {
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

        // Check if the book exists
        $bookExistsQuery = 'SELECT BookID FROM Books WHERE ISBN = :isbn LIMIT 1';
        $checkStmt = $pdo->prepare($bookExistsQuery);
        $checkStmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);
        $checkStmt->execute();
        $bookId = $checkStmt->fetchColumn();

        // If the book does not exist, insert it
        if (!$bookId) {
            $insertBookStmt = $pdo->prepare('INSERT INTO Books (Title, Author, ISBN, PublicationYear, Genre, Image, Description) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $insertBookStmt->execute([$bookTitle, $author, $isbn, $publicationYear, $genre, $image, $description]);
            $bookId = $pdo->lastInsertId();
        }

        // Insert into the Reviews table
        $stmt = $pdo->prepare('INSERT INTO Reviews (UserID, BookID, ReviewText, Title, Author, ISBN, PublicationYear, Genre, Description, CreatedAt, Image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)');
        $stmt->execute([$userId, $bookId, $reviewText, $bookTitle, $author, $isbn, $publicationYear, $genre, $description, $image]);
        $reviewId = $pdo->lastInsertId();

        // Notify friends about the new review
        notifyFriends($pdo, $userId, $reviewId, $bookTitle);

        // Commit the transaction
        $pdo->commit();

        // Redirect to the index page with a success message
        $_SESSION['success_message'] = 'Review submitted successfully and friends notified!';
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
