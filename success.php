<?php
session_start();

// If the user is not logged in, redirect them to the login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$username = htmlspecialchars($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome!</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form">
            <h2>Login Successful!</h2>
            <p style="text-align: center;">Welcome back, <strong><?php echo $username; ?></strong>!</p>
            <p style="text-align: center; margin-top: 20px;">
                <a href="login.php" class="forgot-password">Logout (simulation)</a>
            </p>
        </div>
    </div>
</body>
</html>