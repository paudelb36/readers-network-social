<?php
// all_friends.php

session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
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

    <div class="container mx-auto mt-16">
    <a href="friends.php" class="inline-block mb-4 px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Back to Friends</a> <!-- Back button -->

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold mb-4">Your Friends</h2>
            <ul class="space-y-4">
                <?php if (!empty($friends)): ?>
                    <?php foreach ($friends as $friend): ?>
                        <li class="flex items-center">
                            <a href="profile.php?user_id=<?php echo htmlspecialchars($friend['UserID']); ?>" class="flex items-center space-x-4">
                                <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($friend['ProfilePicture']); ?>" class="w-10 h-10 rounded-full" alt="<?php echo htmlspecialchars($friend['Username']); ?>">
                                <div class="ml-4">
                                    <span class="text-gray-800"><?php echo htmlspecialchars($friend['FirstName']) . ' ' . htmlspecialchars($friend['LastName']); ?></span>
                                    <br>
                                    <span class="block text-sm text-gray-500">@<?php echo htmlspecialchars($friend['Username']); ?></span>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No friends added yet.</p>
                <?php endif; ?>
            </ul>

        </div>
    </div>

</body>

</html>