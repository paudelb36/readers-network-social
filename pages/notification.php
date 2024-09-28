<?php
//notification.php 
include '../includes/config.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch notifications for the logged-in user, including the `IsRead` status
$stmt = $pdo->prepare("
    SELECT n.*, u.Username, u.FirstName, u.LastName, u.ProfilePicture, b.Title as BookTitle, r.ReviewID
    FROM Notifications n
    JOIN Users u ON n.ActorID = u.UserID
    LEFT JOIN Reviews r ON n.Type = 'reviews' AND r.UserID = n.ActorID
    LEFT JOIN Books b ON r.BookID = b.BookID
    WHERE n.RecipientID = :userId
    ORDER BY n.CreatedAt DESC
    LIMIT 10
");

$stmt->execute(['userId' => $userId]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retrieve the count of unread notifications
$stmt = $pdo->prepare("
    SELECT COUNT(*) as unread_count
    FROM Notifications
    WHERE RecipientID = :userId AND IsRead = 0
");
$stmt->execute(['userId' => $userId]);
$unreadNotification = $stmt->fetch(PDO::FETCH_ASSOC);
$unreadCount = $unreadNotification['unread_count'];
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
        <a href="index.php" class="inline-block mb-4 px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Back to Home</a>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-xl font-semibold">
                    Notifications
                    <span class="bg-blue-500 text-white rounded-full px-2 py-1 text-sm"><?php echo count($notifications); ?></span>
                </h1>
                <a href="./mark_as_read.php" class="text-blue-500 text-sm">Mark all as read</a>
            </div>

            <ul>
                <?php if (empty($notifications)): ?>
                    <li class="py-3 text-gray-500">No notifications found.</li>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <li class="flex items-center py-3 border-b last:border-b-0 <?php echo $notification['IsRead'] ? 'bg-gray-50' : 'bg-blue-100'; ?>">
                            <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($notification['ProfilePicture']); ?>" alt="Profile" class="w-10 h-10 rounded-full mr-3">
                            <div class="flex-grow">
                                <p class="text-sm">
                                    <span class="font-semibold"><?php echo htmlspecialchars($notification['FirstName'] . ' ' . $notification['LastName']); ?></span>
                                    <?php
                                    switch ($notification['Type']) {
                                        case 'reaction':
                                            echo "reacted to your review";
                                            break;
                                        case 'comment':
                                            echo "commented on your review";
                                            break;
                                        case 'friend_request':
                                            echo "<a href='view_requests.php' class='text-black-500 hover:underline'>sent you a friend request</a>";
                                            break;
                                        case 'reviews':
                                            echo "<a href='view_review.php?review_id=" . htmlspecialchars($notification['ReviewID']) . "' class='text-black-500 hover:underline'>posted a new review for the book '" . htmlspecialchars($notification['BookTitle']) . "'</a>";
                                            break;
                                    }
                                    ?>
                                </p>
                                <p class="text-xs text-gray-500">
                                    <?php
                                    $time = new DateTime($notification['CreatedAt'], new DateTimeZone('Asia/Kathmandu'));
                                    $now = new DateTime('now', new DateTimeZone('Asia/Kathmandu'));
                                    $interval = $time->diff($now);
                                    if ($interval->d > 0) {
                                        echo $interval->d . " day" . ($interval->d > 1 ? "s" : "") . " ago";
                                    } elseif ($interval->h > 0) {
                                        echo $interval->h . "h ago";
                                    } elseif ($interval->i > 0) {
                                        echo $interval->i . "m ago";
                                    } else {
                                        echo "Just now";
                                    }
                                    ?>
                                </p>
                                <!-- <?php if ($notification['Type'] == 'comment' && !empty($notification['Content'])): ?>
                                    <p class="text-sm mt-1 bg-gray-100 p-2 rounded"><?php echo htmlspecialchars($notification['Content']); ?></p>
                                <?php endif; ?> -->
                            </div>
                            <form action="delete_notification.php" method="POST">
                                <input type="hidden" name="notification_id" value="<?php echo $notification['NotificationID']; ?>">
                                <button type="submit" class="text-red-500 hover:underline">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>


                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</body>

</html>