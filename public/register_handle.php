<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header('Location: /register.php');
    exit;
}

// CSRF Protection
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    header('Location: /register.php?error=security');
    exit;
}

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);
$confirm_password = trim($_POST['confirm_password']);

// Validation
if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    header('Location: /register.php?error=emptyfields');
    exit();
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
    header('Location: /register.php?error=invaliduser');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: /register.php?error=invalidemail');
    exit();
}

if ($password !== $confirm_password) {
    header('Location: /register.php?error=passwordcheck');
    exit();
}

if (strlen($password) < 8) {
    header('Location: /register.php?error=passwordshort');
    exit();
}

// Enhanced password strength check
if (!preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
    header('Location: /register.php?error=passwordweak');
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // Check if username or email exists
    $query = "SELECT id FROM users WHERE username = :username OR email = :email";
    $stmt = $db->prepare($query);
    $stmt->execute(['username' => $username, 'email' => $email]);

    if ($stmt->rowCount() > 0) {
        header('Location: /register.php?error=usertaken');
        exit();
    }

    // Hash password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $query = "INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        'username' => $username,
        'email' => $email,
        'password_hash' => $password_hash
    ]);

    $user_id = $db->lastInsertId();
    
    // Log registration
    $database->logAudit($user_id, 'registration_success');

    header('Location: /login.php?success=registered');
    exit();

} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    header('Location: /register.php?error=system');
    exit();
}
?>
