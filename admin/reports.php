<?php
// reports.php
include('../includes/config.php'); 

// Fetch reported users
$query = "SELECT UserID, Username, ReportReason, ReportDate FROM Reports WHERE ReportType = 'user'";
$stmt = $pdo->query($query);
$reportedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include('components/header.php'); ?>
    <div class="flex">
        <?php include('components/sidebar.php'); ?>
        <main class="flex-1 p-6">
            <div class="container mx-auto">
                <h2 class="text-2xl font-semibold mb-6">Reported Users</h2>
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">UserID</th>
                            <th class="py-2 px-4 border-b">Username</th>
                            <th class="py-2 px-4 border-b">Report Reason</th>
                            <th class="py-2 px-4 border-b">Report Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportedUsers as $report): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['UserID']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($report['Username']); ?></td>
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
