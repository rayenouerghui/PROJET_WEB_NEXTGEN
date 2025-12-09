<?php
// app/controllers/HistoryController.php
class HistoryController {
    private $pdo;

    public function __construct() {
        $this->pdo = Config::getConnexion();
    }

    public function index() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Requêtes SQL directes
        $sqlUsers = "SELECT id_user, nom, prenom, email FROM utilisateur ORDER BY nom, prenom";
        $allUsers = $this->pdo->query($sqlUsers)->fetchAll();

        $sqlHistories = "SELECT * FROM historique ORDER BY date_action DESC";
        $allHistories = $this->pdo->query($sqlHistories)->fetchAll();

        // Grouper les historiques par utilisateur
        $historiesByUser = [];
        
        foreach ($allUsers as $user) {
            $userId = $user['id_user'];
            
            $userHistories = array_filter($allHistories, function($h) use ($userId) {
                return $h['id_user'] == $userId;
            });
            
            $historiesByUser[$userId] = [
                'user' => [
                    'id' => $user['id_user'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'email' => $user['email']
                ],
                'histories' => array_values($userHistories)
            ];
        }

        require __DIR__ . '/../views/history/index.php';
    }

    public function create() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Requête SQL directe
        $stmt = $this->pdo->query("SELECT id_user, nom, prenom FROM utilisateur ORDER BY nom");
        $users = $stmt->fetchAll();

        require __DIR__ . '/../views/history/create.php';
    }

    public function store() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/history/create');
            exit;
        }

        $userId = $_POST['user_id'] ?? 0;
        $action = trim($_POST['action'] ?? '');
        $note = trim($_POST['note'] ?? '');

        $errors = [];
        if ($userId <= 0) $errors['user_id'] = 'Utilisateur requis';
        if ($action === '') $errors['action'] = 'Action requise';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['user_id' => $userId, 'action' => $action, 'note' => $note];
            header('Location: /user_nextgen/history/create');
            exit;
        }

        // Requête SQL directe
        $stmt = $this->pdo->prepare("INSERT INTO historique (id_user, type_action, description) VALUES (:uid, :action, :description)");
        $stmt->execute([
            ':uid' => $userId,
            ':action' => $action,
            ':description' => $note ?: null
        ]);

        $_SESSION['success'] = 'Historique créé avec succès';
        header('Location: /user_nextgen/history');
        exit;
    }

    public function edit() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        $id = $_GET['id'] ?? 0;

        // Requêtes SQL directes
        $stmt = $this->pdo->prepare("SELECT * FROM historique WHERE id_historique = :id");
        $stmt->execute([':id' => $id]);
        $history = $stmt->fetch();

        if (!$history) {
            header('Location: /user_nextgen/history');
            exit;
        }

        $stmt = $this->pdo->query("SELECT id_user, nom, prenom FROM utilisateur ORDER BY nom");
        $users = $stmt->fetchAll();

        require __DIR__ . '/../views/history/edit.php';
    }

    public function update() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/history');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $action = trim($_POST['action'] ?? '');
        $note = trim($_POST['note'] ?? '');

        $errors = [];
        if ($action === '') $errors['action'] = 'Action requise';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /user_nextgen/history/edit?id=' . $id);
            exit;
        }

        // Requête SQL directe
        $stmt = $this->pdo->prepare("UPDATE historique SET type_action = :action, description = :description WHERE id_historique = :id");
        $stmt->execute([
            ':action' => $action,
            ':description' => $note,
            ':id' => $id
        ]);

        $_SESSION['success'] = 'Historique modifié avec succès';
        header('Location: /user_nextgen/history');
        exit;
    }

    public function delete() {
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;

            // Requête SQL directe
            $stmt = $this->pdo->prepare("DELETE FROM historique WHERE id_historique = :id");
            $stmt->execute([':id' => $id]);

            $_SESSION['success'] = 'Historique supprimé avec succès';
        }

        header('Location: /user_nextgen/history');
        exit;
    }
}
