<?php
// register.php

// Include your database connection file
require_once '../includes/config.php';
include '../includes/header.php';

// Initialize variables for form data
$firstName = $lastName = $email = $username = $password = $confirmPassword = '';
$errors = [];

// Process the form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validate inputs
    if (empty($firstName) || empty($lastName) || empty($email) || empty($username) || empty($password) || empty($confirmPassword)) {
        $errors[] = 'All fields are required.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }

    // Check if email or username already exists
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM Users WHERE Email = ? OR Username = ?');
        $stmt->execute([$email, $username]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Email or username already exists.';
        }
    }

    // Register the user if no errors
    if (empty($errors)) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare('INSERT INTO Users (FirstName, LastName, Email, Username, PasswordHash) VALUES (?, ?, ?, ?, ?)');
        if ($stmt->execute([$firstName, $lastName, $email, $username, $passwordHash])) {
            // Redirect to login page after successful registration
            header('Location: ./login.php');
            exit();
        } else {
            $errors[] = 'An error occurred while registering.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/styles.css"> <!-- Ensure this path is correct -->
</head>
<body>
    <div class="min-h-screen bg-gray-100 flex flex-col justify-center py-12 sm:py-24">
        <div class="relative py-3 sm:max-w-xl sm:mx-auto">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-blue-600 shadow-lg transform -skew-y-6 sm:skew-y-0 sm:-rotate-6 sm:rounded-3xl"></div>
            <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
                <div class="max-w-md mx-auto">
                    <h1 class="text-2xl font-semibold text-center mb-6">Create Account</h1>
                    
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

                    <form action="register.php" method="POST" class="space-y-6">
                        <!-- Form fields for registration -->
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div class="relative">
                                <input
                                    autoComplete="off"
                                    id="first-name"
                                    name="firstName"
                                    type="text"
                                    class="peer placeholder-transparent h-12 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:border-blue-600"
                                    placeholder="First Name"
                                    value="<?php echo htmlspecialchars($firstName); ?>"
                                />
                                <label
                                    htmlFor="first-name"
                                    class="absolute left-0 top-0 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm"
                                >
                                    First Name
                                </label>
                            </div>
                            <div class="relative">
                                <input
                                    autoComplete="off"
                                    id="last-name"
                                    name="lastName"
                                    type="text"
                                    class="peer placeholder-transparent h-12 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:border-blue-600"
                                    placeholder="Last Name"
                                    value="<?php echo htmlspecialchars($lastName); ?>"
                                />
                                <label
                                    htmlFor="last-name"
                                    class="absolute left-0 top-0 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm"
                                >
                                    Last Name
                                </label>
                            </div>
                        </div>
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
                                id="username"
                                name="username"
                                type="text"
                                class="peer placeholder-transparent h-12 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:border-blue-600"
                                placeholder="Username"
                                value="<?php echo htmlspecialchars($username); ?>"
                            />
                            <label
                                htmlFor="username"
                                class="absolute left-0 top-0 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm"
                            >
                                Username
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
                            <input
                                autoComplete="off"
                                id="confirm-password"
                                name="confirmPassword"
                                type="password"
                                class="peer placeholder-transparent h-12 w-full border-b-2 border-gray-300 text-gray-900 focus:outline-none focus:border-blue-600"
                                placeholder="Confirm Password"
                            />
                            <label
                                htmlFor="confirm-password"
                                class="absolute left-0 top-0 text-gray-600 text-sm peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 transition-all peer-focus:-top-3.5 peer-focus:text-gray-600 peer-focus:text-sm"
                            >
                                Confirm Password
                            </label>
                        </div>
                        <div class="relative">
                            <button type="submit" class="bg-blue-500 text-white rounded-md px-4 py-2 w-full hover:bg-blue-600 transition duration-200">Sign Up</button>
                        </div>
                    </form>
                    <div class="w-full flex justify-center mt-6">
                        <p class="text-gray-600 text-sm">Already have an account?</p>
                        <a href="./login.php" class="ml-2 text-blue-500 hover:text-blue-700 text-sm">Log In</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
