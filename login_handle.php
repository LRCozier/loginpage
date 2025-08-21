<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Redirect to login page or show an error
    header('Location: login.php');
    exit('Invalid request method.');
}

$users = [
    // username => hashed_password
    'admin' => '$2y$10$w5J1yB/J3Kz.N9gZ4nF6Gu3nGuOKj2J3kX/YgZ.wN8dJ4oO5eF3B.', // Password is "password123"
    'user'  => '$2y$10$8.PClvV/3e9p3j8aB3x6I.1e4F2z.C6o/E2yZ7u8uB3kX5c.Z4zC.'  // Password is "securepass"
];

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($username) || empty($password)) {
    // Redirect back with an error if fields are empty
    header('Location: login.php?error=1');
    exit;
}

if (isset($users[$username]) && password_verify($password, $users[$username])) {
    // Login Successful

    session_regenerate_id(true);
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $username; // Store username for personalization

    // Redirect to a protected "success" page
    header('Location: success.php');
    exit;

} else {
    // Login Failed - Redirect back to the login page with an error flag in the URL.
    header('Location: login.php?error=1');
    exit;
}

?>