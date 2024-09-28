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

// Fetch review details along with the count of reports
$query = "
    SELECT rev.*, COUNT(rep.ReportID) AS ReportCount, GROUP_CONCAT(rep.Reason SEPARATOR ', ') AS Reasons
    FROM Reviews rev
    LEFT JOIN Reports rep ON rev.ReviewID = rep.ReportedPostID
    WHERE rev.ReviewID = ?
    GROUP BY rev.ReviewID
";
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
            <?php if (!empty($review['Image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($review['Image']) ?>" alt="Review Image" class="mb-4 w-1/2">
            <?php endif; ?>
            <p class="mb-2"><strong>Number of Reports:</strong> <?= htmlspecialchars($review['ReportCount']) ?></p>
            <p class="mb-2"><strong>Report Reasons:</strong> <?= htmlspecialchars($review['Reasons']) ?></p>
            <a href="content_filtering.php" class="text-blue-500 hover:underline">Back to Reported Reviews</a>

            <!-- Actions: Hide, Unhide, Delete -->
            <form action="change_review_status.php" method="POST" class="mt-4">
                <input type="hidden" name="review_id" value="<?= htmlspecialchars($reviewId) ?>">
                <?php if ($review['Status'] === 'hidden'): ?>
                    <button type="submit" name="action" value="unhide" class="bg-green-500 text-white rounded px-4 py-2">Unhide</button>
                <?php else: ?>
                    <button type="submit" name="action" value="hide" class="bg-yellow-500 text-white rounded px-4 py-2">Hide</button>
                <?php endif; ?>
                <button type="submit" name="action" value="delete" class="bg-red-500 text-white rounded px-4 py-2 ml-2">Delete</button>
            </form>
        </div>
    </div>
</body>
</html>
