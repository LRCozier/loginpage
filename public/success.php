<?php
session_start();
require_once __DIR__ . '/../config/database.php';

error_log("SUCCESS PAGE: Session check - loggedin: " . ($_SESSION['loggedin'] ?? 'NOT SET'));

// Redirect if not logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    error_log("SUCCESS PAGE ERROR: User not logged in, redirecting to login");
    header('Location: login.php');
    exit;
}

error_log("SUCCESS PAGE: User logged in - ID: " . ($_SESSION['user_id'] ?? 'UNKNOWN') . ", Username: " . ($_SESSION['username'] ?? 'UNKNOWN'));

try {
    $database = new Database();
    
    // Get user details
    $query = "SELECT username, email, created_at, last_login FROM users WHERE id = :user_id";
    $stmt = $database->executeQuery($query, ['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        error_log("SUCCESS PAGE ERROR: User not found in database for ID: " . $_SESSION['user_id']);
        session_destroy();
        header('Location: login.php');
        exit;
    }
    
    $username = htmlspecialchars($user['username']);
    $email = htmlspecialchars($user['email']);
    $member_since = date('F j, Y', strtotime($user['created_at']));
    $last_login = $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'First login!';
    
    error_log("SUCCESS PAGE: Displaying welcome for user: $username");
    
} catch (Exception $e) {
    error_log("SUCCESS PAGE ERROR: " . $e->getMessage());
    // Continue with session data if DB fails
    $username = htmlspecialchars($_SESSION['username']);
    $email = 'Not available';
    $member_since = 'Unknown';
    $last_login = 'Unknown';
    error_log("SUCCESS PAGE: Using session data due to DB error");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - User Authentication System</title>
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
        <div class="login-form">
            <h2>Welcome Back! 👋</h2>
            
            <div class="success-message">
                <p>Login successful! Welcome to your dashboard.</p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <div style="font-size: 48px; margin-bottom: 20px;">🎉</div>
                <h3 style="margin-bottom: 10px; color: var(--color-text-primary);">Hello, <strong><?php echo $username; ?></strong>!</h3>
                <p style="color: var(--color-text-secondary); margin-bottom: 5px;">Email: <?php echo $email; ?></p>
                <p style="color: var(--color-text-secondary); margin-bottom: 5px;">Member since: <?php echo $member_since; ?></p>
                <p style="color: var(--color-text-secondary);">Last login: <?php echo $last_login; ?></p>
            </div>
            
            <div style="background: var(--color-bg); padding: 15px; border-radius: 6px; margin: 20px 0;">
                <h4 style="margin-bottom: 10px; color: var(--color-text-primary);">Quick Stats</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div style="text-align: center;">
                        <div style="font-size: 24px; font-weight: bold; color: var(--color-primary);">1</div>
                        <div style="font-size: 12px; color: var(--color-text-secondary);">Active Session</div>
                    </div>
                    <div style="text-align: center;">
                        <div style="font-size: 24px; font-weight: bold; color: var(--color-primary);">100%</div>
                        <div style="font-size: 12px; color: var(--color-text-secondary);">Security</div>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="logout.php" class="btn-logout-link">Logout</a>
            </div>
        </div>
    </div>
    
    <script src="themeswitcher.js"></script>
</body>
</html>
