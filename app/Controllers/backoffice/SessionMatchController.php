<?php

require_once __DIR__ . '/../../Models/backoffice/SessionMatchModel.php';

class SessionMatchController {
    private $sessionModel;
    
    public function __construct() {
        try {
            $this->sessionModel = new SessionMatchModel();
        } catch (Exception $e) {
            error_log("Erreur SessionMatchController::__construct: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function liste() {
        header('Content-Type: application/json');
        
        try {
            $statut = isset($_GET['statut']) ? $_GET['statut'] : null;
            $sessions = $this->sessionModel->getAllSessions($statut);
            
            echo json_encode([
                'success' => true,
                'sessions' => $sessions
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
            $idSession = isset($data['id_session']) ? (int)$data['id_session'] : 0;
            
            if ($idSession === 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID session requis'
                ]);
                return;
            }
            
            $success = $this->sessionModel->supprimerSession($idSession);
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Session supprimée'
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
    
    public function modifier() {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $idSession = isset($data['id_session']) ? (int)$data['id_session'] : 0;
            $statut = isset($data['statut']) ? trim($data['statut']) : '';
            
            if ($idSession === 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID session requis'
                ]);
                return;
            }
            
            if (!in_array($statut, ['active', 'terminee', 'annulee'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Statut invalide. Valeurs autorisées: active, terminee, annulee'
                ]);
                return;
            }
            
            $success = $this->sessionModel->updateStatut($idSession, $statut);
            
            if ($success) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Session modifiée avec succès'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la modification'
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
}

?>


