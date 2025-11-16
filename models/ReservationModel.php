<?php
// models/ReservationModel.php
class ReservationModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        try {
            $sql = "INSERT INTO reservation (id_evenement, nom_complet, email, telephone, nombre_places, message) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->query($sql, [
                $data['id_evenement'],
                $data['nom_complet'],
                $data['email'],
                $data['telephone'],
                $data['nombre_places'],
                $data['message']
            ]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Erreur création réservation: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAll() {
        try {
            $sql = "SELECT r.*, e.titre as evenement 
                    FROM reservation r 
                    JOIN evenement e ON r.id_evenement = e.id_evenement 
                    ORDER BY r.id_reservation DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getAll réservations: " . $e->getMessage());
            return [];
        }
    }
    
    public function getByEvent($eventId) {
        try {
            $stmt = $this->db->query("SELECT * FROM reservation WHERE id_evenement = ?", [$eventId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getByEvent: " . $e->getMessage());
            return [];
        }
    }
}
?>