<?php

require_once __DIR__ . '/../../../config/db.php';

class AttenteMatchModel {
    private $db;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    public function ajouterAttente($idUtilisateur, $idJeu) {
        try {
            $stmt = $this->db->prepare("
                SELECT id_attente FROM attentematch 
                WHERE id_user = :id_utilisateur 
                AND id_jeu = :id_jeu 
                AND matched = FALSE
            ");
            $stmt->execute([
                ':id_utilisateur' => $idUtilisateur,
                ':id_jeu' => $idJeu
            ]);
            
            if ($stmt->fetch()) {
                return false;
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO attentematch (id_user, id_jeu, date_ajout, matched)
                VALUES (:id_utilisateur, :id_jeu, NOW(), FALSE)
            ");
            
            $stmt->execute([
                ':id_utilisateur' => $idUtilisateur,
                ':id_jeu' => $idJeu
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur AttenteMatchModel::ajouterAttente: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAttentesParJeu($idJeu, $limit = 2) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, u.email, u.nom, u.prenom, j.titre AS nom_jeu
                FROM attentematch a
                INNER JOIN utilisateur u ON a.id_user = u.id_user
                INNER JOIN jeu j ON a.id_jeu = j.id_jeu
                WHERE a.id_jeu = :id_jeu 
                AND a.matched = FALSE
                ORDER BY a.date_ajout ASC
                LIMIT :limit
            ");
            $stmt->bindValue(':id_jeu', $idJeu, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur AttenteMatchModel::getAttentesParJeu: " . $e->getMessage());
            return [];
        }
    }
    
    public function marquerCommeMatched($idsAttente) {
        try {
            if (empty($idsAttente)) {
                return false;
            }
            
            $placeholders = implode(',', array_fill(0, count($idsAttente), '?'));
            $stmt = $this->db->prepare("
                UPDATE AttenteMatch 
                SET matched = TRUE 
                WHERE id_attente IN ($placeholders)
            ");
            
            return $stmt->execute($idsAttente);
        } catch (PDOException $e) {
            error_log("Erreur AttenteMatchModel::marquerCommeMatched: " . $e->getMessage());
            return false;
        }
    }
    
    public function utilisateurAcheteJeu($idUtilisateur, $idJeu) {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count
                FROM jeu_achete
                WHERE user_id = :id_utilisateur
                AND jeu_id = :id_jeu
            ");
            $stmt->execute([
                ':id_utilisateur' => $idUtilisateur,
                ':id_jeu' => $idJeu
            ]);
            
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Erreur AttenteMatchModel::utilisateurAcheteJeu: " . $e->getMessage());
            return false;
        }
    }
    
    public function getAllAttentesActives() {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, u.email, u.nom, u.prenom, j.titre AS nom_jeu
                FROM attentematch a
                INNER JOIN utilisateur u ON a.id_user = u.id_user
                INNER JOIN jeu j ON a.id_jeu = j.id_jeu
                WHERE a.matched = FALSE
                ORDER BY a.date_ajout DESC
            ");
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur AttenteMatchModel::getAllAttentesActives: " . $e->getMessage());
            return [];
        }
    }
    
    public function nettoyerAnciennesAttentes($joursAncien = 7) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM attentematch
                WHERE matched = TRUE
                AND date_ajout < DATE_SUB(NOW(), INTERVAL :jours DAY)
            ");
            $stmt->execute([':jours' => $joursAncien]);
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Erreur AttenteMatchModel::nettoyerAnciennesAttentes: " . $e->getMessage());
            return 0;
        }
    }
    
    public function supprimerAttente($idAttente) {
        try {
            $stmt = $this->db->prepare("DELETE FROM attentematch WHERE id_attente = :id_attente");
            return $stmt->execute([':id_attente' => $idAttente]);
        } catch (PDOException $e) {
            error_log("Erreur AttenteMatchModel::supprimerAttente: " . $e->getMessage());
            return false;
        }
    }
}

?>