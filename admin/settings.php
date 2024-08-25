<?php
session_start();
include('../includes/config.php'); // Database configuration

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Redirect to the login page if not logged in
    exit();
}

// Fetch the current admin's information
$adminID = $_SESSION['admin_id'];
$query = "SELECT * FROM Admin WHERE AdminID = :adminID";
$stmt = $pdo->prepare($query);
$stmt->execute(['adminID' => $adminID]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $profilePicture = $admin['ProfilePicture']; // Default to the current profile picture

    // Handle profile picture upload
    if (!empty($_FILES['profile_picture']['name'])) {
        $targetDir = "../uploads/profile-pictures/";
        $fileName = basename($_FILES['profile_picture']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow only certain file types
        $allowedTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array(strtolower($fileType), $allowedTypes)) {
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFilePath)) {
                $profilePicture = $fileName;
            }
        }
    }

    // Update the admin information
    $query = "UPDATE Admin SET Username = :username, Email = :email, FirstName = :first_name, LastName = :last_name, ProfilePicture = :profile_picture WHERE AdminID = :adminID";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'profile_picture' => $profilePicture,
        'adminID' => $adminID
    ]);

    // Optionally handle password change
    if (!empty($_POST['new_password'])) {
        $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $query = "UPDATE Admin SET PasswordHash = :password WHERE AdminID = :adminID";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'password' => $newPassword,
            'adminID' => $adminID
        ]);
    }

    // Refresh the page to show updated data
    header('Location: settings.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body>
    <?php include('components/header.php'); ?>

    <div class="flex">
        <?php include('components/sidebar.php'); ?>

        <!-- Main Content -->
        <main class="flex-1 p-4">
            <div class="container mx-auto">
                <h2 class="text-xl font-semibold mb-4">Account Settings</h2>

                <form action="settings.php" method="POST" enctype="multipart/form-data" class="bg-white p-3 rounded-lg shadow space-y-3">
                    <!-- Profile Picture -->
                    <div>
                        <label for="profile_picture" class="block text-sm font-medium">Profile Picture</label>
                        <input type="file" name="profile_picture" id="profile_picture" class="mt-1 text-sm">
                        <?php if ($admin['ProfilePicture']): ?>
                            <img src="../uploads/profile-pictures/<?php echo $admin['ProfilePicture']; ?>" alt="Profile Picture" class="w-16 h-16 rounded-full mt-2">
                        <?php endif; ?>
                    </div>

                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium">Username</label>
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($admin['Username']); ?>" class="mt-1 p-1 border rounded w-full text-sm" required>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium">Email</label>
                        <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($admin['Email']); ?>" class="mt-1 p-1 border rounded w-full text-sm" required>
                    </div>

                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium">First Name</label>
                        <input type="text" name="first_name" id="first_name" value="<?php echo htmlspecialchars($admin['FirstName']); ?>" class="mt-1 p-1 border rounded w-full text-sm">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium">Last Name</label>
                        <input type="text" name="last_name" id="last_name" value="<?php echo htmlspecialchars($admin['LastName']); ?>" class="mt-1 p-1 border rounded w-full text-sm">
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium">New Password</label>
                        <input type="password" name="new_password" id="new_password" class="mt-1 p-1 border rounded w-full text-sm">
                        <small class="text-gray-500">Leave blank if you don't want to change the password.</small>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-1 px-4 rounded text-sm">Update Settings</button>
                </form>
            </div>
        </main>
    </div>
</body>

</html>
