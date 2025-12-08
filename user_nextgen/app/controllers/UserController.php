<?php
// app/controllers/UserController.php
class UserController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Utiliser le modèle Utilisateur
        $utilisateurModel = new Utilisateur();
        $userObj = $utilisateurModel->findById($_SESSION['user']['id_user']);
        $user = $userObj ? (array)$userObj : [];

        require __DIR__ . '/../views/user/profile.php';
    }

    public function edit() {
        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Utiliser le modèle Utilisateur
        $utilisateurModel = new Utilisateur();
        $userObj = $utilisateurModel->findById($_SESSION['user']['id_user']);
        $user = $userObj ? (array)$userObj : [];

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

        // Utiliser le modèle Utilisateur
        $utilisateurModel = new Utilisateur();
        $user = $utilisateurModel->findById($id);
        
        if ($user) {
            $user->nom = $nom;
            $user->prenom = $prenom;
            $user->email = $email;
            $user->update();

            $_SESSION['user'] = [
                'id_user' => $user->id_user,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role
            ];

            // Enregistrer la modification dans l'historique
            Historique::log($id, 'edit_profile', 'Modification des informations du profil');
        }

        header('Location: /user_nextgen/profile');
        exit;
    }

    public function adminDashboard() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Utiliser les modèles
        $utilisateurModel = new Utilisateur();
        $historiqueModel = new Historique();
        
        $usersObj = $utilisateurModel->getAll();
        $users = array_map(function($u) { return (array)$u; }, $usersObj);
        
        $histories = $historiqueModel->getAllWithUserInfo();

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
                // Enregistrer la suppression dans l'historique AVANT de supprimer
                Historique::log($userId, 'account_deleted', 'Compte supprimé par un administrateur');
                
                // Utiliser le modèle Utilisateur
                $utilisateurModel = new Utilisateur();
                $user = $utilisateurModel->findById($userId);
                if ($user) {
                    $user->delete();
                    $_SESSION['success'] = 'Utilisateur supprimé avec succès';
                }
            }
        }

        header('Location: /user_nextgen/admin/users');
        exit;
    }

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

        // Utiliser le modèle Utilisateur
        $utilisateurModel = new Utilisateur();
        
        $filters = [];
        if ($search !== '') $filters['search'] = $search;
        if ($role !== '' && $role !== null) $filters['role'] = $role;
        if ($statut !== '' && $statut !== null) $filters['statut'] = $statut;

        $usersObj = $utilisateurModel->search($filters, $perPage, $offset);
        $users = array_map(function($u) { return (array)$u; }, $usersObj);
        
        $total = $utilisateurModel->count($filters);
        $totalPages = ceil($total / $perPage);

        require __DIR__ . '/../views/admin/users_list.php';
    }

    public function viewUser() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $id = $_GET['id'] ?? 0;

        // Utiliser les modèles
        $utilisateurModel = new Utilisateur();
        $historiqueModel = new Historique();
        
        $userObj = $utilisateurModel->findById($id);
        
        if (!$userObj) {
            header('Location: /user_nextgen/admin/users');
            exit;
        }
        
        $user = (array)$userObj;
        $historiesObj = $historiqueModel->getByUserId($id);
        $histories = array_map(function($h) { return (array)$h; }, $historiesObj);

        require __DIR__ . '/../views/admin/user_view.php';
    }

    public function editUserAdmin() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $id = $_GET['id'] ?? 0;

        // Utiliser le modèle Utilisateur
        $utilisateurModel = new Utilisateur();
        $userObj = $utilisateurModel->findById($id);

        if (!$userObj) {
            header('Location: /user_nextgen/admin/users');
            exit;
        }
        
        $user = (array)$userObj;

        require __DIR__ . '/../views/admin/user_edit.php';
    }

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

        // Requête SQL pour mettre à jour l'utilisateur
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET nom=:nom,prenom=:prenom,email=:email,role=:role WHERE id_user=:id");
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':role' => $role,
            ':id' => $id
        ]);

        // Mettre à jour le statut
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET statut=:statut WHERE id_user=:id");
        $stmt->execute([':statut' => $statut, ':id' => $id]);

        // Mettre à jour le crédit
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET credit=:credit WHERE id_user=:id");
        $stmt->execute([':credit' => $credit, ':id' => $id]);

        // Enregistrer la modification dans l'historique
        $this->addHistory($id, 'edit_by_admin', 'Profil modifié par un administrateur');

        $_SESSION['success'] = 'Utilisateur modifié avec succès';
        header('Location: /user_nextgen/admin/users/view?id=' . $id);
        exit;
    }

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
                // Requête SQL pour mettre à jour le statut
                $stmt = $this->pdo->prepare("UPDATE utilisateur SET statut=:statut WHERE id_user=:id");
                $stmt->execute([':statut' => $statusMap[$action], ':id' => $id]);
                
                // Enregistrer l'action dans l'historique
                $actionLabels = [
                    'suspend' => 'Compte suspendu par un administrateur',
                    'ban' => 'Compte banni par un administrateur',
                    'activate' => 'Compte réactivé par un administrateur'
                ];
                $this->addHistory($id, 'status_change', $actionLabels[$action] ?? 'Changement de statut');
                
                $_SESSION['success'] = 'Statut utilisateur modifié avec succès';
            }
        }

        header('Location: /user_nextgen/admin/users');
        exit;
    }

    public function exportUsers() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Requête SQL pour récupérer tous les utilisateurs
        $stmt = $this->pdo->query("SELECT id_user,nom,prenom,email,role,credit,photo_profile,statut,date_inscription FROM utilisateur");
        $users = $stmt->fetchAll();

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="utilisateurs_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        
        fputcsv($output, ['ID', 'Nom', 'Prénom', 'Email', 'Rôle', 'Crédit', 'Statut', 'Date inscription']);

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

    // Méthode pour ajouter un historique
    private function addHistory($userId, $typeAction, $description) {
        try {
            $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :desc)");
            $stmt->execute([
                ':uid' => $userId,
                ':action' => $typeAction,
                ':desc' => $description
            ]);
        } catch (Exception $e) {
            // En cas d'erreur, on continue sans bloquer l'application
            error_log("Erreur historique: " . $e->getMessage());
        }
    }
}
