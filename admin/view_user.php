<?php
// view_user.php
include('../includes/config.php');

// Set the time zone to Kathmandu
date_default_timezone_set('Asia/Kathmandu');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: reports.php");
    exit();
}

$userId = $_GET['id'];

// Fetch user details
$userQuery = "SELECT UserID, Username, Email, FirstName, LastName, IsSuspended, SuspensionEndDate, IsBanned 
              FROM Users WHERE UserID = ?";
$userStmt = $pdo->prepare($userQuery);
$userStmt->execute([$userId]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: reports.php");
    exit();
}

// Fetch reports for this user
$reportsQuery = "SELECT r.ReportID, r.Reason, r.Status, r.CreatedAt, 
                 CONCAT(u.FirstName, ' ', u.LastName) AS ReporterName
                 FROM Reports r
                 JOIN Users u ON r.ReporterID = u.UserID
                 WHERE r.ReportedUserID = ?
                 ORDER BY r.CreatedAt DESC";
$reportsStmt = $pdo->prepare($reportsQuery);
$reportsStmt->execute([$userId]);
$reports = $reportsStmt->fetchAll(PDO::FETCH_ASSOC);

$reportCount = count($reports);

// Handle user status changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case 'suspend':
            $timeValue = $_POST['suspension_time_value'];
            $timeUnit = $_POST['suspension_time_unit'];

            // Calculate the suspension end time based on the selected unit
            $suspensionDuration = "+$timeValue $timeUnit";
            $suspensionEndDate = date('Y-m-d H:i:s', strtotime($suspensionDuration)); // Kathmandu time zone

            $updateQuery = "UPDATE Users SET IsSuspended = 1, SuspensionEndDate = ? WHERE UserID = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([$suspensionEndDate, $userId]);
            break;

        case 'ban':
            $updateQuery = "UPDATE Users SET IsBanned = 1 WHERE UserID = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([$userId]);
            break;

        case 'unsuspend':
            $updateQuery = "UPDATE Users SET IsSuspended = 0, SuspensionEndDate = NULL WHERE UserID = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([$userId]);
            break;

        case 'unban':
            $updateQuery = "UPDATE Users SET IsBanned = 0 WHERE UserID = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([$userId]);
            break;
    }

    // Refresh user data
    $userStmt->execute([$userId]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include('components/header.php'); ?>
    <div class="flex">
        <?php include('components/sidebar.php'); ?>
        <main class="flex-1 p-6">
            <div class="container mx-auto">
                <h2 class="text-2xl font-semibold mb-4">User Details</h2>
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <p><strong>Name:</strong> <?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?></p>
                    <p><strong>Username:</strong> <?= htmlspecialchars($user['Username']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($user['Email']) ?></p>
                    <p><strong>Status:</strong> 
                        <?php
                        if ($user['IsBanned']) {
                            echo 'Banned';
                        } elseif ($user['IsSuspended']) {
                            echo 'Suspended until ' . $user['SuspensionEndDate'];
                        } else {
                            echo 'Active';
                        }
                        ?>
                    </p>
                    <form action="view_user.php?id=<?= $userId ?>" method="POST" class="mt-4">
                        <?php if (!$user['IsBanned'] && !$user['IsSuspended']): ?>
                            <div class="flex items-center mb-4">
                                <label for="suspension_time_value" class="mr-2">Suspend for:</label>
                                <input type="number" name="suspension_time_value" id="suspension_time_value" min="1" required class="border p-2 rounded w-20 mr-2">
                                <select name="suspension_time_unit" id="suspension_time_unit" required class="border p-2 rounded">
                                    <option value="minutes">Minutes</option>
                                    <option value="hours">Hours</option>
                                    <option value="days">Days</option>
                                </select>
                            </div>
                            <button type="submit" name="action" value="suspend" class="bg-yellow-500 text-white rounded px-4 py-2 mr-2">Suspend</button>
                            <button type="submit" name="action" value="ban" class="bg-red-500 text-white rounded px-4 py-2">Ban</button>
                        <?php elseif ($user['IsSuspended']): ?>
                            <button type="submit" name="action" value="unsuspend" class="bg-green-500 text-white rounded px-4 py-2">Unsuspend</button>
                        <?php elseif ($user['IsBanned']): ?>
                            <button type="submit" name="action" value="unban" class="bg-green-500 text-white rounded px-4 py-2">Unban</button>
                        <?php endif; ?>
                    </form>
                </div>

                <h2 class="text-2xl font-semibold mb-4">Reports (<?= $reportCount ?>)</h2>
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="border-b">Report ID</th>
                            <th class="border-b">Reporter</th>
                            <th class="border-b">Reason</th>
                            <th class="border-b">Status</th>
                            <th class="border-b">Created At</th>
                            <th class="border-b">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports as $report): ?>
                            <tr>
                                <td class="border-b"><?= htmlspecialchars($report['ReportID']) ?></td>
                                <td class="border-b"><?= htmlspecialchars($report['ReporterName']) ?></td>
                                <td class="border-b"><?= htmlspecialchars($report['Reason']) ?></td>
                                <td class="border-b"><?= htmlspecialchars($report['Status']) ?></td>
                                <td class="border-b"><?= htmlspecialchars($report['CreatedAt']) ?></td>
                                <td class="border-b">
                                    <form action="change_report_status.php" method="POST" class="inline">
                                        <input type="hidden" name="report_id" value="<?= htmlspecialchars($report['ReportID']) ?>">
                                        <input type="hidden" name="user_id" value="<?= $userId ?>">
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
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
