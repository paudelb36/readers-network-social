<?php
// user_details.php
include('../includes/config.php');

// Check if UserID is set in the URL
if (isset($_GET['id'])) {
    $userID = intval($_GET['id']); // Sanitize input

    // Fetch user details
    $query = "SELECT UserID, Username, Email, PasswordHash, FirstName, LastName, ProfilePicture, Bio, JoinDate, LastLogin, IsAdmin, IsSuspended, SuspensionEndDate, IsBanned, Location, FavoriteGenres, DateOfBirth FROM Users WHERE UserID = :userid";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':userid', $userID, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if user exists
    if (!$user) {
        echo "User not found.";
        exit;
    }
} else {
    echo "No user selected.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <?php include('components/header.php'); ?>
    <div class="flex">
        <?php include('components/sidebar.php'); ?>
        <main class="flex-1 p-6">
            <div class="mt-4">
                <a href="users_info.php" class="inline-flex items-center bg-blue-500 text-white font-semibold py-2 px-4 rounded hover:bg-blue-600 transition duration-200">
                    <!-- Back Icon -->
                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </a>
            </div>

            <div class="container mx-auto">
                <h2 class="text-2xl font-semibold mb-4">User Details</h2>
                <div class="bg-white p-4 rounded shadow flex gap-9">
                <img src="../uploads/profile-pictures/<?php echo htmlspecialchars($user['ProfilePicture']); ?>" alt="Profile Picture" class="w-40 h-40 rounded-full object-cover mr-4">
                <div>
                        <p><strong>Username:</strong> <?php echo htmlspecialchars($user['Username']); ?></p>
                        <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['FirstName']); ?></p>
                        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['LastName']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['Email']); ?></p>
                        <p><strong>Bio:</strong> <?php echo htmlspecialchars($user['Bio']); ?></p>
                        <p><strong>Join Date:</strong> <?php echo htmlspecialchars($user['JoinDate']); ?></p>
                        <p><strong>Is Suspended:</strong> <?php echo htmlspecialchars($user['IsSuspended'] ? 'Yes' : 'No'); ?></p>
                        <p><strong>Suspension End Date:</strong> <?php echo htmlspecialchars($user['SuspensionEndDate']); ?></p>
                        <p><strong>Is Banned:</strong> <?php echo htmlspecialchars($user['IsBanned'] ? 'Yes' : 'No'); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($user['Location']); ?></p>
                        <p><strong>Favorite Genres:</strong> <?php echo htmlspecialchars($user['FavoriteGenres']); ?></p>
                        <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($user['DateOfBirth']); ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
