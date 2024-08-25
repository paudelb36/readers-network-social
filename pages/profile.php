<?php
// profile.php

// Include your database connection file
require_once '../includes/config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

// Fetch the user ID of the logged-in user
$loggedInUserId = $_SESSION['user_id'];

// Get the user_id from the query parameter
if (isset($_GET['user_id']) && is_numeric($_GET['user_id'])) {
    $profileUserId = intval($_GET['user_id']);
} else {
    header('Location: index.php'); // Redirect if no user_id is provided
    exit();
}

// Fetch profile user data
$stmt = $pdo->prepare('SELECT Username, FirstName, LastName, Email, ProfilePicture, Bio, Location, FavoriteGenres, DateOfBirth, JoinDate FROM Users WHERE UserID = ?');
$stmt->execute([$profileUserId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

// Fetch posts and reviews
$sql = "
    (SELECT 'post' AS type, p.PostID AS id, NULL AS BookID, NULL AS Title, NULL AS Author, NULL AS Genre, NULL AS PublicationYear, p.Content, p.Image, p.CreatedAt, u.Username,u.FirstName, u.LastName, u.ProfilePicture
    FROM Posts p
    JOIN Users u ON p.UserID = u.UserID
    WHERE p.UserID = ?)
    UNION
    (SELECT 'review' AS type, r.ReviewID AS id, r.BookID, r.Title, r.Author, r.Genre, r.PublicationYear, r.ReviewText AS Content, r.Image, r.CreatedAt,u.Username, u.FirstName, u.LastName, u.ProfilePicture
    FROM Reviews r
    JOIN Users u ON r.UserID = u.UserID
    WHERE r.UserID = ?)
    ORDER BY CreatedAt DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$profileUserId, $profileUserId]);

$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's bookshelves
$bookshelfStmt = $pdo->prepare('
    SELECT b.BookID, b.Title, b.Author, b.Genre, b.PublicationYear
    FROM ReadingList r
    JOIN Books b ON r.BookID = b.BookID
    WHERE r.UserID = ?
');
$bookshelfStmt->execute([$profileUserId]);
$bookshelves = $bookshelfStmt->fetchAll(PDO::FETCH_ASSOC);

// Determine if the profile is being viewed by the owner
$isOwner = ($loggedInUserId == $profileUserId);


// Include the header (optional)
include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Readers Network</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"> <!-- Tailwind CSS CDN -->
    <link rel="stylesheet" href="assets/css/main.css"> <!-- Additional custom CSS -->
</head>
<body class="bg-gray-100">

<!-- Top Profile Section -->
<div class="container mx-auto mt-16">
    <div class="bg-white shadow-md rounded-lg p-6 max-w-3xl mx-auto">
        <div class="flex items-center">
            <?php if (!empty($user['ProfilePicture'])): ?>
                <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($user['ProfilePicture']); ?>" alt="Profile Picture" class="w-24 h-24 rounded-full object-cover mr-4">
            <?php else: ?>
                <img src="../assets/images/default-profile.png" alt="Default Profile Picture" class="w-24 h-24 rounded-full object-cover mr-4">
            <?php endif; ?>
            <div class="ml-6">
                <h1 class="text-3xl font-semibold"><?php echo htmlspecialchars($user['FirstName']); ?> <?php echo htmlspecialchars($user['LastName']); ?></h1>
                <p class="text-gray-600">@<?php echo htmlspecialchars($user['Username']); ?></p>
            </div>
        </div>
        
        <div class="mt-6">
            <h2 class="text-lg font-semibold">Bio</h2>
            <p class="text-gray-700 mt-2"><?php echo htmlspecialchars($user['Bio']); ?></p>
        </div>
        
        <?php if ($isOwner): ?>
        <div class="mt-8">
            <a href="edit_profile.php" class="inline-block px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300">
                Edit Profile
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Main Content Section -->
<div class="container mx-auto px-4 mt-8 flex space-x-4">

    <!-- Left Section: User Info -->
    <div class="w-1/4 bg-white p-6 shadow-md rounded-lg">
        <h2 class="text-xl font-semibold mb-4">About Me</h2>
        <div class="mt-6">
            <h3 class="font-semibold text-gray-700">Location</h3>
            <p class="text-gray-600"><?php echo htmlspecialchars($user['Location']); ?></p>
        </div>
        <div class="mt-4">
            <h3 class="font-semibold text-gray-700">Joined</h3>
            <p class="text-gray-600"><?php echo date('F Y', strtotime($user['JoinDate'])); ?></p>
        </div>
        <div class="mt-4">
            <h3 class="font-semibold text-gray-700">Favorite Genres</h3>
            <p class="text-gray-600"><?php echo htmlspecialchars($user['FavoriteGenres']); ?></p>
        </div>
        <div class="mt-4">
            <h3 class="font-semibold text-gray-700">Date of Birth</h3>
            <p class="text-gray-600"><?php echo htmlspecialchars(date('F j, Y', strtotime($user['DateOfBirth']))); ?></p>
        </div>
    </div>

    <!-- Middle Section: User Feed -->
    <div class="w-1/2 bg-white p-6 shadow-md rounded-lg">
        <h2 class="text-xl font-semibold mb-4">Posts</h2>

        <!-- Display user posts and reviews -->
        <?php foreach ($posts as $post): ?>
        <article class="bg-gray-50 p-4 rounded-lg mb-4">
            <div class="flex pb-6 items-center justify-between">
                <div class="flex">
                    <a class="inline-block mr-4" href="#">
                    <a class="inline-block mr-4" href="#">
                    <!-- Ensure the src attribute correctly points to the profile picture path -->
                    <img class="rounded-full max-w-none w-12 h-12" src="../uploads/profile-pictures/<?php echo htmlspecialchars($post['ProfilePicture']); ?>" alt="Profile Picture" />
                </a>
                    </a>
                    <div class="flex flex-col">
                        <div>
                        <a class="inline-block text-lg font-bold dark:text-white" href="#">
                            <?php echo htmlspecialchars($post['FirstName']); ?> <?php echo htmlspecialchars($post['LastName']); ?>
                        </a>                        </div>
                        <div class="text-slate-500 dark:text-slate-300">
                            <?php
                            $createdAt = new DateTime($post['CreatedAt']);
                            $formattedDate = $createdAt->format('F j, Y');
                            echo htmlspecialchars($formattedDate);
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($post['type'] === 'review'): ?>
                <!-- Display review details -->
                <h2 class="text-2xl font-extrabold dark:text-white mb-2"><?php echo htmlspecialchars($post['Title'] ?? 'N/A'); ?> by <?php echo htmlspecialchars($post['Author'] ?? 'N/A'); ?></h2>
                <p class="text-lg font-semibold dark:text-slate-200 mb-2"><?php echo htmlspecialchars($post['Genre'] ?? 'N/A'); ?> | Published in <?php echo htmlspecialchars($post['PublicationYear'] ?? 'N/A'); ?></p>
                <p class="dark:text-slate-200 mb-4"><?php echo htmlspecialchars($post['Content']); ?></p>
            <?php else: ?>
                <!-- Display normal post content -->
                <h2 class="text-3xl font-extrabold dark:text-white mb-2"><?php echo htmlspecialchars($post['Content']); ?></h2>
            <?php endif; ?>

            <?php if ($post['Image']): ?>
                <div class="py-4">
                    <img class="max-w-full rounded-lg" src="<?php echo htmlspecialchars($post['Image']); ?>" alt="Post Image" />
                </div>
            <?php endif; ?>

            <!-- Like functionality (Placeholder) -->
            <div class="mt-4 flex items-center space-x-4">
                <a href="#" class="flex items-center space-x-2 text-blue-500 hover:underline">
                    <span class="inline-block w-6 h-6">
                        <!-- SVG for Like Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                        </svg>
                    </span>
                    <span>Like</span>
                </a>
                <a href="#" class="flex items-center space-x-2 text-blue-500 hover:underline">
                    <span class="inline-block w-6 h-6">
                        <!-- SVG for Comment Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6">
                            <path d="M21 15c0 2.67-3.33 6-9 6S3 17.67 3 15c0-2.67 3.33-6 9-6s9 3.33 9 6zm0-7c0 2.67-3.33 6-9 6S3 10.67 3 8c0-2.67 3.33-6 9-6s9 3.33 9 6z" />
                        </svg>
                    </span>
                    <span>Comment</span>
                </a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

     <!-- Right Section: User's Bookshelves -->
     <div class="w-1/4 bg-white p-6 shadow-md rounded-lg">
        <h2 class="text-xl font-semibold mb-4">My Bookshelves</h2>
        <?php if (empty($bookshelves)): ?>
            <p class="text-gray-500">No books in the bookshelf.</p>
        <?php else: ?>
            <ul class="space-y-4">
                <?php foreach ($bookshelves as $book): ?>
                    <li class="bg-gray-50 p-4 rounded-lg shadow-sm">
                        <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($book['Title']); ?></h3>
                        <p class="text-gray-600"><?php echo htmlspecialchars($book['Author']); ?></p>
                        <p class="text-gray-500"><?php echo htmlspecialchars($book['Genre']); ?> | Published in <?php echo htmlspecialchars($book['PublicationYear']); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>



</body>
</html>
