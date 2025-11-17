<?php
// controllers/UserController.php
require_once 'models/UserModel.php';
require_once 'models/HistoriqueModel.php';

class UserController {
    private $userModel;
    private $historiqueModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->historiqueModel = new HistoriqueModel();
    }

    public function updateProfile() {
        session_start();
        if ($_POST && isset($_SESSION['user_id'])) {
            $data = [
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'email' => $_POST['email'],
                'date_naissance' => $_POST['date_naissance'],
                'pays' => $_POST['pays'],
                'telephone' => $_POST['telephone']
            ];
            
            if ($this->userModel->updateUser($_SESSION['user_id'], $data)) {
                $this->historiqueModel->addAction($_SESSION['user_id'], 'modification_profil', 'Profil mis à jour');
                header('Location: user_profile.php?success=1');
            } else {
                header('Location: user_profile.php?error=1');
            }
        }
    }

    public function getUserProfile() {
        session_start();
        if (isset($_SESSION['user_id'])) {
            return $this->userModel->getUserById($_SESSION['user_id']);
        }
        return null;
    }

    public function getUserHistory() {
        session_start();
        if (isset($_SESSION['user_id'])) {
            return $this->historiqueModel->getUserHistory($_SESSION['user_id']);
        }
        return [];
    }
}
?>