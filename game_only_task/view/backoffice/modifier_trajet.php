<?php
// views/backoffice/modifier_trajet.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

require_once '../../controller/LivraisonController.php';
$controller = new LivraisonController();

$id = (int)($_GET['id'] ?? 0);
$livraison = $controller->getLivraisonById($id);

if (!$livraison) {
    header('Location: admin_livraisons.php');
    exit;
}

$success = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->updatePositionLivreur($id, (float)$_POST['lat'], (float)$_POST['lng']);
    $success = "Position du livreur mise à jour !";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Position Livreur – Livraison #<?= $id ?></title>
  <link rel="stylesheet" href="../frontoffice/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
  <style>
    .toast { min-width: 300px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .map-container { height: 600px; border-radius: 1.5rem; margin: 2rem 0; }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">

  <?php if ($success): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div class="toast align-items-center text-white bg-success border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body fw-bold">
          <i class="bi bi-check-circle-fill me-2"></i> <?= $success ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <aside class="admin-sidebar">
    <h2>NextGen Admin</h2>
    <nav class="sidebar-menu">
      <a href="admin_livraisons.php" class="item active">Gestion des Livraisons</a>
      <!-- autres liens -->
    </nav>
    <div class="sidebar-actions">
      <a href="../frontoffice/catalogue.php" class="site">Voir le Catalogue</a>
      <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>
    </div>
  </aside>

  <main class="admin-main">
    <h1 class="page-title">Position du livreur – Livraison #<?= $id ?></h1>
    <p>Client : <?= htmlspecialchars($livraison->prenom_user . ' ' . $livraison->nom_user) ?> – Jeu : <?= htmlspecialchars($livraison->nom_jeu) ?></p>

    <div class="card">
      <h2>Clique sur la carte pour déplacer le livreur</h2>
      <div id="mapLivreur" class="map-container"></div>
      <form method="post">
        <input type="hidden" name="lat" id="latLivreur">
        <input type="hidden" name="lng" id="lngLivreur">
        <button type="submit" class="btn-submit">Sauvegarder la position du livreur</button>
      </form>
    </div>

    <a href="admin_livraisons.php" class="btn-cancel">Retour</a>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    const map = L.map('mapLivreur').setView([36.8065, 10.1815], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    let marker = L.marker([36.8065, 10.1815], {
      icon: L.divIcon({
        html: '<i class="bi bi-truck" style="font-size:50px; color:#ec4899;"></i>',
        iconSize: [50, 50]
      })
    }).addTo(map).bindPopup('Livreur').openPopup();

    map.on('click', e => {
      document.getElementById('latLivreur').value = e.latlng.lat.toFixed(8);
      document.getElementById('lngLivreur').value = e.latlng.lng.toFixed(8);
      marker.setLatLng(e.latlng);
      map.setView(e.latlng, 15);
    });

    <?php if ($success): ?>
      const toast = new bootstrap.Toast(document.querySelector('.toast'), { delay: 4000 });
      toast.show();
    <?php endif; ?>
  </script>
</body>
</html>