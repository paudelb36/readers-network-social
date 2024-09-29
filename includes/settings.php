<?php
session_start();
include('../includes/config.php');
include('../includes/header.php'); // Include the header file

// Check if the user is logged in
$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

if (!$loggedIn) {
    header('Location: ../login.php');
    exit();
}

$UserID = $_SESSION['user_id'];
$successMessage = '';
$errorMessage = '';

// Fetch user data from the database
$query = "SELECT PasswordHash FROM users WHERE UserID = :userID";
$stmt = $pdo->prepare($query);
$stmt->execute(['userID' => $UserID]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['updatePassword'])) {
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        // Verify current password
        if (password_verify($currentPassword, $user['PasswordHash'])) {
            // Check if new password matches confirm password
            if ($newPassword === $confirmPassword) {
                // Hash new password and update in database
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updatePasswordQuery = "UPDATE users SET PasswordHash = :newPasswordHash WHERE UserID = :userID";
                $stmt = $pdo->prepare($updatePasswordQuery);
                $stmt->execute(['newPasswordHash' => $newPasswordHash, 'userID' => $UserID]);

                if ($stmt) {
                    $successMessage = "Password updated successfully.";
                } else {
                    $errorMessage = "Error updating password.";
                }
            } else {
                $errorMessage = "New passwords do not match.";
            }
        } else {
            $errorMessage = "Current password is incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Change Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" media="print" onload="this.media='all'">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-4 px-4 py-12">
        <!-- Success/Error Messages -->
        <?php if ($successMessage): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                <p class="font-bold">Success</p>
                <p><?php echo $successMessage; ?></p>
            </div>
        <?php elseif ($errorMessage): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Error</p>
                <p><?php echo $errorMessage; ?></p>
            </div>
        <?php endif; ?>

        <!-- Change Password Form -->
        <div class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-md">
            <h1 class="text-2xl font-semibold text-gray-700 mb-6">Change Password</h1>

            <form action="settings.php" method="POST" class="space-y-4">
                <div>
                    <label for="currentPassword" class="block text-gray-600 font-medium">Current Password:</label>
                    <input type="password" name="currentPassword" id="currentPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label for="newPassword" class="block text-gray-600 font-medium">New Password:</label>
                    <input type="password" name="newPassword" id="newPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div>
                    <label for="confirmPassword" class="block text-gray-600 font-medium">Confirm New Password:</label>
                    <input type="password" name="confirmPassword" id="confirmPassword" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <button type="submit" name="updatePassword" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200">Change Password</button>
            </form>

            <!-- Link to Edit Profile Page -->
            <div class="mt-6 text-center">
                <a href="../pages/edit_profile.php" class="text-blue-500 hover:underline">Edit Profile Information</a>
            </div>
        </div>
    </div>
</body>
</html>
