<!-- Review Prompt for logged-in users -->
<div class="bg-white p-4 rounded-lg shadow-md max-w-lg mx-auto">
    <h2 class="text-lg font-semibold mb-3">Want to write a review?</h2>
    <p class="text-gray-600 mb-4">Share your thoughts and experiences about your favorite books!</p>
    <div class="flex justify">
        <button id="addReviewButton" class="px-2 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">Add Review</button>
    </div>
</div>
<!-- Include the feed -->
<?php include '../pages/feeds/index.php'; ?>