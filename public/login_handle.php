<?php
session_start();
require_once __DIR__ . '/../config/database.php';

error_log("=== LOGIN PROCESS STARTED ===");
error_log("LOGIN: Session ID: " . session_id());
error_log("LOGIN: POST Data: " . print_r($_POST, true));

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    error_log("LOGIN ERROR: Invalid request method - " . $_SERVER["REQUEST_METHOD"]);
    header('Location: /login.php');
    exit;
}

// CSRF Protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    error_log("LOGIN ERROR: CSRF token validation failed");
    error_log("LOGIN: Expected: " . ($_SESSION['csrf_token'] ?? 'NOT SET') . ", Got: " . ($_POST['csrf_token'] ?? 'NOT SET'));
    header('Location: /login.php?error=security');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

error_log("LOGIN: Attempting login for user: '$username'");

// Basic validation
if (empty($username) || empty($password)) {
    error_log("LOGIN ERROR: Empty username or password");
    header('Location: /login.php?error=empty');
    exit;
}

try {
    error_log("LOGIN: Attempting database connection...");
    $database = new Database();
    error_log("LOGIN: Database connection successful");
    
    // Check for brute force protection
    $ip_address = $_SERVER['REMOTE_ADDR'];
    error_log("LOGIN: Checking brute force protection for IP: $ip_address");
    
    $query = "SELECT COUNT(*) as attempts 
              FROM login_attempts 
              WHERE ip_address = :ip_address 
                AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                AND success = false";
    $stmt = $database->executeQuery($query, ['ip_address' => $ip_address]);
    $result = $stmt->fetch();
    
    error_log("LOGIN: Failed attempts in last 15min: " . $result['attempts']);
    
    if ($result['attempts'] >= 5) {
        error_log("LOGIN ERROR: Too many failed attempts - IP locked: $ip_address");
        header('Location: /login.php?error=lockout');
        exit;
    }
    
    // Find user
    $query = "SELECT id, username, password_hash, is_active, failed_login_attempts, account_locked_until 
              FROM users 
              WHERE username = :username OR email = :username";
    error_log("LOGIN: Searching for user: '$username'");
    
    $stmt = $database->executeQuery($query, ['username' => $username]);
    $user = $stmt->fetch();
    error_log("LOGIN: Query executed, user found: " . ($user ? 'YES' : 'NO'));
    
    $login_success = false;
    
    if ($user) {
        error_log("LOGIN: User found - ID: " . $user['id'] . ", Username: " . $user['username']);
        
        // Check if account is locked
        if ($user['account_locked_until'] && strtotime($user['account_locked_until']) > time()) {
            error_log("LOGIN ERROR: Account locked until " . $user['account_locked_until']);
            $database->logAudit(null, 'login_attempt', 'Account locked: ' . $username);
            header('Location: /login.php?error=locked');
            exit;
        }
        
        // Verify password
        error_log("LOGIN: Verifying password...");
        if (password_verify($password, $user['password_hash'])) {
            $login_success = true;
            error_log("LOGIN: Password verification SUCCESS");
            
            // Reset failed attempts on successful login
            $query = "UPDATE users 
                      SET failed_login_attempts = 0, account_locked_until = NULL, last_login = NOW() 
                      WHERE id = :user_id";
            $database->executeQuery($query, ['user_id' => $user['id']]);
            
            // Regenerate session ID for security
            session_regenerate_id(true);
            error_log("LOGIN: Session regenerated");
            
            // Set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();
            
            error_log("LOGIN: Session variables set for user ID: " . $user['id']);
            
            // Log successful login
            $database->logAudit($user['id'], 'login_success');
            
            error_log("=== LOGIN PROCESS COMPLETED SUCCESSFULLY ===");
            header('Location: /success.php');
            exit;
        } else {
            error_log("LOGIN ERROR: Password verification FAILED");
        }
    } else {
        error_log("LOGIN ERROR: User not found - '$username'");
    }
    
    // Failed login
    if ($user) {
        // Increment failed attempts
        $new_attempts = $user['failed_login_attempts'] + 1;
        $lock_until = null;
        
        error_log("LOGIN: Failed attempt $new_attempts for user ID: " . $user['id']);
        
        // Lock account after 5 failed attempts for 30 minutes
        if ($new_attempts >= 5) {
            $lock_until = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            error_log("LOGIN: Account locked until: $lock_until");
        }
        
        // Update user
        $query = "UPDATE users 
                  SET failed_login_attempts = :attempts, account_locked_until = :lock_until 
                  WHERE id = :user_id";
        $database->executeQuery($query, [
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
    $database->executeQuery($query, [
        'ip_address' => $ip_address,
        'username' => $username,
        'success' => $login_success,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
    
    error_log("LOGIN: Login attempt logged - Success: " . ($login_success ? 'YES' : 'NO'));
    
    error_log("=== LOGIN PROCESS FAILED - Invalid credentials ===");
    header('Location: /login.php?error=1');
    exit;
    
} catch (Exception $e) {
    error_log("=== LOGIN PROCESS FAILED WITH EXCEPTION ===");
    error_log("LOGIN ERROR: " . $e->getMessage());
    error_log("LOGIN ERROR: File: " . $e->getFile() . " Line: " . $e->getLine());
    error_log("LOGIN ERROR: Trace: " . $e->getTraceAsString());
    
    header('Location: /login.php?error=system');
    exit;
}
?>