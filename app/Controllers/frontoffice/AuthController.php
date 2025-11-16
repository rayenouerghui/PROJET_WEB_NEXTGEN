<?php

require_once __DIR__ . "/../../Models/frontoffice/UserModel.php";

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new UserModel();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                return ['success' => false, 'message' => 'Veuillez remplir tous les champs'];
            }
            
            // First check if email exists
            $user = $this->userModel->getUserByEmail($email);
            
            if ($user === false || $user === null || empty($user)) {
                return ['success' => false, 'message' => 'Cet email n\'existe pas dans la base de données'];
            }
            
            // Email exists, now verify password
            if (!isset($user['mot_de_passe']) || !password_verify($password, $user['mot_de_passe'])) {
                return ['success' => false, 'message' => 'Mot de passe incorrect'];
            }
            
            // Login successful
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_nom'] = $user['nom'];
            $_SESSION['user_prenom'] = $user['prenom'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_credit'] = $user['credit'];
            
            return ['success' => true, 'message' => 'Connexion réussie', 'role' => $user['role']];
        }
        return ['success' => false, 'message' => 'Méthode non autorisée'];
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $confirmPassword = $_POST['confirmPassword'] ?? '';
            
            // Validation
            if (empty($email) || empty($password) || empty($nom) || empty($prenom)) {
                return ['success' => false, 'message' => 'Veuillez remplir tous les champs'];
            }
            
            if ($password !== $confirmPassword) {
                return ['success' => false, 'message' => 'Les mots de passe ne correspondent pas'];
            }
            
            if (strlen($password) < 8) {
                return ['success' => false, 'message' => 'Le mot de passe doit contenir au moins 8 caractères'];
            }
            
            if ($this->userModel->emailExists($email)) {
                return ['success' => false, 'message' => 'Cet email est déjà utilisé'];
            }
            
            if ($this->userModel->createUser($email, $password, $nom, $prenom)) {
                // Auto login after registration
                $user = $this->userModel->getUserByEmail($email);
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_prenom'] = $user['prenom'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_credit'] = $user['credit'];
                
                return ['success' => true, 'message' => 'Inscription réussie'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de l\'inscription'];
            }
        }
        return ['success' => false, 'message' => 'Méthode non autorisée'];
    }
    
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        return ['success' => true];
    }
    
    public function getCurrentUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['user_id'])) {
            return [
                'id' => $_SESSION['user_id'],
                'email' => $_SESSION['user_email'],
                'nom' => $_SESSION['user_nom'],
                'prenom' => $_SESSION['user_prenom'],
                'role' => $_SESSION['user_role'],
                'credit' => $_SESSION['user_credit']
            ];
        }
        return null;
    }
    
    public function requireAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }
    }
    
    public function requireAdmin() {
        $this->requireAuth();
        if ($_SESSION['user_role'] !== 'admin') {
            header('Location: index.html');
            exit;
        }
    }
}

?>

