<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

require_once '../../controller/CategorieController.php';
$controller = new CategorieController();

// Suppression propre
if (isset($_GET['delete'])) {
    $controller->supprimerCategorie((int)$_GET['delete']);
    $_SESSION['success_message'] = "Catégorie supprimée avec succès !";
    header('Location: admin_categories.php');
    exit;
}

$categories = $controller->listeCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion Catégories – NextGen Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../backoffice/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    .toast-container { z-index: 9999; }
    .toast-body { font-weight: 600; }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">

  <?php if (isset($_SESSION['success_message'])): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3">
      <div class="toast show align-items-center text-white bg-success border-0">
        <div class="d-flex">
          <div class="toast-body fw-bold">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['success_message']) ?>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
      </div>
    </div>
    <?php unset($_SESSION['success_message']); ?>
  <?php endif; ?>

  <aside class="admin-sidebar">
    <h2>NextGen Admin</h2>
    <nav class="sidebar-menu">
        <a href="admin_jeux.php" class="item">Gestion des Jeux</a>
        <a href="admin_users.php" class="item">Gestion des Utilisateurs</a>
        <a href="admin_categories.php" class="item active">Gestion des Catégories</a>
        <a href="admin_livraisons.php" class="item ">Gestion des Livraisons</a>
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
      <h1 class="page-title">Gestion des Catégories</h1>
      <a href="ajouter_categories.php" class="btn-submit">+ Ajouter une catégorie</a>
    </div>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Description</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($categories)): ?>
            <tr><td colspan="4" style="text-align:center;padding:2rem;">Aucune catégorie</td></tr>
          <?php else: foreach ($categories as $cat): ?>
            <tr>
              <td><?= $cat->getIdCategorie() ?></td>
              <td><?= htmlspecialchars($cat->getNomCategorie()) ?></td>
              <td><?= htmlspecialchars($cat->getDescription() ?: '—') ?></td>
              <td class="actions">
                <a href="modifier_categories.php?id=<?= $cat->getIdCategorie() ?>" class="btn-edit">Modifier</a>
                <a href="?delete=<?= $cat->getIdCategorie() ?>" class="btn-delete"
                   onclick="return confirm('Supprimer cette catégorie ?')">
                  Supprimer
                </a>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </main>
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
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.toast').forEach(t => new bootstrap.Toast(t, {delay: 4000}).show());
    });
  </script>
</body>
</html>