<?php
// views/backoffice/modifier_livraison.php

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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_statut'])) {
        $controller->updateStatut($id, $_POST['statut']);
        if ($_POST['statut'] === 'livree') {
            $controller->deleteLivraison($id);
            $_SESSION['success_message'] = "Livraison terminée et supprimée !";
            header('Location: admin_livraisons.php');
            exit;
        }
        $success = "Statut mis à jour !";
        $livraison->setStatut($_POST['statut']);
    }

    if (isset($_POST['update_destination'])) {
        $lat = (float)$_POST['lat'];
        $lng = (float)$_POST['lng'];
        $adresse = $_POST['adresse'];

        // Tu peux ajouter une méthode updateDestination si tu veux, sinon on met à jour directement
        $pdo = $controller->getPdo();
        $sql = "UPDATE livraisons SET adresse_complete = :adresse, position_lat = :lat, position_lng = :lng WHERE id_livraison = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':adresse' => $adresse, ':lat' => $lat, ':lng' => $lng, ':id' => $id]);

        $success = "Destination mise à jour !";
        $livraison = $controller->getLivraisonById($id); // refresh
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier Livraison #<?= $id ?> – Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../frontoffice/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
  <style>
    .toast { min-width: 300px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .map-container { height: 500px; border-radius: 1.5rem; margin: 1.5rem 0; }
    .statut-badge { padding: 0.6rem 1.2rem; border-radius: 50px; font-weight: bold; color: white; }
    .commandee { background: #f59e0b; }
    .emballee { background: #8b5cf6; }
    .en_transit { background: #10b981; }
    .livree { background: #ec4899; }
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
      <a href="admin_jeux.php" class="item">Gestion des Jeux</a>
      <a href="admin_users.php" class="item">Gestion des Utilisateurs</a>
      <a href="admin_categories.php" class="item">Gestion des Catégories</a>
      <a href="admin_livraisons.php" class="item active">Gestion des Livraisons</a>
    </nav>
    <div class="sidebar-actions">
      <a href="../frontoffice/catalogue.php" class="site">Voir le Catalogue</a>
      <a href="../frontoffice/index.php" class="site">Voir le Site</a>
      <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>
    </div>
  </aside>

  <main class="admin-main">
    <h1 class="page-title">Livraison #<?= $id ?> – <?= htmlspecialchars($livraison->nom_jeu) ?></h1>

    <div class="table-container">
      <table class="admin-table">
        <tr><th>Client</th><td><?= htmlspecialchars($livraison->prenom_user . ' ' . $livraison->nom_user) ?></td></tr>
        <tr><th>Adresse actuelle</th><td><?= htmlspecialchars($livraison->getAdresseComplete()) ?></td></tr>
        <tr><th>Statut actuel</th><td><span class="statut-badge <?= $livraison->getStatut() ?>"><?= ucfirst(str_replace('_', ' ', $livraison->getStatut())) ?></span></td></tr>
      </table>
    </div>

    <!-- CHANGEMENT DE STATUT -->
    <div class="card" style="margin-bottom:3rem;">
      <h2>Changer le statut</h2>
      <form method="post">
        <input type="hidden" name="update_statut" value="1">
        <select name="statut" class="form-select" style="width:auto; display:inline-block;">
          <option value="commandee" <?= $livraison->getStatut()=='commandee'?'selected':'' ?>>Commandée</option>
          <option value="emballee" <?= $livraison->getStatut()=='emballee'?'selected':'' ?>>Emballée</option>
          <option value="en_transit" <?= $livraison->getStatut()=='en_transit'?'selected':'' ?>>En transit</option>
          <option value="livree" <?= $livraison->getStatut()=='livree'?'selected':'' ?>>Livrée (supprime)</option>
        </select>
        <button type="submit" class="btn-submit">Mettre à jour le statut</button>
      </form>
    </div>

    <!-- CHANGEMENT DE DESTINATION CLIENT -->
    <div class="card">
      <h2>Modifier la destination du client</h2>
      <div id="mapClient" class="map-container"></div>
      <form method="post">
        <input type="hidden" name="update_destination" value="1">
        <input type="hidden" name="lat" id="latClient">
        <input type="hidden" name="lng" id="lngClient">
        <input type="hidden" name="adresse" id="adresseClient">
        <button type="submit" class="btn-submit">Sauvegarder la nouvelle destination</button>
      </form>
    </div>

    <div style="margin-top:2rem;">
      <a href="admin_livraisons.php" class="btn-cancel">Retour à la liste</a>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    // Carte destination client
    const mapClient = L.map('mapClient').setView([<?= $livraison->getPositionLat() ?>, <?= $livraison->getPositionLng() ?>], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapClient);
    let markerClient = L.marker([<?= $livraison->getPositionLat() ?>, <?= $livraison->getPositionLng() ?>]).addTo(mapClient).bindPopup('Destination client').openPopup();

    mapClient.on('click', async e => {
      const lat = e.latlng.lat.toFixed(8);
      const lng = e.latlng.lng.toFixed(8);
      document.getElementById('latClient').value = lat;
      document.getElementById('lngClient').value = lng;

      const res = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lng}&localityLanguage=fr`);
      const data = await res.json();
      const addr = data.city || data.principalSubdivision || 'Tunisie';

      document.getElementById('adresseClient').value = addr + ', Tunisie';

      if (markerClient) markerClient.remove();
      markerClient = L.marker([lat, lng]).addTo(mapClient).bindPopup('Nouvelle destination').openPopup();
      mapClient.setView([lat, lng], 15);
    });

    <?php if ($success): ?>
      const toast = new bootstrap.Toast(document.querySelector('.toast'), { delay: 4000 });
      toast.show();
    <?php endif; ?>
  </script>
</body>
</html>