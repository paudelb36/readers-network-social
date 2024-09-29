<?php
//trending.php 
require_once '../includes/config.php';

session_start();

// Fetch genres dynamically from the Books table
function fetchGenresFromBooksTable($pdo)
{
    $sql = "SELECT DISTINCT Genre FROM Books";
    $stmt = $pdo->query($sql);
    $genres = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!empty($row['Genre'])) {
            $genres = array_merge($genres, array_map('trim', explode(',', $row['Genre'])));
        }
    }
    return array_unique($genres);
}

// Initialize filter variables
$genresFilter = isset($_GET['genre']) ? explode(',', $_GET['genre']) : [];
$sortBy = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'newest';
$searchTitle = isset($_GET['search_title']) ? $_GET['search_title'] : '';

try {
    // Start building the SQL query
    $sql = "SELECT * FROM Books WHERE 1=1";
    $params = [];

    // Apply title search if provided
    if (!empty($searchTitle)) {
        $sql .= " AND Title LIKE ?";
        $params[] = "%$searchTitle%";
    }

    // Apply genre filter if provided
    if (!empty($genresFilter)) {
        $placeholders = rtrim(str_repeat('?, ', count($genresFilter)), ', ');
        $sql .= " AND (" . implode(' OR ', array_fill(0, count($genresFilter), "Genre LIKE ?")) . ")";
        foreach ($genresFilter as $genre) {
            $params[] = "%$genre%";
        }
    }

    // Apply sorting
    if ($sortBy === 'rating') {
        $sql .= " ORDER BY AverageRating DESC";
    } elseif ($sortBy === 'newest') {
        $sql .= " ORDER BY PublicationYear DESC, BookID DESC";
    }

    error_log("SQL Query: $sql");
    error_log("Params: " . implode(', ', $params));

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch genres for the dropdown
    $genres = fetchGenresFromBooksTable($pdo);
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
    <title>Find Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer>
        // JavaScript for modals and dropdowns
        function toggleDropdown(id) {
            document.getElementById(id).classList.toggle('hidden');
        }

        function showDetailsModal(bookId, title, author, image, isbn, publicationYear, genre) {
            const modal = document.getElementById('book-details-modal');

            // Populate the modal with book details
            document.getElementById('modal-book-title').textContent = title;
            document.getElementById('modal-book-author').textContent = author;
            document.getElementById('modal-book-isbn').textContent = isbn;
            document.getElementById('modal-book-publication-year').textContent = publicationYear;
            document.getElementById('modal-book-genre').textContent = genre;

            // Add image to the modal
            const modalImage = document.getElementById('modal-book-image');
            modalImage.src = image;
            modalImage.alt = title;

            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('book-details-modal');
            modal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const genreDropdown = document.getElementById('genre-dropdown');
            const genreInput = document.getElementById('genre-input');
            const selectedGenresContainer = document.getElementById('selected-genres');

            const genres = <?= json_encode($genres) ?>; // Load PHP genres array into JavaScript
            let selectedGenres = [];

            // Display dropdown based on user input
            genreInput.addEventListener('input', function() {
                const input = genreInput.value.toLowerCase();
                genreDropdown.innerHTML = '';
                if (input.trim() !== '') {
                    genres.forEach(genre => {
                        if (genre.toLowerCase().includes(input) && !selectedGenres.includes(genre)) {
                            const option = document.createElement('li');
                            option.textContent = genre;
                            option.className = 'cursor-pointer px-2 py-1 hover:bg-gray-200';
                            option.addEventListener('click', () => addGenre(genre));
                            genreDropdown.appendChild(option);
                        }
                    });
                }
                genreDropdown.classList.toggle('hidden', genreDropdown.childElementCount === 0);
            });

            // Add genre to the selected list and create tag
            function addGenre(genre) {
                selectedGenres.push(genre);
                genreDropdown.classList.add('hidden');
                genreInput.value = '';
                updateSelectedGenresUI();
            }

            // Remove genre from the selected list
            window.removeGenre = function(genre) {
                selectedGenres = selectedGenres.filter(g => g !== genre);
                updateSelectedGenresUI();
            }

            // Update the UI to show selected genres as tags
            function updateSelectedGenresUI() {
                selectedGenresContainer.innerHTML = '';
                selectedGenres.forEach(genre => {
                    const tag = document.createElement('div');
                    tag.className = 'inline-flex items-center px-2 py-1 text-xs font-medium text-white bg-blue-600 rounded-full mr-2 mb-2';
                    tag.innerHTML = `${genre} <span class="ml-1 cursor-pointer" onclick="removeGenre('${genre}')">✖</span>`;
                    selectedGenresContainer.appendChild(tag);
                });

                // Set the hidden input value to the list of selected genres
                document.getElementById('hidden-genre-input').value = selectedGenres.join(',');
            }
        });
    </script>
</head>

<body class="bg-gray-100 text-gray-900">
    <!-- Include the header -->
    <?php include '../includes/header.php'; ?>

    <div class="container mx-auto p-6 mt-8 max-w-5xl">
        <h1 class="text-3xl font-semibold text-center mb-6">Trending Books</h1>

        <!-- Filter Section -->
        <form method="GET" action="trending.php" class="flex flex-col items-center mb-8">
            <div class="flex justify-center space-x-4 mb-4 w-full">
                <input
                    type="text"
                    name="search_title"
                    placeholder="Search by Title..."
                    value="<?= htmlspecialchars($searchTitle) ?>"
                    class="p-2 border border-gray-300 rounded-md w-1/3">
                <div class="relative w-1/3">
                    <input
                        type="text"
                        id="genre-input"
                        placeholder="Search Genre..."
                        class="p-2 border border-gray-300 rounded-md w-full"
                        autocomplete="off">
                    <ul id="genre-dropdown" class="absolute left-0 z-10 bg-white border border-gray-300 rounded-md shadow-lg mt-1 w-full max-h-60 overflow-auto hidden"></ul>
                </div>

                <select name="sort_by" class="p-2 border border-gray-300 rounded-md">
                    <option value="newest" <?= ($sortBy === 'newest') ? 'selected' : '' ?>>Newest</option>
                    <option value="rating" <?= ($sortBy === 'rating') ? 'selected' : '' ?>>Highest Rating</option>
                </select>

                <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white rounded-md">Filter</button>
            </div>

            <!-- Selected Genres Container -->
            <div id="selected-genres" class="flex flex-wrap justify-center mb-4 w-full"></div>

            <!-- Hidden Input to Store Selected Genres for Form Submission -->
            <input type="hidden" id="hidden-genre-input" name="genre" value="">
        </form>

        <!-- Books Listing -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            <?php if (isset($books) && count($books) > 0): ?>
                <?php foreach ($books as $book): ?>
                    <div class="book-item bg-white shadow-md rounded-md p-3 relative text-sm" data-book-id="<?= $book['BookID'] ?>">
                        <!-- Conditional image display -->
                        <div class="w-full <?= (strpos($book['Image'], 'downloads') !== false) ? 'h-auto' : 'h-48' ?> overflow-hidden">
                            <img src="<?= $book['Image'] ?>" alt="<?= htmlspecialchars($book['Title']) ?>" class="w-full <?= (strpos($book['Image'], 'downloads') !== false) ? 'object-contain' : 'object-cover' ?> rounded-md">
                        </div>

                        <div class="mt-2">
                            <h3 class="text-sm font-semibold"><?= htmlspecialchars($book['Title']) ?></h3>
                            <p class="text-xs text-gray-600">Author: <?= htmlspecialchars($book['Author']) ?></p>
                            <p class="text-xs text-gray-600">Genre: <?= htmlspecialchars($book['Genre']) ?></p>
                            <p class="text-xs text-yellow-500">Rating: <?= isset($book['AverageRating']) && $book['AverageRating'] !== '' ? htmlspecialchars($book['AverageRating']) . '' : 'N/A' ?></p>
                        </div>


                        <!-- Read Later Button with Dropdown -->
                        <div class="mt-2 relative w-full">
                            <div class="inline-flex w-full border border-gray-300 rounded-md">
                                <button onclick="addToReadingList(<?= $book['BookID'] ?>, 'Want to Read')" class="w-2/3 px-4 py-2 bg-indigo-500 text-white rounded-l-md inline-flex items-center text-xs justify-center">
                                    Want to Read
                                </button>
                                <div class="relative w-1/3 group">
                                    <button type="button" class="w-full px-4 py-2 bg-gray-500 text-white rounded-r-md inline-flex items-center text-xs justify-center" onclick="toggleDropdown('dropdown-<?= $book['BookID'] ?>')">
                                        <!-- Down arrow icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    <div id="dropdown-<?= $book['BookID'] ?>" class="hidden absolute left-0 bg-white border rounded-md shadow-lg mt-1 z-10 w-40">
                                        <button onclick="addToReadingList(<?= $book['BookID'] ?>, 'Currently Reading')" class="block w-full px-3 py-1 text-gray-700 hover:bg-gray-100 text-sm">
                                            Currently Reading
                                        </button>
                                        <button onclick="addToReadingList(<?= $book['BookID'] ?>, 'Read')" class="block w-full px-3 py-1 text-gray-700 hover:bg-gray-100 text-sm">
                                            Read
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Show Details Button -->
                        <div class="mt-2">
                            <button
                                onclick="showDetailsModal('<?= htmlspecialchars($book['BookID']) ?>', 
                                   '<?= htmlspecialchars($book['Title']) ?>', 
                                   '<?= htmlspecialchars($book['Author']) ?>', 
                                   '<?= htmlspecialchars($book['Image']) ?>', 
                                   '<?= htmlspecialchars($book['ISBN']) ?>', 
                                   '<?= htmlspecialchars($book['PublicationYear']) ?>', 
                                   '<?= htmlspecialchars($book['Genre']) ?>')"
                                class="px-3 py-2 bg-green-500 text-white rounded-md w-full text-xs">
                                Show Details
                            </button>
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
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full flex">
            <img id="modal-book-image" class="w-1/3 h-48 object-cover rounded-md mr-4" alt="Book Image">
            <div class="w-2/3">
                <h2 class="text-lg font-semibold mb-2">Book Details</h2>
                <p class="text-gray-700">Title: <span id="modal-book-title">Some Book Title</span></p>
                <p class="text-gray-700">Author: <span id="modal-book-author">Some Author</span></p>
                <p class="text-gray-700">ISBN: <span id="modal-book-isbn">123-4567891234</span></p>
                <p class="text-gray-700">Publication Year: <span id="modal-book-publication-year">2024</span></p>
                <p class="text-gray-700">Genre: <span id="modal-book-genre">Fiction</span></p>
                <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-red-500 text-white rounded-lg">Close</button>
            </div>
        </div>
    </div>

    <script>
        function addToReadingList(bookId, status) {
            fetch('add_to_reading_list.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        book_id: bookId,
                        status: status
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert(data.message, 'success');
                    } else {
                        showAlert(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred while managing the reading list.', 'error');
                });
        }

        // Keep the existing toggleDropdown function if you're using it for the dropdown functionality
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            if (dropdown) {
                dropdown.classList.toggle('hidden');
            }
        }
    </script>
    <script src="../assets/js/alert.js" defer></script> <!-- Include your alert.js file here -->

</body>

</html>