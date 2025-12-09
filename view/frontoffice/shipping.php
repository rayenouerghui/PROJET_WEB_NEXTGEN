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

$id_jeu = (int)($_GET['id'] ?? 0);
$jeu = $jeuController->getJeu($id_jeu);

if (!$jeu) {
    header('Location: catalogue.php');
    exit;
}

// FIX : DÉFINITION AVANT UTILISATION
$livraisonEnCours = $livraisonController->getLivraisonEnCours($_SESSION['user']['id']);

if ($livraisonEnCours) {
    // Message d'erreur + lien tracking avec ID correct
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Livraison impossible – NextGen</title>
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Orbitron:wght@700;900&family=Rajdhani:wght@600;800&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
      <style>
        body { background: url('./bg-jeux.gif') no-repeat center center fixed !important; background-size: cover !important; margin: 0; color: white; min-height: 100vh; }
        body::before { content: ''; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); z-index: 1; }
        main { position: relative; z-index: 2; max-width: 900px; margin: 8rem auto; text-align: center; padding: 2rem; }
        .error-card { background: linear-gradient(135deg, rgba(239,68,68,0.95), rgba(127,29,29,0.9)); border-radius: 2rem; padding: 5rem; box-shadow: 0 20px 60px rgba(0,0,0,0.8); border: 3px solid #ef4444; }
        h1 { font-family: 'Rajdhani', sans-serif; font-size: 4.5rem; text-shadow: 0 0 40px #ef4444; margin-bottom: 2rem; }
        p { font-size: 2rem; margin: 2rem 0; }
        .btn { padding: 1.5rem 4rem; border-radius: 50px; background: linear-gradient(45deg, #8b5cf6, #ec4899); color: white; text-decoration: none; font-weight: 700; font-size: 1.6rem; display: inline-block; margin: 1rem; box-shadow: 0 0 40px rgba(139,92,246,0.7); }
        .btn:hover { transform: translateY(-8px); box-shadow: 0 0 60px rgba(236,72,153,0.9); }
      </style>
    </head>
    <link rel="stylesheet" href="../assets/green-theme.css">
    <body>
      <main>
        <div class="error-card">
          <h1>Impossible !</h1>
          <p>Tu as déjà une commande en cours de livraison.</p>
          <p>Tu ne peux pas en passer une nouvelle tant que celle-ci n'est pas terminée.</p>
          <a href="tracking.php?id_livraison=<?= $livraisonEnCours->getIdLivraison() ?>" class="btn">
            Voir le suivi de ta livraison →
          </a>
          <br>
          <a href="catalogue.php" class="btn">Retour au catalogue</a>
        </div>
      </main>
    </body>
    </html>
    <?php
    exit;
}
?>
<!-- LE RESTE DE TON ANCIEN CODE SHIPPING.PHP (carte, paiement, etc.) -->
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
    .btn.primary { background: linear-gradient(45deg, #8b5cf6, #ec4899); padding: 1rem 3rem; border-radius: 50px; font-weight: 700; text-transform: uppercase; border: none; cursor: pointer; box-shadow: 0 0 30px rgba(139,92,246,0.7); }
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

    <div class="card">
      <h2>Adresse de livraison</h2>
      <div id="map"></div>
      <div id="selectedAddress">Clique sur la carte pour choisir ton adresse</div>
      <button class="btn primary" onclick="confirmAddress()">Confirmer l'adresse</button>
    </div>

    <div class="card">
      <h2>Mode de paiement</h2>
      <label style="display:block; margin:1.5rem 0; font-size:1.4rem;">
        <input type="radio" name="payment" value="credit" checked> Paiement avec le crédit du site
      </label>
      <label style="display:block; margin:1.5rem 0; font-size:1.4rem;">
        <input type="radio" name="payment" value="cash"> Paiement en espèce à la livraison
      </label>
      <button class="btn primary" onclick="goToConfirmation()">Suivant →</button>
    </div>
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

        if (marker) marker.remove();
        marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map).bindPopup('Livraison ici !').openPopup();
        map.setView([e.latlng.lat, e.latlng.lng], 16);
      });
    }

    window.onload = initMap;

    function confirmAddress() {
      if (!selectedAddr) return alert('Clique sur la carte !');
      localStorage.setItem('deliveryAddress', selectedAddr);
      localStorage.setItem('lat', selectedLat);
      localStorage.setItem('lng', selectedLng);
      alert('Adresse confirmée !');
    }

    function goToConfirmation() {
      if (!localStorage.getItem('deliveryAddress')) return alert('Confirme ton adresse d\'abord !');
      const payment = document.querySelector('input[name="payment"]:checked').value;
      localStorage.setItem('paymentMethod', payment);
      location.href = 'confirmation.php';
    }
  </script>
</body>
</html>