<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <form action="register_handle.php" method="POST" class="login-form">
            <h2>Create Account</h2>

            <?php
            // Display error messages
            if (isset($_GET['error'])) {
                $errorMsg = '';
                switch ($_GET['error']) {
                    case 'emptyfields':    $errorMsg = 'Please fill in all fields.'; break;
                    case 'invaliduser':    $errorMsg = 'Username can only contain letters and numbers.'; break;
                    case 'invalidemail':   $errorMsg = 'Please enter a valid email address.'; break;
                    case 'passwordcheck':  $errorMsg = 'Your passwords do not match.'; break;
                    case 'passwordshort':  $errorMsg = 'Password must be at least 8 characters long.'; break;
                    case 'usertaken':      $errorMsg = 'Username or email is already taken.'; break;
                    default:               $errorMsg = 'An unknown error occurred. Please try again.'; break;
                }
                echo '<p class="error-message">' . htmlspecialchars($errorMsg) . '</p>';
            }
            ?>

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn-login">Register</button>

            <div class="form-footer">
                <a href="login.php" class="forgot-password">Already have an account? Login</a>
            </div>
        </form>
    </div>
</body>
</html>