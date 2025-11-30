<?php
// app/controllers/AuthController.php
class AuthController {
    private $userModel;

    public function __construct() {
        $pdo = Config::getConnexion();
        $this->userModel = new Utilisateur($pdo);
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

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            $_SESSION['errors'] = ['auth' => 'Aucun compte trouvé avec cet email. Veuillez créer un compte.'];
            $_SESSION['old'] = ['email'=>$email];
            header('Location: /user_nextgen/login');
            exit;
        }
        
        if (!password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['errors'] = ['auth' => 'Mot de passe incorrect'];
            $_SESSION['old'] = ['email'=>$email];
            header('Location: /user_nextgen/login');
            exit;
        }

        unset($user['mot_de_passe']);
        $_SESSION['user'] = [
            'id_user' => $user['id_user'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        session_regenerate_id(true);

        // Rediriger vers l'accueil après connexion
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
        if ($this->userModel->findByEmail($email)) $errors['email'] = 'Email déjà utilisé';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['name'=>$name,'prenom'=>$prenom,'email'=>$email,'role'=>$role];
            header('Location: /user_nextgen/register');
            exit;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $id = $this->userModel->create([
            'nom'=>$name,
            'prenom'=>$prenom,
            'email'=>$email,
            'password'=>$hash,
            'role'=>$role
        ]);

        // Rediriger vers la page de connexion après inscription
        $_SESSION['success'] = 'Inscription réussie! Vous pouvez maintenant vous connecter.';
        header('Location: /user_nextgen/login');
        exit;
    }

    public function logout() {
        session_unset();
        session_destroy();
        header('Location: /user_nextgen/');
        exit;
    }
}
