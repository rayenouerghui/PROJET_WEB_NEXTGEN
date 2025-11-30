<?php
// app/controllers/HistoryController.php
class HistoryController {
    private $model;

    public function __construct() {
        $pdo = Config::getConnexion();
        $this->model = new Historique($pdo);
    }

    public function index() {
        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Seuls les admins peuvent accéder à l'historique
        if ($_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/');
            exit;
        }

        // Récupérer TOUS les utilisateurs
        $pdo = Config::getConnexion();
        $userModel = new Utilisateur($pdo);
        $allUsers = $userModel->getAll();
        
        // Récupérer tous les historiques
        $histories = $this->model->getAllWithUser();
        
        // Créer un tableau pour chaque utilisateur avec ses historiques
        $historiesByUser = [];
        foreach ($allUsers as $user) {
            $historiesByUser[$user['id_user']] = [
                'user' => [
                    'id' => $user['id_user'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ],
                'histories' => []
            ];
        }
        
        // Ajouter les historiques à chaque utilisateur
        foreach ($histories as $h) {
            if (isset($historiesByUser[$h['id_user']])) {
                $historiesByUser[$h['id_user']]['histories'][] = $h;
            }
        }
        
        require_once __DIR__ . '/../views/history/index.php';
    }

    public function create() {
        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Seuls les admins peuvent créer un historique
        if ($_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/');
            exit;
        }

        // Charger la liste des utilisateurs pour le formulaire
        $users = $this->model->getUsersList();
        require_once __DIR__ . '/../views/history/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/history/create');
            exit;
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Seuls les admins peuvent créer un historique
        if ($_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/');
            exit;
        }

        $user_id = (int)($_POST['user_id'] ?? 0);
        $action = trim($_POST['action'] ?? '');
        $note = trim($_POST['note'] ?? '');

        $errors = [];
        if ($user_id === 0) $errors['user_id'] = 'Utilisateur requis';
        if ($action === '') $errors['action'] = 'Action requise';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['user_id'=>$user_id, 'action'=>$action, 'note'=>$note];
            header('Location: /user_nextgen/history/create');
            exit;
        }

        $this->model->create([
            'user_id' => $user_id,
            'action' => $action,
            'note' => $note
        ]);

        $_SESSION['success'] = 'Historique créé avec succès';
        header('Location: /user_nextgen/history');
        exit;
    }

    public function edit() {
        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Seuls les admins peuvent modifier un historique
        if ($_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/');
            exit;
        }

        $id = $_GET['id'] ?? 0;
        $history = $this->model->findById($id);

        if (!$history) {
            header('Location: /user_nextgen/history');
            exit;
        }

        require_once __DIR__ . '/../views/history/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/history');
            exit;
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Seuls les admins peuvent modifier un historique
        if ($_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $action = trim($_POST['action'] ?? '');
        $note = trim($_POST['note'] ?? '');

        $history = $this->model->findById($id);
        if (!$history) {
            header('Location: /user_nextgen/history');
            exit;
        }

        $errors = [];
        if ($action === '') $errors['action'] = 'Action requise';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            header('Location: /user_nextgen/history/edit?id=' . $id);
            exit;
        }

        $this->model->update($id, [
            'action' => $action,
            'note' => $note
        ]);

        $_SESSION['success'] = 'Historique modifié avec succès';
        header('Location: /user_nextgen/history');
        exit;
    }

    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /user_nextgen/history');
            exit;
        }

        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }

        // Seuls les admins peuvent supprimer un historique
        if ($_SESSION['user']['role'] !== 'admin') {
            header('Location: /user_nextgen/');
            exit;
        }

        $id = $_POST['id'] ?? 0;
        $history = $this->model->findById($id);

        if ($history) {
            $this->model->delete($id);
            $_SESSION['success'] = 'Historique supprimé avec succès';
        }

        header('Location: /user_nextgen/history');
        exit;
    }
}
