<?php
session_start(); // Start the session
include '../includes/config.php'; // Adjust the path based on your file structure

// Check if user is logged in using 'user_id'
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Get the user_id from the URL parameter or default to logged-in user
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : $_SESSION['user_id'];

// Fetch user info from the users table
$queryUser = "SELECT UserID, FirstName, LastName, Username, ProfilePicture, Bio, Location, FavoriteGenres 
              FROM users 
              WHERE UserID = ?";
$stmtUser = $pdo->prepare($queryUser);
$stmtUser->bindParam(1, $userId, PDO::PARAM_INT);
$stmtUser->execute();

if ($stmtUser->rowCount() === 0) {
    // Handle case where user is not found
    echo "User not found.";
    exit();
}

$userInfo = $stmtUser->fetch(PDO::FETCH_ASSOC);

// Fetch user posts/reviews from the reviews table
$queryReviews = "SELECT Title, ReviewText, Rating, CreatedAt, Image 
                 FROM reviews 
                 WHERE UserID = ?
                 ORDER BY CreatedAt DESC";
$stmtReviews = $pdo->prepare($queryReviews);
$stmtReviews->bindParam(1, $userId, PDO::PARAM_INT);
$stmtReviews->execute();
$resultReviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's bookshelf (reading list) from the readinglist table
$queryReadingList = "SELECT books.Title, books.Author, books.Image, readinglist.Status, readinglist.DateAdded 
                     FROM readinglist 
                     JOIN books ON readinglist.BookID = books.BookID 
                     WHERE readinglist.UserID = ?";
$stmtReadingList = $pdo->prepare($queryReadingList);
$stmtReadingList->bindParam(1, $userId, PDO::PARAM_INT);
$stmtReadingList->execute();
$resultReadingList = $stmtReadingList->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($userInfo['Username']); ?>'s Profile</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CSS -->
</head>

<body class="bg-gray-100">

    <!-- Profile Section -->
    <div class="max-w-7xl mx-auto mt-10 p-6">
        <!-- Top Section: Profile Picture, Name, Username -->
        <div class="flex items-center space-x-4 mb-6 mt-6">
            <!-- Display Profile Picture -->
            <?php if (!empty($userInfo['ProfilePicture'])): ?>
                <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($userInfo['ProfilePicture']); ?>" alt="Profile Picture" class="w-24 h-24 rounded-full object-cover mr-4">
            <?php else: ?>
                <img src="../assets/images/default-profile.png" alt="Default Profile Picture" class="w-24 h-24 rounded-full object-cover mr-4">
            <?php endif; ?>

            <div>
                <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($userInfo['FirstName'] . ' ' . $userInfo['LastName']); ?></h1>
                <p class="text-gray-600">@<?php echo htmlspecialchars($userInfo['Username']); ?></p>

                <!-- Show Edit Profile button only for logged-in user -->
                <?php if ($_SESSION['user_id'] == $userInfo['UserID']): ?>
                    <a href="edit_profile.php" class="mt-2 inline-block px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Edit Profile</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Bottom Section: About, Posts, and Bookshelves -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Column: About Section -->
            <div class="col-span-1 bg-white p-6 rounded-lg shadow-lg transition-transform transform hover:scale-105">
                <h2 class="text-2xl font-semibold mb-4 text-blue-600">About Me</h2>

                <p class="mb-2 text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($userInfo['Bio'])); ?></p>
                <p class="text-gray-600"><strong>Location:</strong> <?php echo htmlspecialchars($userInfo['Location']); ?></p>
                <p class="text-gray-600"><strong>Favorite Genres:</strong> <?php echo htmlspecialchars($userInfo['FavoriteGenres']); ?></p>
            </div>

            <!-- Middle Column: User Posts/Reviews -->
            <div class="col-span-1 bg-white p-4 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold mb-4">Posts</h2>
                <?php if (count($resultReviews) > 0): ?>
                    <?php foreach ($resultReviews as $review): ?>
                        <article class="mb-4 p-6 rounded-xl bg-gray-50 shadow-md">
                            <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($review['Title']); ?></h3>
                            <!-- Book Cover Image -->
                            <?php if (!empty($review['Image'])): ?>
                                <img src="<?php echo htmlspecialchars($review['Image']); ?>" alt="Book Cover" class="mt-2 w-36 h-auto rounded-lg shadow-sm">
                            <?php endif; ?>
                            <p class="mt-2"><?php echo nl2br(htmlspecialchars($review['ReviewText'])); ?></p>
                            <p class="text-sm text-gray-500">Posted on: <?php echo htmlspecialchars((new DateTime($review['CreatedAt']))->format('F j, Y')); ?></p>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">No posts yet.</p>
                <?php endif; ?>
            </div>

            <!-- Right Column: Bookshelves -->
            <div class="col-span-1 bg-white p-4 rounded-lg shadow-lg">
                <h2 class="text-xl font-semibold mb-4">Bookshelves</h2>
                <?php if (count($resultReadingList) > 0): ?>
                    <?php foreach ($resultReadingList as $book): ?>
                        <div class="flex items-center space-x-4 mb-4">
                            <img class="w-12 h-12 object-cover rounded" src="<?php echo htmlspecialchars($book['Image']); ?>" alt="Book Cover">
                            <div>
                                <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($book['Title']); ?></h3>
                                <p class="text-gray-600"><?php echo htmlspecialchars($book['Author']); ?></p>
                                <p class="text-sm text-gray-500">Status: <?php echo htmlspecialchars($book['Status']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">No books in your shelf yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>
