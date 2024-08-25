<?php
// Database connection
include('config.php');

// Login function
function login($email, $password) {
    global $conn;

    $sql = "SELECT * FROM Users WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user_id'] = $user['ID'];
        $_SESSION['username'] = $user['Username'];
        return true;
    }
    return false;
}
?>
