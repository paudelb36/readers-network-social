<?php
// reports.php
include('../includes/config.php'); 

// Fetch reported users
$query = "SELECT r.ReportID, r.ReportedUserID, r.Reason, r.Status, r.CreatedAt, 
          CONCAT(u.FirstName, ' ', u.LastName) AS FullName 
          FROM Reports r 
          JOIN Users u ON r.ReportedUserID = u.UserID"; // Assuming UserID is the primary key for Users
$stmt = $pdo->query($query);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reported Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Custom styles for smaller font and table */
        .table-small {
            font-size: 0.875rem; /* Smaller font size */
        }
        .table-small th, .table-small td {
            padding: 0.5rem; /* Less padding */
        }
    </style>
</head>
<body>
    <?php include('components/header.php'); ?>
    <div class="flex">
        <?php include('components/sidebar.php'); ?>
        <main class="flex-1 p-6">
            <div class="container mx-auto">
                <h2 class="text-2xl font-semibold mb-4">Reported Users</h2>
                <table class="min-w-full bg-white border border-gray-200 table-small">
                    <thead>
                        <tr>
                            <th class="border-b">Report ID</th>
                            <th class="border-b">Reported User</th>
                            <th class="border-b">Reason</th>
                            <th class="border-b">Status</th>
                            <th class="border-b">Created At</th>
                            <th class="border-b">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($reports): ?>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td class="border-b"><?= htmlspecialchars($report['ReportID']) ?></td>
                                    <td class="border-b"><?= htmlspecialchars($report['FullName']) ?></td>
                                    <td class="border-b"><?= htmlspecialchars($report['Reason']) ?></td>
                                    <td class="border-b"><?= htmlspecialchars($report['Status']) ?></td>
                                    <td class="border-b"><?= htmlspecialchars($report['CreatedAt']) ?></td>
                                    <td class="border-b">
                                        <a href="view_user.php?id=<?= htmlspecialchars($report['ReportedUserID']) ?>" class="text-blue-500 hover:underline text-sm">View</a>
                                        <form action="change_user_status.php" method="POST" class="inline">
                                            <input type="hidden" name="report_id" value="<?= htmlspecialchars($report['ReportID']) ?>">
                                            <select name="status" class="border rounded p-1 text-sm">
                                                <option value="Pending" <?= $report['Status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="Resolved" <?= $report['Status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                                                <option value="Rejected" <?= $report['Status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                                            </select>
                                            <button type="submit" class="bg-blue-500 text-white rounded px-2 py-1 ml-2 text-sm">Update</button>
                                        </form>
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
