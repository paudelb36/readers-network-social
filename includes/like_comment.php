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