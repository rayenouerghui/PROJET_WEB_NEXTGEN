<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response
ini_set('log_errors', 1);

require_once __DIR__ . "/../../app/Controllers/frontoffice/AuthController.php";

header('Content-Type: application/json');

try {
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
} catch (Exception $e) {
    error_log("auth.php - Exception: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Une erreur est survenue: ' . $e->getMessage()
    ]);
}

?>

