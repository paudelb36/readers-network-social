<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';
session_start();

function sendJsonResponse($success, $message, $statusCode = 200) {
    $response = array(
        'success' => $success,
        'message' => $message
    );
    ob_clean(); // Clear the output buffer
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode($response);
    exit();
}

// Function to send notifications to friends
function notifyFriends($pdo, $userId, $reviewId, $bookTitle) {
    $friendsQuery = "SELECT FriendID FROM Friends WHERE UserID = ?";
    $friendsStmt = $pdo->prepare($friendsQuery);
    $friendsStmt->execute([$userId]);
    $friends = $friendsStmt->fetchAll(PDO::FETCH_COLUMN);

    $notifyStmt = $pdo->prepare("INSERT INTO Notifications (Content, IsRead, CreatedAt, ActorID, Type, RecipientID) VALUES (?, 0, NOW(), ?, 'review', ?)");

    foreach ($friends as $friendId) {
        $content = "Your friend has posted a new review for the book '$bookTitle'.";
        $notifyStmt->execute([$content, $userId, $friendId]);
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
        throw new Exception('User not logged in');
    }

    $userId = $_SESSION['user_id'];
    $reviewText = trim($_POST['review_text'] ?? '');
    $bookTitle = trim($_POST['book_title'] ?? '');
    $author = trim($_POST['book_author'] ?? '');
    $isbn = trim($_POST['book_isbn'] ?? '');
    $publicationYear = trim($_POST['book_year'] ?? '');
    $genre = trim($_POST['book_genre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = null;

    if (empty($reviewText) || empty($bookTitle) || empty($author)) {
        throw new Exception('Please fill in all required fields.');
    }

    // Handle uploaded image
    if (isset($_FILES['book_image_upload']) && $_FILES['book_image_upload']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['book_image_upload']['tmp_name'];
        $imageName = basename($_FILES['book_image_upload']['name']);
        $uploadDir = '../uploads/reviews/';
        $imagePath = $uploadDir . uniqid() . '-' . $imageName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!move_uploaded_file($imageTmpPath, $imagePath)) {
            throw new Exception('There was an error uploading the image.');
        }

        $image = $imagePath;
    }

    // Check for the downloaded image path
    if (empty($image) && !empty(trim($_POST['downloaded_image'] ?? ''))) {
        $image = trim($_POST['downloaded_image']);
    }

    if (!empty($image) && !file_exists($image)) {
        throw new Exception('The image file does not exist.');
    }

    $pdo->beginTransaction();

    $bookExistsQuery = 'SELECT BookID FROM Books WHERE ISBN = :isbn LIMIT 1';
    $checkStmt = $pdo->prepare($bookExistsQuery);
    $checkStmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);
    $checkStmt->execute();
    $bookId = $checkStmt->fetchColumn();

    if (!$bookId) {
        $insertBookStmt = $pdo->prepare('INSERT INTO Books (Title, Author, ISBN, PublicationYear, Genre, Image, Description) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $insertBookStmt->execute([$bookTitle, $author, $isbn, $publicationYear, $genre, $image, $description]);
        $bookId = $pdo->lastInsertId();
    }

    $stmt = $pdo->prepare('INSERT INTO Reviews (UserID, BookID, ReviewText, Title, Author, ISBN, PublicationYear, Genre, Description, CreatedAt, Image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)');
    $stmt->execute([$userId, $bookId, $reviewText, $bookTitle, $author, $isbn, $publicationYear, $genre, $description, $image]);
    $reviewId = $pdo->lastInsertId();

    notifyFriends($pdo, $userId, $reviewId, $bookTitle);

    $pdo->commit();

    sendJsonResponse(true, 'Review submitted successfully and friends notified!');
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    sendJsonResponse(false, 'Error: ' . $e->getMessage(), 500);
}
?>
