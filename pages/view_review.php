<?php
// Include config file to connect to the database
require_once '../includes/config.php';
// Start the session
session_start();

// Message handling
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
$errorMessage = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['success'], $_SESSION['error']);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if a review_id is provided in the URL
if (!isset($_GET['review_id'])) {
    header('Location: index.php');
    exit();
}

// Sanitize the review_id
$reviewId = intval($_GET['review_id']);

// Update the main review query to include the average rating
$reviewQuery = "
    SELECT r.ReviewID, r.UserID, r.BookID, r.ReviewText, r.CreatedAt, 
           r.Title, r.Author, r.ISBN, r.PublicationYear, r.Genre, r.Description, r.Image,
           u.Username, u.ProfilePicture, u.FirstName, u.LastName,
           AVG(rt.Rating) as AverageRating,
           COUNT(DISTINCT rt.UserID) as TotalReviews
    FROM Reviews r
    JOIN Users u ON r.UserID = u.UserID
    LEFT JOIN Ratings rt ON r.BookID = rt.BookID
    WHERE r.ReviewID = :review_id
    GROUP BY r.ReviewID
";

// Prepare and execute the query
$stmt = $pdo->prepare($reviewQuery);
$stmt->bindParam(':review_id', $reviewId, PDO::PARAM_INT);
$stmt->execute();
$review = $stmt->fetch(PDO::FETCH_ASSOC);

// If no review is found, show an error or redirect
if (!$review) {
    echo "Review not found!";
    exit();
}

// Fetch like and comment counts
$likeQuery = "SELECT COUNT(*) as LikeCount FROM Likes WHERE ReviewID = :review_id";
$likeStmt = $pdo->prepare($likeQuery);
$likeStmt->bindParam(':review_id', $reviewId, PDO::PARAM_INT);
$likeStmt->execute();
$likeCount = $likeStmt->fetchColumn();

$commentQuery = "SELECT COUNT(*) as CommentCount FROM Comments WHERE ReviewID = :review_id";
$commentStmt = $pdo->prepare($commentQuery);
$commentStmt->bindParam(':review_id', $reviewId, PDO::PARAM_INT);
$commentStmt->execute();
$commentCount = $commentStmt->fetchColumn();

// Fetch comments for the review
$commentsQuery = "SELECT c.CommentID, c.Content, c.CreatedAt, u.Username, u.ProfilePicture 
                  FROM Comments c 
                  JOIN Users u ON c.UserID = u.UserID 
                  WHERE c.ReviewID = :review_id 
                  ORDER BY c.CreatedAt DESC";
$commentsStmt = $pdo->prepare($commentsQuery);
$commentsStmt->bindParam(':review_id', $reviewId, PDO::PARAM_INT);
$commentsStmt->execute();
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

// After fetching the review
$averageRating = null; // Initialize the variable to avoid undefined warnings

if ($review) {
    $bookId = $review['BookID'];
    $averageRating = isset($review['AverageRating']) ? $review['AverageRating'] : null; // Safely assign the average rating

    // Fetch ratings for the book
    $queryRatings = "SELECT r.UserID, r.Rating, r.CreatedAt, u.ProfilePicture, u.FirstName, u.LastName
     FROM Ratings r
     JOIN Users u ON r.UserID = u.UserID
     WHERE r.BookID = :book_id";
    $stmtRatings = $pdo->prepare($queryRatings);
    $stmtRatings->bindParam(':book_id', $bookId, PDO::PARAM_INT);
    $stmtRatings->execute();
    $ratings = $stmtRatings->fetchAll(PDO::FETCH_ASSOC);

    // Fetch opinions for the review
    $queryOpinions = "SELECT o.UserID, o.OpinionText, o.CreatedAt, u.ProfilePicture, u.FirstName, u.LastName
      FROM Opinions o
      JOIN Users u ON o.UserID = u.UserID
      WHERE o.ReviewID = :review_id";
    $stmtOpinions = $pdo->prepare($queryOpinions);
    $stmtOpinions->bindParam(':review_id', $reviewId, PDO::PARAM_INT);
    $stmtOpinions->execute();
    $opinions = $stmtOpinions->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Handle the case where the review does not exist
    echo "Review not found!";
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Review</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" media="print" onload="this.media='all'">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .star {
            cursor: pointer;
            color: lightgray;
        }

        .star.selected {
            color: gold;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>


    <div class="container mx-auto mt-9 p-6">
        <?php if ($successMessage): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($successMessage); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($errorMessage); ?></span>
            </div>
        <?php endif; ?>
        <a href="index.php" class="inline-block mb-4 px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Back</a> <!-- Back button -->

        <div class="flex">
            <!-- Review Details -->
            <div class="flex-1 p-6 bg-white rounded-lg shadow-md">
                <article>
                    <div class="flex items-center">
                        <a href="profile.php?user_id=<?php echo htmlspecialchars($review['UserID']); ?>">
                            <img class="rounded-full w-12 h-12" src="../uploads/profile-pictures/<?php echo htmlspecialchars($review['ProfilePicture']); ?>" alt="Profile Picture" />
                        </a>
                        <div class="ml-4">
                            <a href="profile.php?user_id=<?php echo htmlspecialchars($review['UserID']); ?>" class="block text-lg font-bold dark:text-white">
                                <?php echo htmlspecialchars($review['FirstName'] . ' ' . $review['LastName']); ?>
                            </a>
                            <span class="block text-sm text-gray-500 dark:text-gray-400">@<?php echo htmlspecialchars($review['Username']); ?></span>
                            <span class="text-sm text-gray-400 dark:text-gray-300">
                                Posted on: <?php echo htmlspecialchars((new DateTime($review['CreatedAt']))->format('F j, Y')); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Title and Book Details -->
                    <h2 class="mt-4 text-2xl font-bold dark:text-white"><?php echo htmlspecialchars($review['Title']); ?></h2>
                    <p class="text-lg font-semibold dark:text-slate-200">by <?php echo htmlspecialchars($review['Author']); ?></p>

                    <!-- Average Rating Display -->
                    <div class="mt-2">
                        <strong class="text-lg">Average Rating:</strong>
                        <span class="text-yellow-500 font-semibold"><?php echo htmlspecialchars($averageRating ? round($averageRating, 2) : 'N/A'); ?></span>
                    </div>

                    <!-- Book Cover and Info -->
                    <div class="mt-4 flex">
                        <?php if ($review['Image']): ?>
                            <img class="w-36 h-auto rounded-lg shadow-sm mr-6" src="<?php echo htmlspecialchars($review['Image']); ?>" alt="Book Cover">
                        <?php endif; ?>
                        <div>
                            <p class="text-sm dark:text-slate-200">Genres: <?php echo htmlspecialchars($review['Genre']); ?></p>
                            <p class="mt-4 dark:text-slate-200 leading-relaxed"><?php echo nl2br(htmlspecialchars($review['ReviewText'])); ?></p>

                            <?php if ($review['Description']): ?>
                                <h3 class="mt-4 text-lg font-semibold dark:text-white">Book Description:</h3>
                                <p class="dark:text-slate-200"><?php echo nl2br(htmlspecialchars($review['Description'])); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Like and Comment Section -->
                    <div class="flex items-center mt-4">
                        <!-- Like Button -->
                        <button class="like-button flex items-center mr-4" data-review-id="<?php echo htmlspecialchars($review['ReviewID']); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1 heart-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="like-count"><?php echo $likeCount; ?></span>
                        </button>

                        <!-- Comment Button -->
                        <button class="comment-button flex items-center" data-review-id="<?php echo htmlspecialchars($review['ReviewID']); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            <span class="comment-count"><?php echo $commentCount; ?></span>
                        </button>
                    </div>

                    <!-- Comment Section (Initially Hidden) -->
                    <div class="comment-section mt-4 hidden" id="comment-section-<?php echo htmlspecialchars($review['ReviewID']); ?>">
                        <h3 class="text-lg font-semibold mb-2">Comments</h3>
                        <div class="comments-list mb-4">
                            <!-- Comments will be loaded here dynamically -->
                        </div>
                        <form class="comment-form flex" data-review-id="<?php echo htmlspecialchars($review['ReviewID']); ?>">
                            <input type="text" class="comment-input flex-grow mr-2 p-2 border rounded" name="comment" placeholder="Write a comment...">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Post</button>
                        </form>
                    </div>
                </article>
            </div>

            <!-- Sidebar for Likes and Comments -->
            <div class="w-2/4 ml-6 bg-white rounded-lg shadow-md p-4">
                <!-- Rating and Opinion Submission Section -->
                <div class="mt-6 bg-white p-4 rounded-lg shadow-lg">
                    <h2 class="text-xl font-semibold mb-4">Rating and Opinion</h2>
                    <form action="submit_rating_opinion.php" method="POST">
                        <input type="hidden" name="review_id" value="<?php echo htmlspecialchars($review['ReviewID']); ?>">

                        <!-- Star Rating Section -->
                        <h3 class="text-lg font-semibold">Leave a Rating</h3>
                        <div class="flex items-center">
                            <div id="star-rating" class="flex">
                                <i class="star fas fa-star" data-value="1"></i>
                                <i class="star fas fa-star" data-value="2"></i>
                                <i class="star fas fa-star" data-value="3"></i>
                                <i class="star fas fa-star" data-value="4"></i>
                                <i class="star fas fa-star" data-value="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="rating-value" value="0" required>
                        </div>

                        <div class="mb-4 mt-4">
                            <label for="opinion" class="block text-sm font-medium text-black-700">Your Opinion:</label>
                            <textarea name="opinion" id="opinion" rows="2" required class="mt-2 block w-full border-black rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <button type="submit" class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Submit</button>
                    </form>
                </div>

                <?php
                // Fetch the review and associated book details
                $queryReview = "SELECT r.*, b.BookID 
        FROM Reviews r
        JOIN Books b ON r.BookID = b.BookID
        WHERE r.ReviewID = ?";
                $stmtReview = $pdo->prepare($queryReview);
                $stmtReview->bindParam(1, $reviewId, PDO::PARAM_INT);
                $stmtReview->execute();
                $review = $stmtReview->fetch(PDO::FETCH_ASSOC);

                if ($review) {
                    $bookId = $review['BookID'];

                    // Fetch ratings for the book
                    $queryRatings = "SELECT r.UserID, r.Rating, r.CreatedAt, u.ProfilePicture, u.FirstName, u.LastName
            FROM Ratings r
            JOIN Users u ON r.UserID = u.UserID
            WHERE r.BookID = ?";
                    $stmtRatings = $pdo->prepare($queryRatings);
                    $stmtRatings->bindParam(1, $bookId, PDO::PARAM_INT);
                    $stmtRatings->execute();
                    $ratings = $stmtRatings->fetchAll(PDO::FETCH_ASSOC);

                    // Fetch opinions for the review
                    $queryOpinions = "SELECT o.UserID, o.OpinionText, o.CreatedAt, u.ProfilePicture, u.FirstName, u.LastName
            FROM Opinions o
            JOIN Users u ON o.UserID = u.UserID
            WHERE o.ReviewID = ?";
                    $stmtOpinions = $pdo->prepare($queryOpinions);
                    $stmtOpinions->bindParam(1, $reviewId, PDO::PARAM_INT);
                    $stmtOpinions->execute();
                    $opinions = $stmtOpinions->fetchAll(PDO::FETCH_ASSOC);
                }
                ?>

                <!-- Display Opinions -->
                <?php if (!empty($opinions)): ?>
                    <?php foreach ($opinions as $opinion): ?>
                        <div class="bg-gray-100 rounded-lg p-4 mb-4 shadow-md">
                            <div class="flex items-start">
                                <img class="rounded-full w-12 h-12 mr-4" src="../uploads/profile-pictures/<?php echo htmlspecialchars($opinion['ProfilePicture']); ?>" alt="Profile Picture">
                                <div>
                                    <a href="profile.php?user_id=<?php echo htmlspecialchars($opinion['UserID']); ?>" class="font-bold text-lg dark:text-white">
                                        <?php echo htmlspecialchars($opinion['FirstName'] . ' ' . $opinion['LastName']); ?>
                                    </a>
                                    <p class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars((new DateTime($opinion['CreatedAt']))->format('F j, Y')); ?>
                                    </p>

                                    <!-- Display Ratings for the Current User -->
                                    <?php
                                    // Fetch the rating for the specific user for the current opinion
                                    $queryUserRating = "SELECT Rating FROM Ratings WHERE UserID = ? AND BookID = ?";
                                    $stmtUserRating = $pdo->prepare($queryUserRating);
                                    $stmtUserRating->bindParam(1, $opinion['UserID'], PDO::PARAM_INT);
                                    $stmtUserRating->bindParam(2, $bookId, PDO::PARAM_INT);
                                    $stmtUserRating->execute();
                                    $userRating = $stmtUserRating->fetch(PDO::FETCH_ASSOC);
                                    ?>

                                    <?php if ($userRating): ?>
                                        <div class="mt-2">
                                            <div class="flex items-center">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="<?php echo $i <= $userRating['Rating'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" class="w-4 h-4 text-yellow-500" viewBox="0 0 24 24">
                                                        <path d="M12 .587l3.668 7.435L24 9.748l-6 5.851 1.416 8.264L12 18.902 4.584 23.863 6 15.599 0 9.748l8.332-1.726L12 .587z" />
                                                    </svg>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-sm text-gray-500">No rating given.</p>
                                    <?php endif; ?>

                                    <p class="mt-2 text-gray-700 dark:text-white">
                                        <?php echo nl2br(htmlspecialchars($opinion['OpinionText'])); ?>
                                    </p>

                                    <!-- Edit Button (Only for the logged-in user) -->
                                    <?php if ($opinion['UserID'] === $_SESSION['user_id']): ?>
                                        <button class="mt-2 px-2 py-1 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 edit-opinion"
                                            data-opinion="<?php echo htmlspecialchars($opinion['OpinionText']); ?>"
                                            data-rating="<?php echo htmlspecialchars($userRating['Rating']); ?>">Edit</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No opinions yet.</p>
                <?php endif; ?>

                <script>
                    // Handle edit button click
                    document.querySelectorAll('.edit-opinion').forEach(button => {
                        button.addEventListener('click', () => {
                            const opinionText = button.getAttribute('data-opinion');
                            const rating = button.getAttribute('data-rating');

                            // Populate the form with the existing opinion and rating
                            document.getElementById('opinion').value = opinionText;
                            document.getElementById('rating-value').value = rating;

                            // Update the star rating display
                            const stars = document.querySelectorAll('#star-rating .star');
                            stars.forEach(star => star.classList.remove('text-yellow-500'));
                            for (let i = 0; i < rating; i++) {
                                stars[i].classList.add('text-yellow-500');
                            }
                        });
                    });

                    // Handle star rating selection
                    const starRating = document.querySelectorAll('#star-rating .star');
                    starRating.forEach(star => {
                        star.addEventListener('click', () => {
                            const ratingValue = star.getAttribute('data-value');
                            document.getElementById('rating-value').value = ratingValue;

                            // Update star styles
                            starRating.forEach(s => s.classList.remove('text-yellow-500'));
                            for (let i = 0; i < ratingValue; i++) {
                                starRating[i].classList.add('text-yellow-500');
                            }
                        });
                    });
                </script>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Star rating functionality
            const starContainer = document.getElementById('star-rating');
            const ratingInput = document.getElementById('rating-value');
            const stars = starContainer.querySelectorAll('.star');

            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const value = this.getAttribute('data-value');
                    highlightStars(value);
                });

                star.addEventListener('mouseout', function() {
                    highlightStars(ratingInput.value);
                });

                star.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    ratingInput.value = value;
                    highlightStars(value);
                });
            });

            function highlightStars(value) {
                stars.forEach(star => {
                    star.classList.toggle('selected', star.getAttribute('data-value') <= value);
                });
            }
        });
        // Like functionality
        document.querySelectorAll('.like-button').forEach(button => {
            button.addEventListener('click', function() {
                const reviewId = this.dataset.reviewId;
                fetch('../includes/like_review.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `review_id=${reviewId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Toggle like button UI
                            this.classList.toggle('liked');
                            const likeCount = this.querySelector('.like-count');
                            let currentCount = parseInt(likeCount.textContent);
                            likeCount.textContent = this.classList.contains('liked') ? currentCount + 1 : currentCount - 1;

                            // Toggle heart icon to full red when liked
                            const heartIcon = this.querySelector('.heart-icon');
                            if (this.classList.contains('liked')) {
                                heartIcon.setAttribute('fill', 'red'); // Make the heart fully red
                                heartIcon.setAttribute('stroke', 'red'); // Change the stroke color to red
                            } else {
                                heartIcon.setAttribute('fill', 'none'); // Reset to outline
                                heartIcon.setAttribute('stroke', 'currentColor'); // Reset to original stroke color
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>
    <script src="../assets/js/comment.js"></script>

</body>

</html>