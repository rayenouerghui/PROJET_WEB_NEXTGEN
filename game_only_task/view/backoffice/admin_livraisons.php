<?php
// views/backoffice/admin_livraisons.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

require_once '../../controller/LivraisonController.php';
$controller = new LivraisonController();

// Suppression
if (isset($_GET['delete'])) {
    $controller->deleteLivraison((int)$_GET['delete']);
    $_SESSION['success_message'] = "La livraison a été supprimée avec succès !";
    header('Location: admin_livraisons.php');
    exit;
}

// Message de succès
$success_message = $_SESSION['success_message'] ?? '';
unset($_SESSION['success_message']);

$livraisons = $controller->getAllLivraisons();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin – Livraisons</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../frontoffice/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
  <style>
    .toast { min-width: 300px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .statut-badge { padding: 0.5rem 1rem; border-radius: 50px; font-weight: bold; color: white; }
    .commandee { background: #f59e0b; }
    .emballee { background: #8b5cf6; }
    .en_transit { background: #10b981; }
    .livree { background: #ec4899; }
    a, a:hover { text-decoration: none !important; }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">

  <!-- Toast succès -->
  <?php if ($success_message): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body fw-bold">
          <i class="bi bi-check-circle-fill me-2"></i>
          <?= htmlspecialchars($success_message) ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <aside class="admin-sidebar">
    <h2>NextGen Admin</h2>
    <nav class="sidebar-menu">
      <a href="admin_jeux.php" class="item">Gestion des Jeux</a>
      <a href="admin_users.php" class="item">Gestion des Utilisateurs</a>
      <a href="admin_categories.php" class="item">Gestion des Catégories</a>
      <a href="admin_livraisons.php" class="item active">Gestion des Livraisons</a>
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
      <h1 class="page-title">Gestion des Livraisons</h1>
    </div>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Client</th>
            <th>Jeu</th>
            <th>Adresse</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($livraisons)): ?>
            <tr>
              <td colspan="7" style="text-align:center; padding:3rem; font-size:1.5rem; color:#999;">
                Aucune livraison en cours
              </td>
            </tr>
          <?php endif; ?>

          <?php foreach ($livraisons as $l): ?>
            <tr>
              <td>#<?= $l->getIdLivraison() ?></td>
              <td><?= htmlspecialchars($l->prenom_user . ' ' . $l->nom_user) ?></td>
              <td><?= htmlspecialchars($l->nom_jeu) ?></td>
              <td style="max-width:250px; word-wrap:break-word;">
                <?= htmlspecialchars($l->getAdresseComplete()) ?>
              </td>
              <td>
                <span class="statut-badge <?= $l->getStatut() ?>">
                  <?= ucfirst(str_replace('_', ' ', $l->getStatut())) ?>
                </span>
              </td>
              <td><?= date('d/m/Y H:i', strtotime($l->getDateCommande())) ?></td>
              <td class="actions">
                <a href="modifier_livraison.php?id=<?= $l->getIdLivraison() ?>" class="btn-edit" title="Statut + destination client">
                <i class="bi bi-geo-alt"></i> Destination
                </a>
                <a href="modifier_trajet.php?id=<?= $l->getIdLivraison() ?>" class="btn-edit" title="Position du livreur" style="background:#10b981;">
                <i class="bi bi-truck"></i> Livreur
                </a>
                <a href="?delete=<?= $l->getIdLivraison() ?>" class="btn-delete"
                   onclick="return confirm('Supprimer cette livraison et son trajet ?')">Supprimer</a>
                <a href="../frontoffice/tracking.php?id_livraison=<?= $l->getIdLivraison() ?>" target="_blank" class="btn-edit" style="background:#8b5cf6;">
                    <i class="bi bi-eye"></i> Voir suivi
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    <?php if ($success_message): ?>
      const toast = new bootstrap.Toast(document.getElementById('successToast'), { delay: 5000 });
      toast.show();
    <?php endif; ?>
  </script>
</body>
</html>