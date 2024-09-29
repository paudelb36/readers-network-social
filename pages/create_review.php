<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/config.php';
session_start();

function sendJsonResponse($success, $message, $statusCode = 200)
{
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
function notifyFriends($pdo, $userId, $reviewId, $bookTitle)
{
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

    // Decode JSON input
    $inputData = json_decode(file_get_contents('php://input'), true);
    if (!$inputData) {
        throw new Exception('Invalid JSON input');
    }

    // Log the received input data
    error_log("Received input data: " . print_r($inputData, true));

    $reviewText = trim($inputData['review_text'] ?? '');
    $bookTitle = trim($inputData['book_title'] ?? '');
    $author = trim($inputData['book_author'] ?? '');
    $isbn = trim($inputData['book_isbn'] ?? '');
    $publicationYear = trim($inputData['book_year'] ?? '');
    $genre = trim($inputData['book_genre'] ?? '');
    $image = trim($inputData['downloaded_image'] ?? '');

    // Log the parsed values
    error_log("Parsed values: reviewText: '$reviewText', bookTitle: '$bookTitle', author: '$author'");

    if (empty($reviewText)) {
        throw new Exception('Please enter your review text.');
    }

    if (empty($bookTitle) || empty($author)) {
        throw new Exception('Please ensure book title and author are provided.');
    }



    $pdo->beginTransaction();

    // Check if the book already exists in the database
    $bookExistsQuery = 'SELECT BookID FROM Books WHERE ISBN = :isbn LIMIT 1';
    $checkStmt = $pdo->prepare($bookExistsQuery);
    $checkStmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);
    $checkStmt->execute();
    $bookId = $checkStmt->fetchColumn();

    if (!$bookId) {
        $insertBookStmt = $pdo->prepare('INSERT INTO Books (Title, Author, ISBN, PublicationYear, Genre, Image) VALUES (?, ?, ?, ?, ?, ?)');
        $insertBookStmt->execute([$bookTitle, $author, $isbn, $publicationYear, $genre, $image]);
        $bookId = $pdo->lastInsertId();
    }

    // Insert the review into the Reviews table
    $stmt = $pdo->prepare('INSERT INTO Reviews (UserID, BookID, ReviewText, Title, Author, ISBN, PublicationYear, Genre, CreatedAt, Image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)');
    $stmt->execute([$userId, $bookId, $reviewText, $bookTitle, $author, $isbn, $publicationYear, $genre, $image]);
    $reviewId = $pdo->lastInsertId();

    // Notify friends about the new review
    notifyFriends($pdo, $userId, $reviewId, $bookTitle);

    $pdo->commit();

    sendJsonResponse(true, 'Review submitted successfully and friends notified!');
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Error in create_review.php: " . $e->getMessage());
    sendJsonResponse(false, 'Error: ' . $e->getMessage(), 500);
}
