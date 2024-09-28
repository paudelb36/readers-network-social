<?php
// content_filtering.php
include('../includes/config.php');

// Set the time zone to Kathmandu
date_default_timezone_set('Asia/Kathmandu');

// Fetch reported reviews
$query = "SELECT r.ReportID, r.ReportedPostID AS ReportedReviewID, r.Reason, r.Status, r.CreatedAt, rev.Title
          FROM Reports r 
          JOIN Reviews rev ON r.ReportedPostID = rev.ReviewID"; 
$stmt = $pdo->query($query);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reported Reviews</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .table-small {
            font-size: 0.875rem;
        }
        .table-small th, .table-small td {
            padding: 0.5rem;
        }
    </style>
</head>
<body>
    <?php include('components/header.php'); ?>
    <div class="flex">
        <?php include('components/sidebar.php'); ?>
        <main class="flex-1 p-6">
            <div class="container mx-auto">
                <h2 class="text-2xl font-semibold mb-4">Reported Reviews</h2>
                <table class="min-w-full bg-white border border-gray-200 table-small">
                    <thead>
                        <tr>
                            <th class="border-b">Report ID</th>
                            <th class="border-b">Reported Review</th>
                            <th class="border-b">Reason</th>
                            <th class="border-b">Status</th>
                            <th class="border-b">Created At</th>
                            <th class="border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reports): ?>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td class="border-b"><?= htmlspecialchars($report['ReportID']) ?></td>
                                    <td class="border-b"><?= htmlspecialchars($report['Title']) ?></td>
                                    <td class="border-b"><?= htmlspecialchars($report['Reason']) ?></td>
                                    <td class="border-b"><?= htmlspecialchars($report['Status']) ?></td>
                                    <td class="border-b"><?= htmlspecialchars($report['CreatedAt']) ?></td>
                                    <td class="border-b">
                                        <a href="view_review.php?id=<?= htmlspecialchars($report['ReportedReviewID']) ?>" class="text-blue-500 hover:underline text-sm">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-red-500 text-center py-2">No reports found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
