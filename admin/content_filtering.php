<?php
// content_filtering.php
include('../includes/config.php'); 

// Fetch reported posts
$queryPosts = "SELECT PostID, UserID, ReportReason, ReportDate FROM Reports WHERE ReportType = 'post'";
$stmtPosts = $pdo->query($queryPosts);
$reportedPosts = $stmtPosts->fetchAll(PDO::FETCH_ASSOC);

// Fetch reported comments
$queryComments = "SELECT CommentID, PostID, UserID, ReportReason, ReportDate FROM Reports WHERE ReportType = 'comment'";
$stmtComments = $pdo->query($queryComments);
$reportedComments = $stmtComments->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Filtering</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include('components/header.php'); ?>
    <div class="flex">
        <?php include('components/sidebar.php'); ?>
        <main class="flex-1 p-6">
            <div class="container mx-auto">
                <h2 class="text-2xl font-semibold mb-6">Reported Content</h2>
                
                <!-- Reported Posts -->
                <h3 class="text-xl font-semibold mb-4">Reported Posts</h3>
                <table class="min-w-full bg-white border border-gray-200 mb-6">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">PostID</th>
                            <th class="py-2 px-4 border-b">UserID</th>
                            <th class="py-2 px-4 border-b">Report Reason</th>
                            <th class="py-2 px-4 border-b">Report Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportedPosts as $report): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['PostID']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['UserID']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['ReportReason']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['ReportDate']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Reported Comments -->
                <h3 class="text-xl font-semibold mb-4">Reported Comments</h3>
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">CommentID</th>
                            <th class="py-2 px-4 border-b">PostID</th>
                            <th class="py-2 px-4 border-b">UserID</th>
                            <th class="py-2 px-4 border-b">Report Reason</th>
                            <th class="py-2 px-4 border-b">Report Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportedComments as $report): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['CommentID']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['PostID']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['UserID']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['ReportReason']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['ReportDate']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
