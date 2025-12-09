<?php
// views/backoffice/admin_users.php

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

require_once '../../controller/userController.php';
$userController = new userController();

// Gestion suppression
if (isset($_GET['delete'])) {
    $userController->deleteUser((int)$_GET['delete']);
    $_SESSION['success_message'] = "L'utilisateur a été supprimé avec succès !";
    header('Location: admin_users.php');
    exit;
}

// Récupération du message flash (succès modification ou suppression)
$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$users = $userController->getAllUsers();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin – NextGen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <!-- Bootstrap CSS + Icons pour le toast -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    .toast {
      min-width: 300px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">

  <!-- Toast de succès -->
  <?php if (!empty($success_message)): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body fw-bold">
          <i class="bi bi-check-circle-fill me-2"></i>
          <?= htmlspecialchars($success_message) ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <aside class="admin-sidebar">
    <h2>NextGen Admin</h2>
    <nav class="sidebar-menu">
      <a href="admin_jeux.php" class="item">Gestion des Jeux</a>
      <a href="admin_users.php" class="item active">Gestion des Utilisateurs</a>
      <a href="admin_categories.php" class="item">Gestion des Catégories</a>
      <a href="admin_livraisons.php" class="item">Gestion des Livraisons</a>
      <a href="admin_reclamations.php" class="item">Gestion des Réclamations</a>
    </nav>
    <div class="sidebar-actions">
      <a href="../frontoffice/catalogue.php" class="site">Voir le Catalogue</a>
      <a href="../frontoffice/index.php" class="site">Voir le Site</a>
      <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>
    </div>
  </aside>

  <main class="admin-main">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:2rem;">
      <h1 class="page-title">Gestion des Utilisateurs</h1>
      <a href="ajouter_users.php" class="btn-submit">+ Ajouter un utilisateur</a>
    </div>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Prénom</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Téléphone</th>
            <th>Rôle</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td><?= $u->getId() ?></td>
              <td><?= htmlspecialchars($u->getPrenom()) ?></td>
              <td><?= htmlspecialchars($u->getNom()) ?></td>
              <td><?= htmlspecialchars($u->getEmail()) ?></td>
              <td><?= htmlspecialchars($u->getTelephone()) ?></td>
              <td><span class="badge <?= $u->getRole() === 'admin' ? 'bg-danger' : 'bg-primary' ?>"><?= ucfirst($u->getRole()) ?></span></td>
              <td class="actions">
                <a href="modifier_users.php?id=<?= $u->getId() ?>" class="btn-edit">Modifier</a>
                <a href="?delete=<?= $u->getId() ?>" class="btn-delete"
                   onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Bootstrap JS pour le toast -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    <?php if (!empty($success_message)): ?>
    const toastEl = document.getElementById('successToast');
    const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
    toast.show();
    <?php endif; ?>
  </script>
  <style>
  /* ==== REMOVE UNDERLINES FROM ALL LINKS ==== */
  a, a:hover, a:visited, a:active {
      text-decoration: none !important;
  }
  a:hover {
      opacity: 0.85;
      transition: opacity 0.2s;
  }

  /* If your "Modifier" and "Supprimer" buttons are <a> tags with btn classes */
  .btn-edit, .btn-delete {
      text-decoration: none !important;
  }
</style>
</body>
</html>