<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: connexion.php');
    exit;
}

require_once '../../controller/jeuController.php';
require_once '../../controller/LivraisonController.php';

$jeuController = new JeuController();
$livraisonController = new LivraisonController();

// Récupère le jeu
$id_jeu = (int)($_GET['id'] ?? 0);
$jeu = $jeuController->getJeu($id_jeu);

if (!$jeu) {
    header('Location: catalogue.php');
    exit;
}


$livraisonEnCours = $livraisonController->getLivraisonEnCours($_SESSION['user']['id']);

if ($livraisonEnCours) {
    header("Location: tracking.php?id_livraison=" . $livraisonEnCours->getIdLivraison());
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $lat = $_POST['lat'] ?? '';
    $lng = $_POST['lng'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $payment = $_POST['payment'] ?? 'credit';

  
    if ($lat && $lng && $adresse) {
        
        $_SESSION['temp_lat'] = $lat;
        $_SESSION['temp_lng'] = $lng;
        $_SESSION['temp_adresse'] = $adresse;
        $_SESSION['temp_mode_paiement'] = $payment;

        
        header("Location: confirmation.php?id=$id_jeu");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Livraison – <?= htmlspecialchars($jeu->getTitre()) ?></title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Orbitron:wght@700;900&family=Rajdhani:wght@600;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
  <style>
    body { background: url('./bg-jeux.gif') no-repeat center center fixed !important; background-size: cover !important; margin: 0; color: white; min-height: 100vh; }
    body::before { content: ''; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 1; }
    main { position: relative; z-index: 2; max-width: 1100px; margin: 2rem auto; padding: 0 2rem; }
    .card { background: linear-gradient(135deg, rgba(56,28,135,0.95), rgba(59,7,100,0.9)); border-radius: 1.5rem; padding: 2.5rem; margin-bottom: 2rem; box-shadow: 0 15px 40px rgba(0,0,0,0.6); border: 1px solid rgba(139,92,246,0.6); }
    h1, h2 { font-family: 'Rajdhani', sans-serif; font-weight: 800; text-shadow: 0 0 20px rgba(255,255,255,0.3); }
    .btn { padding: 1rem 3rem; border-radius: 50px; font-weight: 700; text-transform: uppercase; border: none; cursor: pointer; transition: all 0.3s; }
    .btn.primary { background: linear-gradient(45deg, #8b5cf6, #ec4899); box-shadow: 0 0 30px rgba(139,92,246,0.7); }
    .btn.primary:hover { transform: translateY(-4px); box-shadow: 0 0 50px rgba(236,72,153,0.9); }
    #map { height: 500px; border-radius: 1.5rem; margin: 1.5rem 0; }
    #selectedAddress { background: rgba(0,0,0,0.5); padding: 1.5rem; border-radius: 1rem; color: #00ffc3; font-size: 1.4rem; margin-top: 1rem; }
  </style>
</head>
<body>
  <main>
    <div class="card">
      <h1>Acheter : <?= htmlspecialchars($jeu->getTitre()) ?></h1>
      <img src="../../resources/<?= htmlspecialchars($jeu->getSrcImg()) ?>" style="max-height:200px; border-radius:1rem;">
      <p style="font-size:1.5rem;"><strong>Prix :</strong> <?= number_format($jeu->getPrix(), 3) ?> TND</p>
      <p style="font-size:1.5rem;"><strong>Livraison :</strong> 8.000 TND</p>
      <p style="font-size:1.8rem; color:#00ffc3;"><strong>Total :</strong> <?= number_format($jeu->getPrix() + 8, 3) ?> TND</p>
    </div>

    <form method="post">
      <input type="hidden" name="lat" id="lat">
      <input type="hidden" name="lng" id="lng">
      <input type="hidden" name="adresse" id="adresse">

      <div class="card">
        <h2>Adresse de livraison</h2>
        <div id="map"></div>
        <div id="selectedAddress">Clique sur la carte pour choisir ton adresse</div>
        <button type="button" class="btn primary" onclick="confirmAddress()">Confirmer l'adresse</button>
      </div>

      <div class="card">
        <h2>Mode de paiement</h2>
        <label style="display:block; margin:1.5rem 0; font-size:1.4rem;">
          <input type="radio" name="payment" value="credit" checked> Paiement avec le crédit du site
        </label>
        <label style="display:block; margin:1.5rem 0; font-size:1.4rem;">
          <input type="radio" name="payment" value="cash"> Paiement en espèce à la livraison
        </label>
        <button type="submit" class="btn primary">Suivant →</button>
      </div>
    </form>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
  <script>
    let map, marker;
    let selectedLat = null, selectedLng = null, selectedAddr = '';

    function initMap() {
      map = L.map('map').setView([36.8065, 10.1815], 12);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
      setTimeout(() => map.invalidateSize(), 500);

      map.on('click', async e => {
        selectedLat = e.latlng.lat.toFixed(8);
        selectedLng = e.latlng.lng.toFixed(8);

        const res = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${e.latlng.lat}&longitude=${e.latlng.lng}&localityLanguage=fr`);
        const data = await res.json();
        selectedAddr = data.city || data.principalSubdivision || 'Tunisie';

        document.getElementById('selectedAddress').innerHTML = `<strong>Adresse :</strong> ${selectedAddr}<br>Lat: ${selectedLat}, Lng: ${selectedLng}`;
        document.getElementById('lat').value = selectedLat;
        document.getElementById('lng').value = selectedLng;
        document.getElementById('adresse').value = selectedAddr + ', Tunisie';

        if (marker) marker.remove();
        marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map).bindPopup('Livraison ici !').openPopup();
        map.setView([e.latlng.lat, e.latlng.lng], 16);
      });
    }

    function confirmAddress() {
      if (!selectedAddr) return alert('Clique sur la carte d\'abord !');
      alert('Adresse confirmée ! Tu peux passer au paiement.');
    }

    window.onload = initMap;
  </script>
</body>
</html>