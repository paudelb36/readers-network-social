<?php
include '../includes/config.php'; // Include your database configuration
session_start(); // Start the session

$userId = $_SESSION['user_id']; // Logged-in user ID

// Fetch incoming friend requests
$stmt = $pdo->prepare("SELECT * FROM FriendRequests WHERE RequestedID = :userId AND Status = 'Pending'");
$stmt->execute(['userId' => $userId]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friend Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?> <!-- Include the header -->

    <div class="container mx-auto mt-20">
        <a href="friends.php" class="inline-block mb-4 px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Back to Friends</a> <!-- Back button -->

        <div class="bg-white p-6 rounded-lg shadow-md mb-6">
            <h2 class="text-2xl font-semibold mb-4">Friend Requests</h2>
            <ul class="space-y-4">
                <?php if (!empty($requests)): ?>
                    <?php foreach ($requests as $request): ?>
                        <?php
                        // Fetch user data for requester
                        $stmt = $pdo->prepare("SELECT Username, FirstName, LastName, ProfilePicture FROM Users WHERE UserID = ?");
                        $stmt->execute([$request['RequesterID']]);
                        $requester = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <li class="flex items-center justify-between p-4 bg-white shadow-md rounded-md">
                            <div class="flex items-center space-x-4">
                                <a href="profile.php?user_id=<?php echo htmlspecialchars($request['RequesterID']); ?>" class="flex items-center space-x-4">
                                    <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($requester['ProfilePicture']); ?>" class="w-10 h-10 rounded-full" alt="<?php echo htmlspecialchars($requester['Username']); ?>">
                                    <div>
                                        <span class="text-gray-800"><?php echo htmlspecialchars($requester['FirstName']) . ' ' . htmlspecialchars($requester['LastName']); ?></span>
                                        <br>
                                        <span class="block text-sm text-gray-500">@<?php echo htmlspecialchars($requester['Username']); ?></span>
                                    </div>
                                </a>
                            </div>
                            <div class="flex space-x-1"> <!-- Flex container for buttons -->
                                <form action="handle_request.php" method="POST" class="inline-block">
                                    <input type="hidden" name="requester_id" value="<?php echo htmlspecialchars($request['RequesterID']); ?>">
                                    <button type="submit" name="action" value="accept" class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-700">Accept</button>
                                </form>
                                <form action="handle_request.php" method="POST" class="inline-block ml-2">
                                    <input type="hidden" name="requester_id" value="<?php echo htmlspecialchars($request['RequesterID']); ?>">
                                    <button type="submit" name="action" value="reject" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600">Reject</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-600">No friend requests.</p>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>
</html>
