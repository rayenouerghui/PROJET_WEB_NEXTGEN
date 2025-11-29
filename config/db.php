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
            $dsn = "mysql:host=$this->host;dbname=$this->dbname;charset=$this->charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            $messageErreur = $e->getMessage();
            $erreurConnexion = "Erreur de connexion à la base de données";
            
            if (strpos($messageErreur, '2002') !== false) {
                $erreurConnexion .= ": MySQL n'est pas démarré";
            } elseif (strpos($messageErreur, '1049') !== false) {
                $erreurConnexion .= ": La base de données n'existe pas";
            } else {
                $erreurConnexion .= ": $messageErreur";
            }
            
            throw new PDOException($erreurConnexion);
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