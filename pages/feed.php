<?php
// session_start(); 

require_once '../includes/config.php';

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Combine posts and reviews with a UNION query
$postsQuery = "
    SELECT 'post' AS source, p.UnifiedPostID AS PostID, p.UserID AS UserID, p.Content AS Content, p.CreatedAt AS CreatedAt, p.Image AS Image, u.Username, u.ProfilePicture, u.FirstName, u.LastName, NULL AS Title, NULL AS Author, NULL AS Genre, NULL AS PublicationYear
    FROM Posts p
    JOIN Users u ON p.UserID = u.UserID
    UNION
    SELECT 'review' AS source, r.UnifiedPostID AS PostID, r.UserID AS UserID, r.ReviewText AS Content, r.CreatedAt AS CreatedAt, r.Image AS Image, u.Username, u.ProfilePicture, u.FirstName, u.LastName, r.Title, r.Author, r.Genre, r.PublicationYear
    FROM Reviews r
    JOIN Users u ON r.UserID = u.UserID
    ORDER BY CreatedAt DESC
";

$stmt = $pdo->prepare($postsQuery);
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch like counts for all posts at once
$unifiedPostIds = array_column($posts, 'PostID');
if (!empty($unifiedPostIds)) {
    $inQuery = implode(',', array_fill(0, count($unifiedPostIds), '?'));
    $likeCountQuery = "SELECT UnifiedPostID, COUNT(*) AS like_count FROM Likes WHERE UnifiedPostID IN ($inQuery) GROUP BY UnifiedPostID";
    $likeStmt = $pdo->prepare($likeCountQuery);
    $likeStmt->execute($unifiedPostIds);
    $likes = $likeStmt->fetchAll(PDO::FETCH_ASSOC);
    $likeMap = [];
    foreach ($likes as $like) {
        $likeMap[$like['UnifiedPostID']] = $like['like_count'];
    }
}

foreach ($posts as $post):
    $likeCount = $likeMap[$post['PostID']] ?? 0;
?>
    <!-- Card -->
    <article class="mb-4 break-inside p-6 rounded-xl bg-white dark:bg-slate-800 flex flex-col bg-clip-border">
        <div class="flex pb-6 items-center justify-between">
            <div class="flex flex-grow">
                <a class="inline-block mr-4" href="#">
                    <!-- Ensure the src attribute correctly points to the profile picture path -->
                    <img class="rounded-full max-w-none w-12 h-12" src="../uploads/profile-pictures/<?php echo htmlspecialchars($post['ProfilePicture']); ?>" alt="Profile Picture" />
                </a>

                <div class="flex flex-col flex-grow">
                    <div class="flex items-center">
                        <!-- Posted user info  -->
                        <div class="flex-grow">
                            <!-- Link to user profile page with UserID as a query parameter -->
                            <a class="block text-lg font-bold dark:text-white" href="profile.php?user_id=<?php echo htmlspecialchars($post['UserID']); ?>">
                                <?php echo htmlspecialchars($post['FirstName'] ?? ''); ?> <?php echo htmlspecialchars($post['LastName'] ?? ''); ?>
                            </a>
                            <span class="block text-sm text-gray-500 dark:text-gray-400">
                                @<?php echo htmlspecialchars($post['Username']); ?>
                            </span>
                        </div>
                        <!-- Three-Dot Button -->
                        <div class="relative ml-4">
                            <button class="text-gray-500 hover:text-gray-700 focus:outline-none" id="options-button-<?php echo htmlspecialchars($post['PostID']); ?>">
                                <svg version="1.1" id="Icons" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 32 32" style="enable-background:new 0 0 32 32;" xml:space="preserve" class="w-6 h-6">
                                    <g>
                                        <path d="M16,10c1.7,0,3-1.3,3-3s-1.3-3-3-3s-3,1.3-3,3S14.3,10,16,10z"></path>
                                        <path d="M16,13c-1.7,0-3,1.3-3,3s1.3,3,3,3s3-1.3,3-3S17.7,13,16,13z"></path>
                                        <path d="M16,22c-1.7,0-3,1.3-3,3s1.3,3,3,3s3-1.3,3-3S17.7,22,16,22z"></path>
                                    </g>
                                </svg>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-md shadow-lg opacity-0 transition-opacity duration-150 ease-in-out" id="options-menu-<?php echo htmlspecialchars($post['PostID']); ?>">
                                <ul class="py-1 text-sm">
                                    <li><a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">View</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">Report Content</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">Report User</a></li>
                                    <li><a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">Add to Read Later</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Posted date  -->
                    <div class="text-slate-300 dark:text-slate-200 mt-2">
                        <?php
                        $createdAt = new DateTime($post['CreatedAt']);
                        $formattedDate = $createdAt->format('F j, Y');
                        echo htmlspecialchars($formattedDate);
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($post['source'] === 'review'): ?>
            <!-- Display review details -->
            <div class="mb-4">
                <h2 class="text-2xl font-extrabold dark:text-white mb-2"><?php echo htmlspecialchars($post['Title'] ?? 'N/A'); ?> by <?php echo htmlspecialchars($post['Author'] ?? 'N/A'); ?></h2>
                <p class="text-lg font-semibold dark:text-slate-200 mb-2"><?php echo htmlspecialchars($post['Genre'] ?? 'N/A'); ?> | Published in <?php echo htmlspecialchars($post['PublicationYear'] ?? 'N/A'); ?></p>
                <p class="dark:text-slate-200 mb-4">
                    <?php echo htmlspecialchars($post['Content']); ?>
                </p>
            </div>
        <?php else: ?>
            <!-- Display normal post content -->
            <h2 class="text-3xl font-extrabold dark:text-white mb-2"><?php echo htmlspecialchars($post['Content']); ?></h2>
        <?php endif; ?>

        <?php if ($post['Image']): ?>
            <div class="py-4">
                <img class="max-w-full rounded-lg" src="<?php echo htmlspecialchars($post['Image']); ?>" alt="Post Image" />
            </div>
        <?php endif; ?>

        <!-- Like functionality -->
        <div class="mt-4 flex items-center space-x-4">
            <a href="#" class="flex items-center space-x-2 text-blue-500 hover:underline like-btn"
                data-unified-post-id="<?php echo htmlspecialchars($post['PostID']); ?>"
                data-post-type="<?php echo htmlspecialchars($post['source']); ?>">
                <span class="inline-block w-6 h-6">
                    <!-- SVG for Like Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="w-6 h-6">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                    </svg>
                </span>
                <span class="like-count"><?php echo htmlspecialchars($likeCount); ?></span>
            </a>
        </div>
    </article>
<?php endforeach; ?>


<script>
    //three dots js code
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle the visibility of the options menu
        document.querySelectorAll('[id^="options-button-"]').forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.id.split('-').pop();
                const menu = document.getElementById('options-menu-' + postId);
                const isVisible = menu.style.opacity === '1';

                // Hide all other menus
                document.querySelectorAll('[id^="options-menu-"]').forEach(m => m.style.opacity = '0');

                menu.style.opacity = isVisible ? '0' : '1';
            });
        });

        // Close the menu if clicked outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('[id^="options-button-"]')) {
                document.querySelectorAll('[id^="options-menu-"]').forEach(m => m.style.opacity = '0');
            }
        });
    }); // Example JavaScript for like functionality
    document.querySelectorAll('.like-btn').forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            const postId = this.getAttribute('data-post-id');
            const postType = this.getAttribute('data-post-type');

            fetch('like_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        post_id: postId,
                        post_type: postType
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const countElement = this.querySelector('.like-count');
                        countElement.textContent = data.new_like_count;
                    } else {
                        console.error('Failed to like post');
                    }
                });
        });
    });
</script>
<script src="../assets/js/like.js"></script>
<!-- <script src="../assets/js/main.js"></script> -->