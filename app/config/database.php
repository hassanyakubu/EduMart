<?php
/**
 * Database Configuration
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        // Load database credentials from settings
        require_once __DIR__ . '/../../settings/db_cred.php';
        
        $host = defined('SERVER') ? SERVER : 'localhost';
        $username = defined('USERNAME') ? USERNAME : 'root';
        $password = defined('PASSWD') ? PASSWD : '';
        $database = defined('DATABASE') ? DATABASE : 'ecommerce_2025A_hassan_yakubu';
        
        try {
            $this->connection = new mysqli($host, $username, $password, $database);
            
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            $this->connection->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    private function __clone() {}
    
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>
