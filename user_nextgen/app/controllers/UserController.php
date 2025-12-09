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

        // Requête SQL directe
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_user = :id");
        $stmt->execute([':id' => $_SESSION['user']['id_user']]);
        $user = $stmt->fetch();

        require __DIR__ . '/../views/user/profile.php';
    }

    public function edit() {
        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Requête SQL directe
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_user = :id");
        $stmt->execute([':id' => $_SESSION['user']['id_user']]);
        $user = $stmt->fetch();

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

        // Requête SQL directe pour mettre à jour
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email WHERE id_user = :id");
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':id' => $id
        ]);

        // Récupérer les données mises à jour
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_user = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        $_SESSION['user'] = [
            'id_user' => $user['id_user'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'email' => $user['email'],
            'role' => $user['role']
        ];

        // Enregistrer dans l'historique
        $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :desc)");
        $stmt->execute([
            ':uid' => $id,
            ':action' => 'edit_profile',
            ':desc' => 'Modification des informations du profil'
        ]);

        header('Location: /user_nextgen/profile');
        exit;
    }

    public function adminDashboard() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Requêtes SQL directes
        $stmt = $this->pdo->query("SELECT * FROM utilisateur ORDER BY date_inscription DESC");
        $users = $stmt->fetchAll();
        
        $sql = "SELECT h.*, u.nom as user_nom, u.prenom as user_prenom, u.email as user_email 
                FROM historique h 
                JOIN utilisateur u ON u.id_user = h.id_user 
                ORDER BY h.date_action DESC";
        $histories = $this->pdo->query($sql)->fetchAll();

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
                // Enregistrer dans l'historique AVANT de supprimer
                $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :desc)");
                $stmt->execute([
                    ':uid' => $userId,
                    ':action' => 'account_deleted',
                    ':desc' => 'Compte supprimé par un administrateur'
                ]);
                
                // Supprimer l'utilisateur
                $stmt = $this->pdo->prepare("DELETE FROM utilisateur WHERE id_user = :id");
                $stmt->execute([':id' => $userId]);
                $_SESSION['success'] = 'Utilisateur supprimé avec succès';
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

        // Requête SQL directe avec filtres
        $sql = "SELECT * FROM utilisateur WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (nom LIKE :query OR prenom LIKE :query OR email LIKE :query)";
            $params[':query'] = "%$search%";
        }

        if ($role !== '' && $role !== null) {
            $sql .= " AND role = :role";
            $params[':role'] = $role;
        }

        if ($statut !== '' && $statut !== null) {
            $sql .= " AND statut = :statut";
            $params[':statut'] = $statut;
        }

        $sql .= " ORDER BY date_inscription DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll();

        // Compter le total
        $sqlCount = "SELECT COUNT(*) FROM utilisateur WHERE 1=1";
        if ($search !== '') {
            $sqlCount .= " AND (nom LIKE :query OR prenom LIKE :query OR email LIKE :query)";
        }
        if ($role !== '' && $role !== null) {
            $sqlCount .= " AND role = :role";
        }
        if ($statut !== '' && $statut !== null) {
            $sqlCount .= " AND statut = :statut";
        }

        $stmtCount = $this->pdo->prepare($sqlCount);
        foreach ($params as $key => $value) {
            if ($key !== ':limit' && $key !== ':offset') {
                $stmtCount->bindValue($key, $value);
            }
        }
        $stmtCount->execute();
        $total = (int)$stmtCount->fetchColumn();
        $totalPages = ceil($total / $perPage);

        require __DIR__ . '/../views/admin/users_list.php';
    }

    public function viewUser() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $id = $_GET['id'] ?? 0;

        // Requêtes SQL directes
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_user = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        if (!$user) {
            header('Location: /user_nextgen/admin/users');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM historique WHERE id_user = :uid ORDER BY date_action DESC");
        $stmt->execute([':uid' => $id]);
        $histories = $stmt->fetchAll();

        require __DIR__ . '/../views/admin/user_view.php';
    }

    public function editUserAdmin() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $id = $_GET['id'] ?? 0;

        // Requête SQL directe
        $stmt = $this->pdo->prepare("SELECT * FROM utilisateur WHERE id_user = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();

        if (!$user) {
            header('Location: /user_nextgen/admin/users');
            exit;
        }

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

        // Requêtes SQL directes
        $stmt = $this->pdo->prepare("UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, role = :role, statut = :statut, credit = :credit WHERE id_user = :id");
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':email' => $email,
            ':role' => $role,
            ':statut' => $statut,
            ':credit' => $credit,
            ':id' => $id
        ]);

        // Enregistrer dans l'historique
        $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :desc)");
        $stmt->execute([
            ':uid' => $id,
            ':action' => 'edit_by_admin',
            ':desc' => 'Profil modifié par un administrateur'
        ]);

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
                // Requête SQL directe
                $stmt = $this->pdo->prepare("UPDATE utilisateur SET statut = :statut WHERE id_user = :id");
                $stmt->execute([':statut' => $statusMap[$action], ':id' => $id]);
                
                // Enregistrer dans l'historique
                $actionLabels = [
                    'suspend' => 'Compte suspendu par un administrateur',
                    'ban' => 'Compte banni par un administrateur',
                    'activate' => 'Compte réactivé par un administrateur'
                ];
                $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :desc)");
                $stmt->execute([
                    ':uid' => $id,
                    ':action' => 'status_change',
                    ':desc' => $actionLabels[$action] ?? 'Changement de statut'
                ]);
                
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

        // Requête SQL directe
        $stmt = $this->pdo->query("SELECT id_user, nom, prenom, email, role, credit, statut, date_inscription FROM utilisateur ORDER BY date_inscription DESC");
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
}
