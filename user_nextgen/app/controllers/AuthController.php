<?php
// app/controllers/AuthController.php
class AuthController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    public function showLogin() {
        require_once __DIR__ . '/../views/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $errors = [];
        if ($email === '') $errors['email'] = 'Email requis';
        if ($password === '') $errors['password'] = 'Mot de passe requis';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['email'=>$email];
            header('Location: /user_nextgen/login');
            exit;
        }

        // Requête SQL directement dans le contrôleur
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $userData = $stmt->fetch();

        if (!$userData) {
            $_SESSION['errors'] = ['auth' => 'Aucun compte trouvé avec cet email. Veuillez créer un compte.'];
            $_SESSION['old'] = ['email'=>$email];
            header('Location: /user_nextgen/login');
            exit;
        }
        
        if (!password_verify($password, $userData['mot_de_passe'])) {
            $_SESSION['errors'] = ['auth' => 'Mot de passe incorrect'];
            $_SESSION['old'] = ['email'=>$email];
            header('Location: /user_nextgen/login');
            exit;
        }

        $_SESSION['user'] = [
            'id_user' => $userData['id_user'],
            'nom' => $userData['nom'],
            'prenom' => $userData['prenom'],
            'email' => $userData['email'],
            'role' => $userData['role']
        ];
        session_regenerate_id(true);

        // Enregistrer l'action de connexion dans l'historique
        $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :desc)");
        $stmt->execute([
            ':uid' => $userData['id_user'],
            ':action' => 'login',
            ':desc' => 'Connexion réussie au système'
        ]);

        header('Location: /user_nextgen/');
        exit;
    }

    public function showRegister() {
        require_once __DIR__ . '/../views/register.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/register');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        $errors = [];
        if ($name === '') $errors['name'] = 'Nom requis';
        if ($prenom === '') $errors['prenom'] = 'Prénom requis';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email invalide';
        if (!in_array($role, ['user', 'admin'])) $errors['role'] = 'Rôle invalide';
        if (strlen($password) < 6) $errors['password'] = 'Mot de passe min 6 caractères';
        if ($password !== $password_confirm) $errors['password_confirm'] = 'Les mots de passe ne correspondent pas';

        // Vérifier si email existe déjà - Requête SQL directe
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors['email'] = 'Email déjà utilisé';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name'=>$name,'prenom'=>$prenom,'email'=>$email,'role'=>$role];
            header('Location: /user_nextgen/register');
            exit;
        }

        // Créer l'utilisateur - Requête SQL directe
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES (:nom, :prenom, :email, :password, :role)");
        $stmt->execute([
            ':nom' => $name,
            ':prenom' => $prenom,
            ':email' => $email,
            ':password' => $hash,
            ':role' => $role
        ]);
        
        $newUserId = $this->pdo->lastInsertId();

        // Enregistrer l'action d'inscription dans l'historique - Requête SQL directe
        $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :desc)");
        $stmt->execute([
            ':uid' => $newUserId,
            ':action' => 'inscription',
            ':desc' => 'Création du compte utilisateur'
        ]);

        $_SESSION['success'] = 'Inscription réussie! Vous pouvez maintenant vous connecter.';
        header('Location: /user_nextgen/login');
        exit;
    }

    public function logout() {
        // Enregistrer la déconnexion avant de détruire la session - Requête SQL directe
        if (isset($_SESSION['user']['id_user'])) {
            $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :desc)");
            $stmt->execute([
                ':uid' => $_SESSION['user']['id_user'],
                ':action' => 'logout',
                ':desc' => 'Déconnexion du système'
            ]);
        }
        
        session_unset();
        session_destroy();
        header('Location: /user_nextgen/');
        exit;
    }
}
