<?php
include '../includes/config.php';

// Start the session if it hasn't been started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

// If the user is not logged in, redirect to the login page
if (!$loggedIn) {
    header('Location: login.php');
    exit();
}

// Retrieve the search query and type from the GET parameters
$query = isset($_GET['query']) ? trim($_GET['query']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'all';

// Initialize empty results arrays
$bookResults = [];
$userResults = [];

if (!empty($query)) {
    switch ($type) {
        case 'users':
            // Search for users based on the query
            $stmt = $pdo->prepare("SELECT UserID, FirstName, LastName, ProfilePicture FROM users WHERE FirstName LIKE :query OR LastName LIKE :query");
            $stmt->execute(['query' => "%$query%"]);
            $userResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'books':
            // Search for books based on the query
            $stmt = $pdo->prepare("SELECT BookID, Title, Author, ISBN, PublicationYear, Genre, Image, Description FROM books WHERE Title LIKE :query OR Author LIKE :query OR ISBN LIKE :query OR Genre LIKE :query");
            $stmt->execute(['query' => "%$query%"]);
            $bookResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;

        case 'all':
        default:
            // Search for books based on the query
            $stmt = $pdo->prepare("SELECT BookID, Title, Author, ISBN, PublicationYear, Genre, Image, Description, 'book' as type FROM books WHERE Title LIKE :query OR Author LIKE :query OR ISBN LIKE :query OR Genre LIKE :query");
            $stmt->execute(['query' => "%$query%"]);
            $bookResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Search for users based on the query
            $stmt = $pdo->prepare("SELECT UserID, FirstName, LastName, ProfilePicture, 'user' as type FROM users WHERE FirstName LIKE :query OR LastName LIKE :query");
            $stmt->execute(['query' => "%$query%"]);
            $userResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
    }
}

// Combine book and user results if type is 'all'
$results = ($type === 'all') ? array_merge($bookResults, $userResults) : ($type === 'books' ? $bookResults : $userResults);

/**
 * Function to get the image path based on the availability of the image in different directories
 */
function getImagePath($imageName) {
    if (empty($imageName)) {
        return "../uploads/book-images/default-book.png";
    }

    $basePathDownloads = "../uploads/downloads/";
    $basePathReviews = "../uploads/reviews/";

    // Check if the image is in the downloads directory
    $imagePath = $basePathDownloads . basename($imageName);
    if (file_exists($imagePath)) {
        return $imagePath;
    }

    // Check if the image is in the reviews directory
    $imagePath = $basePathReviews . basename($imageName);
    if (file_exists($imagePath)) {
        return $imagePath;
    }

    // If the image is not found in both directories, use a default image
    return "../uploads/book-images/default-book.png";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Readers Network</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>

    <div class="container mx-auto mt-20 p-4">
        <h1 class="text-2xl font-bold mb-4">Search Results for "<?= htmlspecialchars($query) ?>"</h1>

        <?php if (empty($results)): ?>
            <p class="text-gray-600">No results found.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($results as $result): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="book-cover-container h-64 w-full bg-gray-100 flex justify-center items-center">
                            <?php
                            // Determine whether it's a book or user result, and set the image path accordingly
                            if (isset($result['BookID'])) {
                                $imagePath = getImagePath($result['Image']);
                            } else {
                                // Use a default profile picture or the user's actual profile picture
                                $imagePath = !empty($result['ProfilePicture']) ? "../uploads/profile-pictures/" . htmlspecialchars($result['ProfilePicture']) : "../uploads/profile-pictures/default-profile.png";
                            }
                            ?>
                            <!-- Smaller and centered image -->
                            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($result['Title'] ?? ($result['FirstName'] . ' ' . $result['LastName'])) ?> Image" 
                                 class="w-32 h-auto object-contain mx-auto rounded shadow-lg">
                        </div>
                        <div class="p-4">
                            <?php if (isset($result['BookID'])): ?>
                                <!-- Book specific content -->
                                <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($result['Title'] ?? 'No Title Available') ?></h2>
                                <p class="text-gray-600 mb-2">by <?= htmlspecialchars($result['Author'] ?? 'Unknown Author') ?></p>
                                <p class="text-sm text-gray-500">ISBN: <?= htmlspecialchars($result['ISBN'] ?? 'N/A') ?></p>
                                <p class="text-sm text-gray-500">Genre: <?= htmlspecialchars($result['Genre'] ?? 'Unknown Genre') ?></p>
                                <p class="text-sm text-gray-500 mb-2">Published: <?= htmlspecialchars($result['PublicationYear'] ?? 'Unknown Year') ?></p>
                                <p class="text-sm text-gray-700 mb-4 line-clamp-3"><?= nl2br(htmlspecialchars($result['Description'] ?? 'No description available.')) ?></p>
                                <a href="book_details.php?book_id=<?= $result['BookID'] ?>" class="text-blue-500 hover:underline">View Book Details</a>
                            <?php else: ?>
                                <!-- User specific content -->
                                <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($result['FirstName'] . ' ' . $result['LastName']) ?></h2>
                                <a href="profile.php?user_id=<?= $result['UserID'] ?>" class="text-blue-500 hover:underline">View Profile</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
