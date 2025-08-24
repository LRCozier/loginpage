<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    const theme = localStorage.getItem('theme') || 'light';
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
</script>
</head>
<body>
    <div class="theme-toggle-container">
        <button id="theme-toggle-btn" aria-label="Toggle dark mode">
            <svg id="sun-icon" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.02 0 1.41l1.06 1.06c.39.39 1.02.39 1.41 0s.39-1.02 0-1.41L5.99 4.58zm12.37 12.37c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.02 0 1.41l1.06 1.06c.39.39 1.02.39 1.41 0 .39-.39.39-1.02 0-1.41l-1.06-1.06zm1.06-10.96c.39-.39.39-1.02 0-1.41-.39-.39-1.02-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.02 0 1.41s1.02.39 1.41 0l1.06-1.06zM7.05 18.36c.39-.39.39-1.02 0-1.41-.39-.39-1.02-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.02 0 1.41s1.02.39 1.41 0l1.06-1.06z"/></svg>
            <svg id="moon-icon" style="display: block;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1.01-.23-.26-.38-.61-.38-.99 0-.83.67-1.5 1.5-1.5H16c2.76 0 5-2.24 5-5 0-4.42-4.03-8-9-8zm-5.5 9c0-3.03 2.47-5.5 5.5-5.5.08 0 .16 0 .24.01C9.6 6.8 8 8.74 8 11c0 2.21 1.79 4 4 4 .24 0 .48-.02.71-.05.02.16.04.33.04.5 0 3.03-2.47 5.5-5.5 5.5S6.5 15.03 6.5 12z"/></svg>
        </button>
    </div>
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
    <script src="themeswitcher.js"></script>
</body>
</html>