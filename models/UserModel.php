<?php
// models/UserModel.php
require_once 'Database.php';

class UserModel {
    private $db;
    private $table = 'users';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // CREATE - Ajouter un utilisateur
    public function createUser($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO {$this->table} (email, password, nom, prenom, role, date_naissance, pays, telephone) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['email'],
            $hashedPassword,
            $data['nom'],
            $data['prenom'],
            $data['role'],
            $data['date_naissance'],
            $data['pays'],
            $data['telephone']
        ]);
    }

    // READ - Récupérer un utilisateur par email
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ - Récupérer un utilisateur par ID
    public function getUserById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_user = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // READ - Récupérer tous les utilisateurs
    public function getAllUsers() {
        $sql = "SELECT * FROM {$this->table} ORDER BY date_inscription DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // UPDATE - Mettre à jour un utilisateur
    public function updateUser($id, $data) {
        $sql = "UPDATE {$this->table} SET nom = ?, prenom = ?, email = ?, date_naissance = ?, pays = ?, telephone = ? WHERE id_user = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['date_naissance'],
            $data['pays'],
            $data['telephone'],
            $id
        ]);
    }

    // DELETE - Supprimer un utilisateur
    public function deleteUser($id) {
        $sql = "DELETE FROM {$this->table} WHERE id_user = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    // UPDATE - Changer le statut
    public function updateStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET statut = ? WHERE id_user = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
}
?>