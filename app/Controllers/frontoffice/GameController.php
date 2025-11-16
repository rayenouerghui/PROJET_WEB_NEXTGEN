<?php

require_once __DIR__ . "/../../Models/frontoffice/GameModel.php";

class GameController {
    private $gameModel;
    
    public function __construct() {
        $this->gameModel = new GameModel();
    }
    
    public function getAllGames() {
        return $this->gameModel->getAllGames();
    }
    
    public function getGameById($id) {
        return $this->gameModel->getGameById($id);
    }
    
    public function purchaseGame($userId, $gameId) {
        return $this->gameModel->purchaseGame($userId, $gameId);
    }
    
    public function getUserPurchasedGames($userId) {
        return $this->gameModel->getUserPurchasedGames($userId);
    }
    
    public function isGamePurchased($userId, $gameId) {
        return $this->gameModel->isGamePurchased($userId, $gameId);
    }
}

?>

