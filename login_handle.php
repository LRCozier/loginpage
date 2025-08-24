<?php
// Start the session to manage user state.
session_start();

// Ensure the script is accessed via a POST request.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: login.php');
    exit('Invalid request method.');
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Basic validation to ensure fields are not empty.
if (empty($username) || empty($password)) {
    header('Location: login.php?error=1');
    exit;
}

$userFile = 'users.json';
$users = file_exists($userFile) ? json_decode(file_get_contents($userFile), true) : [];

$foundUser = null;

// Loop through the users array to find a matching username.
foreach ($users as $user) {
    if ($user['username'] === $username) {
        $foundUser = $user;
        break; // Stop the loop once the user is found.
    }
}

// Check if a user was found & Verify the submitted password against the stored hash.
if ($foundUser && password_verify($password, $foundUser['password_hash'])) {
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Set session variables to mark the user as logged in.
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $foundUser['username'];
    
    // Redirect to the success page.
    header('Location: success.php');
    exit;
    
} else {
    
    // Redirect back to the login page with a generic error message.
    header('Location: login.php?error=1');
    exit;
}
?>