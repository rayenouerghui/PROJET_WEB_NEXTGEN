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

        header('Location: /user_nextgen/admin/users');
        exit;
    }

    // Liste des utilisateurs avec recherche, filtres et pagination
    public function listUsers() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $statut = $_GET['statut'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $users = $this->userModel->search($search, $role, $statut, $perPage, $offset);
        $total = $this->userModel->count($search, $role, $statut);
        $totalPages = ceil($total / $perPage);

        require __DIR__ . '/../views/admin/users_list.php';
    }

    // Voir le profil détaillé d'un utilisateur
    public function viewUser() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        $user = $this->userModel->findById($id);

        if (!$user) {
            header('Location: /user_nextgen/admin/users');
            exit;
        }

        $histories = $this->histModel->getByUserId($id);

        require __DIR__ . '/../views/admin/user_view.php';
    }

    // Éditer un utilisateur (admin)
    public function editUserAdmin() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        $user = $this->userModel->findById($id);

        if (!$user) {
            header('Location: /user_nextgen/admin/users');
            exit;
        }

        require __DIR__ . '/../views/admin/user_edit.php';
    }

    // Mettre à jour un utilisateur (admin)
    public function updateUserAdmin() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/admin/users');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $nom = trim($_POST['nom'] ?? '');
        $prenom = trim($_POST['prenom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = $_POST['role'] ?? 'user';
        $statut = $_POST['statut'] ?? 'actif';
        $credit = floatval($_POST['credit'] ?? 0);

        $errors = [];
        if ($nom === '') $errors['nom'] = 'Nom requis';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email invalide';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /user_nextgen/admin/users/edit?id=' . $id);
            exit;
        }

        $this->userModel->update($id, [
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'role' => $role
        ]);

        $this->userModel->updateStatus($id, $statut);
        $this->userModel->updateCredit($id, $credit);

        $_SESSION['success'] = 'Utilisateur modifié avec succès';
        header('Location: /user_nextgen/admin/users/view?id=' . $id);
        exit;
    }

    // Suspendre/Bannir/Réactiver un utilisateur
    public function suspendUser() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['user_id'] ?? 0;
            $action = $_POST['action'] ?? '';

            $statusMap = [
                'suspend' => 'suspendu',
                'ban' => 'banni',
                'activate' => 'actif'
            ];

            if (isset($statusMap[$action])) {
                $this->userModel->updateStatus($id, $statusMap[$action]);
                $_SESSION['success'] = 'Statut utilisateur modifié avec succès';
            }
        }

        header('Location: /user_nextgen/admin/users');
        exit;
    }

    // Export CSV des utilisateurs
    public function exportUsers() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $users = $this->userModel->getAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="utilisateurs_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        
        // En-têtes CSV
        fputcsv($output, ['ID', 'Nom', 'Prénom', 'Email', 'Rôle', 'Crédit', 'Statut', 'Date inscription']);

        // Données
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id_user'],
                $user['nom'],
                $user['prenom'],
                $user['email'],
                $user['role'],
                $user['credit'] ?? 0,
                $user['statut'] ?? 'actif',
                $user['date_inscription'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }
}
