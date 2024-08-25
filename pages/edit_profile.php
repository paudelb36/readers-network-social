<?php
require_once '../includes/config.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header('Location: login.php');
    exit();
}

// Fetch user data for editing
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT Username, Email, ProfilePicture, Bio, Location, FavoriteGenres, DateOfBirth FROM Users WHERE UserID = ?');
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);
    $location = trim($_POST['location']);
    $favoriteGenres = trim($_POST['favorite_genres']);
    $dateOfBirth = trim($_POST['date_of_birth']);

    // Initialize profile picture filename
    $profilePictureFilename = $user['ProfilePicture'];

   
    $response = array('status' => 'error', 'message' => 'An unknown error occurred.');
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = time() . '_' . $_FILES['profile_picture']['name']; // Use a timestamp to avoid filename conflicts
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
    
        // Define allowed file types and size limit
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
    
        if (in_array($fileType, $allowedFileTypes) && $fileSize <= $maxFileSize) {
            // Move the uploaded file to the desired directory
            $uploadDir = '../uploads/profile-pictures/';
            $destPath = $uploadDir . $fileName;
    
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Update profile picture path in the database
                $profilePictureFilename = $fileName;
                $response['status'] = 'success';
                $response['message'] = 'Profile picture uploaded successfully.';
            } else {
                $response['message'] = 'Failed to move uploaded file.';
            }
        } else {
            $response['message'] = 'Invalid file type or size.';
        }
    } else {
        $response['message'] = 'No file uploaded or upload error.';
    }
    
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    
    

    // Update user details
    $stmt = $pdo->prepare('UPDATE Users SET Username = ?, Email = ?, Bio = ?, Location = ?, FavoriteGenres = ?, DateOfBirth = ?, ProfilePicture = ? WHERE UserID = ?');
    if ($stmt->execute([$username, $email, $bio, $location, $favoriteGenres, $dateOfBirth, $profilePictureFilename, $userId])) {
        echo "Profile updated successfully.";
    } else {
        echo "Failed to update profile.";
    }

    // Redirect to the profile page
    header("Location: profile.php?user_id=" . $userId);
}
// Include the header (optional)
include '../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<div class="container mx-auto mt-10">
    <div class="bg-white shadow-md rounded-lg p-6">
        <h1 class="text-2xl font-semibold">Edit Profile</h1>
        <form action="edit_profile.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['Username']); ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['Email']); ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md" required>
            </div>
            <div>
                <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                <textarea name="bio" id="bio" rows="4" class="mt-1 block w-full p-2 border border-gray-300 rounded-md"><?php echo htmlspecialchars($user['Bio']); ?></textarea>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($user['Location']); ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="favorite_genres" class="block text-sm font-medium text-gray-700">Favorite Genres</label>
                <input type="text" name="favorite_genres" id="favorite_genres" value="<?php echo htmlspecialchars($user['FavoriteGenres']); ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo htmlspecialchars($user['DateOfBirth']); ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                <input type="file" name="profile_picture" id="profile_picture" class="mt-1 block w-full border border-gray-300 rounded-md">
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Update Profile</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
