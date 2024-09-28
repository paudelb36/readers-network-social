<?php
// reports.php
include('../includes/config.php');

// Fetch reported users
$query = "SELECT r.ReportID, r.ReportedUserID, r.Reason, r.Status as ReportStatus, r.CreatedAt, 
          CONCAT(u.FirstName, ' ', u.LastName) AS FullName, 
          u.IsSuspended, u.SuspensionEndDate, u.IsBanned
          FROM Reports r 
          JOIN Users u ON r.ReportedUserID = u.UserID";
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
                <h2 class="text-2xl font-semibold mb-4">Reported Users</h2>
                <table class="min-w-full bg-white border border-gray-200 table-small">
                    <thead>
                        <tr>
                            <th class="border-b">Report ID</th>
                            <th class="border-b">Reported User</th>
                            <th class="border-b">Reason</th>
                            <th class="border-b">Report Status</th>
                            <th class="border-b">User Status</th>
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
                                    <td class="border-b"><?= htmlspecialchars($report['ReportStatus']) ?></td>
                                    <td class="border-b">
                                        <?php
                                        if ($report['IsBanned']) {
                                            echo 'Banned';
                                        } elseif ($report['IsSuspended']) {
                                            echo 'Suspended until ' . $report['SuspensionEndDate'];
                                        } else {
                                            echo 'Active';
                                        }
                                        ?>
                                    </td>
                                    <td class="border-b"><?= htmlspecialchars($report['CreatedAt']) ?></td>
                                    <td class="border-b">
                                        <a href="view_user.php?id=<?= htmlspecialchars($report['ReportedUserID']) ?>" class="bg-blue-500 text-white rounded px-4 py-2 text-sm">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-red-500 text-center py-2">No reports found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>