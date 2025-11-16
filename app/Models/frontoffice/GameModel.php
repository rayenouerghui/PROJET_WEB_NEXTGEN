<?php

require_once dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
require_once __DIR__ . "/UserModel.php";

class GameModel {
    private $db;
    
    public function __construct() {
        $this->db = config::getConnexion();
    }
    
    public function getAllGames() {
        $stmt = $this->db->query("SELECT j.*, c.nom_categorie FROM jeu j LEFT JOIN categorie c ON j.id_categorie = c.id_categorie ORDER BY j.id_jeu DESC");
        return $stmt->fetchAll();
    }
    
    public function getGameById($id) {
        $stmt = $this->db->prepare("SELECT j.*, c.nom_categorie FROM jeu j LEFT JOIN categorie c ON j.id_categorie = c.id_categorie WHERE j.id_jeu = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function createGame($titre, $prix, $src_img, $est_gratuit, $lien_externe, $id_categorie) {
        $stmt = $this->db->prepare("INSERT INTO jeu (titre, prix, src_img, est_gratuit, lien_externe, id_categorie) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$titre, $prix, $src_img, $est_gratuit, $lien_externe, $id_categorie]);
    }
    
    public function updateGame($id, $titre, $prix, $src_img, $est_gratuit, $lien_externe, $id_categorie) {
        $stmt = $this->db->prepare("UPDATE jeu SET titre = ?, prix = ?, src_img = ?, est_gratuit = ?, lien_externe = ?, id_categorie = ? WHERE id_jeu = ?");
        return $stmt->execute([$titre, $prix, $src_img, $est_gratuit, $lien_externe, $id_categorie, $id]);
    }
    
    public function deleteGame($id) {
        $stmt = $this->db->prepare("DELETE FROM jeu WHERE id_jeu = ?");
        return $stmt->execute([$id]);
    }
    
    public function isGamePurchased($userId, $gameId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM jeu_achete WHERE user_id = ? AND jeu_id = ?");
        $stmt->execute([$userId, $gameId]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function purchaseGame($userId, $gameId) {
        $game = $this->getGameById($gameId);
        if (!$game) {
            return ['success' => false, 'message' => 'Jeu introuvable'];
        }
        
        // Check if already purchased
        if ($this->isGamePurchased($userId, $gameId)) {
            return ['success' => false, 'message' => 'Vous possédez déjà ce jeu'];
        }
        
        // Check user credit
        $userModel = new UserModel();
        $user = $userModel->getUserById($userId);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Utilisateur introuvable'];
        }
        
        $price = $game['est_gratuit'] ? 0 : $game['prix'];
        
        if ($user['credit'] < $price) {
            return ['success' => false, 'message' => 'Crédit insuffisant'];
        }
        
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Deduct credit
            $updateCredit = $this->db->prepare("UPDATE utilisateur SET credit = credit - ? WHERE id_user = ?");
            $updateCredit->execute([$price, $userId]);
            
            // Add purchase
            $stmt = $this->db->prepare("INSERT INTO jeu_achete (user_id, jeu_id) VALUES (?, ?)");
            $stmt->execute([$userId, $gameId]);
            
            $this->db->commit();
            
            // Update session credit
            $_SESSION['user_credit'] = $user['credit'] - $price;
            
            return ['success' => true, 'message' => 'Achat réussi', 'new_credit' => $user['credit'] - $price];
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Erreur lors de l\'achat'];
        }
    }
    
    public function getUserPurchasedGames($userId) {
        $stmt = $this->db->prepare("SELECT j.*, ja.date_achat FROM jeu j INNER JOIN jeu_achete ja ON j.id_jeu = ja.jeu_id WHERE ja.user_id = ? ORDER BY ja.date_achat DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getAllCategories() {
        $stmt = $this->db->query("SELECT * FROM categorie ORDER BY nom_categorie");
        return $stmt->fetchAll();
    }
}

?>

