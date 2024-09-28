<?php
// profile.php
session_start(); // Start the session
include '../includes/config.php'; // Adjust the path based on your file structure

// Check if user is logged in using 'user_id'
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
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

// Fetch user posts/reviews from the reviews table and get genres from the books table
$queryReviews = "SELECT r.ReviewID, r.Title, r.ReviewText, r.CreatedAt, r.Image, r.Author, b.Genre 
                 FROM reviews r 
                 JOIN books b ON r.BookID = b.BookID 
                 WHERE r.UserID = ? 
                 ORDER BY r.CreatedAt DESC";
$stmtReviews = $pdo->prepare($queryReviews);
$stmtReviews->bindParam(1, $userId, PDO::PARAM_INT);
$stmtReviews->execute();
$resultReviews = $stmtReviews->fetchAll(PDO::FETCH_ASSOC);



// Fetch user's bookshelf counts
$queryBookshelfCounts = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN Status = 'Read' THEN 1 ELSE 0 END) as read_count,
    SUM(CASE WHEN Status = 'Currently Reading' THEN 1 ELSE 0 END) as currently_reading_count,
    SUM(CASE WHEN Status = 'Want to Read' THEN 1 ELSE 0 END) as want_to_read_count
    FROM readinglist 
    WHERE UserID = ?";
$stmtBookshelfCounts = $pdo->prepare($queryBookshelfCounts);
$stmtBookshelfCounts->bindParam(1, $userId, PDO::PARAM_INT);
$stmtBookshelfCounts->execute();
$bookshelfCounts = $stmtBookshelfCounts->fetch(PDO::FETCH_ASSOC);

// Check friendship status
$isFriend = false;
if ($_SESSION['user_id'] != $userId) {
    $queryFriendship = "SELECT * FROM friends WHERE UserID = ? AND FriendID = ?";
    $stmtFriendship = $pdo->prepare($queryFriendship);
    $stmtFriendship->bindParam(1, $_SESSION['user_id'], PDO::PARAM_INT);
    $stmtFriendship->bindParam(2, $userId, PDO::PARAM_INT);
    $stmtFriendship->execute();
    $isFriend = $stmtFriendship->rowCount() > 0;
}

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($userInfo['Username']); ?>'s Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" media="print" onload="this.media='all'">
    <style>
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 1024px) {
            .profile-grid {
                grid-template-columns: 300px 1fr 300px;
            }
        }

        .side-column {
            height: fit-content;
        }

        .scrollable-column {
            max-height: 80vh;
            overflow-y: auto;
        }

        .review-article {
            display: flex;
            align-items: start;
        }

        .review-image {
            flex-shrink: 0;
            margin-right: 1rem;
        }

        .review-content {
            flex-grow: 1;
        }
    </style>

</head>

<body class="bg-gray-100">

    <!-- Profile Section -->
    <div class="max-w-7xl mx-auto mt-10 p-6">
        <!-- Main Container: Using Grid Layout for Columns with Defined Widths -->
        <div class="grid grid-cols-1 lg:grid-cols-6 gap-6">
            <!-- Left Section: Top Section and About Section (Column 1) -->
            <div class="space-y-6 lg:col-span-2">
                <!-- Top Section: Profile Picture, Name, Username -->
                <!-- Top Section: Profile Picture, Name, Username -->
                <div class="flex items-center space-x-4 bg-white p-6 rounded-lg shadow-lg">
                    <!-- Display Profile Picture -->
                    <?php if (!empty($userInfo['ProfilePicture'])): ?>
                        <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($userInfo['ProfilePicture']); ?>" alt="Profile Picture" class="w-24 h-24 rounded-full object-cover">
                    <?php else: ?>
                        <img src="../assets/images/default-profile.png" alt="Default Profile Picture" class="w-24 h-24 rounded-full object-cover">
                    <?php endif; ?>

                    <div>
                        <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($userInfo['FirstName'] . ' ' . $userInfo['LastName']); ?></h1>
                        <p class="text-gray-600">@<?php echo htmlspecialchars($userInfo['Username']); ?></p>
                        <!-- Show buttons based on friendship status -->
                        <?php if ($_SESSION['user_id'] == $userInfo['UserID']): ?>
                            <a href="edit_profile.php" class="mt-2 inline-block px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Edit Profile</a>
                        <?php elseif ($isFriend): ?>
                            <div class="relative inline-block mt-2">
                                <p class="font-xl cursor-pointer">Friends</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


                <!-- About Section -->
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <h2 class="text-2xl font-semibold mb-4 text-blue-600">About Me</h2>
                    <p class="mb-2 text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($userInfo['Bio'])); ?></p>
                    <p class="text-gray-600"><strong>Location:</strong> <?php echo htmlspecialchars($userInfo['Location']); ?></p>
                    <p class="text-gray-600"><strong>Favorite Genres:</strong> <?php echo htmlspecialchars($userInfo['FavoriteGenres']); ?></p>
                </div>
            </div>

            <!-- Middle Section: User Posts/Reviews (Column 2) -->
            <div class="space-y-6 lg:col-span-3">
                <!-- Adjust the container to exclude the height of the navbar -->
                <div class="bg-white p-4 rounded-lg shadow-lg h-[calc(100vh-50px)] overflow-y-auto">
                    <h2 class="text-xl font-semibold mb-4">Reviews</h2>
                    <?php if (count($resultReviews) > 0): ?>
                        <?php foreach ($resultReviews as $review): ?>
                            <article class="mb-4 p-6 rounded-xl bg-gray-50 shadow-md flex justify-between items-start">
                                <div class="review-content flex-1">
                                    <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($review['Title']); ?></h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <strong>Author:</strong> <?php echo htmlspecialchars($review['Author'] ?? 'Unknown'); ?>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <strong>Genres:</strong> <?php echo htmlspecialchars($review['Genre'] ?? 'Not specified'); ?>
                                    </p>
                                    <p class="mt-2"><?php echo nl2br(htmlspecialchars($review['ReviewText'])); ?></p>
                                    <p class="text-sm text-gray-500 mt-2">Posted on: <?php echo htmlspecialchars((new DateTime($review['CreatedAt']))->format('F j, Y')); ?></p>
                                    <div class="mt-4">
                                        <a
                                            href='view_review.php?review_id=<?php echo htmlspecialchars($review['ReviewID']); ?>'
                                            class=" inline-block px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                                            View
                                        </a>


                                    </div>
                                </div>
                                <div class="review-image ml-4">
                                    <?php if (!empty($review['Image'])): ?>
                                        <img src="<?php echo htmlspecialchars($review['Image']); ?>" alt="Book Cover" class="w-32 h-auto rounded-lg shadow-sm">
                                    <?php else: ?>
                                        <div class="w-32 h-48 bg-gray-200 flex items-center justify-center rounded-lg">
                                            <span class="text-gray-500">No Image</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-gray-500">No posts yet.</p>
                    <?php endif; ?>

                </div>


            </div>


            <!-- Right Section: Bookshelves (Column 3) -->
            <div class="space-y-6 lg:col-span-1">
                <div class="bg-white p-4 rounded-lg shadow-lg">
                    <h2 class="text-xl font-semibold mb-4">Bookshelves
                        <a href="bookshelf.php?user_id=<?php echo $userId; ?>" class="text-sm text-blue-600">(Edit)</a>
                    </h2>
                    <ul>
                        <li>
                            <a href="bookshelf.php?user_id=<?php echo $userId; ?>&status=all" class="text-blue-600">
                                All (<?php echo isset($bookshelfCounts['total']) ? $bookshelfCounts['total'] : 0; ?>)
                            </a>
                        </li>
                        <li>
                            <a href="bookshelf.php?user_id=<?php echo $userId; ?>&status=read" class="text-blue-600">
                                Read (<?php echo isset($bookshelfCounts['read_count']) ? $bookshelfCounts['read_count'] : 0; ?>)
                            </a>
                        </li>
                        <li>
                            <a href="bookshelf.php?user_id=<?php echo $userId; ?>&status=current" class="text-blue-600">
                                Currently Reading (<?php echo isset($bookshelfCounts['currently_reading_count']) ? $bookshelfCounts['currently_reading_count'] : 0; ?>)
                            </a>
                        </li>
                        <li>
                            <a href="bookshelf.php?user_id=<?php echo $userId; ?>&status=want" class="text-blue-600">
                                Want to Read (<?php echo isset($bookshelfCounts['want_to_read_count']) ? $bookshelfCounts['want_to_read_count'] : 0; ?>)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>



</body>

</html>