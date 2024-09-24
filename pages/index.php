<?php
// index.php
include '../includes/config.php'; // Adjust the path based on your file structure

session_start();
// Include friend suggestions
$suggestedUsers = [];
if (isset($_SESSION['user_id'])) {
    include 'friend_suggestions.php'; // This file should output $suggestedUsers
}
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
    <link rel="stylesheet" href="../assets/css/main.css">

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
                    <li><a href="./index.php" class="text-blue-600 hover:underline">Home</a></li>
                    <li><a href="./friends.php" class="text-blue-600 hover:underline">Friends</a></li>
                    <li><a href="#" class="text-blue-600 hover:underline">Trendings</a></li>
                    <li><a href="./notification.php" class="text-blue-600 hover:underline">Notifications</a></li>
                    <?php if ($loggedIn): ?>
                        <li><a href="profile.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="text-blue-600 hover:underline">Profile</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="text-blue-600 hover:underline">Login</a></li>
                        <li><a href="register.php" class="text-blue-600 hover:underline">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Feed Section (Centered and Scrollable) -->
        <div class="w-full md:w-1/2 mx-auto top-16 overflow-y-auto p-4 space-y-4 feed-container">
            <?php if ($loggedIn): ?>
                <!-- Review Prompt -->
                <div class="bg-white p-4 rounded-lg shadow-md max-w-lg mx-auto">
                    <h2 class="text-lg font-semibold mb-3">Want to write a review?</h2>
                    <p class="text-gray-600 mb-4">Share your thoughts and experiences about your favorite books!</p>
                    <div class="flex justify">
                        <button id="addReviewButton" class="px-2 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Add Review</button>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-white p-2 rounded-lg shadow-md max-w-lg mx-auto">
                    <p class="text-gray-600">
                        Please <a href="login.php" class="text-blue-500 hover:underline">log in</a> to write a review.
                    </p>
                </div>

                <div id="readers_network_homecontent" class="bg-white p-4 rounded-lg shadow-md max-w-lg mx-auto mt-4">
                    <h1 class="text-3xl text-center font-semibold mb-2">Welcome to Readers Network!</h1>
                    <img src="../assets/images/cristina-gottardi-8hJQKRIQZMY-unsplash.jpg" alt="Books and Reading" class="w-full h-48 object-cover rounded-lg mb-4">
                    <p>Connect with fellow book lovers and share your thoughts on your favorite reads.</p>
                    <p>Discover new books through reviews, quotes, and recommendations from our vibrant community.</p>
                    <p>It’s easy to organize your reading list and measure your reading journey. Join us and start your book adventure today!</p>

                </div>


            <?php endif; ?>

            <!-- Feed -->
            <?php
            // Check if the user is logged in
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
                // Include the feed.php only if the user is logged in
                include '../pages/feed.php';
            } else {
                // Display "Create Account", "Sign In" buttons and a message about Readers Network for non-logged-in users
                echo '
        <!-- New div with the text about Readers Network -->
        <div class="bg-gray-100 p-4 rounded-lg shadow-md max-w-lg mx-auto mt-6 text-center">
            <p class="text-2xl font-normal text-gray-900">See what\'s happening on Readers Network now…</p>
        </div>
        <div id="readers_network_homecontent_btns" class="mt-4">
        <a class="bg-blue-500 text-white font-semibold py-2 px-3 text-sm rounded-md max-w-xs mx-auto block text-center hover:bg-blue-600 transition duration-300 ease-in-out transform hover:scale-105" href="register.php">Create Account</a>
        <div class="text-center mt-3">
        <a class="bg-green-500 text-white font-semibold py-2 px-3 text-sm rounded-md max-w-xs mx-auto block text-center hover:bg-green-600 transition duration-300 ease-in-out transform hover:scale-105" href="login.php">Log In</a>
        </div>
    </div>
        ';
            }
            ?>


        </div>


    </div>


    <!-- Book Review Form Popup -->
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

                        <div>
                            <input type="number" name="rating" placeholder="Rating (1-5)" class="w-full p-2 border border-gray-300 rounded-lg" min="1" max="5" required>
                        </div>
                        <div class="col-span-2">
                            <textarea name="review_text" rows="3" placeholder="Write your review..." class="w-full p-2 border border-gray-300 rounded-lg" required></textarea>
                        </div>
                        <div class="col-span-2 flex items-center space-x-4">
                            <input type="file" name="book_image_upload" id="book_image_upload" class="w-full border border-gray-300 rounded-lg py-2 px-3">
                            <img id="book_cover_image" src="" alt="Book Cover" class="w-24 h-auto hidden mt-2"> <!-- Changed w-32 to w-24 for smaller size -->
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
    <div class="w-1/4 fixed top-16 right-0 p-4 space-y-4 bg-gray-100 h-full overflow-y-auto">
        <!-- New Friend Suggestions -->
        <div class="bg-white p-4 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-2">Friend Suggestions</h2>
            <ul class="space-y-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php
                    // User is logged in, include the friend suggestions logic
                    include 'friend_suggestions.php'; // This should set $suggestedUsers
                    if (!empty($suggestedUsers)): ?>
                        <?php foreach ($suggestedUsers as $user): ?>
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
                            </li>
                        <?php endforeach; ?>
                        <!-- Show More button -->
                        <div class="text-center mt-4">
                            <a href="friends.php" class="inline-block bg-blue-500 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-600 transition duration-300 ease-in-out">
                                Show More
                            </a>
                        </div>
                    <?php else: ?>
                        <p>No suggestions available at the moment.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-gray-600">
                        Please <a href="login.php" class="text-blue-500 hover:underline">log in</a> to see friend suggestions.
                    </p>
                <?php endif; ?>
            </ul>
        </div>
    </div>





    <!-- review form pop up script  -->
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

    <!-- for the search of book and retreiving book data -->
    <script>
        const API_KEY = 'AIzaSyAjH7g5XK4YYA5t2GH1rVcd2-PKzGsgp0c';
        const searchBookButton = document.getElementById('searchBookButton');
        const bookSearchInput = document.getElementById('bookSearch');
        const resultsList = document.getElementById('resultsList');
        const searchResults = document.getElementById('searchResults');
        const reviewForm = document.getElementById('reviewForm');

        // Event listener for the search button
        searchBookButton.addEventListener('click', async () => {
            const query = bookSearchInput.value;
            if (!query) {
                alert('Please enter a book title, author, or ISBN to search.');
                return;
            }

            try {
                const response = await fetch(`https://www.googleapis.com/books/v1/volumes?q=${encodeURIComponent(query)}&key=${API_KEY}`);
                const data = await response.json();

                if (data.items && data.items.length > 0) {
                    resultsList.innerHTML = ''; // Clear previous results
                    data.items.forEach(book => {
                        const listItem = document.createElement('li');
                        listItem.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-200', 'flex', 'items-center', 'space-x-2');

                        const bookImage = book.volumeInfo.imageLinks ? book.volumeInfo.imageLinks.thumbnail : 'default-image-url.png';
                        listItem.innerHTML = `
                        <img src="${bookImage}" alt="Book Cover" class="w-8 h-auto">
                        <div>
                            <strong>${book.volumeInfo.title}</strong><br>
                            <span class="text-sm text-gray-600">${book.volumeInfo.authors ? book.volumeInfo.authors.join(', ') : 'Unknown Author'}</span>
                        </div>
                    `;
                        listItem.onclick = () => populateForm(book.volumeInfo);
                        resultsList.appendChild(listItem);
                    });
                    searchResults.classList.remove('hidden'); // Show search results
                } else {
                    alert('No books found. Please try a different search.');
                }
            } catch (error) {
                console.error('Error fetching book data:', error);
                alert('An error occurred while searching for books. Please try again later.');
            }
        });

        // Populate form with selected book details
        function populateForm(book) {
            document.getElementById('book_title').value = book.title || '';
            document.getElementById('book_author').value = book.authors ? book.authors.join(', ') : '';
            document.getElementById('book_isbn').value = book.industryIdentifiers ? book.industryIdentifiers[0].identifier : '';
            document.getElementById('book_year').value = book.publishedDate ? book.publishedDate.split('-')[0] : '';
            document.getElementById('book_genre').value = book.categories ? book.categories.join(', ') : '';

            // Set book cover image in the review form
            const bookCoverImage = document.getElementById('book_cover_image');
            const bookImage = book.imageLinks ? book.imageLinks.thumbnail : 'default-image-url.png';
            bookCoverImage.src = bookImage; // Update image preview
            bookCoverImage.classList.remove('hidden'); // Show the image

            // Set the hidden input value for the image path
            const bookImageInput = document.getElementById('book_image');
            bookImageInput.value = bookImage; // Store the image URL for submission

            searchResults.classList.add('hidden'); // Hide search results after selection
        }
        // Function to download the image and submit the form
        async function submitReview() {
            const bookImage = document.getElementById('book_cover_image').src; // Book cover image URL
            const bookTitle = document.getElementById('book_title').value; // Book title from the form

            try {
                // Download the image
                const response = await fetch('download_image.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        imageUrl: bookImage,
                        title: bookTitle
                    })
                });

                const result = await response.json();
                if (result.success) {
                    // Proceed to submit the review form
                    const formData = new FormData(reviewForm);
                    formData.append('downloaded_image', result.imagePath); // Add the downloaded image path to form data

                    // Submit the form
                    const submitResponse = await fetch('create_review.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (submitResponse.ok) {
                        window.location.href = 'index.php'; // Redirect after successful submission
                    } else {
                        alert('Failed to submit the review. Please try again.');
                    }
                } else {
                    alert(result.message || 'Failed to download image.');
                }
            } catch (error) {
                alert(error.message);
            }
        }

        // Attach the submit function to the review form's submit event
        reviewForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default form submission
            submitReview(); // Call the submitReview function
        });
    </script>



    <!-- remove the data in form after closing -->
    <script>
        document.getElementById('closeReviewFormButton').addEventListener('click', () => {
            // Hide the review form popup
            document.getElementById('reviewFormPopup').classList.add('hidden');

            // Clear the form and search results
            clearForm();
        });

        function clearForm() {
            // Clear form fields
            document.querySelector('input[name="book_title"]').value = '';
            document.querySelector('input[name="book_author"]').value = '';
            document.querySelector('input[name="book_isbn"]').value = '';
            document.querySelector('input[name="book_year"]').value = '';
            document.querySelector('input[name="book_genre"]').value = '';
            document.querySelector('textarea[name="review_text"]').value = '';
            document.querySelector('input[name="rating"]').value = '';

            // Clear file input if needed
            document.querySelector('input[name="book_image"]').value = '';

            // Clear previous search results
            const resultsList = document.getElementById('resultsList');
            resultsList.innerHTML = ''; // Clear the list
            document.getElementById('searchResults').classList.add('hidden'); // Hide the search results
        }
    </script>

    <script src="../assets/js/main.js"></script>
</body>

</html>