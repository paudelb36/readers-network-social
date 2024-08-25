<?php
session_start();
include('../includes/config.php'); // Include database configuration

if (isset($_SESSION['admin_id'])) {
    header('Location: index.php'); // Redirect if already logged in
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        // Check if username or email already exists
        $query = "SELECT * FROM Admin WHERE Username = :username OR Email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->execute(['username' => $username, 'email' => $email]);

        if ($stmt->rowCount() > 0) {
            $error = "Username or Email already exists!";
        } else {
            // Hash the password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insert new admin into the database
            $query = "INSERT INTO Admin (Username, Email, PasswordHash) VALUES (:username, :email, :password_hash)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password_hash' => $passwordHash
            ]);

            $_SESSION['admin_id'] = $pdo->lastInsertId();
            header('Location: index.php'); // Redirect to the dashboard
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto mt-20 max-w-md">
        <div class="bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-center mb-6">Admin Registration</h2>
            <?php if ($error): ?>
                <p class="text-red-500 mb-4"><?php echo $error; ?></p>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <div class="mb-4">
                    <label for="username" class="block font-semibold">Username</label>
                    <input type="text" name="username" id="username" class="mt-2 p-2 border rounded w-full" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block font-semibold">Email</label>
                    <input type="email" name="email" id="email" class="mt-2 p-2 border rounded w-full" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block font-semibold">Password</label>
                    <input type="password" name="password" id="password" class="mt-2 p-2 border rounded w-full" required>
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="block font-semibold">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="mt-2 p-2 border rounded w-full" required>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded w-full">Register</button>
            </form>
        </div>
    </div>
</body>

</html>
