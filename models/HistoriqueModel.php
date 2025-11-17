<?php
// models/HistoriqueModel.php
require_once 'Database.php';

class HistoriqueModel {
    private $db;
    private $table = 'historique';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // CREATE - Ajouter une action
    public function addAction($id_user, $type_action, $description) {
        $sql = "INSERT INTO {$this->table} (id_user, type_action, description) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_user, $type_action, $description]);
    }

    // READ - Historique d'un utilisateur (JOINTURE)
    public function getUserHistory($id_user) {
        $sql = "SELECT h.*, u.nom, u.prenom 
                FROM {$this->table} h 
                INNER JOIN users u ON h.id_user = u.id_user 
                WHERE h.id_user = ? 
                ORDER BY h.date_action DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // READ - Tout l'historique avec jointure
    public function getAllHistory() {
        $sql = "SELECT h.*, u.nom, u.prenom, u.email 
                FROM {$this->table} h 
                INNER JOIN users u ON h.id_user = u.id_user 
                ORDER BY h.date_action DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>