<?php

require_once __DIR__ . '/../../Models/backoffice/AttenteMatchModel.php';
require_once __DIR__ . '/../../Models/backoffice/SessionMatchModel.php';
require_once __DIR__ . '/../backoffice/services/MatchService.php';
require_once __DIR__ . '/../../../config/db.php';

class MatchController {
    private $attenteModel;
    private $sessionModel;
    private $matchService;
    
    public function __construct() {
        try {
            $this->attenteModel = new AttenteMatchModel();
            $this->sessionModel = new SessionMatchModel();
            $this->matchService = new MatchService();
        } catch (Exception $e) {
            error_log("Erreur MatchController::__construct: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function ajouterAttente() {
        header('Content-Type: application/json');
        
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['id_utilisateur']) || !isset($data['id_jeu'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Données manquantes: id_utilisateur et id_jeu requis'
                ]);
                return;
            }
            
            $idUtilisateur = (int)$data['id_utilisateur'];
            $idJeu = (int)$data['id_jeu'];
            
            if ($idUtilisateur <= 0 || $idJeu <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'IDs invalides'
                ]);
                return;
            }
            
            if (!$this->attenteModel->utilisateurAcheteJeu($idUtilisateur, $idJeu)) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Vous devez avoir acheté ce jeu pour trouver un match'
                ]);
                return;
            }
            
            $idAttente = $this->attenteModel->ajouterAttente($idUtilisateur, $idJeu);
            
            if ($idAttente === false) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Vous êtes déjà en attente pour ce jeu'
                ]);
                return;
            }
            
            $resultat = $this->matchService->verifierMatchs($idJeu);
            
            http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'Vous avez été ajouté à la file d\'attente',
                'id_attente' => $idAttente,
                'match_immediat' => ($resultat['matchs_crees'] > 0)
            ]);
            
        } catch (Exception $e) {
            error_log("Erreur MatchController::ajouterAttente: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
    
    public function statutAttente() {
        header('Content-Type: application/json');
        
        try {
            $idUtilisateur = isset($_GET['id_utilisateur']) ? (int)$_GET['id_utilisateur'] : 0;
            $idJeu = isset($_GET['id_jeu']) ? (int)$_GET['id_jeu'] : 0;
            
            if ($idUtilisateur === 0 || $idJeu === 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Paramètres manquants'
                ]);
                return;
            }
            
            $sessions = $this->sessionModel->getSessionsUtilisateur($idUtilisateur);
            $sessionJeu = null;
            
            foreach ($sessions as $session) {
                if ($session['id_jeu'] == $idJeu && $session['statut'] == 'active') {
                    $sessionJeu = $session;
                    break;
                }
            }
            
            if ($sessionJeu) {
                echo json_encode([
                    'success' => true,
                    'matched' => true,
                    'session' => $sessionJeu
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'matched' => false,
                    'message' => 'En attente d\'un match...'
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
    
    public function jeuxAchetes() {
        header('Content-Type: application/json');
        
        try {
            $idUtilisateur = isset($_GET['id_utilisateur']) ? (int)$_GET['id_utilisateur'] : 0;
            
            if ($idUtilisateur === 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID utilisateur requis'
                ]);
                return;
            }
            
            $db = Database::getInstance()->getConnection();
            
            $stmt = $db->prepare("
                SELECT DISTINCT j.id_jeu, j.titre AS nom, j.src_img AS image_url, '' AS categorie, j.prix
                FROM jeu_achete ja
                INNER JOIN jeu j ON ja.jeu_id = j.id_jeu
                WHERE ja.user_id = :id_utilisateur
                ORDER BY ja.date_achat DESC
            ");
            $stmt->execute([':id_utilisateur' => $idUtilisateur]);
            
            $jeux = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'jeux' => $jeux
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }
    
    public function afficherPage() {
        $message = '';
        $messageType = '';
        $jeux = [];
        $sessionActive = null;
        $idUtilisateur = isset($_GET['id_utilisateur']) ? (int)$_GET['id_utilisateur'] : 1;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'ajouter_attente') {
            try {
                $idJeu = isset($_POST['id_jeu']) ? (int)$_POST['id_jeu'] : 0;
                
                if ($idUtilisateur > 0 && $idJeu > 0) {
                    if (!$this->attenteModel->utilisateurAcheteJeu($idUtilisateur, $idJeu)) {
                        $message = 'Vous devez avoir acheté ce jeu pour trouver un match';
                        $messageType = 'error';
                    } else {
                        $idAttente = $this->attenteModel->ajouterAttente($idUtilisateur, $idJeu);
                        if ($idAttente === false) {
                            $message = 'Vous êtes déjà en attente pour ce jeu';
                            $messageType = 'warning';
                        } else {
                            $resultat = $this->matchService->verifierMatchs($idJeu);
                            if ($resultat['matchs_crees'] > 0) {
                                $message = 'Match trouvé ! Une session a été créée.';
                                $messageType = 'success';
                            } else {
                                $message = 'Vous avez été ajouté à la file d\'attente. En attente d\'autres joueurs...';
                                $messageType = 'info';
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                $message = 'Erreur: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                SELECT DISTINCT j.id_jeu, j.titre AS nom, j.src_img AS image_url, '' AS categorie, j.prix
                FROM jeu_achete ja
                INNER JOIN jeu j ON ja.jeu_id = j.id_jeu
                WHERE ja.user_id = :id_utilisateur
                ORDER BY ja.date_achat DESC
            ");
            $stmt->execute([':id_utilisateur' => $idUtilisateur]);
            $jeux = $stmt->fetchAll();
            
            $sessions = $this->sessionModel->getSessionsUtilisateur($idUtilisateur);
            if (!empty($sessions)) {
                $sessionActive = $sessions[0];
            }
        } catch (Exception $e) {
            $message = 'Erreur de connexion: ' . $e->getMessage();
            $messageType = 'error';
        }
        
        $lienDiscord = null;
        $sessionPageUrl = '#';
        if ($sessionActive) {
            $lienDiscord = $this->sessionModel->genererLienDiscord($sessionActive['id_session']);
            $lienSession = $sessionActive['lien_session'];
            if (strpos($lienSession, 'http') === 0) {
                $sessionUuid = basename(parse_url($lienSession, PHP_URL_PATH));
            } else {
                $sessionUuid = basename($lienSession);
            }
            $sessionPageUrl = '../session.php?uuid=' . urlencode($sessionUuid);
        }
        
        require_once __DIR__ . '/../../Views/frontoffice/matchmaking_view.php';
    }
    
    public function afficherSession() {
        $uuid = isset($_GET['uuid']) ? trim($_GET['uuid']) : '';
        $session = null;
        $lienDiscord = null;
        
        if (!empty($uuid)) {
            try {
                $db = Database::getInstance()->getConnection();
                
                $stmt = $db->prepare("
                    SELECT s.*, j.nom as nom_jeu
                    FROM SessionMatch s
                    INNER JOIN jeux j ON s.id_jeu = j.id_jeu
                    WHERE (s.lien_session LIKE :uuid1 OR s.lien_session LIKE :uuid2)
                    AND s.statut = 'active'
                    ORDER BY s.date_creation DESC
                    LIMIT 1
                ");
                $stmt->execute([
                    ':uuid1' => 'session/' . $uuid,
                    ':uuid2' => '%/' . $uuid
                ]);
                $session = $stmt->fetch();
                
                if ($session) {
                    $session['participants'] = json_decode($session['participants'], true);
                    $lienDiscord = $this->sessionModel->genererLienDiscord($session['id_session']);
                }
            } catch (Exception $e) {
                error_log("Erreur MatchController::afficherSession: " . $e->getMessage());
            }
        }
        
        require_once __DIR__ . '/../../Views/frontoffice/session_view.php';
    }
}

?>
