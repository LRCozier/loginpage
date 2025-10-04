<?php
session_start();
require_once __DIR__ . '/../config/database.php';

error_log("=== REGISTRATION PROCESS STARTED ===");
error_log("REGISTRATION: Session ID: " . session_id());
error_log("REGISTRATION: POST Data: " . print_r($_POST, true));

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    error_log("REGISTRATION ERROR: Invalid request method - " . $_SERVER["REQUEST_METHOD"]);
    header('Location: /register.php');
    exit;
}

// CSRF Protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
    error_log("REGISTRATION ERROR: CSRF token validation failed");
    error_log("REGISTRATION: Expected: " . ($_SESSION['csrf_token'] ?? 'NOT SET') . ", Got: " . ($_POST['csrf_token'] ?? 'NOT SET'));
    header('Location: /register.php?error=security');
    exit;
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

error_log("REGISTRATION: Processing user - Username: '$username', Email: '$email'");

// Validation
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    error_log("REGISTRATION ERROR: Empty fields detected");
    header('Location: /register.php?error=emptyfields');
    exit();
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    error_log("REGISTRATION ERROR: Invalid username format - '$username'");
    header('Location: /register.php?error=invaliduser');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("REGISTRATION ERROR: Invalid email format - '$email'");
    header('Location: /register.php?error=invalidemail');
    exit();
}

if ($password !== $confirm_password) {
    error_log("REGISTRATION ERROR: Password mismatch");
    header('Location: /register.php?error=passwordcheck');
    exit();
}

if (strlen($password) < 8) {
    error_log("REGISTRATION ERROR: Password too short");
    header('Location: /register.php?error=passwordshort');
    exit();
}

// Enhanced password strength check
if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    error_log("REGISTRATION ERROR: Weak password");
    header('Location: /register.php?error=passwordweak');
    exit();
}

try {
    error_log("REGISTRATION: Attempting database connection...");
    $database = new Database();
    $db = $database->getConnection(); // Keep for lastInsertId
    error_log("REGISTRATION: Database connection successful");

    // Check if username or email exists
    $query = "SELECT id FROM users WHERE username = :username OR email = :email";
    error_log("REGISTRATION: Executing duplicate check query");
    $stmt = $database->executeQuery($query, ['username' => $username, 'email' => $email]);

    if ($stmt->rowCount() > 0) {
        error_log("REGISTRATION ERROR: Username or email already exists");
        header('Location: /register.php?error=usertaken');
        exit();
    }

    error_log("REGISTRATION: No duplicate users found, hashing password...");
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    if (!$password_hash) {
        throw new Exception("Password hashing failed");
    }
    error_log("REGISTRATION: Password hashed successfully");

    // Insert user
    $query = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)";
    error_log("REGISTRATION: Executing user insert query");
    $database->executeQuery($query, [
        'username' => $username,
        'email' => $email,
        'password_hash' => $password_hash
    ]);

    $user_id = $database->conn->lastInsertId();
    error_log("REGISTRATION SUCCESS: User inserted with ID: $user_id");
    
    // Log registration
    error_log("REGISTRATION: Attempting audit log...");
    $database->logAudit($user_id, 'registration_success');
    error_log("REGISTRATION: Audit log completed");

    error_log("=== REGISTRATION PROCESS COMPLETED SUCCESSFULLY ===");
    header('Location: /login.php?success=registered');
    exit();

} catch (Exception $e) {
    error_log("=== REGISTRATION PROCESS FAILED ===");
    error_log("REGISTRATION ERROR: " . $e->getMessage());
    error_log("REGISTRATION ERROR: File: " . $e->getFile() . " Line: " . $e->getLine());
    error_log("REGISTRATION ERROR: Trace: " . $e->getTraceAsString());
    
    header('Location: /register.php?error=system');
    exit();
}
?>
