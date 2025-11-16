<?php
// models/EventModel.php
class EventModel {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // ==================== CRUD CATÉGORIES ====================
    
    /**
     * Récupère toutes les catégories
     */
    public function getAllCategories() {
        try {
            $sql = "SELECT * FROM categoriev ORDER BY nom_categorie";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getAllCategories: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère une catégorie par son ID
     */
    public function getCategoryById($id) {
        try {
            $sql = "SELECT * FROM categoriev WHERE id_categorie = ?";
            $stmt = $this->db->query($sql, [$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Erreur getCategoryById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Ajoute une nouvelle catégorie
     */
    public function addCategory($nom, $description) {
        try {
            $sql = "INSERT INTO categoriev (nom_categorie, description_categorie) VALUES (?, ?)";
            $stmt = $this->db->query($sql, [$nom, $description]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Erreur addCategory: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime une catégorie
     */
    public function deleteCategory($id) {
        try {
            $sql = "DELETE FROM categoriev WHERE id_categorie = ?";
            $stmt = $this->db->query($sql, [$id]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Erreur deleteCategory: " . $e->getMessage());
            return false;
        }
    }
    
    // ==================== CRUD ÉVÉNEMENTS ====================
    
    /**
     * Récupère tous les événements avec leurs catégories
     */
    public function getAllEvents() {
        try {
            $sql = "SELECT e.*, c.nom_categorie 
                    FROM evenement e 
                    LEFT JOIN categoriev c ON e.id_categorie = c.id_categorie 
                    ORDER BY e.date_evenement DESC";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getAllEvents: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Récupère un événement par son ID
     */
    public function getEventById($id) {
        try {
            $sql = "SELECT e.*, c.nom_categorie 
                    FROM evenement e 
                    LEFT JOIN categoriev c ON e.id_categorie = c.id_categorie 
                    WHERE e.id_evenement = ?";
            $stmt = $this->db->query($sql, [$id]);
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Erreur getEventById: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Récupère les événements d'une catégorie spécifique
     */
    public function getEventsByCategory($categoryId) {
        try {
            $sql = "SELECT e.*, c.nom_categorie 
                    FROM evenement e 
                    LEFT JOIN categoriev c ON e.id_categorie = c.id_categorie 
                    WHERE e.id_categorie = ? 
                    ORDER BY e.date_evenement DESC";
            $stmt = $this->db->query($sql, [$categoryId]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getEventsByCategory: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Crée un nouvel événement
     */
    public function createEvent($data) {
        try {
            $sql = "INSERT INTO evenement (titre, description, date_evenement, lieu, id_categorie) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->query($sql, [
                $data['titre'],
                $data['description'],
                $data['date_evenement'],
                $data['lieu'],
                $data['id_categorie']
            ]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Erreur createEvent: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Met à jour un événement existant
     */
    public function updateEvent($id, $data) {
        try {
            $sql = "UPDATE evenement 
                    SET titre = ?, description = ?, date_evenement = ?, lieu = ?, id_categorie = ?
                    WHERE id_evenement = ?";
            $stmt = $this->db->query($sql, [
                $data['titre'],
                $data['description'],
                $data['date_evenement'],
                $data['lieu'],
                $data['id_categorie'],
                $id
            ]);
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Erreur updateEvent: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Supprime un événement et ses réservations associées
     */
    public function deleteEvent($id) {
        try {
            // Commencer une transaction
            $this->db->getConnection()->beginTransaction();
            
            // 1. Supprimer les réservations associées
            $sql_reservations = "DELETE FROM reservation WHERE id_evenement = ?";
            $this->db->query($sql_reservations, [$id]);
            
            // 2. Supprimer l'événement
            $sql_event = "DELETE FROM evenement WHERE id_evenement = ?";
            $stmt = $this->db->query($sql_event, [$id]);
            $success = $stmt->rowCount() > 0;
            
            // Valider la transaction
            $this->db->getConnection()->commit();
            
            return $success;
            
        } catch (Exception $e) {
            // Annuler la transaction en cas d'erreur
            $this->db->getConnection()->rollBack();
            error_log("Erreur deleteEvent: " . $e->getMessage());
            return false;
        }
    }
    
    // ==================== MÉTHODES UTILITAIRES ====================
    
    /**
     * Vérifie si une catégorie existe
     */
    public function categoryExists($id) {
        try {
            $sql = "SELECT COUNT(*) FROM categoriev WHERE id_categorie = ?";
            $stmt = $this->db->query($sql, [$id]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log("Erreur categoryExists: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Vérifie si un événement existe
     */
    public function eventExists($id) {
        try {
            $sql = "SELECT COUNT(*) FROM evenement WHERE id_evenement = ?";
            $stmt = $this->db->query($sql, [$id]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log("Erreur eventExists: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Compte le nombre d'événements par catégorie
     */
    public function countEventsByCategory($categoryId) {
        try {
            $sql = "SELECT COUNT(*) FROM evenement WHERE id_categorie = ?";
            $stmt = $this->db->query($sql, [$categoryId]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            error_log("Erreur countEventsByCategory: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Récupère les événements à venir
     */
    public function getUpcomingEvents($limit = 5) {
        try {
            $sql = "SELECT e.*, c.nom_categorie 
                    FROM evenement e 
                    LEFT JOIN categoriev c ON e.id_categorie = c.id_categorie 
                    WHERE e.date_evenement >= NOW() 
                    ORDER BY e.date_evenement ASC 
                    LIMIT ?";
            $stmt = $this->db->query($sql, [$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur getUpcomingEvents: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Recherche des événements par titre ou description
     */
    public function searchEvents($query) {
        try {
            $sql = "SELECT e.*, c.nom_categorie 
                    FROM evenement e 
                    LEFT JOIN categoriev c ON e.id_categorie = c.id_categorie 
                    WHERE e.titre LIKE ? OR e.description LIKE ? 
                    ORDER BY e.date_evenement DESC";
            $searchTerm = "%$query%";
            $stmt = $this->db->query($sql, [$searchTerm, $searchTerm]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Erreur searchEvents: " . $e->getMessage());
            return [];
        }
    }
}
?>