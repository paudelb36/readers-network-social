<?php
include '../includes/config.php';
session_start();

$userId = $_SESSION['user_id'];

// Fetch notifications for the logged-in user
$stmt = $pdo->prepare("
    SELECT n.*, u.Username, u.FirstName, u.LastName, u.ProfilePicture
    FROM Notifications n
    JOIN Users u ON n.ActorID = u.UserID
    WHERE n.RecipientID = :userId
    ORDER BY n.CreatedAt DESC
    LIMIT 10
");
$stmt->execute(['userId' => $userId]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body class="bg-gray-100">
    <?php include '../includes/header.php'; ?>

    <div class="container mx-auto mt-20 max-w-2xl">
        <a href="index.php" class="inline-block mb-4 px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Back to Home</a> <!-- Back button -->

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-semibold">Notifications <span class="bg-blue-500 text-white rounded-full px-2 py-1 text-sm"><?php echo count($notifications); ?></span></h1>
                <a href="./mark_as_read.php" class="text-blue-500 text-sm">Mark all as read</a>
            </div>

            <ul>
                <?php if (empty($notifications)): ?>
                    <li class="py-3 text-gray-500">No notifications found.</li>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <li class="flex items-center py-3 border-b last:border-b-0">
                            <img src="<?php echo htmlspecialchars($notification['ProfilePicture']); ?>" alt="Profile" class="w-10 h-10 rounded-full mr-3">
                            <div class="flex-grow">
                                <p class="text-sm">
                                    <span class="font-semibold"><?php echo htmlspecialchars($notification['FirstName'] . ' ' . $notification['LastName']); ?></span>
                                    <?php
                                    switch ($notification['Type']) {
                                        case 'reaction':
                                            echo "reacted to your recent post";
                                            break;
                                        case 'follow':
                                            echo "followed you";
                                            break;
                                        case 'group_join':
                                            echo "has joined your group";
                                            break;
                                        case 'message':
                                            echo "sent you a private message";
                                            break;
                                        case 'comment':
                                            echo "commented on your picture";
                                            break;
                                        case 'friend_request':
                                            echo "sent you a friend request";
                                            break;
                                        default:
                                            echo "interacted with your content";
                                    }
                                    ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?php
                                    $time = new DateTime($notification['CreatedAt']);
                                    $now = new DateTime();
                                    $interval = $time->diff($now);
                                    if ($interval->d > 0) {
                                        echo $interval->d . " day" . ($interval->d > 1 ? "s" : "") . " ago";
                                    } elseif ($interval->h > 0) {
                                        echo $interval->h . "h ago";
                                    } else {
                                        echo $interval->i . "m ago";
                                    }
                                    ?>
                                </p>
                                <?php if ($notification['Type'] == 'message' && !empty($notification['Content'])): ?>
                                    <p class="text-sm mt-1 bg-gray-100 p-2 rounded"><?php echo htmlspecialchars($notification['Content']); ?></p>
                                <?php endif; ?>
                            </div>
                            <form action="delete_notification.php" method="POST">
                                <input type="hidden" name="notification_id" value="<?php echo $notification['NotificationID']; ?>">
                                <button type="submit" class="text-red-500 hover:underline">
                                    <i class="fas fa-trash"></i> <!-- Trash icon -->
                                </button>
                            </form>

                            <?php if ($notification['Type'] == 'comment'): ?>
                                <img src="path_to_commented_picture.jpg" alt="Commented Picture" class="w-12 h-12 object-cover rounded">
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>

</html>