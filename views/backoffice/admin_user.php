<?php
session_start();
require_once '../controllers/AdminController.php';

// Vérifier si admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../frontoffice/login.php');
    exit();
}

$adminController = new AdminController();
$users = $adminController->getAllUsers();

// Gérer les actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    if ($_GET['action'] === 'delete') {
        $adminController->deleteUser($_GET['id']);
        header('Location: admin_users.php');
        exit();
    } elseif ($_GET['action'] === 'toggle_status') {
        $user = $adminController->getUserById($_GET['id']);
        $newStatus = $user['statut'] === 'actif' ? 'desactive' : 'actif';
        $adminController->updateUserStatus($_GET['id'], $newStatus);
        header('Location: admin_users.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>NextGen - Gestion Utilisateurs</title>
    <style>
        /* VOTRE CSS EXISTANT */
    </style>
</head>
<body>
    <!-- VOTRE HTML EXISTANT AVEC LA TABLE -->

    <div class="table-container">
        <h3>Liste des Utilisateurs</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom complet</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Statut</th>
                    <th>Date inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>#<?= $user['id_user'] ?></td>
                    <td><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><span class="badge <?= $user['role'] === 'admin' ? 'warning' : 'success' ?>"><?= $user['role'] ?></span></td>
                    <td>
                        <span class="badge <?= $user['statut'] === 'actif' ? 'success' : 'danger' ?>">
                            <?= $user['statut'] ?>
                        </span>
                    </td>
                    <td><?= date('d/m/Y', strtotime($user['date_inscription'])) ?></td>
                    <td>
                        <button class="btn btn-edit" onclick="editUser(<?= $user['id_user'] ?>)">✏️ Modifier</button>
                        <button class="btn btn-delete" onclick="confirmDelete(<?= $user['id_user'] ?>, '<?= $user['prenom'] ?>')">🗑️ Supprimer</button>
                        <button class="btn btn-status" onclick="toggleStatus(<?= $user['id_user'] ?>)">
                            <?= $user['statut'] === 'actif' ? '⏸️ Désactiver' : '▶️ Activer' ?>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function confirmDelete(userId, userName) {
            if (confirm(`Êtes-vous sûr de vouloir supprimer l'utilisateur "${userName}" ?`)) {
                window.location.href = `admin_users.php?action=delete&id=${userId}`;
            }
        }

        function toggleStatus(userId) {
            window.location.href = `admin_users.php?action=toggle_status&id=${userId}`;
        }

        function editUser(userId) {
            // Ouvrir modal d'édition
            openModal();
            // Remplir avec les données de l'utilisateur
            // (à implémenter avec AJAX ou formulaire)
        }
    </script>
</body>
</html>