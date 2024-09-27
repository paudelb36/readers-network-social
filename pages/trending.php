<?php
// Includes, session, and fetching books (same as above)
require_once '../includes/config.php';

session_start();

// Initialize filter variables
$genre = isset($_GET['genre']) ? $_GET['genre'] : null;
$sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'newest';

try {
    // Fetching books with filters applied
    $sql = "SELECT * FROM Books WHERE 1 = 1";
    if (!empty($genre)) {
        $sql .= " AND Genre = :genre";
    }
    if ($sortBy === 'rating') {
        $sql .= " ORDER BY AverageRating DESC";
    } else {
        $sql .= " ORDER BY BookID DESC"; // Default is newest
    }
    $stmt = $pdo->prepare($sql);
    if (!empty($genre)) {
        $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
    }
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching trending books: " . $e->getMessage());
    $_SESSION['error_message'] = "Error loading trending books.";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trending Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer>
        // JavaScript for modals and dropdowns
        function toggleDropdown(id) {
            document.getElementById(id).classList.toggle('hidden');
        }

        function showDetailsModal(bookId) {
            const modal = document.getElementById('book-details-modal');
            // Fetch book details dynamically if needed
            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('book-details-modal');
            modal.classList.add('hidden');
        }
    </script>
</head>

<body class="bg-gray-100 text-gray-900">
    <!-- Include the header -->
    <?php include '../includes/header.php'; ?>

    <div class="container mx-auto p-6 mt-8 max-w-5xl">
        <h1 class="text-3xl font-semibold text-center mb-6">Trending Books</h1>

        <!-- Filter Section -->
        <form method="GET" action="trending.php" class="flex justify-center mb-8 space-x-4">
            <select name="genre" class="p-2 border border-gray-300 rounded-md">
                <option value="">All Genres</option>
                <option value="Fiction" <?= ($genre === 'Fiction') ? 'selected' : '' ?>>Fiction</option>
                <option value="Non-fiction" <?= ($genre === 'Non-fiction') ? 'selected' : '' ?>>Non-fiction</option>
            </select>

            <select name="sort_by" class="p-2 border border-gray-300 rounded-md">
                <option value="newest" <?= ($sortBy === 'newest') ? 'selected' : '' ?>>Newest</option>
                <option value="rating" <?= ($sortBy === 'rating') ? 'selected' : '' ?>>Highest Rating</option>
            </select>

            <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white rounded-md">Filter</button>
        </form>

        <!-- Books Listing -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <?php if (isset($books) && count($books) > 0): ?>
                <?php foreach ($books as $book): ?>
                    <div class="bg-white shadow-md rounded-md p-3 relative text-sm"> <!-- Reduced padding and size -->
                        <!-- Conditional image display -->
                        <div class="w-full <?= (strpos($book['Image'], 'downloads') !== false) ? 'h-auto' : 'h-48' ?> overflow-hidden"> <!-- Reduced height -->
                            <img src="<?= $book['Image'] ?>" alt="<?= $book['Title'] ?>" class="w-full <?= (strpos($book['Image'], 'downloads') !== false) ? 'object-contain' : 'object-cover' ?> rounded-md">
                        </div>

                        <div class="mt-2">
                            <h3 class="text-sm font-semibold"><?= htmlspecialchars($book['Title']) ?></h3> <!-- Smaller font size -->
                            <p class="text-xs text-gray-600">Author: <?= htmlspecialchars($book['Author']) ?></p> <!-- Smaller font size -->
                            <p class="text-xs text-gray-600">Genre: <?= htmlspecialchars($book['Genre']) ?></p> <!-- Smaller font size -->
                            <p class="text-xs text-yellow-500">Rating: <?= htmlspecialchars($book['AverageRating']) ?>/5</p> <!-- Smaller font size -->
                        </div>

                        <!-- Read Later Button with Dropdown -->
                        <div class="mt-2 relative">
                            <button onclick="toggleDropdown('dropdown-<?= $book['BookID'] ?>')" class="px-3 py-1 bg-indigo-500 text-white rounded-md w-full inline-flex items-center text-xs justify-center"> <!-- Added w-full -->
                                Read Later
                                <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            <div id="dropdown-<?= $book['BookID'] ?>" class="hidden absolute bg-white border rounded-md shadow-lg mt-1 z-10 w-full"> <!-- Adjusted width -->
                                <a href="#" class="block px-3 py-1 text-gray-700 hover:bg-gray-100 text-sm">Currently Reading</a>
                                <a href="#" class="block px-3 py-1 text-gray-700 hover:bg-gray-100 text-sm">Read</a>
                            </div>
                        </div>

                        <!-- Show Details Button -->
                        <div class="mt-2">
                            <button onclick="showDetailsModal('<?= $book['BookID'] ?>')" class="px-3 py-1 bg-green-500 text-white rounded-md w-full text-xs">Show Details</button> <!-- Smaller button size -->
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="col-span-4 text-center text-gray-600">No books found matching your criteria.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Book Details Modal -->
    <div id="book-details-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full">
            <h2 class="text-lg font-semibold mb-4">Book Details</h2>
            <p class="text-gray-700">Title: <span id="modal-book-title">Some Book Title</span></p>
            <p class="text-gray-700">Author: <span id="modal-book-author">Some Author</span></p>
            <p class="text-gray-700">Description: <span id="modal-book-description">Lorem ipsum dolor sit amet...</span></p>
            <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg">Close</button>
        </div>
    </div>

</body>

</html>