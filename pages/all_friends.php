<?php
// all_friends.php

session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

// Handle unfriend action
if (isset($_POST['unfriend']) && isset($_POST['friend_id'])) {
    $friendId = $_POST['friend_id'];
    $currentUserId = $_SESSION['user_id'];
    
    $unfriendQuery = "DELETE FROM Friends WHERE (UserID = :currentUserId AND FriendID = :friendId) OR (UserID = :friendId AND FriendID = :currentUserId)";
    $unfriendStmt = $pdo->prepare($unfriendQuery);
    $unfriendStmt->execute(['currentUserId' => $currentUserId, 'friendId' => $friendId]);
    
    // Redirect to refresh the page
    header('Location: all_friends.php');
    exit();
}

// Fetch all friends of the logged-in user
$currentUserId = $_SESSION['user_id'];
$query = "SELECT u.UserID, u.Username, u.FirstName, u.LastName, u.ProfilePicture
          FROM Users u
          JOIN Friends f ON (u.UserID = f.FriendID)
          WHERE f.UserID = :currentUserId";
$stmt = $pdo->prepare($query);
$stmt->execute(['currentUserId' => $currentUserId]);
$friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Friends</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-9 px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <a href="friends.php" class="inline-block mb-6 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300">Back to Friends</a>

            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-3xl font-semibold mb-6 text-gray-800">Your Friends</h2>
                <?php if (!empty($friends)): ?>
                    <ul class="space-y-6">
                        <?php foreach ($friends as $friend): ?>
                            <li class="flex items-center justify-between bg-gray-50 p-4 rounded-lg hover:bg-gray-100 transition duration-300">
                                <a href="profile.php?user_id=<?php echo htmlspecialchars($friend['UserID']); ?>" class="flex items-center space-x-4">
                                    <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($friend['ProfilePicture']); ?>" class="w-16 h-16 rounded-full border-2 border-blue-500" alt="<?php echo htmlspecialchars($friend['Username']); ?>">
                                    <div>
                                        <span class="text-xl font-medium text-gray-800"><?php echo htmlspecialchars($friend['FirstName']) . ' ' . htmlspecialchars($friend['LastName']); ?></span>
                                        <span class="block text-sm text-gray-500">@<?php echo htmlspecialchars($friend['Username']); ?></span>
                                    </div>
                                </a>
                                <form method="post" onsubmit="return confirm('Are you sure you want to unfriend this person?');">
                                    <input type="hidden" name="friend_id" value="<?php echo htmlspecialchars($friend['UserID']); ?>">
                                    <button type="submit" name="unfriend" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition duration-300">Unfriend</button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-gray-600 text-center py-8">No friends added yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>