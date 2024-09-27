<?php
include '../includes/config.php';

// Ensure the user is logged in
$loggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];
$fullName = ''; // Initialize $fullName

// Set $unreadCount to 0 by default
$unreadCount = 0;

if ($loggedIn) {
    $userId = $_SESSION['user_id'];

    // Fetch the user's details
    $stmt = $pdo->prepare("SELECT FirstName, LastName FROM users WHERE UserID = :userId");
    $stmt->execute(['userId' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $fullName = $user['FirstName'] . ' ' . $user['LastName'];
        $_SESSION['full_name'] = $fullName;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Readers Network</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.1/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="bg-white shadow-md fixed top-0 left-0 right-0 z-10">
        <div class="container mx-auto px-4 py-2 flex items-center justify-between">
            <a href="./index.php" class="flex items-center space-x-2">
                <span class="book-logo">📚</span>
                <span class="text-lg font-semibold">Readers Network</span>
            </a>
            <div class="flex items-center space-x-4">
                <a href="./index.php" class="text-gray-600 hover:text-gray-900">Home</a>

                <a
                    href="<?php echo $loggedIn ? './friends.php' : '#'; ?>"
                    class="text-gray-600 hover:text-gray-900"
                    onclick="return <?php echo $loggedIn ? 'true' : 'showLoginAlert()'; ?>">
                    Friends
                </a>

                <a
                    href="<?php echo $loggedIn ? './notification.php' : '#'; ?>"
                    class="relative text-gray-600 hover:text-gray-900"
                    onclick="return <?php echo $loggedIn ? 'true' : 'showLoginAlert()'; ?>">
                    Notifications
                </a>



                <a href="./trending.php" class="text-gray-600 hover:text-gray-900">Trendings</a>
            </div>

            <script>
                function showLoginAlert() {
                    alert("Please log in to access this feature.");
                    return false; // Prevents the default link behavior
                }
            </script>

            <div class="relative">
                <?php if ($loggedIn): ?>
                    <form class="flex items-center">
                        <input type="search" placeholder="Search" class="border border-gray-300 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="submit" class="ml-2 px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Search</button>
                    </form>
                <?php endif; ?>
            </div>


            <!-- Profile or Login Button -->
            <?php if ($loggedIn): ?>
                <!-- Profile Dropdown -->
                <div class="relative" id="profileDropdown">
                    <button class="flex items-center space-x-2 text-gray-600 hover:text-gray-900" id="profileButton">
                        <span><?= htmlspecialchars($fullName); ?></span> <!-- Display the full name -->
                        <svg class="profile-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white shadow-lg rounded-lg hidden" id="dropdownContent">
                        <a href="./profile.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Profile</a>
                        <a href="#" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Settings</a>
                        <hr class="my-1 border-gray-200">
                        <a href="./logout.php" class="block px-4 py-2 text-gray-600 hover:bg-gray-100">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Login Button -->
                <!-- <a href="./login.php" class="px-3 py-1.5 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Login</a> -->
            <?php endif; ?>
        </div>
    </nav>

    <script>
        // Toggle the profile dropdown
        document.addEventListener('DOMContentLoaded', function() {
            const profileButton = document.getElementById('profileButton');
            const dropdownContent = document.getElementById('dropdownContent');
            const profileDropdown = document.getElementById('profileDropdown');

            profileButton.addEventListener('click', function(event) {
                event.stopPropagation();
                dropdownContent.classList.toggle('hidden');
            });

            // Close the dropdown when clicking outside of it
            document.addEventListener('click', function(event) {
                if (!profileDropdown.contains(event.target)) {
                    dropdownContent.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>