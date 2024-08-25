<?php
// login.php

// Include your database connection file
require_once '../includes/config.php';
session_start();

$email = $password = '';
$errors = [];

// Process the form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($email) || empty($password)) {
        $errors[] = 'Email and password are required.';
    }

    // Authenticate user
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT * FROM Users WHERE Email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['PasswordHash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['UserID'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['logged_in'] = true;

            // Redirect to index.php
            header('Location: ./index.php');
            exit();
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Make sure the path is correct -->
</head>
<body>
    <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:py-24">
        <div class="relative py-3 sm:max-w-xl sm:mx-auto">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-600 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
            <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
                <div class="max-w-md mx-auto">
                    <h1 class="text-2xl font-semibold text-center mb-6">Login</h1>
                    
                    <!-- Display errors -->
                    <?php if (!empty($errors)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <strong class="font-bold">Error:</strong>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" class="space-y-6">
                        <div class="relative">
                            <input
                                autoComplete="off"
                                id="email"
                                name="email"
                                type="email"
                                class="peer placeholder-transparent h-12 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:border-blue-600"
                                placeholder="Email Address"
                                value="<?php echo htmlspecialchars($email); ?>"
                            />
                            <label
                                htmlFor="email"
                                class="absolute left-0 top-0 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm"
                            >
                                Email Address
                            </label>
                        </div>
                        <div class="relative">
                            <input
                                autoComplete="off"
                                id="password"
                                name="password"
                                type="password"
                                class="peer placeholder-transparent h-12 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:border-blue-600"
                                placeholder="Password"
                            />
                            <label
                                htmlFor="password"
                                class="absolute left-0 top-0 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm"
                            >
                                Password
                            </label>
                        </div>
                        <div class="relative">
                            <button type="submit" class="bg-blue-500 text-white rounded-md px-4 py-2 w-full hover:bg-blue-600 transition duration-200">Log In</button>
                        </div>
                    </form>
                    <div class="w-full flex justify-center mt-6">
                        <p class="text-gray-600 text-sm">Don't have an account?</p>
                        <a href="./register.php" class="ml-2 text-blue-500 hover:text-blue-700 text-sm">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
