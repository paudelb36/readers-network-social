<?php
// index.php
include '../includes/config.php'; // Adjust the path based on your file structure

session_start();
include 'friend_suggestions.php';

// Check if the user is logged in
$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$username = $loggedIn ? $_SESSION['username'] : ''; // Assuming you store the username in session

include '../includes/header.php';

$posts = [];
try {
    // Fetch posts and reviews
    $sql = "SELECT posts.PostID, posts.UserID, posts.Content, posts.CreatedAt, posts.Image AS PostImage,
                   reviews.ReviewID, reviews.Rating, reviews.ReviewText, reviews.Title AS ReviewTitle,
                   reviews.Author AS ReviewAuthor, reviews.ISBN AS ReviewISBN, reviews.PublicationYear AS ReviewYear,
                   reviews.Genre AS ReviewGenre, reviews.Description AS ReviewDescription, reviews.Image AS ReviewImage
            FROM posts
            LEFT JOIN reviews ON posts.PostID = reviews.PostID
            ORDER BY posts.CreatedAt DESC";

    $stmt = $pdo->query($sql);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Query failed: ' . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css"> <!-- Tailwind CSS CDN -->
    <link rel="stylesheet" href="assets/css/main.css">
</head>

<body class="bg-gray-100">


    <!-- Main Container -->
    <div class="flex justify-center h-screen pt-16">
        <!-- Left Sidebar (Fixed) -->
        <div class="w-1/4 fixed top-16 left-0 p-4 space-y-4 bg-gray-100 h-full overflow-y-auto">
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-2">Daily Book Recommendation</h2>
                <div class="flex items-center space-x-4" id="book-recommendation">
                    <a href="#" id="book-link" target="_blank">
                        <img src="" class="w-16 h-24 rounded-lg" alt="Book Cover" id="book-cover">
                    </a>
                    <div>
                        <a href="#" id="book-link-title" class="font-medium" target="_blank">
                            <h3 id="book-title"></h3>
                        </a>
                        <p class="text-gray-600 text-sm" id="book-author"></p>
                    </div>
                </div>
            </div>
            <!-- Shortcuts -->
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-2">Shortcuts</h2>
                <ul class="space-y-2">
                    <li><a href="#" class="text-blue-600 hover:underline">Home</a></li>
                    <li><a href="#" class="text-blue-600 hover:underline">Friends</a></li>
                    <li><a href="#" class="text-blue-600 hover:underline">Messages</a></li>
                    <li><a href="#" class="text-blue-600 hover:underline">Notifications</a></li>
                    <li><a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="text-blue-600 hover:underline">Profile</a></li>
                </ul>
            </div>
        </div>

        <!-- Feed Section (Centered and Scrollable) -->
        <div class="w-full md:w-1/2 mx-auto top-20 overflow-y-auto py-4 space-y-4 feed-container">
            <!-- Post Creation Form -->
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-2">Create a Post</h2>
                <form id="postForm" action="create_post.php" method="POST" enctype="multipart/form-data">
                    <textarea id="postContent" name="post_content" rows="1" placeholder="What's on your mind?" class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    <div class="mt-2 mb-2">
                        <input type="file" name="post_image" class="w-full border border-gray-300 rounded-lg py-2 px-3">
                    </div>
                    <div class="flex justify-end space-x-4">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Post</button>
                        <button type="button" id="addReviewButton" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">Add Review</button>
                    </div>
                </form>
            </div>
            <script>
                // JavaScript to check if the post content is empty
                document.getElementById('postForm').addEventListener('submit', function(event) {
                    const content = document.getElementById('postContent').value.trim();
                    if (content === '') {
                        event.preventDefault(); // Prevent form submission
                        alert('Post content cannot be empty.'); // Show alert
                    }
                });
            </script>
            <!-- Feed -->
            <?php include '../pages/feed.php'; ?>

        </div>



    </div>
    </div>


    <!-- Book Review Form Popup -->
    <div id="reviewFormPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
            <h2 class="text-lg font-semibold mb-4">Submit a Book Review</h2>
            <form action="create_review.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="text" name="book_title" placeholder="Book Title" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="book_author" placeholder="Author Name" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="book_isbn" placeholder="ISBN" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="book_year" placeholder="Publication Year" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="book_genre" placeholder="Genre" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-3">
                    <textarea name="review_text" rows="3" placeholder="Write your review..." class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                </div>
                <div class="mb-3">
                    <input type="number" name="rating" placeholder="Rating (1-5)" class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" min="1" max="5" required>
                </div>
                <div class="mb-4">
                    <input type="file" name="book_image" class="w-full border border-gray-300 rounded-lg py-2 px-3">
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Submit Review</button>
                    <button type="button" id="closeReviewFormButton" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Close</button>
                </div>
            </form>
        </div>
    </div>


    <!-- Right Sidebar (Fixed) -->
    <!-- Updated Friend Suggestions Section -->
    <div class="w-1/4 fixed top-16 right-0 p-4 space-y-4 bg-gray-100 h-full overflow-y-auto">
        <!-- New Friend Suggestions -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-2">Friends Suggestions</h2>
            <ul class="space-y-4">
                <?php if (!empty($suggestedUsers)): ?>
                    <?php $displayedUsers = array_slice($suggestedUsers, 0, 5); // Display only the first 5 suggestions 
                    ?>
                    <?php foreach ($displayedUsers as $user): ?>
                        <li class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <a href="profile.php?user_id=<?php echo htmlspecialchars($user['UserID']); ?>" class="flex items-center space-x-4">
                                    <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($user['ProfilePicture']); ?>" class="w-10 h-10 rounded-full" alt="<?php echo htmlspecialchars($user['Username']); ?>">
                                    <div>
                                        <span class="text-gray-800"><?php echo htmlspecialchars($user['FirstName']) . ' ' . htmlspecialchars($user['LastName']); ?></span>
                                        <br>
                                        <span class="block text-sm text-gray-500">@<?php echo htmlspecialchars($user['Username']); ?></span>
                                    </div>
                                </a>
                            </div>
                            <button class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Add</button>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No suggestions available at the moment.</p>
                <?php endif; ?>
            </ul>
            <?php if (count($suggestedUsers) > 5): // Check if there are more than 5 suggestions 
            ?>
                <div class="mt-4 text-center">
                    <a href="friends.php" class="inline-block px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-300">
                        Show More
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>







    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addReviewButton = document.getElementById('addReviewButton');
            const reviewFormPopup = document.getElementById('reviewFormPopup');
            const closeReviewFormButton = document.getElementById('closeReviewFormButton');

            // Show the popup when the "Add Review" button is clicked
            addReviewButton.addEventListener('click', function() {
                reviewFormPopup.classList.remove('hidden');
            });

            // Hide the popup when the "Close" button is clicked
            closeReviewFormButton.addEventListener('click', function() {
                reviewFormPopup.classList.add('hidden');
            });

            // Hide the popup when clicking outside of the form
            document.addEventListener('click', function(event) {
                if (reviewFormPopup && !reviewFormPopup.contains(event.target) && event.target !== addReviewButton) {
                    reviewFormPopup.classList.add('hidden');
                }
            });
        });
    </script>

    <script src="../assets/js/main.js"></script>
</body>

</html>