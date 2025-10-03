<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['user_id'])) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Log logout action
        $database->logAudit($_SESSION['user_id'], 'logout');
        
    } catch (Exception $e) {
        error_log("Logout error: " . $e->getMessage());
    }
}

// Clear all session variables
$_SESSION = array();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php?logout=1");
exit;
?>
