<?php
// session_start(); // Ensure sessions are started

require_once '../includes/config.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Get the user ID from session
$userId = $_SESSION['user_id'];

// Query to fetch reviews with the new structure
$reviewsQuery = "
    SELECT r.ReviewID, r.UserID, r.BookID, r.Rating, r.ReviewText, r.CreatedAt, 
           r.Title, r.Author, r.ISBN, r.PublicationYear, r.Genre, r.Description, r.Image,
           u.Username, u.ProfilePicture, u.FirstName, u.LastName
    FROM Reviews r
    JOIN Users u ON r.UserID = u.UserID
    ORDER BY r.CreatedAt DESC
";

// Prepare and execute the query
$stmt = $pdo->prepare($reviewsQuery);
$stmt->execute();
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch likes for all reviews
$likesQuery = "SELECT ReviewID, COUNT(*) as LikeCount FROM Likes GROUP BY ReviewID";
$likesStmt = $pdo->prepare($likesQuery);
$likesStmt->execute();
$likes = $likesStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Fetch comments for all reviews
$commentsQuery = "SELECT ReviewID, COUNT(*) as CommentCount FROM Comments GROUP BY ReviewID";
$commentsStmt = $pdo->prepare($commentsQuery);
$commentsStmt->execute();
$comments = $commentsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Loop through the reviews and display them with likes and comments count
foreach ($reviews as $review):
    $likeCount = $likes[$review['ReviewID']] ?? 0;
    $commentCount = $comments[$review['ReviewID']] ?? 0;
?>

    <!-- Card -->
    <article class="mb-4 break-inside p-6 rounded-xl bg-white dark:bg-slate-800 flex flex-col bg-clip-border shadow-md">
        <div class="flex pb-4 items-center justify-between">
            <!-- Profile Image and User Info -->
            <div class="flex flex-grow">
                <a class="inline-block mr-4" href="#">
                    <img class="rounded-full max-w-none w-12 h-12" src="../uploads/profile-pictures/<?php echo htmlspecialchars($review['ProfilePicture']); ?>" alt="Profile Picture" />
                </a>
                <div class="flex flex-col flex-grow">
                    <div class="flex items-center">
                        <div class="flex-grow">
                            <a class="block text-lg font-bold dark:text-white" href="profile.php?user_id=<?php echo htmlspecialchars($review['UserID']); ?>">
                                <?php echo htmlspecialchars($review['FirstName'] . ' ' . $review['LastName']); ?>
                            </a>
                            <span class="block text-sm text-gray-500 dark:text-gray-400">
                                @<?php echo htmlspecialchars($review['Username']); ?>
                            </span>
                        </div>
                        <!-- Three-Dot Button -->
                        <div class="relative ml-4">
                            <button class="text-gray-500 hover:text-gray-700 focus:outline-none" id="options-button-<?php echo htmlspecialchars($review['ReviewID']); ?>">
                                <svg class="w-6 h-6" viewBox="0 0 32 32">
                                    <path d="M16,10c1.7,0,3-1.3,3-3s-1.3-3-3-3s-3,1.3-3,3S14.3,10,16,10z"></path>
                                    <path d="M16,13c-1.7,0-3,1.3-3,3s1.3,3,3,3s3-1.3,3-3S17.7,13,16,13z"></path>
                                    <path d="M16,22c-1.7,0-3,1.3-3,3s1.3,3,3,3s3-1.3,3-3S17.7,22,16,22z"></path>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg hidden" id="options-menu-<?php echo htmlspecialchars($review['ReviewID']); ?>">
                                <ul class="py-1 text-sm">
                                    <li><a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">View</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">Report Content</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">Report User</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">Add to Read Later</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="text-slate-300 dark:text-slate-200 mt-1">
                        <?php
                        $createdAt = new DateTime($review['CreatedAt']);
                        echo htmlspecialchars($createdAt->format('F j, Y'));
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Display review details -->
        <div class="mb-4">
            <!-- Title and Author at the top -->
            <h3 class="text-xl font-extrabold dark:text-white mb-2">
                <?php echo htmlspecialchars($review['Title']); ?>
            </h3>
            <p class="text-md font-semibold dark:text-slate-200 mb-2">
                by <?php echo htmlspecialchars($review['Author']); ?>
            </p>

            <!-- Flexbox for Image and Details -->
            <div class="flex flex-col md:flex-row items-start md:items-center">
                <!-- Book Cover on the left side -->
                <?php if ($review['Image']): ?>
                    <div class="md:mr-8 mb-4 md:mb-0">
                        <img class="w-36 h-auto rounded-lg shadow-sm" src="<?php echo htmlspecialchars($review['Image']); ?>" alt="Book Cover" />
                    </div>
                <?php endif; ?>

                <!-- Book Information on the right side -->
                <div class="flex-grow">
                    <!-- Genre and Rating -->
                    <p class="text-sm font-semibold dark:text-slate-200 mb-2">
                        Genres: <?php echo htmlspecialchars($review['Genre']); ?>
                    </p>
                    <p class="text-sm font-bold dark:text-white mb-3">
                        Rating: <?php echo htmlspecialchars($review['Rating']); ?> / 5
                    </p>

                    <!-- Review Text -->
                    <p class="dark:text-slate-200 mb-4 leading-relaxed">
                        <?php echo nl2br(htmlspecialchars($review['ReviewText'])); ?>
                    </p>

                    <!-- Book Description, if available -->
                    <?php if ($review['Description']): ?>
                        <h3 class="text-md font-semibold dark:text-white mt-4 mb-2">Book Description:</h3>
                        <p class="dark:text-slate-200 mb-4">
                            <?php echo nl2br(htmlspecialchars($review['Description'])); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Like and Comment Section -->
        <div class="flex items-center mt-4">
            <!-- Like Button -->
            <button class="like-button flex items-center mr-4" data-review-id="<?php echo htmlspecialchars($review['ReviewID']); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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

<?php endforeach; ?>


<script>
    //three dots js code

    document.addEventListener('DOMContentLoaded', function() {
        // Toggle the visibility of the options menu
        document.querySelectorAll('[id^="options-button-"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent bubbling up to the document click listener
                const postId = this.id.split('-').pop();
                const menu = document.getElementById('options-menu-' + postId);
                const isVisible = menu.classList.contains('visible');

                // Hide all other menus
                document.querySelectorAll('[id^="options-menu-"]').forEach(m => {
                    m.classList.remove('visible');
                    m.style.opacity = '0';
                    m.style.display = 'none'; // Also set display to none
                });

                if (!isVisible) {
                    menu.classList.add('visible');
                    menu.style.opacity = '1';
                    menu.style.display = 'block'; // Make the menu visible
                }
            });
        });

        // Close the menu if clicked outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[id^="options-button-"]')) {
                document.querySelectorAll('[id^="options-menu-"]').forEach(m => {
                    m.classList.remove('visible');
                    m.style.opacity = '0';
                    m.style.display = 'none'; // Hide the menu
                });
            }
        });
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
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    // Comment functionality
    document.querySelectorAll('.comment-button').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.dataset.reviewId;
            const commentSection = document.getElementById(`comment-section-${reviewId}`);
            commentSection.classList.toggle('hidden');
            if (!commentSection.classList.contains('hidden')) {
                loadComments(reviewId);
            }
        });
    });

    document.querySelectorAll('.comment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const reviewId = this.dataset.reviewId;
            const commentInput = this.querySelector('.comment-input');
            const comment = commentInput.value.trim();

            if (comment) {
                submitComment(reviewId, comment);
                commentInput.value = '';
            }
        });
    });

    function loadComments(reviewId) {
        const commentsList = document.querySelector(`#comment-section-${reviewId} .comments-list`);

        fetch(`../includes/get_comments.php?review_id=${reviewId}`)
            .then(response => response.json())
            .then(data => {
                commentsList.innerHTML = '';
                data.comments.forEach(comment => {
                    const commentElement = document.createElement('div');
                    commentElement.className = 'comment mb-2';
                    commentElement.innerHTML = `
                    <strong>${comment.Username}:</strong> ${comment.Content}
                    <small class="text-gray-500">${comment.CreatedAt}</small>
                `;
                    commentsList.appendChild(commentElement);
                });
            });
    }

    function submitComment(reviewId, comment) {
        fetch('../includes/add_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `review_id=${reviewId}&comment=${encodeURIComponent(comment)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadComments(reviewId);
                    const commentCount = document.querySelector(`[data-review-id="${reviewId}"] .comment-count`);
                    commentCount.textContent = parseInt(commentCount.textContent) + 1;
                }
            });
    }
</script>

<script src="../assets/js/like.js"></script>
<!-- <script src="../assets/js/main.js"></script> -->