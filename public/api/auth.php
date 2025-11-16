<?php

require_once __DIR__ . "/../../app/Controllers/frontoffice/AuthController.php";

header('Content-Type: application/json');

$authController = new AuthController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'login':
        $result = $authController->login();
        echo json_encode($result);
        break;
        
    case 'register':
        $result = $authController->register();
        echo json_encode($result);
        break;
        
    case 'logout':
        $result = $authController->logout();
        echo json_encode($result);
        break;
        
    case 'getCurrentUser':
        $user = $authController->getCurrentUser();
        echo json_encode(['success' => true, 'user' => $user]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Action non valide']);
        break;
}

?>

