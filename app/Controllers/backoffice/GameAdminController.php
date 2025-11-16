<?php

require_once __DIR__ . "/../../Models/frontoffice/GameModel.php";
require_once __DIR__ . "/../../Controllers/frontoffice/AuthController.php";

class GameAdminController {
    private $gameModel;
    private $authController;
    
    public function __construct() {
        $this->gameModel = new GameModel();
        $this->authController = new AuthController();
        $this->authController->requireAdmin();
    }
    
    public function getAllGames() {
        return $this->gameModel->getAllGames();
    }
    
    public function getGameById($id) {
        return $this->gameModel->getGameById($id);
    }
    
    public function createGame() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $prix = $_POST['prix'] ?? 0;
            $src_img = $_POST['src_img'] ?? '';
            $est_gratuit = isset($_POST['est_gratuit']) ? 1 : 0;
            $lien_externe = $_POST['lien_externe'] ?? '';
            $id_categorie = !empty($_POST['id_categorie']) ? $_POST['id_categorie'] : null;
            
            if (empty($titre)) {
                return ['success' => false, 'message' => 'Le titre est requis'];
            }
            
            if ($this->gameModel->createGame($titre, $prix, $src_img, $est_gratuit, $lien_externe, $id_categorie)) {
                return ['success' => true, 'message' => 'Jeu créé avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la création'];
            }
        }
        return ['success' => false, 'message' => 'Méthode non autorisée'];
    }
    
    public function updateGame($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $titre = $_POST['titre'] ?? '';
            $prix = $_POST['prix'] ?? 0;
            $src_img = $_POST['src_img'] ?? '';
            $est_gratuit = isset($_POST['est_gratuit']) ? 1 : 0;
            $lien_externe = $_POST['lien_externe'] ?? '';
            $id_categorie = !empty($_POST['id_categorie']) ? $_POST['id_categorie'] : null;
            
            if (empty($titre)) {
                return ['success' => false, 'message' => 'Le titre est requis'];
            }
            
            if ($this->gameModel->updateGame($id, $titre, $prix, $src_img, $est_gratuit, $lien_externe, $id_categorie)) {
                return ['success' => true, 'message' => 'Jeu mis à jour avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
            }
        }
        return ['success' => false, 'message' => 'Méthode non autorisée'];
    }
    
    public function deleteGame($id) {
        if ($this->gameModel->deleteGame($id)) {
            return ['success' => true, 'message' => 'Jeu supprimé avec succès'];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de la suppression'];
        }
    }
    
    public function getAllCategories() {
        return $this->gameModel->getAllCategories();
    }
}

?>

