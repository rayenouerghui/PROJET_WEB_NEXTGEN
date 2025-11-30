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

        if ($_SESSION['user']['role'] === 'admin') {
            $histories = $this->model->getAllWithUser();
        } else {
            $histories = $this->model->getByUserId($_SESSION['user']['id_user']);
        }

        require_once __DIR__ . '/../views/history/index.php';
    }

    public function create() {
        if (!isset($_SESSION['user'])) {
            header('Location: /user_nextgen/login');
            exit;
        }
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

        $action = trim($_POST['action'] ?? '');
        $note = trim($_POST['note'] ?? '');

        $errors = [];
        if ($action === '') $errors['action'] = 'Action requise';

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = ['action'=>$action, 'note'=>$note];
            header('Location: /user_nextgen/history/create');
            exit;
        }

        $this->model->create([
            'user_id' => $_SESSION['user']['id_user'],
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

        $id = $_GET['id'] ?? 0;
        $history = $this->model->findById($id);

        if (!$history) {
            header('Location: /user_nextgen/history');
            exit;
        }

        // Vérifier que l'utilisateur peut modifier cet historique
        if ($_SESSION['user']['role'] !== 'admin' && $history['id_user'] != $_SESSION['user']['id_user']) {
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

        $id = $_POST['id'] ?? 0;
        $action = trim($_POST['action'] ?? '');
        $note = trim($_POST['note'] ?? '');

        $history = $this->model->findById($id);
        if (!$history || ($_SESSION['user']['role'] !== 'admin' && $history['id_user'] != $_SESSION['user']['id_user'])) {
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

        $id = $_POST['id'] ?? 0;
        $history = $this->model->findById($id);

        if ($history && ($_SESSION['user']['role'] === 'admin' || $history['id_user'] == $_SESSION['user']['id_user'])) {
            $this->model->delete($id);
            $_SESSION['success'] = 'Historique supprimé avec succès';
        }

        header('Location: /user_nextgen/history');
        exit;
    }
}
