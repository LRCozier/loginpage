<?php
session_start();
require_once __DIR__ . '/../config/database.php';

error_log("LOGOUT: Process started for user ID: " . ($_SESSION['user_id'] ?? 'UNKNOWN'));

if (isset($_SESSION['user_id'])) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Log logout action
        error_log("LOGOUT: Logging audit trail for user ID: " . $_SESSION['user_id']);
        $database->logAudit($_SESSION['user_id'], 'logout');
        
    } catch (Exception $e) {
        error_log("LOGOUT ERROR: " . $e->getMessage());
    }
} else {
    error_log("LOGOUT: No user ID in session");
}

// Clear all session variables
$_SESSION = array();
error_log("LOGOUT: Session variables cleared");

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
    error_log("LOGOUT: Session cookie deleted");
}

// Destroy the session
session_destroy();
error_log("LOGOUT: Session destroyed");

// Redirect to login page
error_log("LOGOUT: Redirecting to login page");
header("Location: login.php?logout=1");
exit;
?>)

Debug.php (<?php
error_log("DEBUG PAGE: Accessed");
header('Content-Type: text/plain');

echo "=== AUTH SYSTEM DEBUG PAGE ===\n\n";

// Check session
echo "SESSION STATUS:\n";
echo "Session ID: " . (session_id() ?: 'NOT STARTED') . "\n";
echo "Logged in: " . (isset($_SESSION['loggedin']) ? 'YES' : 'NO') . "\n";
echo "Username: " . ($_SESSION['username'] ?? 'NOT SET') . "\n\n";

// Check environment variables
echo "ENVIRONMENT VARIABLES:\n";
echo "DB_HOST: " . (getenv('DB_HOST') ?: 'NOT SET') . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ?: 'NOT SET') . "\n"; 
echo "DB_USERNAME: " . (getenv('DB_USERNAME') ?: 'NOT SET') . "\n";
echo "APP_ENV: " . (getenv('APP_ENV') ?: 'NOT SET') . "\n\n";

// Test database connection
echo "DATABASE TEST:\n";
try {
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
    echo "✓ Database connection: SUCCESS\n";
    
    // Test if users table exists
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Users table: EXISTS\n";
        
        // Count users
        $stmt = $db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "✓ Total users: " . $result['count'] . "\n";
    } else {
        echo "✗ Users table: DOES NOT EXIST\n";
    }
    
} catch (Exception $e) {
    echo "✗ Database connection: FAILED - " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";

error_log("DEBUG PAGE: Completed");
?>
