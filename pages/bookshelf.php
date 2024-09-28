<?php
// bookshelf.php
session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Get the user_id from the URL parameter or default to logged-in user
$userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : $_SESSION['user_id'];

// Get the status filter from URL parameter, default to 'all'
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Define the possible statuses
$validStatuses = ['all', 'read', 'currently_reading', 'want_to_read'];

// Ensure the status is valid, default to 'all' if invalid
if (!in_array($status, $validStatuses)) {
    $status = 'all';
}

// Convert status to match database values
$statusMapping = [
    'read' => 'Read',
    'currently_reading' => 'Currently Reading',
    'want_to_read' => 'Want to Read',
    'all' => 'all'
];
$statusFilter = $statusMapping[$status];

// Prepare the SQL query based on the status filter
$query = "SELECT books.BookID, books.Title, books.Author, books.Image, readinglist.Status, readinglist.DateAdded 
          FROM readinglist 
          JOIN books ON readinglist.BookID = books.BookID 
          WHERE readinglist.UserID = ?";

// Modify the query only if a specific status is selected
if ($statusFilter !== 'all') {
    $query .= " AND readinglist.Status = ?";
}

$query .= " ORDER BY readinglist.DateAdded DESC";

// Prepare the statement and bind parameters based on the status
$stmt = $pdo->prepare($query);
$stmt->bindParam(1, $userId, PDO::PARAM_INT);

if ($statusFilter !== 'all') {
    $stmt->bindParam(2, $statusFilter, PDO::PARAM_STR);
}

// Execute the query
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch bookshelf counts for sidebar
$queryBookshelfCounts = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN Status = 'Read' THEN 1 ELSE 0 END) as read_count,
    SUM(CASE WHEN Status = 'Currently Reading' THEN 1 ELSE 0 END) as currently_reading_count,
    SUM(CASE WHEN Status = 'Want to Read' THEN 1 ELSE 0 END) as want_to_read_count
    FROM readinglist 
    WHERE UserID = ?";
$stmtBookshelfCounts = $pdo->prepare($queryBookshelfCounts);
$stmtBookshelfCounts->bindParam(1, $userId, PDO::PARAM_INT);
$stmtBookshelfCounts->execute();
$bookshelfCounts = $stmtBookshelfCounts->fetch(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookshelf</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-9 px-4 py-8">
        <h1 class="text-3xl font-bold mb-2">Bookshelf</h1>
        <a href="profile.php" class="inline-block mb-3 px-4 py-1 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Back </a> <!-- Back button -->

        <div class="flex flex-col md:flex-row">
            <!-- Sidebar -->
            <div class="w-full md:w-1/4 mb-4 md:mb-0">
                <div class="bg-white p-4 rounded-lg shadow-lg">
                    <h2 class="text-xl font-semibold mb-4">Bookshelves</h2>
                    <ul>
                        <li class="mb-2">
                            <a href="?user_id=<?php echo $userId; ?>&status=all" class="text-blue-600 <?php echo $status === 'all' ? 'font-bold' : ''; ?>">
                                All (<?php echo isset($bookshelfCounts['total']) ? $bookshelfCounts['total'] : 0; ?>)
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="?user_id=<?php echo $userId; ?>&status=read" class="text-blue-600 <?php echo $status === 'read' ? 'font-bold' : ''; ?>">
                                Read (<?php echo isset($bookshelfCounts['read_count']) ? $bookshelfCounts['read_count'] : 0; ?>)
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="?user_id=<?php echo $userId; ?>&status=currently_reading" class="text-blue-600 <?php echo $status === 'currently_reading' ? 'font-bold' : ''; ?>">
                                Currently Reading (<?php echo isset($bookshelfCounts['currently_reading_count']) ? $bookshelfCounts['currently_reading_count'] : 0; ?>)
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="?user_id=<?php echo $userId; ?>&status=want_to_read" class="text-blue-600 <?php echo $status === 'want_to_read' ? 'font-bold' : ''; ?>">
                                Want to Read (<?php echo isset($bookshelfCounts['want_to_read_count']) ? $bookshelfCounts['want_to_read_count'] : 0; ?>)
                            </a>
                        </li>
                    </ul>
                </div>
            </div>


            <!-- Book List -->
            <div class="w-full md:w-3/4 md:pl-8">
                <div class="bg-white p-4 rounded-lg shadow-lg">
                    <h2 class="text-xl font-semibold mb-4">
                        <?php echo ucfirst(str_replace('_', ' ', $status)); ?> Books
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php if (empty($books)): ?>
                            <p>No books found.</p>
                        <?php else: ?>
                            <?php foreach ($books as $book): ?>
                                <div class="border p-4 rounded-lg relative flex flex-col items-center text-center">
                                    <img src="<?php echo htmlspecialchars($book['Image']); ?>" alt="<?php echo htmlspecialchars($book['Title']); ?>" class="w-36 h-auto object-cover mb-2 rounded">
                                    <h3 class="font-semibold"><?php echo htmlspecialchars($book['Title']); ?></h3>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($book['Author']); ?></p>
                                    <p class="text-sm text-gray-500">Status: <?php echo htmlspecialchars($book['Status']); ?></p>
                                    <p class="text-xs text-gray-400">Added: <?php echo date('M j, Y', strtotime($book['DateAdded'])); ?></p>

                                    <!-- Dropdown Menu for Actions -->
                                    <div class="absolute top-2 right-2 inline-block text-left">
                                        <button
                                            onClick="toggleDropdown(this)"
                                            class="absolute top-0 right-0 text-gray-600 hover:text-gray-800 focus:outline-none p-2"
                                            aria-label="More options">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                                <circle cx="12" cy="6" r="2" />
                                                <circle cx="12" cy="12" r="2" />
                                                <circle cx="12" cy="18" r="2" />
                                            </svg>
                                        </button>
                                        <div class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-lg shadow-lg z-10">
                                            <!-- Show delete option for all statuses -->
                                            <a href="../includes/delete_book.php?book_id=<?php echo $book['BookID']; ?>&user_id=<?php echo $userId; ?>" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-100" onclick="return confirm('Are you sure you want to delete this book from your shelf?');">Delete</a>

                                            <!-- Conditional menu items based on book's current status -->
                                            <?php if ($book['Status'] === 'Read'): ?>
                                                <a href="update_status.php?book_id=<?php echo $book['BookID']; ?>&user_id=<?php echo $userId; ?>&status=Currently Reading" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Move to Currently Reading</a>
                                                <a href="update_status.php?book_id=<?php echo $book['BookID']; ?>&user_id=<?php echo $userId; ?>&status=Want to Read" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Move to Want to Read</a>
                                            <?php elseif ($book['Status'] === 'Currently Reading'): ?>
                                                <a href="update_status.php?book_id=<?php echo $book['BookID']; ?>&user_id=<?php echo $userId; ?>&status=Read" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Move to Read</a>
                                                <a href="update_status.php?book_id=<?php echo $book['BookID']; ?>&user_id=<?php echo $userId; ?>&status=Want to Read" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Move to Want to Read</a>
                                            <?php elseif ($book['Status'] === 'Want to Read'): ?>
                                                <a href="update_status.php?book_id=<?php echo $book['BookID']; ?>&user_id=<?php echo $userId; ?>&status=Read" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Move to Read</a>
                                                <a href="update_status.php?book_id=<?php echo $book['BookID']; ?>&user_id=<?php echo $userId; ?>&status=Currently Reading" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Move to Currently Reading</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for dropdown toggle -->
    <script>
        function toggleDropdown(button) {
            var dropdown = button.nextElementSibling;
            dropdown.classList.toggle('hidden');
        }
    </script>
</body>

</html>