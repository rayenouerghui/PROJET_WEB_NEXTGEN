<?php
// controllers/AdminController.php
require_once 'models/UserModel.php';
require_once 'models/HistoriqueModel.php';

class AdminController {
    private $userModel;
    private $historiqueModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->historiqueModel = new HistoriqueModel();
    }

    public function getAllUsers() {
        return $this->userModel->getAllUsers();
    }

    public function deleteUser($id) {
        if ($this->userModel->deleteUser($id)) {
            $this->historiqueModel->addAction($_SESSION['user_id'], 'suppression', 'Utilisateur supprimé ID: ' . $id);
            return true;
        }
        return false;
    }

    public function updateUserStatus($id, $status) {
        if ($this->userModel->updateStatus($id, $status)) {
            $this->historiqueModel->addAction($_SESSION['user_id'], 'modification', 'Statut utilisateur modifié ID: ' . $id);
            return true;
        }
        return false;
    }

    public function getAllHistory() {
        return $this->historiqueModel->getAllHistory();
    }
}
?>