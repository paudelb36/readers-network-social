<?php
session_start();
include('../includes/config.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch total number of users
$query = "SELECT COUNT(*) as totalUsers FROM Users";
$stmt = $pdo->query($query);
$totalUsers = $stmt->fetchColumn();

// Fetch new registrations
$query = "SELECT COUNT(*) as newRegistrations FROM Users WHERE JoinDate >= DATE_SUB(NOW(), INTERVAL 2 DAY)";
$stmt = $pdo->query($query);
$newRegistrations = $stmt->fetchColumn();

include('components/header.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="flex">
        <?php include('components/sidebar.php'); ?>

        <!-- Main Content -->
        <main class="flex-1 p-6">
            <div class="container mx-auto">
                <h2 class="text-2xl font-semibold mb-6">Dashboard Overview</h2>

                <!-- Dashboard Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Users</h3>
                        <p class="mt-4 text-4xl font-bold"><?php echo $totalUsers; ?></p>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">New Registrations</h3>
                        <p class="mt-4 text-4xl font-bold"><?php echo $newRegistrations; ?></p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
