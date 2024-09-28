<?php
// index.php
include '../includes/config.php'; // Adjust the path based on your file structure

session_start();

// Check if the user is logged in
$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

// Include friend suggestions
$suggestedUsers = [];
if ($loggedIn) {
    include 'friend_suggestions.php'; // This file should output $suggestedUsers
    $username = $_SESSION['username']; // Assuming you store the username in session
}

include '../includes/header.php';

$posts = [];
if ($loggedIn) {
    try {
        // Fetch reviews 
        $sql = "SELECT ReviewID, UserID, ReviewText, Title AS ReviewTitle, Author AS ReviewAuthor, ISBN AS ReviewISBN, 
                PublicationYear AS ReviewYear, Genre AS ReviewGenre, Description AS ReviewDescription, Image AS ReviewImage, CreatedAt
                FROM reviews
                ORDER BY CreatedAt DESC";
        $stmt = $pdo->query($sql);
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo 'Query failed: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="../assets/css/main.css">

    <script src="../assets/js/review.js" defer></script>
    <script src="../assets/js/search.js" defer></script>
    <script src="../assets/js/main.js" defer></script>

</head>

<body class="bg-gray-100">

    <!-- Main Container -->
    <div class="flex justify-center h-screen pt-16">
        <!-- Left Sidebar (Fixed) -->
        <?php include '../includes/left_sidebar.php'; ?>

        <!-- Feed Section (Centered and Scrollable) -->
        <div class="w-full md:w-1/2 mx-auto top-16 overflow-y-auto p-4 space-y-4 feed-container">
            <?php include $loggedIn ? '../includes/feed_content.php' : '../includes/welcome_content.php'; ?>
        </div>
    </div>
    <!-- book review popup Form -->
    <div id="reviewFormPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-4xl w-full">
            <h2 class="text-lg font-semibold mb-4">Submit a Book Review</h2>

            <div class="flex space-x-4">
                <div class="flex-grow mb-4">
                    <div class="flex space-x-2">
                        <input type="text" id="bookSearch" placeholder="Search for a book..." class="flex-grow p-2 border border-gray-300 rounded-lg">
                        <button type="button" id="searchBookButton" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Search</button>
                    </div>

                    <div id="searchResults" class="mt-2 hidden max-w-xs">
                        <ul id="resultsList" class="border border-gray-300 rounded-lg max-h-60 overflow-y-auto"></ul>
                    </div>
                </div>

                <form id="reviewForm" action="create_review.php" method="POST" enctype="multipart/form-data" class="flex-grow">
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <input type="text" id="book_title" name="book_title" placeholder="Book Title" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <input type="text" id="book_author" name="book_author" placeholder="Author Name" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        </div>
                        <div>
                            <input type="text" id="book_isbn" name="book_isbn" placeholder="ISBN" class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <input type="text" id="book_year" name="book_year" placeholder="Publication Year" class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <input type="text" id="book_genre" name="book_genre" placeholder="Genre" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        </div>
                        <div class="col-span-2">
                            <textarea name="review_text" rows="3" placeholder="Write your review..." class="w-full p-2 border border-gray-300 rounded-lg" required></textarea>
                        </div>
                        <div class="col-span-2 flex items-center space-x-4">
                            <input type="file" name="book_image_upload" id="book_image_upload" class="w-full border border-gray-300 rounded-lg py-2 px-3">
                            <img id="book_cover_image" src="" alt="Book Cover" class="w-24 h-auto hidden mt-2">
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Submit Review</button>
                        <button type="button" id="closeReviewFormButton" class="px-4 py-2 bg-gray-500 text-white rounded-lg">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Right Sidebar (Fixed) -->
    <?php include '../includes/right_sidebar.php'; ?>


    <!-- review form pop up script  -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addReviewButton = document.getElementById('addReviewButton'),
                reviewFormPopup = document.getElementById('reviewFormPopup'),
                closeReviewFormButton = document.getElementById('closeReviewFormButton');
            addReviewButton.addEventListener('click', () => reviewFormPopup.classList.remove('hidden'));
            closeReviewFormButton.addEventListener('click', () => reviewFormPopup.classList.add('hidden'));
            document.addEventListener('click', function(e) {
                if (reviewFormPopup && !reviewFormPopup.contains(e.target) && e.target !== addReviewButton) reviewFormPopup.classList.add('hidden');
            });
        });
    </script>
    <!-- for the search of book and retreiving book data -->
    <script src="../assets/js/search_book_api.js"></script>
    <!-- remove the data in form after closing -->
    <script>
        document.getElementById('closeReviewFormButton').addEventListener('click', () => {
            document.getElementById('reviewFormPopup').classList.add('hidden');
            clearForm();
        });

        function clearForm() {
            document.querySelector('input[name="book_title"]').value = '';
            document.querySelector('input[name="book_author"]').value = '';
            document.querySelector('input[name="book_isbn"]').value = '';
            document.querySelector('input[name="book_year"]').value = '';
            document.querySelector('input[name="book_genre"]').value = '';
            document.querySelector('textarea[name="review_text"]').value = '';
            document.querySelector('input[name="rating"]').value = '';
            document.querySelector('input[name="book_image"]').value = '';
            document.getElementById('resultsList').innerHTML = '';
            document.getElementById('searchResults').classList.add('hidden');
        }
    </script>
    <script src="../assets/js/main.js"></script>
</body>

</html>