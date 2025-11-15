<?php

require_once __DIR__ . '/../models/AttenteMatchModel.php';
require_once __DIR__ . '/../models/SessionMatchModel.php';
require_once __DIR__ . '/EmailService.php';
require_once __DIR__ . '/../config/db.php';

class MatchService {
    private $attenteModel;
    private $sessionModel;
    private $emailService;
    private $minJoueurs = 2;
    
    public function __construct() {
        $this->attenteModel = new AttenteMatchModel();
        $this->sessionModel = new SessionMatchModel();
        $this->emailService = new EmailService();
    }
    
    public function verifierMatchs($idJeu) {
        $matchsCrees = 0;
        
        try {
            $attentes = $this->attenteModel->getAttentesParJeu($idJeu, 10);
            
            if (count($attentes) < $this->minJoueurs) {
                return ['matchs_crees' => 0, 'message' => 'Pas assez de joueurs en attente'];
            }
            
            $groupes = array_chunk($attentes, $this->minJoueurs);
            
            foreach ($groupes as $groupe) {
                if (count($groupe) >= $this->minJoueurs) {
                    $participants = array_map(function($attente) {
                        return (int)$attente['id_utilisateur'];
                    }, $groupe);
                    
                    $lienSession = $this->sessionModel->genererLienSession();
                    $idSession = $this->sessionModel->creerSession($idJeu, $participants, $lienSession);
                    
                    if ($idSession) {
                        $idsAttente = array_map(function($attente) {
                            return (int)$attente['id_attente'];
                        }, $groupe);
                        
                        $this->attenteModel->marquerCommeMatched($idsAttente);
                        $lienDiscord = $this->sessionModel->genererLienDiscord($idSession);
                        $this->envoyerEmailsMatch($idSession, $participants, $lienSession, $idJeu, $lienDiscord);
                        $matchsCrees++;
                    }
                }
            }
            
            return ['matchs_crees' => $matchsCrees];
            
        } catch (Exception $e) {
            error_log("Erreur MatchService::verifierMatchs: " . $e->getMessage());
            return ['matchs_crees' => 0, 'erreur' => $e->getMessage()];
        }
    }
    
    private function envoyerEmailsMatch($idSession, $participants, $lienSession, $idJeu, $lienDiscord) {
        try {
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("SELECT nom FROM jeux WHERE id_jeu = :id_jeu");
            $stmt->execute([':id_jeu' => $idJeu]);
            $jeu = $stmt->fetch();
            $nomJeu = $jeu ? $jeu['nom'] : 'le jeu';
            
            $placeholders = implode(',', array_fill(0, count($participants), '?'));
            $stmt = $db->prepare("
                SELECT id_utilisateur, email, nom, prenom 
                FROM utilisateurs 
                WHERE id_utilisateur IN ($placeholders)
            ");
            $stmt->execute($participants);
            $utilisateurs = $stmt->fetchAll();
            
            foreach ($utilisateurs as $utilisateur) {
                $this->emailService->envoyerEmailMatch(
                    $utilisateur['email'],
                    $utilisateur['prenom'] . ' ' . $utilisateur['nom'],
                    $nomJeu,
                    $lienSession,
                    $idSession,
                    $lienDiscord
                );
            }
            
        } catch (Exception $e) {
            error_log("Erreur MatchService::envoyerEmailsMatch: " . $e->getMessage());
        }
    }
    
    public function verifierTousLesMatchs() {
        try {
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("
                SELECT DISTINCT id_jeu 
                FROM AttenteMatch 
                WHERE matched = FALSE
            ");
            $stmt->execute();
            $jeux = $stmt->fetchAll();
            
            $totalMatchs = 0;
            foreach ($jeux as $jeu) {
                $resultat = $this->verifierMatchs($jeu['id_jeu']);
                $totalMatchs += $resultat['matchs_crees'];
            }
            
            return ['matchs_crees' => $totalMatchs];
            
        } catch (Exception $e) {
            error_log("Erreur MatchService::verifierTousLesMatchs: " . $e->getMessage());
            return ['matchs_crees' => 0, 'erreur' => $e->getMessage()];
        }
    }
}

?>
