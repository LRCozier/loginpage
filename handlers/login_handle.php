<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: /login.php');
    exit;
}

// CSRF Protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: /login.php?error=security');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

// Basic validation
if (empty($username) || empty($password)) {
    header('Location: /login.php?error=empty');
    exit;
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check for brute force protection
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $query = "SELECT COUNT(*) as attempts 
              FROM login_attempts 
              WHERE ip_address = :ip_address 
                AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                AND success = false";
    $stmt = $db->prepare($query);
    $stmt->execute(['ip_address' => $ip_address]);
    $result = $stmt->fetch();
    
    if ($result['attempts'] >= 5) {
        header('Location: /login.php?error=lockout');
        exit;
    }
    
    // Find user
    $query = "SELECT id, username, password_hash, is_active, failed_login_attempts, account_locked_until 
              FROM users 
              WHERE username = :username OR email = :username";
    $stmt = $db->prepare($query);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();
    
    $login_success = false;
    
    if ($user) {
        // Check if account is locked
        if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()) {
            $database->logAudit(null, 'login_attempt', 'Account locked: ' . $username);
            header('Location: /login.php?error=locked');
            exit;
        }
        
        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            $login_success = true;
            
            // Reset failed attempts on successful login
            $query = "UPDATE users 
                      SET failed_login_attempts = 0, account_locked_until = NULL, last_login = NOW() 
                      WHERE id = :user_id";
            $stmt = $db->prepare($query);
            $stmt->execute(['user_id' => $user['id']]);
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();
            
            // Log successful login
            $database->logAudit($user['id'], 'login_success');
            
            header('Location: /success.php');
            exit;
        }
    }
    
    // Failed login
    if ($user) {
        // Increment failed attempts
        $new_attempts = $user['failed_login_attempts'] + 1;
        $lock_until = null;
        
        // Lock account after 5 failed attempts for 30 minutes
        if ($new_attempts >= 5) {
            $lock_until = date('Y-m-d H:i:s', strtotime('+30 minutes'));
        }
        
        $query = "UPDATE users 
                  SET failed_login_attempts = :attempts, account_locked_until = :lock_until 
                  WHERE id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->execute([
            'attempts' => $new_attempts,
            'lock_until' => $lock_until,
            'user_id' => $user['id']
        ]);
        
        $database->logAudit($user['id'], 'login_failed', 'Failed attempt ' . $new_attempts);
    } else {
        $database->logAudit(null, 'login_failed', 'Unknown user: ' . $username);
    }
    
    // Log login attempt
    $query = "INSERT INTO login_attempts (ip_address, username, success, user_agent) 
              VALUES (:ip_address, :username, :success, :user_agent)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        'ip_address' => $ip_address,
        'username' => $username,
        'success' => $login_success,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
    
    header('Location: /login.php?error=1');
    exit;
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    header('Location: /login.php?error=system');
    exit;
}
?>
