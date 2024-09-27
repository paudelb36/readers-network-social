<?php
session_start(); // Start the session
include '../includes/config.php'; // Adjust the path based on your file structure

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}
include '../includes/header.php';

// Fetch user info from the database
$userId = $_SESSION['user_id']; // Get logged-in user's ID
$queryUser = "SELECT * FROM users WHERE UserID = ?";
$stmtUser = $pdo->prepare($queryUser);
$stmtUser->bindParam(1, $userId, PDO::PARAM_INT);
$stmtUser->execute();

if ($stmtUser->rowCount() === 0) {
    echo "User not found.";
    exit();
}

$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission, perform validations and updates to the user profile
    $username = $_POST['username'];
    $email = $_POST['email'];
    $bio = $_POST['bio'];
    $location = $_POST['location'];
    $favoriteGenres = $_POST['favorite_genres'];
    $dateOfBirth = $_POST['date_of_birth'];

    // Handle file upload for profile picture
    $profilePicture = $_FILES['profile_picture']['name'];
    if ($profilePicture) {
        $targetDir = "../uploads/profile-pictures/";
        $targetFile = $targetDir . basename($profilePicture);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile);
    } else {
        $profilePicture = $user['ProfilePicture']; // Keep the old picture if no new one is uploaded
    }

    // Update user information in the database
    $queryUpdate = "UPDATE users SET Username = ?, Email = ?, Bio = ?, Location = ?, FavoriteGenres = ?, DateOfBirth = ?, ProfilePicture = ? WHERE UserID = ?";
    $stmtUpdate = $pdo->prepare($queryUpdate);
    $stmtUpdate->execute([$username, $email, $bio, $location, implode(',', $favoriteGenres), $dateOfBirth, $profilePicture, $userId]);

    // Redirect back to profile after updating
    header('Location: profile.php?user_id=' . $userId);
    exit();
}

// Split favorite genres for the select
$selectedGenres = explode(',', $user['FavoriteGenres']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" media="print" onload="this.media='all'">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <link rel="stylesheet" href="../assets/css/main.css">


</head>

<body class="bg-gray-100">

    <div class="max-w-7xl mx-auto p-6 mt-10"> <!-- Add mt-6 for top margin -->
        <h1 class="text-2xl font-bold mb-6">Edit Profile</h1>
        <a href="profile.php" class="inline-block mb-4 px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">Back to Profile</a> <!-- Back button -->

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
                <textarea name="bio" id="bio" rows="2" class="mt-1 block w-full p-2 border border-gray-300 rounded-md"><?php echo htmlspecialchars($user['Bio']); ?></textarea>
            </div>
            <div>
                <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                <input type="text" name="location" id="location" value="<?php echo htmlspecialchars($user['Location']); ?>" class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
                <label for="favorite_genres" class="block mb-2 text-sm font-medium text-gray-900">Favorite Genres</label>
                <select id="favorite_genres" name="favorite_genres[]" multiple="multiple" class="w-full p-2.5 border border-gray-300 rounded-lg">
                    <option value="Fantasy" <?php echo in_array('Fantasy', $selectedGenres) ? 'selected' : ''; ?>>Fantasy</option>
                    <option value="Science Fiction" <?php echo in_array('Science Fiction', $selectedGenres) ? 'selected' : ''; ?>>Science Fiction</option>
                    <option value="Romance" <?php echo in_array('Romance', $selectedGenres) ? 'selected' : ''; ?>>Romance</option>
                    <option value="Thriller" <?php echo in_array('Thriller', $selectedGenres) ? 'selected' : ''; ?>>Thriller</option>
                    <option value="Mystery" <?php echo in_array('Mystery', $selectedGenres) ? 'selected' : ''; ?>>Mystery</option>
                    <option value="Non-fiction" <?php echo in_array('Non-fiction', $selectedGenres) ? 'selected' : ''; ?>>Non-fiction</option>
                    <option value="Horror" <?php echo in_array('Horror', $selectedGenres) ? 'selected' : ''; ?>>Horror</option>
                    <option value="Historical Fiction" <?php echo in_array('Historical Fiction', $selectedGenres) ? 'selected' : ''; ?>>Historical Fiction</option>
                </select>
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
    <script>
        new TomSelect('#favorite_genres', {
            plugins: ['remove_button'], // Adds a remove button to each selected item
            create: false, // Disallow user-created options
            maxItems: null, // Allow selection of any number of items
            placeholder: 'Select your favorite genres...', // Placeholder text
            hideSelected: true, // Hide selected items from the dropdown
            closeAfterSelect: false, // Keep the dropdown open after selecting an item
            render: {
                item: function(data, escape) {
                    return `<div class="genre-item">${escape(data.text)}</div>`;
                },
                option: function(data, escape) {
                    return `<div class="genre-option">${escape(data.text)}</div>`;
                }
            }
        });
    </script>

</body>

</html>