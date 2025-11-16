<?php

require_once __DIR__ . '/AttenteMatchController.php';
require_once __DIR__ . '/SessionMatchController.php';
require_once __DIR__ . '/BaseController.php';

class MatchmakingAdminController extends BaseController {
    private $attenteController;
    private $sessionController;
    private $attenteModel;
    private $sessionModel;
    private $matchService;
    
    public function __construct() {
        try {
            $this->attenteController = new AttenteMatchController();
            $this->sessionController = new SessionMatchController();
            require_once __DIR__ . '/../../Models/backoffice/AttenteMatchModel.php';
            require_once __DIR__ . '/../../Models/backoffice/SessionMatchModel.php';
            require_once __DIR__ . '/services/MatchService.php';
            $this->attenteModel = new AttenteMatchModel();
            $this->sessionModel = new SessionMatchModel();
            $this->matchService = new MatchService();
        } catch (Exception $e) {
            error_log("Erreur MatchmakingAdminController::__construct: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function afficherPage() {
        $message = '';
        $messageType = '';
        $attentes = [];
        $sessions = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            try {
                if ($_POST['action'] === 'verifier_matchs') {
                    $idJeu = isset($_POST['id_jeu']) ? (int)$_POST['id_jeu'] : 0;
                    if ($idJeu > 0) {
                        $resultat = $this->matchService->verifierMatchs($idJeu);
                        $message = "Vérification effectuée. " . $resultat['matchs_crees'] . " match(s) créé(s).";
                        $messageType = 'success';
                    }
                } elseif ($_POST['action'] === 'supprimer_attente') {
                    $idAttente = isset($_POST['id_attente']) ? (int)$_POST['id_attente'] : 0;
                    if ($idAttente > 0) {
                        if ($this->attenteModel->supprimerAttente($idAttente)) {
                            $message = 'Attente supprimée avec succès';
                            $messageType = 'success';
                        } else {
                            $message = 'Erreur lors de la suppression';
                            $messageType = 'error';
                        }
                    }
                } elseif ($_POST['action'] === 'supprimer_session') {
                    $idSession = isset($_POST['id_session']) ? (int)$_POST['id_session'] : 0;
                    if ($idSession > 0) {
                        if ($this->sessionModel->supprimerSession($idSession)) {
                            $message = 'Session supprimée avec succès';
                            $messageType = 'success';
                        } else {
                            $message = 'Erreur lors de la suppression';
                            $messageType = 'error';
                        }
                    }
                } elseif ($_POST['action'] === 'modifier_session') {
                    $idSession = isset($_POST['id_session']) ? (int)$_POST['id_session'] : 0;
                    $statut = isset($_POST['statut']) ? trim($_POST['statut']) : '';
                    if ($idSession > 0 && in_array($statut, ['active', 'terminee', 'annulee'])) {
                        if ($this->sessionModel->updateStatut($idSession, $statut)) {
                            $message = 'Session modifiée avec succès';
                            $messageType = 'success';
                        } else {
                            $message = 'Erreur lors de la modification';
                            $messageType = 'error';
                        }
                    }
                } elseif ($_POST['action'] === 'nettoyer_attentes') {
                    $jours = isset($_POST['jours']) ? (int)$_POST['jours'] : 7;
                    $supprimees = $this->attenteModel->nettoyerAnciennesAttentes($jours);
                    $message = "$supprimees ancienne(s) attente(s) supprimée(s)";
                    $messageType = 'success';
                }
            } catch (Exception $e) {
                $message = 'Erreur: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
        
        try {
            $attentes = $this->attenteModel->getAllAttentesActives();
            $sessions = $this->sessionModel->getAllSessions();
        } catch (Exception $e) {
            $message = 'Erreur de connexion: ' . $e->getMessage();
            $messageType = 'error';
            $attentes = [];
            $sessions = [];
        }
        
        $attentesParJeu = [];
        if (is_array($attentes)) {
            foreach ($attentes as $attente) {
                $idJeu = $attente['id_jeu'];
                if (!isset($attentesParJeu[$idJeu])) {
                    $attentesParJeu[$idJeu] = [
                        'id_jeu' => $idJeu,
                        'nom_jeu' => isset($attente['nom_jeu']) ? $attente['nom_jeu'] : 'Jeu inconnu',
                        'attentes' => []
                    ];
                }
                $attentesParJeu[$idJeu]['attentes'][] = $attente;
            }
        }
        
        $data = [
            'message' => $message,
            'messageType' => $messageType,
            'attentes' => $attentes,
            'attentesParJeu' => $attentesParJeu,
            'sessions' => $sessions
        ];
        
        $this->view('matchmaking_admin', $data);
    }
}

?>

