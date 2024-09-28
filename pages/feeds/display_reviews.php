<?php
// display_reviews.php

// Display alert messages
if (isset($_SESSION['error_message'])): ?>
    <script>
        alert("<?php echo htmlspecialchars($_SESSION['error_message']); ?>");
    </script>
<?php
    unset($_SESSION['error_message']);
endif;

if (isset($_SESSION['success_message'])): ?>
    <script>
        alert("<?php echo htmlspecialchars($_SESSION['success_message']); ?>");
    </script>
<?php
    unset($_SESSION['success_message']);
endif;

// Check if there are any visible reviews
if (empty($reviews)): ?>
    <p class="text-center text-gray-500 dark:text-gray-400 mt-4">No reviews available at the moment.</p>
    <?php else:
    $userLikes = getUserLikes($pdo, $_SESSION['user_id']);

    // Modify the part where you output each review
    foreach ($reviews as $review):
        $likeCount = $likes[$review['ReviewID']] ?? 0;
        $commentCount = $comments[$review['ReviewID']] ?? 0;
        $isLiked = in_array($review['ReviewID'], $userLikes);
        $isCurrentUserPost = ($_SESSION['user_id'] == $review['UserID']);

    ?>
        <article class="mb-4 break-inside p-6 rounded-xl bg-white dark:bg-slate-800 flex flex-col bg-clip-border shadow-md">
            <!-- User info and options menu -->
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
                                        <li><a href="view_review.php?review_id=<?php echo htmlspecialchars($review['ReviewID']); ?>" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">View Details</a></li>
                                        <?php if ($isCurrentUserPost): ?>
                                            <li>
                                                <button onclick="openUpdateModal(<?php echo htmlspecialchars($review['ReviewID']); ?>)" class="block w-full text-left px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">
                                                    Edit Review
                                                </button>
                                            </li>

                                            <li><button onclick="deletePost(<?php echo htmlspecialchars($review['ReviewID']); ?>)" class="block w-full text-left px-4 py-2 text-red-600 hover:bg-gray-100 dark:hover:bg-gray-600">Delete Post</button></li>
                                        <?php else: ?>
                                            <li><a href="#" onclick="openReportModal('post', <?php echo htmlspecialchars($review['ReviewID']); ?>)" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">Report Content</a></li>
                                            <li><a href="#" onclick="openReportModal('user', <?php echo htmlspecialchars($review['UserID']); ?>)" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600">Report User</a></li>
                                        <?php endif; ?>
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

            <!-- Review content -->
            <div class="mb-4 review-content" id="review-content-<?php echo htmlspecialchars($review['ReviewID']); ?>">
                <!-- Flexbox for Image and Details -->
                <div class="flex flex-col md:flex-row">
                    <!-- Book Cover on the left side -->
                    <?php if ($review['Image']): ?>
                        <div class="md:mr-8 mb-4 md:mb-0 flex-shrink-0">
                            <img class="w-36 h-auto rounded-lg shadow-sm" src="<?php echo htmlspecialchars($review['Image']); ?>" alt="Book Cover" />
                        </div>
                    <?php endif; ?>

                    <!-- Book Information on the right side -->
                    <div class="flex-grow">
                        <!-- Title and Author at the top -->
                        <h3 class="text-xl font-extrabold dark:text-white mb-2">
                            <?php echo htmlspecialchars($review['Title']); ?>
                        </h3>
                        <p class="text-md font-semibold dark:text-slate-200 mb-2">
                            by <?php echo htmlspecialchars($review['Author']); ?>
                        </p>
                        <!-- Genre and Rating -->
                        <p class="text-sm font-semibold dark:text-slate-200 mb-2">
                            Genres: <?php echo htmlspecialchars($review['Genre']); ?>
                        </p>

                        <!-- Review Text -->
                        <p class="dark:text-slate-200 mb-4 leading-relaxed review-text" id="review-text-<?php echo htmlspecialchars($review['ReviewID']); ?>">
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
                <button class="like-button flex items-center mr-4 <?php echo $isLiked ? 'liked' : ''; ?>" data-review-id="<?php echo htmlspecialchars($review['ReviewID']); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-1 heart-icon" fill="<?php echo $isLiked ? 'red' : 'none'; ?>" viewBox="0 0 24 24" stroke="<?php echo $isLiked ? 'red' : 'currentColor'; ?>">
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
<?php
    endforeach;
endif;
?>

<!-- Edit Review Modal -->
<div id="edit-review-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Review</h3>
            <form id="edit-review-form" action="update_review.php" method="POST" class="mt-2">
                <input type="hidden" id="edit-review-id" name="review_id">
                <textarea id="edit-review-text" name="review_text" class="w-full px-3 py-2 text-gray-700 border rounded-lg focus:outline-none" rows="4" required></textarea>
                <button type="submit" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update</button>
                <button type="button" onclick="closeEditForm()" class="mt-4 bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Cancel</button>
            </form>
        </div>
    </div>
</div>
<script>
    function openEditForm(reviewID) {
        // Fetch the review data from the server
        fetch('get_review.php?review_id=' + reviewID)
            .then(response => response.json())
            .then(data => {
                // Populate the form with the current review data
                document.getElementById('edit-review-id').value = data.ReviewID;
                document.getElementById('edit-review-text').value = data.ReviewText;
                // Open the modal
                document.getElementById('edit-review-modal').classList.remove('hidden');
            })
            .catch(error => console.error('Error fetching review data:', error));
    }

    function closeEditForm() {
        document.getElementById('edit-review-modal').classList.add('hidden');
    }
    // Show and hide the modal
    function openUpdateForm(reviewID) {
        document.getElementById('update-form-modal').classList.remove('hidden');
        // Use AJAX or Fetch API to populate the modal with review data
        fetch(`get_review.php?review_id=${reviewID}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('update-review-id').value = data.ReviewID;
                document.getElementById('update-review-text').value = data.ReviewText;
            });
    }

    function closeUpdateForm() {
        document.getElementById('update-form-modal').classList.add('hidden');
    }
</script>