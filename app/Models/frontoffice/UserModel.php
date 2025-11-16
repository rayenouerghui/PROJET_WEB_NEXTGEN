<?php

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

class UserModel {
    private $db;
    
    public function __construct() {
        try {
            $this->db = config::getConnexion();
        } catch (Exception $e) {
            error_log("UserModel::__construct - Database connection failed: " . $e->getMessage());
            throw new Exception("Impossible de se connecter à la base de données. Vérifiez que MySQL est démarré et que la base de données 'nextgen_db' existe.");
        }
    }
    
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ? $user : false;
    }
    
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE id_user = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ? $user : false;
    }
    
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function createUser($email, $password, $nom, $prenom) {
        try {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Default credit is 300 TND, default role is 'user'
            $stmt = $this->db->prepare("INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, role, credit) VALUES (?, ?, ?, ?, 'user', 300)");
            $result = $stmt->execute([$email, $hashedPassword, $nom, $prenom]);
            
            if (!$result) {
                error_log("UserModel::createUser - Execute failed. Error: " . print_r($stmt->errorInfo(), true));
                return false;
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("UserModel::createUser - PDO Exception: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("UserModel::createUser - General Exception: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateUser($id, $nom, $prenom, $email) {
        $stmt = $this->db->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ? WHERE id_user = ?");
        return $stmt->execute([$nom, $prenom, $email, $id]);
    }
    
    public function updatePassword($id, $hashedPassword) {
        $stmt = $this->db->prepare("UPDATE utilisateur SET mot_de_passe = ? WHERE id_user = ?");
        return $stmt->execute([$hashedPassword, $id]);
    }
}

?>
