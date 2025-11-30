<?php
// app/controllers/UserController.php
class UserController {
    private $userModel;
    private $histModel;

    public function __construct() {
        $pdo = Config::getConnexion();
        $this->userModel = new Utilisateur($pdo);
        $this->histModel = new Historique($pdo);
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        $user = $this->userModel->findById($_SESSION['user']['id_user']);
        $histories = $this->histModel->getByUserId($user['id_user']);

        require __DIR__ . '/../views/user/profile.php';
    }

    public function edit() {
        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        $user = $this->userModel->findById($_SESSION['user']['id_user']);
        require __DIR__ . '/../views/user/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/profile/edit');
            exit;
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        $id = $_SESSION['user']['id_user'];
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');

        $errors = [];
        if ($nom === '') $errors['nom'] = 'Nom requis';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email invalide';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /user_nextgen/profile/edit');
            exit;
        }

        $this->userModel->update(
            $id,
            [
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'role' => $_SESSION['user']['role']
            ]
        );

        $user = $this->userModel->findById($id);
        $_SESSION['user'] = [
            'id_user' => $user['id_user'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        header('Location: /user_nextgen/profile');
        exit;
    }

    public function adminDashboard() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $users = $this->userModel->getAll();
        $histories = $this->histModel->getAllWithUser();

        require __DIR__ . '/../views/admin/dashboard.php';
    }

    public function deleteUser() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
            $userId = (int)$_POST['user_id'];
            if ($userId !== $_SESSION['user']['id_user']) {
                $this->userModel->delete($userId);
                $_SESSION['success'] = 'Utilisateur supprimé avec succès';
            }
        }

        header('Location: /user_nextgen/admin/dashboard');
        exit;
    }
}
