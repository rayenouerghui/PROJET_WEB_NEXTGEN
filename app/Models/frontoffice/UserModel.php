<?php

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';

class UserModel {
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM utilisateur WHERE id_user = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createUser($email, $password, $nom, $prenom) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO utilisateur (email, mot_de_passe, nom, prenom, credit, role) VALUES (?, ?, ?, ?, 300, 'user')");
        return $stmt->execute([$email, $hashedPassword, $nom, $prenom]);
    }
    
    public function updateUser($id, $nom, $prenom, $email) {
        $stmt = $this->db->prepare("UPDATE utilisateur SET nom = ?, prenom = ?, email = ? WHERE id_user = ?");
        return $stmt->execute([$nom, $prenom, $email, $id]);
    }
    
    public function verifyPassword($email, $password) {
        $user = $this->getUserByEmail($email);
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }
    
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }
}

?>

