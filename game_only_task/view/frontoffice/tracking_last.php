<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: connexion.php');
    exit;
}

require_once '../../controller/LivraisonController.php';
$controller = new LivraisonController();

$livraison = $controller->getLivraisonEnCours($_SESSION['user']['id']);

if (!$livraison) {
    // Aucune livraison
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Aucune livraison</title>
          <style>body{background:url(./bg-jeux.gif) fixed center/cover;color:white;text-align:center;padding-top:15vh;font-family:Rajdhani,sans-serif;}
                    h1{font-size:4rem;} a{color:#ec4899;font-size:2rem;text-decoration:underline;}</style>
    <link rel="stylesheet" href="../assets/green-theme.css">
</head>
          <body><h1>Aucune livraison en cours 😔</h1>
          <p><a href="catalogue.php">Retour au catalogue</a></p></body></html>';
    exit;
}

// Il y a une livraison → redirige vers le bon tracking avec l'ID
header("Location: tracking.php?id_livraison=" . $livraison->getIdLivraison());
exit;
?>