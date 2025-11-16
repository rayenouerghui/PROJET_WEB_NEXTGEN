<?php

require_once __DIR__ . "/../../../app/Controllers/backoffice/GameAdminController.php";

header('Content-Type: application/json');

$controller = new GameAdminController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getAll':
        $games = $controller->getAllGames();
        echo json_encode(['success' => true, 'games' => $games]);
        break;
        
    case 'getById':
        $id = $_GET['id'] ?? 0;
        $game = $controller->getGameById($id);
        if ($game) {
            echo json_encode(['success' => true, 'game' => $game]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Jeu introuvable']);
        }
        break;
        
    case 'create':
        $result = $controller->createGame();
        echo json_encode($result);
        break;
        
    case 'update':
        $id = $_GET['id'] ?? 0;
        $result = $controller->updateGame($id);
        echo json_encode($result);
        break;
        
    case 'delete':
        $id = $_GET['id'] ?? 0;
        $result = $controller->deleteGame($id);
        echo json_encode($result);
        break;
        
    case 'getCategories':
        $categories = $controller->getAllCategories();
        echo json_encode(['success' => true, 'categories' => $categories]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Action non valide']);
        break;
}

?>

