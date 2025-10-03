<?php
session_start();

// Generate CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: success.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Authentication System</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    const theme = localStorage.getItem('theme') || 'light';
    if (theme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
    </script>
</head>
<body>
    <div class="theme-toggle-wrapper">
        <button class="theme-toggle" id="theme-toggle-btn" role="switch" aria-checked="false" aria-label="Toggle theme">
            <span class="theme-toggle__icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="sun-icon"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.02 0 1.41l1.06 1.06c.39.39 1.02.39 1.41 0s.39-1.02 0-1.41L5.99 4.58zm12.37 12.37c-.39-.39-1.02-.39-1.41 0-.39.39-.39 1.02 0 1.41l1.06 1.06c.39.39 1.02.39 1.41 0 .39-.39.39-1.02 0-1.41l-1.06-1.06zm1.06-10.96c.39-.39.39-1.02 0-1.41-.39-.39-1.02-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.02 0 1.41s1.02.39 1.41 0l1.06-1.06zM7.05 18.36c.39-.39.39-1.02 0-1.41-.39-.39-1.02-.39-1.41 0l-1.06 1.06c-.39.39-.39 1.02 0 1.41s1.02.39 1.41 0l1.06-1.06z"/></svg>
            </span>
            <span class="theme-toggle__icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="moon-icon"><path d="M21.64,13.24a1,1,0,0,0-1.41,1.41A9,9,0,1,1,13.24,3.36a1,1,0,0,0,1.41-1.41A11,11,0,1,0,21.64,13.24Z"/></svg>
            </span>
            <span class="theme-toggle__thumb"></span>
        </button>
    </div>
    
    <div class="login-container">
        <form action="/handlers/login_handle.php" method="POST" class="login-form" id="loginForm">
            <h2>Login to Your Account</h2>
            
            <?php
            if (isset($_GET['error'])) {
                $errorMsg = 'Invalid username or password.';
                switch ($_GET['error']) {
                    case 'empty': $errorMsg = 'Please fill in all fields.'; break;
                    case 'security': $errorMsg = 'Security validation failed.'; break;
                    case 'lockout': $errorMsg = 'Too many failed attempts. Please try again later.'; break;
                    case 'locked': $errorMsg = 'Account temporarily locked. Please try again later.'; break;
                    case 'system': $errorMsg = 'System error. Please try again.'; break;
                }
                echo '<div class="error-message">' . htmlspecialchars($errorMsg) . '</div>';
            }

            if (isset($_GET['success']) && $_GET['success'] == 'registered') {
                echo '<div class="success-message">Registration successful! Please log in.</div>';
            }
            
            if (isset($_GET['logout'])) {
                echo '<div class="success-message">You have been successfully logged out.</div>';
            }
            ?>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="input-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required autocomplete="username" 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            
            <div class="input-group-inline">
                <input type="checkbox" id="show-password" onchange="togglePassword()">
                <label for="show-password">Show Password</label>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">Login</button>
            
            <div class="form-footer">
                <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
                <span>&middot;</span>
                <a href="register.php" class="forgot-password">Don't have an account? Sign Up</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const showPasswordCheckbox = document.getElementById('show-password');
            passwordField.type = showPasswordCheckbox.checked ? 'text' : 'password';
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.textContent = 'Logging in...';
        });
    </script>
    <script src="themeswitcher.js"></script>
</body>
</html>
