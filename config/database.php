<?php
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;
    
    public function __construct() {
        $this->host = $this->getEnvVariable('DB_HOST', 'localhost');
        $this->db_name = $this->getEnvVariable('DB_NAME', 'user_auth_system');
        $this->username = $this->getEnvVariable('DB_USERNAME', 'root');
        $this->password = $this->getEnvVariable('DB_PASSWORD', '');
    }
    
    private function getEnvVariable($key, $default = '') {
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        
        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        
        return $default;
    }
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4", 
                $this->username, 
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_TIMEOUT => 5
                ]
            );
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            
            $app_env = $this->getEnvVariable('APP_ENV', 'production');
            if ($app_env === 'production') {
                throw new Exception("Service temporarily unavailable. Please try again later.");
            } else {
                throw new Exception("Database connection failed: " . $exception->getMessage());
            }
        }
        
        return $this->conn;
    }
    
    public function logAudit($user_id, $action_type, $description = '', $ip_address = null, $user_agent = null) {
        try {
            $query = "INSERT INTO user_audit_log (user_id, action_type, description, ip_address, user_agent) 
                      VALUES (:user_id, :action_type, :description, :ip_address, :user_agent)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                'user_id' => $user_id,
                'action_type' => $action_type,
                'description' => $description,
                'ip_address' => $ip_address ?: ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
                'user_agent' => $user_agent ?: ($_SERVER['HTTP_USER_AGENT'] ?? '')
            ]);
            return true;
        } catch (Exception $e) {
            error_log("Audit log error: " . $e->getMessage());
            return false;
        }
    }
    
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            $stmt = $conn->query("SELECT 1");
            return $stmt->fetchColumn() === 1;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>


