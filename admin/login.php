<?php
session_start();
include('../includes/config.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch admin from the database
    $query = "SELECT * FROM Admin WHERE Username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['PasswordHash'])) {
        // Set session variables
        $_SESSION['admin_id'] = $admin['AdminID'];
        $_SESSION['username'] = $admin['Username'];
        header('Location: index.php'); // Redirect to the dashboard
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-20 max-w-md">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-center mb-6">Admin Login</h2>
            <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="mb-4">
                    <label for="username" class="block font-semibold">Username</label>
                    <input type="text" name="username" id="username" class="mt-2 p-2 border rounded w-full" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block font-semibold">Password</label>
                    <input type="password" name="password" id="password" class="mt-2 p-2 border rounded w-full" required>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded w-full">Login</button>
            </form>
        </div>
    </div>
</body>

</html>
