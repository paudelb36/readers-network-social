<?php
// view_review.php
include('../includes/config.php');

// Set the time zone to Kathmandu
date_default_timezone_set('Asia/Kathmandu');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: content_filtering.php");
    exit();
}

$reviewId = $_GET['id'];

// Fetch review details
$query = "SELECT * FROM Reviews WHERE ReviewID = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$reviewId]);
$review = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$review) {
    header("Location: content_filtering.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Review</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-4">Review Details</h2>
        <div class="bg-white p-4 rounded shadow border border-gray-200">
            <h3 class="text-xl font-semibold mb-2"><?= htmlspecialchars($review['Title']) ?></h3>
            <p class="mb-2"><strong>Author:</strong> <?= htmlspecialchars($review['Author']) ?></p>
            <p class="mb-2"><strong>Genre:</strong> <?= htmlspecialchars($review['Genre']) ?></p>
            <p class="mb-2"><strong>ISBN:</strong> <?= htmlspecialchars($review['ISBN']) ?></p>
            <p class="mb-2"><strong>Publication Year:</strong> <?= htmlspecialchars($review['PublicationYear']) ?></p>
            <p class="mb-4"><?= nl2br(htmlspecialchars($review['ReviewText'])) ?></p>
            <a href="content_filtering.php" class="text-blue-500 hover:underline">Back to Reported Reviews</a>
        </div>
    </div>
</body>
</html>
