<?php
// friends.php

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

// Fetch the suggested users from session
$suggestedUsers = isset($_SESSION['suggestedUsers']) ? $_SESSION['suggestedUsers'] : [];

// Fetch all users from the database
include '../includes/config.php';
$query = "SELECT UserID, Username, FirstName, LastName, ProfilePicture FROM Users WHERE UserID != :currentUserId";
$stmt = $pdo->prepare($query);
$stmt->execute(['currentUserId' => $_SESSION['user_id']]);
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch friends of the logged-in user
$queryFriends = "SELECT u.UserID, u.Username, u.FirstName, u.LastName, u.ProfilePicture
                  FROM Users u
                  JOIN Friends f ON u.UserID = f.FriendID
                  WHERE f.UserID = :currentUserId";
$stmtFriends = $pdo->prepare($queryFriends);
$stmtFriends->execute(['currentUserId' => $_SESSION['user_id']]);
$friends = $stmtFriends->fetchAll(PDO::FETCH_ASSOC);

// Fetch friend requests
$queryRequests = "SELECT u.UserID, u.Username, u.FirstName, u.LastName, u.ProfilePicture
                  FROM Users u
                  JOIN FriendRequests fr ON u.UserID = fr.RequesterID
                  WHERE fr.RequestedID = :currentUserId AND fr.Status = 'Pending'";
$stmtRequests = $pdo->prepare($queryRequests);
$stmtRequests->execute(['currentUserId' => $_SESSION['user_id']]);
$friendRequests = $stmtRequests->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto mt-16 flex">
    <!-- Sidebar -->
    <div class="w-1/4 bg-white p-4 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Friends Management</h2>
        <ul class="space-y-2">
            <li><a href="all_friends.php" class="text-blue-500 hover:underline">All Friends</a></li>
            <li><a href="friend_requests.php" class="text-blue-500 hover:underline">Friend Requests</a></li>
            <li><a href="friends.php" class="text-blue-500 hover:underline">Suggestions</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="w-3/4 ml-4">
        <!-- Display Friend Requests -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-2xl font-semibold mb-4">Friend Requests</h2>
            <ul class="space-y-4">
                <?php if (!empty($friendRequests)): ?>
                    <?php foreach ($friendRequests as $request): ?>
                        <li class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($request['ProfilePicture']); ?>" class="w-10 h-10 rounded-full" alt="<?php echo htmlspecialchars($request['Username']); ?>">
                                <div>
                                    <span class="text-gray-800"><?php echo htmlspecialchars($request['FirstName']) . ' ' . htmlspecialchars($request['LastName']); ?></span>
                                    <br>
                                    <span class="block text-sm text-gray-500">@<?php echo htmlspecialchars($request['Username']); ?></span>
                                </div>
                            </div>
                            <button onclick="handleRequest(<?php echo htmlspecialchars($request['UserID']); ?>, 'accept')" class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Accept</button>
                            <button onclick="handleRequest(<?php echo htmlspecialchars($request['UserID']); ?>, 'reject')" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600">Reject</button>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No friend requests at the moment.</p>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Display All Users -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">All Users</h2>
            <ul class="space-y-4">
                <?php if (!empty($allUsers)): ?>
                    <?php foreach ($allUsers as $user): ?>
                        <li class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($user['ProfilePicture']); ?>" class="w-10 h-10 rounded-full" alt="<?php echo htmlspecialchars($user['Username']); ?>">
                                <div>
                                    <span class="text-gray-800"><?php echo htmlspecialchars($user['FirstName']) . ' ' . htmlspecialchars($user['LastName']); ?></span>
                                    <br>
                                    <span class="block text-sm text-gray-500">@<?php echo htmlspecialchars($user['Username']); ?></span>
                                </div>
                            </div>
                            <?php if (!in_array($user['UserID'], array_column($friends, 'UserID'))): ?>
                                <button onclick="sendFriendRequest(<?php echo htmlspecialchars($user['UserID']); ?>)" class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Add Friend</button>
                            <?php else: ?>
                                <span class="text-gray-500">Already friends</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No users available.</p>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<script>
function handleRequest(userId, action) {
    fetch('handle_request.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ userId: userId, action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Request ' + (action === 'accept' ? 'accepted' : 'rejected') + ' successfully!');
            location.reload(); // Reload the page to reflect changes
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function sendFriendRequest(userId) {
    fetch('send_request.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ userId: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Friend request sent successfully!');
            location.reload(); // Reload the page to reflect changes
        } else {
            alert('Error: ' + data.message);
        }
    });
}
</script>

</body>
</html>
