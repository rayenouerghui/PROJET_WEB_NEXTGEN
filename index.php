<?php

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

session_start();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

if ($controller === 'matchmaking' || strpos($path, '/api/') !== false || strpos($path, 'matchmaking') !== false) {
    
    $isAdmin = (strpos($path, '/backoffice/') !== false || 
                strpos($path, '/admin/') !== false || 
                strpos($path, '/api/admin/') !== false) ||
               in_array($action, ['get_attentes', 'get_sessions', 'verifier_matchs', 'supprimer_attente', 'supprimer_session', 'modifier_session', 'nettoyer_attentes']);
    
    if ($isAdmin) {
        
        try {
            require_once __DIR__ . '/backoffice/controllers/AttenteMatchController.php';
            require_once __DIR__ . '/backoffice/controllers/SessionMatchController.php';
            
            $attenteController = new AttenteMatchController();
            $sessionController = new SessionMatchController();
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Erreur d\'initialisation: ' . $e->getMessage()
            ]);
            exit;
        }
        
        switch ($action) {
            case 'get_attentes':
                $attenteController->liste();
                break;
            case 'get_sessions':
                $sessionController->liste();
                break;
            case 'verifier_matchs':
                $attenteController->verifierMatchs();
                break;
            case 'supprimer_attente':
                $attenteController->supprimer();
                break;
            case 'supprimer_session':
                $sessionController->supprimer();
                break;
            case 'modifier_session':
                $sessionController->modifier();
                break;
            case 'nettoyer_attentes':
                $attenteController->nettoyer();
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
                break;
        }
        exit;
        
    } else {
        try {
            require_once __DIR__ . '/frontoffice/controllers/MatchController.php';
            
            $matchController = new MatchController();
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Erreur d\'initialisation: ' . $e->getMessage()
            ]);
            exit;
        }
        
        switch ($action) {
            case 'ajouter_attente':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $matchController->ajouterAttente();
                } else {
                    http_response_code(405);
                    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                }
                break;
            case 'statut_attente':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $matchController->statutAttente();
                } else {
                    http_response_code(405);
                    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                }
                break;
            case 'jeux_achetes':
                if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                    $matchController->jeuxAchetes();
                } else {
                    http_response_code(405);
                    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
                }
                break;
            default:
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Action non reconnue']);
                break;
        }
        exit;
    }
    
} else {
    if (strpos($path, '/backoffice/') !== false) {
        header('Location: backoffice/index.html');
    } else {
        header('Location: frontoffice/index.html');
    }
    exit;
}

?>
