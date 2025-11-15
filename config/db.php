<?php

class Database {
    private static $instance = null;
    private $connection;
    
    private $host = 'localhost';
    private $dbname = 'nextgen_db';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            $errorMsg = "Erreur de connexion à la base de données";
            if (strpos($e->getMessage(), '2002') !== false) {
                $errorMsg .= ": MySQL n'est pas démarré. Veuillez démarrer MySQL dans XAMPP.";
            } elseif (strpos($e->getMessage(), '1049') !== false) {
                $errorMsg .= ": La base de données '{$this->dbname}' n'existe pas. Veuillez l'importer.";
            } else {
                $errorMsg .= ": " . $e->getMessage();
            }
            throw new PDOException($errorMsg);
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
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

