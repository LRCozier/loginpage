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
        
        error_log("DB CONFIG - Host: " . $this->host . ", DB: " . $this->db_name . ", User: " . $this->username);
        
        // Establish connection upon instantiation
        $this->getConnection();
    }
    
    private function getEnvVariable($key, $default = '') {
        $value = getenv($key);
        if ($value !== false) return $value;
        if (isset($_ENV[$key])) return $_ENV[$key];
        if (isset($_SERVER[$key])) return $_SERVER[$key];
        return $default;
    }
    
    public function getConnection() {
        if ($this->conn) {
            return $this->conn;
        }
        
        try {
            error_log("DATABASE: Attempting connection to " . $this->host);
            
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
            
            error_log("DATABASE: Connection successful");
            
        } catch(PDOException $exception) {
            error_log("DATABASE ERROR: " . $exception->getMessage());
            error_log("DATABASE DETAILS - Host: " . $this->host . ", DB: " . $this->db_name . ", User: " . $this->username);
            throw new Exception("Database connection failed: " . $exception->getMessage());
        }
        
        return $this->conn;
    }
    
    public function executeQuery($query, $params = []) {
        try {
            error_log("SQL QUERY: " . $query);
            error_log("SQL PARAMS: " . print_r($params, true));
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute($params);
            
            error_log("SQL EXECUTION: " . ($result ? "SUCCESS" : "FAILED"));
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQL ERROR: " . print_r($errorInfo, true));
                throw new Exception("Query execution failed: " . $errorInfo[2]);
            }
            
            return $stmt;
            
        } catch (Exception $e) {
            error_log("EXECUTE QUERY ERROR: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function logAudit($user_id, $action_type, $description = '', $ip_address = null, $user_agent = null) {
        try {
            error_log("AUDIT LOG: User $user_id, Action: $action_type, Desc: $description");
            
            $query = "INSERT INTO user_audit_log (user_id, action_type, description, ip_address, user_agent) 
                      VALUES (:user_id, :action_type, :description, :ip_address, :user_agent)";
            $stmt = $this->executeQuery($query, [
                'user_id' => $user_id,
                'action_type' => $action_type,
                'description' => $description,
                'ip_address' => $ip_address ?: ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
                'user_agent' => $user_agent ?: ($_SERVER['HTTP_USER_AGENT'] ?? '')
            ]);
            
            error_log("AUDIT LOG: Successfully logged action");
            return true;
            
        } catch (Exception $e) {
            error_log("AUDIT LOG ERROR: " . $e->getMessage());
            return false;
        }
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}
?>