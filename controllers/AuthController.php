<?php
// controllers/AuthController.php
require_once 'models/UserModel.php';
require_once 'models/HistoriqueModel.php';

class AuthController {
    private $userModel;
    private $historiqueModel;

    public function __construct() {
        $this->userModel = new UserModel();
        $this->historiqueModel = new HistoriqueModel();
    }

    public function login() {
        if ($_POST) {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $user = $this->userModel->getUserByEmail($email);
            
            if ($user && password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                
                // Ajouter à l'historique
                $this->historiqueModel->addAction($user['id_user'], 'connexion', 'Connexion réussie');
                
                if ($user['role'] === 'admin') {
                    header('Location: ../views/backoffice/admin_dashboard.php');
                } else {
                    header('Location: ../views/frontoffice/user_dashboard.php');
                }
                exit();
            } else {
                $error = "Email ou mot de passe incorrect";
                include '../views/frontoffice/login.php';
            }
        }
    }

    public function register() {
        if ($_POST) {
            $data = [
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'nom' => $_POST['nom'],
                'prenom' => $_POST['prenom'],
                'role' => $_POST['user_type'],
                'date_naissance' => $_POST['date_naissance'],
                'pays' => $_POST['pays'],
                'telephone' => $_POST['telephone']
            ];
            
            if ($this->userModel->createUser($data)) {
                // Ajouter à l'historique
                $user = $this->userModel->getUserByEmail($data['email']);
                $this->historiqueModel->addAction($user['id_user'], 'inscription', 'Nouvel utilisateur inscrit');
                
                header('Location: login.php?success=1');
                exit();
            } else {
                $error = "Erreur lors de l'inscription";
                include '../views/frontoffice/register.php';
            }
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: ../index.php');
        exit();
    }
}

// Router basique
if (isset($_POST['action'])) {
    $controller = new AuthController();
    $controller->{$_POST['action']}();
}
?>