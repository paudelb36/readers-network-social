<?php
// sent_requests.php

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

// Include the database configuration
include '../includes/config.php';

// Fetch current user's ID from session
$currentUserId = $_SESSION['user_id'];

// Fetch sent friend requests by the logged-in user
$querySentRequests = "SELECT u.UserID, u.Username, u.FirstName, u.LastName, u.ProfilePicture
                      FROM Users u
                      JOIN FriendRequests fr ON u.UserID = fr.RequestedID
                      WHERE fr.RequesterID = :currentUserId AND fr.Status = 'Pending'";
$stmt = $pdo->prepare($querySentRequests);
$stmt->execute(['currentUserId' => $currentUserId]);
$sentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_request_id'])) {
    // Handle cancellation of the friend request
    $cancelRequestId = $_POST['cancel_request_id'];
    
    // SQL query to cancel the request
    $queryCancelRequest = "DELETE FROM FriendRequests WHERE RequesterID = :requesterId AND RequestedID = :requestedId AND Status = 'Pending'";
    $stmtCancel = $pdo->prepare($queryCancelRequest);
    $stmtCancel->execute(['requesterId' => $currentUserId, 'requestedId' => $cancelRequestId]);
    
    // Redirect to the same page to refresh the requests list
    header('Location: sent_requests.php');
    exit();
}

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sent Friend Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <div class="container mx-auto mt-16">
        <a href="friends.php" class="inline-block mb-4 px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Back to Friends</a> <!-- Back button -->

        <!-- Display Sent Friend Requests -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Sent Friend Requests</h2>
            <ul class="space-y-4">
                <?php if (!empty($sentRequests)): ?>
                    <?php foreach ($sentRequests as $user): ?>
                        <li class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <a href="profile.php?user_id=<?php echo htmlspecialchars($user['UserID']); ?>" class="flex items-center space-x-4">
                                    <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($user['ProfilePicture']); ?>" class="w-10 h-10 rounded-full" alt="<?php echo htmlspecialchars($user['Username']); ?>">
                                    <div>
                                        <span class="text-gray-800"><?php echo htmlspecialchars($user['FirstName']) . ' ' . htmlspecialchars($user['LastName']); ?></span>
                                        <br>
                                        <span class="block text-sm text-gray-500">@<?php echo htmlspecialchars($user['Username']); ?></span>
                                    </div>
                                </a>
                            </div>
                            <form action="sent_requests.php" method="POST" class="inline">
                                <input type="hidden" name="cancel_request_id" value="<?php echo htmlspecialchars($user['UserID']); ?>">
                                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600">Cancel Request</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You have not sent any friend requests yet.</p>
                <?php endif; ?>
            </ul>
        </div>
    </div>

</body>

</html>
