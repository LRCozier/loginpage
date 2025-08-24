<?php

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Redirect or show an error for invalid access method
    header('Location: register.php');
    exit('Invalid request method.');
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);
$confirm_password = trim($_POST['confirm_password']);


//Check for empty fields
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    header('Location: register.php?error=emptyfields');
    exit();
}

// Validate username format (allow only letters and numbers)
if (!preg_match('/^[a-zA-Z0-9]+$/', $username)) {
    header('Location: register.php?error=invaliduser');
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: register.php?error=invalidemail');
    exit();
}

// Check if the two password fields match
if ($password !== $confirm_password) {
    header('Location: register.php?error=passwordcheck');
    exit();
}

// Check password length
if (strlen($password) < 8) {
    header('Location: register.php?error=passwordshort');
    exit();
}

//"Database" Interaction with json file
$userFile = 'users.json';

// Load existing users. If the file doesn't exist, start with an empty array
$users = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];

// Check if the username or email is already taken
foreach ($users as $user) {
    if ($user['username'] === $username || $user['email'] === $email) {
        // Redirect with an error if a match is found
        header('Location: register.php?error=usertaken');
        exit();
    }
}

// Hash the password for secure storage
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Prepare the new user's data.
$newUser = [
    'username' => $username,
    'email' => $email,
    'password_hash' => $password_hash,
];

// Add the new user to the users array
$users[] = $newUser;

// Save the updated array back to the JSON file.
file_put_contents($userFile, json_encode($users, JSON_PRETTY_PRINT));

// Redirect to the login page with a success message to provide user feedback.
header('Location: login.php?success=registered');
exit();