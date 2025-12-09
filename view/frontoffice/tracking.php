<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: connexion.php');
    exit;
}

require_once '../../controller/LivraisonController.php';
require_once '../../controller/jeuController.php';

$controller = new LivraisonController();
$jeuController = new JeuController();

$id_livraison = (int)($_GET['id_livraison'] ?? 0);
$livraison = $controller->getLivraisonById($id_livraison);

if (!$livraison || $livraison->getIdUser() != $_SESSION['user']['id']) {
    header('Location: catalogue.php');
    exit;
}

$jeu = $jeuController->getJeu($livraison->getIdJeu());
$statut = $livraison->getStatut();

// Position livreur (admin peut changer)
$driver_lat = 36.8065;
$driver_lng = 10.1815;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Suivi #<?= $id_livraison ?> – NextGen</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Orbitron:wght@700;900&family=Rajdhani:wght@600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
  <style>
    body { background: url('./bg-jeux.gif') no-repeat center center fixed !important; background-size: cover !important; margin: 0; color: white; min-height: 100vh; }
    body::before { content: ''; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 1; }
    main { position: relative; z-index: 2; max-width: 1200px; margin: 3rem auto; padding: 0 2rem; text-align: center; }

    .card { background: linear-gradient(135deg, rgba(56,28,135,0.95), rgba(59,7,100,0.9)); border-radius: 1.5rem; padding: 3rem; box-shadow: 0 20px 50px rgba(0,0,0,0.7); border: 1px solid rgba(139,92,246,0.6); }

    h1 { font-family: 'Rajdhani', sans-serif; font-size: 3.8rem; text-shadow: 0 0 30px rgba(255,255,255,0.4); }

    .steps { display: flex; justify-content: space-between; align-items: center; margin: 3rem 0; position: relative; }
    .steps::before { content: ''; position: absolute; top: 50%; left: 10%; right: 10%; height: 8px; background: rgba(139,92,246,0.3); border-radius: 4px; }
    .step { width: 90px; height: 90px; border-radius: 50%; background: #4c1d95; border: 6px solid #8b5cf6; display: flex; flex-direction: column; align-items: center; justify-content: center; font-weight: 700; z-index: 2; box-shadow: 0 0 30px rgba(139,92,246,0.6); transition: all 0.5s; }
    .step.active { background: linear-gradient(45deg, #ec4899, #f43f5e); border-color: #f43f5e; transform: scale(1.25); box-shadow: 0 0 60px rgba(236,72,153,0.9); }
    .step i { font-size: 2rem; margin-bottom: 0.5rem; }

    #trackingMap { height: 550px; border-radius: 1.5rem; margin: 2rem 0; }

    .info-box { background: rgba(0,0,0,0.5); padding: 1.5rem; border-radius: 1.5rem; margin: 1.5rem 0; font-size: 1.5rem; border: 1px solid rgba(139,92,246,0.4); }

    .btn-home { padding: 1.2rem 4rem; border-radius: 50px; background: linear-gradient(45deg, #8b5cf6, #ec4899); color: white; text-decoration: none; font-weight: 700; display: inline-block; margin-top: 2rem; box-shadow: 0 0 40px rgba(139,92,246,0.7); }
    .btn-home:hover { transform: translateY(-6px); box-shadow: 0 0 60px rgba(236,72,153,0.9); }
  </style>
</head>
  <link rel="stylesheet" href="../assets/green-theme.css">
<body>
  <main>
    <div class="card">
      <h1>Suivi de ta livraison</h1>
      <p style="font-size:2rem; color:#ec4899;">Commande #<?= $id_livraison ?> – <?= htmlspecialchars($livraison->nom_jeu) ?></p>

      <div class="steps">
        <div class="step <?= in_array($statut, ['commandee','emballee','en_transit','livree']) ? 'active' : '' ?>">
          <i class="bi bi-cart-check"></i><span>Commandée</span>
        </div>
        <div class="step <?= in_array($statut, ['emballee','en_transit','livree']) ? 'active' : '' ?>">
          <i class="bi bi-box-seam"></i><span>Emballée</span>
        </div>
        <div class="step <?= in_array($statut, ['en_transit','livree']) ? 'active' : '' ?>">
          <i class="bi bi-truck"></i><span>En transit</span>
        </div>
        <div class="step <?= $statut === 'livree' ? 'active' : '' ?>">
          <i class="bi bi-house-door"></i><span>Livrée</span>
        </div>
      </div>

      <p style="font-size:1.8rem; color:#00ffc3; text-shadow:0 0 20px #00ffc3;">
        Statut actuel :<br><strong><?= ucfirst(str_replace('_', ' ', $statut)) ?></strong>
      </p>

      <div id="trackingMap"></div>

      <div class="info-box">
        <p><strong>Position actuelle du livreur :</strong><br>Tunis, Avenue Habib Bourguiba</p>
        <p><strong>Estimation :</strong><br>Livraison aujourd'hui avant 18h00</p>
      </div>

      <a href="catalogue.php" class="btn-home">Retour au catalogue</a>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    const map = L.map('trackingMap').setView([36.8065, 10.1815], 10);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

    // LIVREUR (camion rose clignotant)
    const carIcon = L.divIcon({
      html: '<i class="bi bi-truck" style="font-size:50px; color:#ec4899; filter:drop-shadow(0 0 20px #ec4899); animation:pulse 2s infinite;"></i>',
      iconSize: [50, 50],
      className: ''
    });
    L.marker([36.8065, 10.1815], { icon: carIcon }).addTo(map)
      .bindPopup('<strong>Livreur NextGen</strong><br>En route vers toi !').openPopup();

    // DESTINATION CLIENT (maison verte)
    L.marker([<?= $livraison->getPositionLat() ?>, <?= $livraison->getPositionLng() ?>], {
      icon: L.divIcon({
        html: '<i class="bi bi-house-door-fill" style="font-size:50px; color:#10b981; filter:drop-shadow(0 0 20px #10b981);"></i>',
        iconSize: [50, 50]
      })
    }).addTo(map)
      .bindPopup('<strong>Ta maison</strong><br><?= htmlspecialchars($livraison->getAdresseComplete()) ?>')
      .openPopup();

    // Ligne pointillée violette entre livreur et destination
    L.polyline([
      [36.8065, 10.1815],
      [<?= $livraison->getPositionLat() ?>, <?= $livraison->getPositionLng() ?>]
    ], { color: '#8b5cf6', dashArray: '15, 15', weight: 6, opacity: 0.7 }).addTo(map);

    const style = document.createElement('style');
    style.innerHTML = '@keyframes pulse { 0%,100% {opacity:0.8} 50% {opacity:1} }';
    document.head.appendChild(style);
  </script>
</body>
</html>