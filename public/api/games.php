<?php

require_once __DIR__ . "/../../app/Controllers/frontoffice/GameController.php";
require_once __DIR__ . "/../../app/Controllers/frontoffice/AuthController.php";

header('Content-Type: application/json');

session_start();
$authController = new AuthController();
$gameController = new GameController();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'getAll':
        $games = $gameController->getAllGames();
        echo json_encode(['success' => true, 'games' => $games]);
        break;
        
    case 'getById':
        $id = $_GET['id'] ?? 0;
        $game = $gameController->getGameById($id);
        if ($game) {
            $userId = $_SESSION['user_id'] ?? null;
            $isPurchased = $userId ? $gameController->isGamePurchased($userId, $id) : false;
            $game['isPurchased'] = $isPurchased;
            echo json_encode(['success' => true, 'game' => $game]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Jeu introuvable']);
        }
        break;
        
    case 'purchase':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
            break;
        }
        $gameId = $_POST['gameId'] ?? 0;
        $result = $gameController->purchaseGame($_SESSION['user_id'], $gameId);
        echo json_encode($result);
        break;
        
    case 'getPurchased':
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
            break;
        }
        $games = $gameController->getUserPurchasedGames($_SESSION['user_id']);
        echo json_encode(['success' => true, 'games' => $games]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Action non valide']);
        break;
}

?>

