<?php
// users.php
include('../includes/config.php'); 

// Fetch all users
$query = "SELECT UserID, Username, FirstName, LastName, Email, ProfilePicture FROM Users";
$stmt = $pdo->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include('components/header.php'); ?>
    <div class="flex">
        <?php include('components/sidebar.php'); ?>
        <main class="flex-1 p-6">
            <div class="container mx-auto">
                <h2 class="text-2xl font-semibold mb-6">Users List</h2>
                <table class="min-w-full bg-white border border-gray-200">
                    <thead>
                        <tr>
                            <th class="py-2 px-4 border-b">UserID</th>
                            <th class="py-2 px-4 border-b">Username</th>
                            <th class="py-2 px-4 border-b">First Name</th>
                            <th class="py-2 px-4 border-b">Last Name</th>
                            <th class="py-2 px-4 border-b">Email</th>
                            <th class="py-2 px-4 border-b">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['UserID']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['Username']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['FirstName']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['LastName']); ?></td>
                            <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($user['Email']); ?></td>
                            <td class="py-2 px-4 border-b">
                                <a href="user_details.php?id=<?php echo htmlspecialchars($user['UserID']); ?>" class="text-blue-500 hover:underline">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
