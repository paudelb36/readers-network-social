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
<body>
    <div class="container mx-auto mt-10">
        <h1 class="text-2xl font-semibold mb-4">Friend Requests</h1>
        <?php if (!empty($requests)): ?>
            <ul>
                <?php foreach ($requests as $request): ?>
                    <?php
                    // Fetch user data for requester
                    $stmt = $pdo->prepare("SELECT Username, FirstName, LastName FROM Users WHERE UserID = ?");
                    $stmt->execute([$request['RequesterID']]);
                    $requester = $stmt->fetch(PDO::FETCH_ASSOC);
                    ?>
                    <li class="flex items-center justify-between p-4 mb-4 bg-white shadow-md rounded-md">
                        <div class="flex items-center">
                            <span class="font-semibold"><?php echo htmlspecialchars($requester['FirstName']) . ' ' . htmlspecialchars($requester['LastName']); ?></span>
                            <span class="text-gray-600">@<?php echo htmlspecialchars($requester['Username']); ?></span>
                        </div>
                        <div>
                            <form action="handle_request.php" method="POST" class="inline-block">
                                <input type="hidden" name="requester_id" value="<?php echo htmlspecialchars($request['RequesterID']); ?>">
                                <button type="submit" name="action" value="accept" class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600">Accept</button>
                            </form>
                            <form action="handle_request.php" method="POST" class="inline-block ml-2">
                                <input type="hidden" name="requester_id" value="<?php echo htmlspecialchars($request['RequesterID']); ?>">
                                <button type="submit" name="action" value="reject" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600">Reject</button>
                            </form>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No friend requests.</p>
        <?php endif; ?>
    </div>
</body>
</html>
