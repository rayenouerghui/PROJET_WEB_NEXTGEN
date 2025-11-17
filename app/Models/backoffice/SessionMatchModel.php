<?php

require_once __DIR__ . '/../../../config/db.php';

class SessionMatchModel {
    private $db;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
    }
    
    public function creerSession($idJeu, $participants, $lienSession) {
        try {
            $participantsJson = json_encode($participants);
            
            $stmt = $this->db->prepare("
                INSERT INTO sessionmatch (id_jeu, lien_session, date_creation, participants, statut)
                VALUES (:id_jeu, :lien_session, NOW(), :participants, 'active')
            ");
            
            $stmt->execute([
                ':id_jeu' => $idJeu,
                ':lien_session' => $lienSession,
                ':participants' => $participantsJson
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur SessionMatchModel::creerSession: " . $e->getMessage());
            return false;
        }
    }
    
    public function getSession($idSession) {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, j.titre AS nom_jeu
                FROM sessionmatch s
                INNER JOIN jeu j ON s.id_jeu = j.id_jeu
                WHERE s.id_session = :id_session
            ");
            $stmt->execute([':id_session' => $idSession]);
            
            $session = $stmt->fetch();
            if ($session) {
                $session['participants'] = json_decode($session['participants'], true);
            }
            
            return $session;
        } catch (PDOException $e) {
            error_log("Erreur SessionMatchModel::getSession: " . $e->getMessage());
            return false;
        }
    }
    
    public function getSessionsUtilisateur($idUtilisateur) {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, j.titre AS nom_jeu
                FROM sessionmatch s
                INNER JOIN jeu j ON s.id_jeu = j.id_jeu
                WHERE s.statut = 'active'
                ORDER BY s.date_creation DESC
            ");
            $stmt->execute();
            
            $sessions = $stmt->fetchAll();
            $sessionsUtilisateur = [];
            
            foreach ($sessions as $session) {
                $participants = json_decode($session['participants'], true);
                if (is_array($participants) && in_array($idUtilisateur, $participants)) {
                    $session['participants'] = $participants;
                    $sessionsUtilisateur[] = $session;
                }
            }
            
            return $sessionsUtilisateur;
        } catch (PDOException $e) {
            error_log("Erreur SessionMatchModel::getSessionsUtilisateur: " . $e->getMessage());
            return [];
        }
    }
    
    public function getAllSessions($statut = null) {
        try {
            $sql = "
                SELECT s.*, j.titre AS nom_jeu
                FROM sessionmatch s
                INNER JOIN jeu j ON s.id_jeu = j.id_jeu
            ";
            
            if ($statut !== null) {
                $sql .= " WHERE s.statut = :statut";
            }
            
            $sql .= " ORDER BY s.date_creation DESC";
            
            $stmt = $this->db->prepare($sql);
            
            if ($statut !== null) {
                $stmt->execute([':statut' => $statut]);
            } else {
                $stmt->execute();
            }
            
            $sessions = $stmt->fetchAll();
            foreach ($sessions as &$session) {
                $session['participants'] = json_decode($session['participants'], true);
            }
            
            return $sessions;
        } catch (PDOException $e) {
            error_log("Erreur SessionMatchModel::getAllSessions: " . $e->getMessage());
            return [];
        }
    }
    
    public function updateStatut($idSession, $statut) {
        try {
            $stmt = $this->db->prepare("
                UPDATE sessionmatch 
                SET statut = :statut 
                WHERE id_session = :id_session
            ");
            
            return $stmt->execute([
                ':id_session' => $idSession,
                ':statut' => $statut
            ]);
        } catch (PDOException $e) {
            error_log("Erreur SessionMatchModel::updateStatut: " . $e->getMessage());
            return false;
        }
    }
    
    public function supprimerSession($idSession) {
        try {
            $stmt = $this->db->prepare("DELETE FROM sessionmatch WHERE id_session = :id_session");
            return $stmt->execute([':id_session' => $idSession]);
        } catch (PDOException $e) {
            error_log("Erreur SessionMatchModel::supprimerSession: " . $e->getMessage());
            return false;
        }
    }
    
    public function genererLienSession() {
        $uuid = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        
        return "session/" . $uuid;
    }
    
    public function genererLienDiscord($idSession) {
        $configFile = __DIR__ . '/../../../config/discord.php';
        
        if (file_exists($configFile)) {
            $config = require $configFile;
            $codeServeur = $config['server_invite_code'];
            $codeValide = !empty($codeServeur) && $codeServeur !== 'VOTRE_CODE_ICI';
            
            if ($codeValide) {
                return 'https://discord.gg/' . $codeServeur;
            }
        }
        
        return null;
    }
}

?>