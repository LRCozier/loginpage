<?php
session_start();
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
    <title>Responsive Login Form</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <form action="login_handle.php" method="POST" class="login-form">
            <h2>Login</h2>
            
            <?php
            if (isset($_GET['error'])) {
                echo '<p class="error-message">Invalid username or password.</p>';
            }
            ?>

            <div class="input-group">
                <label for="username">Username or Email</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            
            <div class="input-group-inline">
                <input type="checkbox" id="show-password" onclick="togglePassword()">
                <label for="show-password">Show Password</label>
            </div>

            <button type="submit" class="btn-login">Login</button>
            
            <div class="divider">OR</div>

            <div class="social-login-buttons">
                <a href="#" class="btn-social google">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 20 20"><path d="M19.88 10.22c0-.7-.06-1.37-.17-2.02H10.2v3.8h5.42c-.23 1.23-.9 2.28-1.94 3.02v2.48h3.18c1.86-1.72 2.94-4.28 2.94-7.28z" fill="#4285F4"></path><path d="M10.2 20c2.72 0 5.02-.9 6.69-2.48l-3.18-2.48c-.9.6-2.07.95-3.51.95-2.7 0-4.98-1.8-5.8-4.22H1.04v2.56C2.72 17.06 6.18 20 10.2 20z" fill="#34A853"></path><path d="M4.4 11.26c-.2-.6-.32-1.25-.32-1.92s.12-1.32.32-1.92V4.86H1.04C.38 6.18 0 7.95 0 10s.38 3.82 1.04 5.14l3.36-2.58z" fill="#FBBC05"></path><path d="M10.2 3.9c1.48 0 2.8.5 3.85 1.5l2.82-2.82C15.2.9 12.92 0 10.2 0 6.18 0 2.72 2.94 1.04 6.72l3.36 2.58c.82-2.42 3.1-4.22 5.8-4.22z" fill="#EA4335"></path></svg>
                    <span>Continue with Google</span>
                </a>
                <a href="#" class="btn-social apple">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 20 20"><path d="M17.42 10.26c0-2.48-1.12-4.04-3.08-4.04-1.82 0-3.23 1.2-4.09 1.2-.88 0-1.98-1.2-3.6-1.2-1.96 0-3.56 1.53-3.56 4.38 0 2.87 1.43 5.92 2.99 7.82.78.95 1.62 2.04 2.73 2.04 1.08 0 1.48-.68 2.89-.68 1.42 0 1.77.68 2.92.68 1.15 0 1.88-1.03 2.65-1.98.9-.98 1.54-2.02 1.54-3.28zM13.48 4.6c.94-.98 1.5-2.3 1.34-3.6H11.9c-.9 1.08-1.6 2.33-1.42 3.63h1.83c.5-.02 1.07-.03 1.17-.03z" fill="#FFFFFF"></path></svg>
                    <span>Continue with Apple</span>
                </a>
                <a href="#" class="btn-social facebook">
                    <svg aria-hidden="true" width="20" height="20" viewBox="0 0 20 20"><path d="M20 10.04a10 10 0 10-11.53 9.94v-7.02H5.9v-2.91h2.57V7.81c0-2.55 1.5-3.96 3.84-3.96 1.11 0 2.27.2 2.27.2v2.46h-1.29c-1.26 0-1.66.76-1.66 1.59v1.92h2.78l-.44 2.91h-2.34v7.02A10 10 0 0020 10.04z" fill="#FFFFFF"></path></svg>
                    <span>Continue with Facebook</span>
                </a>
            </div>

            <div class="form-footer">
                <a href="#" class="forgot-password">Forgot Password?</a>
            </div>
        </form>
    </div>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>
</body>
</html>