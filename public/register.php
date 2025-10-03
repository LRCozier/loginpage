<?php 
session_start();

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - User Authentication System</title>
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
        <form action="/handlers/register_handle.php" method="POST" class="login-form" id="registerForm">
            <h2>Create Your Account</h2>

            <?php
            if (isset($_GET['error'])) {
                $errorMsg = 'An unknown error occurred.';
                switch ($_GET['error']) {
                    case 'emptyfields': $errorMsg = 'Please fill in all fields.'; break;
                    case 'invaliduser': $errorMsg = 'Username can only contain letters, numbers, and underscores.'; break;
                    case 'invalidemail': $errorMsg = 'Please enter a valid email address.'; break;
                    case 'passwordcheck': $errorMsg = 'Your passwords do not match.'; break;
                    case 'passwordshort': $errorMsg = 'Password must be at least 8 characters long.'; break;
                    case 'passwordweak': $errorMsg = 'Password must include uppercase, lowercase, and numbers.'; break;
                    case 'usertaken': $errorMsg = 'Username or email is already taken.'; break;
                    case 'security': $errorMsg = 'Security validation failed.'; break;
                    case 'system': $errorMsg = 'System error. Please try again.'; break;
                }
                echo '<div class="error-message">' . htmlspecialchars($errorMsg) . '</div>';
            }
            ?>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                       pattern="[a-zA-Z0-9_]+" title="Letters, numbers, and underscores only">
                <small>Letters, numbers, and underscores only</small>
            </div>
            
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required 
                       oninput="checkPasswordStrength(this.value)">
                <div class="password-strength">
                    <div class="strength-bar"></div>
                    <small>Must be 8+ characters with uppercase, lowercase, and numbers</small>
                </div>
            </div>
            
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       oninput="checkPasswordMatch()">
                <small id="password-match"></small>
            </div>

            <button type="submit" class="btn-login" id="registerBtn">Create Account</button>

            <div class="form-footer">
                <a href="login.php" class="forgot-password">Already have an account? Login</a>
            </div>
        </form>
    </div>

    <script>
        function checkPasswordStrength(password) {
            const strengthBar = document.querySelector('.strength-bar');
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            const colors = ['#ff4d4d', '#ff944d', '#ffdd4d', '#8ce08c', '#4caf50'];
            strengthBar.style.width = (strength * 20) + '%';
            strengthBar.style.backgroundColor = colors[strength - 1] || '#ff4d4d';
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const matchText = document.getElementById('password-match');
            
            if (confirm.length === 0) {
                matchText.textContent = '';
            } else if (password === confirm) {
                matchText.textContent = '✓ Passwords match';
                matchText.style.color = '#4caf50';
            } else {
                matchText.textContent = '✗ Passwords do not match';
                matchText.style.color = '#ff4d4d';
            }
        }
        
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('registerBtn');
            btn.disabled = true;
            btn.textContent = 'Creating Account...';
        });
    </script>
    <script src="themeswitcher.js"></script>
</body>
</html>
