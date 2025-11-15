<?php

require_once __DIR__ . '/../models/AttenteMatchModel.php';
require_once __DIR__ . '/../models/SessionMatchModel.php';
require_once __DIR__ . '/../services/MatchService.php';

class AttenteMatchController {
    private $attenteModel;
    private $sessionModel;
    private $matchService;
    
    public function __construct() {
        try {
            $this->attenteModel = new AttenteMatchModel();
            $this->sessionModel = new SessionMatchModel();
            $this->matchService = new MatchService();
        } catch (Exception $e) {
            error_log("Erreur AttenteMatchController::__construct: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function liste() {
        header('Content-Type: application/json');
        
        try {
            if (!$this->attenteModel) {
                throw new Exception("Modèle non initialisé");
            }
            
            $attentes = $this->attenteModel->getAllAttentesActives();
            
            $attentesParJeu = [];
            foreach ($attentes as $attente) {
                $idJeu = $attente['id_jeu'];
                if (!isset($attentesParJeu[$idJeu])) {
                    $attentesParJeu[$idJeu] = [
                        'id_jeu' => $idJeu,
                        'nom_jeu' => $attente['nom_jeu'],
                        'attentes' => []
                    ];
                }
                $attentesParJeu[$idJeu]['attentes'][] = $attente;
            }
            
            echo json_encode([
                'success' => true,
                'attentes' => array_values($attentesParJeu)
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
    
    public function supprimer() {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $idAttente = isset($data['id_attente']) ? (int)$data['id_attente'] : 0;
            
            if ($idAttente === 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID attente requis'
                ]);
                return;
            }
            
            $success = $this->attenteModel->supprimerAttente($idAttente);
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Attente supprimée'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression'
                ]);
            }
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
    
    public function verifierMatchs() {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $idJeu = isset($data['id_jeu']) ? (int)$data['id_jeu'] : 0;
            
            if ($idJeu === 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID jeu requis'
                ]);
                return;
            }
            
            $resultat = $this->matchService->verifierMatchs($idJeu);
            
            echo json_encode([
                'success' => true,
                'message' => 'Vérification effectuée',
                'matchs_crees' => $resultat['matchs_crees']
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
    
    public function nettoyer() {
        header('Content-Type: application/json');
        
        try {
            $jours = isset($_GET['jours']) ? (int)$_GET['jours'] : 7;
            $supprimees = $this->attenteModel->nettoyerAnciennesAttentes($jours);
            
            echo json_encode([
                'success' => true,
                'message' => "$supprimees attentes supprimées",
                'supprimees' => $supprimees
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
}

?>


