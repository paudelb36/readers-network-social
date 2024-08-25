<?php
include '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($email) || empty($password)) {
        echo 'Email and password are required';
        exit;
    }

    // Hash the password for storage
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Sample SQL query for sign-up (to be replaced with actual functionality)
    $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo 'User registered successfully';
    } else {
        echo 'Error registering user';
    }
}
?>
