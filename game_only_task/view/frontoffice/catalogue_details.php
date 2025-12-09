<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../controller/jeuController.php';
$controller = new JeuController();

$id_jeu = (int)($_GET['id'] ?? 0);
$jeu = $controller->getJeu($id_jeu);

if (!$jeu) {
    header('Location: catalogue.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($jeu->getTitre()) ?> – NextGen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">

  <!-- Polices gaming -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Rajdhani:wght@600;800&display=swap" rel="stylesheet">

  <style>
    /* USER DROPDOWN – INTACT */
    .user-dropdown { position: fixed; right:25px; }
    .user-btn { background: none; border: none; display: flex; align-items: center; gap: 12px; cursor: pointer; padding: 8px 14px; border-radius: 50px; transition: all 0.3s ease; font-family: 'Inter', sans-serif; }
    .user-btn:hover { background: rgba(79, 70, 229, 0.1); }
    .user-name { color: #4f46e5; font-weight: 600; font-size: 0.95rem; }
    .user-avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 3px solid #4f46e5; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.25); }
    .dropdown-menu { position: absolute; top: 100%; right: 0; background: white; min-width: 230px; border-radius: 14px; box-shadow: 0 12px 40px rgba(0,0,0,0.18); opacity: 0; visibility: hidden; transform: translateY(-10px); transition: all 0.3s ease; z-index: 1000; overflow: hidden; margin-top: 10px; }
    .user-dropdown:hover .dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); }
    .dropdown-menu a { display: flex; align-items: center; gap: 12px; padding: 14px 20px; color: #374151; font-size: 0.95rem; transition: all 0.2s; text-decoration: none; }
    .dropdown-menu a:hover { background: #f8f9ff; color: #4f46e5; padding-left: 26px; }
    .dropdown-menu hr { margin: 8px 0; border: none; border-top: 1px solid #e5e7eb; }
    .logout-item { color: #ef4444 !important; }
    .logout-item:hover { background: #fef2f2 !important; color: #dc2626 !important; }

    /* BACKGROUND GIF + BLUR FIXE (comme avant) */
    body {
      background: url('../../resources/bg-jeux.gif') no-repeat center top fixed !important;
      background-size: cover !important;
      background-attachment: fixed !important;
      position: relative;
      min-height: 100vh;
      margin: 0;
      padding: 0;
      transform: translateZ(0);
      backface-visibility: hidden;
    }

    body::before {
      content: '';
      position: fixed !important;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0, 0, 0, 0.55);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      z-index: 1;
      pointer-events: none;
    }

    header, .container, footer {
      position: relative;
      z-index: 2;
    }

    /* STYLE GAMING IDENTIQUE À TA CAPTURE – TAILLE RÉDUITE */
    .details-container {
      background: linear-gradient(135deg, rgba(56, 28, 135, 0.9), rgba(59, 7, 100, 0.8));
      border-radius: 1.5rem;
      padding: 2rem;
      box-shadow: 0 15px 40px rgba(0,0,0,0.5);
      border: 1px solid rgba(139, 92, 246, 0.6);
      max-width: 1000px;
      margin: 2.5rem auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
    }

    .game-image {
      width: 100%;
      border-radius: 1rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }

    .game-info h1 {
      font-family: 'Rajdhani', sans-serif;
      font-weight: 800;
      font-size: 2.4rem;   /* Réduit de 3.5rem → 2.4rem */
      color: white;
      text-shadow: 0 0 20px rgba(255,255,255,0.4);
      margin-bottom: 0.5rem;
    }

    .game-category {
      color: #e0d4ff;
      font-size: 1.2rem;   /* Légèrement réduit */
      font-weight: 600;
      margin-bottom: 1rem;
    }

    .game-price {
      color: #00ffc3;
      font-size: 2.2rem;   /* Réduit de 3rem → 2.2rem */
      font-weight: 900;
      text-shadow: 0 0 25px #00ffc3;
      margin: 1rem 0;
    }

    .game-description h3 {
      color: white;
      font-size: 1.5rem;   /* Réduit */
      margin-top: 1.5rem;
    }

    .game-description p {
      color: #e0d4ff;
      line-height: 1.7;
      font-size: 1.05rem;   /* Légèrement réduit */
    }

    .btn-acheter {
      position:absolute;
      top:440px;
      left:520px;
      background: linear-gradient(45deg, #8b5cf6, #ec4899);
      padding: 0.9rem 2.5rem;   /* Réduit */
      border-radius: 50px;
      font-size: 1.1rem;   /* Réduit */
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      box-shadow: 0 0 30px rgba(139, 92, 246, 0.7);
      transition: all 0.3s;
    }

    .btn-acheter:hover {
      background: linear-gradient(45deg, #d946ef, #f43f5e);
      transform: translateY(-6px);
      box-shadow: 0 0 45px rgba(236, 72, 153, 0.9);
    }

    @media (max-width: 768px) {
      .details-container { grid-template-columns: 1fr; padding: 1.5rem; }
      .game-info h1 { font-size: 2rem; }
      .game-price { font-size: 1.9rem; }
    }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body>
  <header>
    <div class="container nav">
      <div class="left">
        <a href="index.php" class="logo">NextGen</a>
        <nav class="menu">
          <a href="index.php" >Accueil</a>
          <a href="catalogue.php" class="active">Produits</a>
          <a href="apropos.html">À Propos</a>
        </nav>
      </div>

      <div style="display:flex; gap:1.5rem; align-items:center; margin-left: auto;">

        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
          <a href="../backoffice/admin_users.php" style="display: flex; align-items: center; gap: 0.5rem; color: #4f46e5; text-decoration: none; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.375rem; transition: all 0.3s; white-space: nowrap;" onmouseover="this.style.background='rgba(79, 70, 229, 0.1)'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='transparent'; this.style.transform='translateY(0)';">
            <i class="bi bi-person-gear"></i> Administration
          </a>
        <?php endif; ?>

        <?php if (isset($_SESSION['user'])): 
          $photo = !empty($_SESSION['user']['photo_profil']) 
              ? '../../resources/' . $_SESSION['user']['photo_profil'] 
              : '../../resources/default.jpg';
        ?>
          <div class="user-dropdown">
            <button class="user-btn">
              <span class="user-name">Bienvenue <?= htmlspecialchars($_SESSION['user']['prenom']) ?></span>
              <img src="<?= $photo ?>" alt="Profil" class="user-avatar">
            </button>

            <div class="dropdown-menu">
              <a href="profil.php">
                <i class="bi bi-person-circle"></i> Gérer mon profil
              </a>
              <a href="tracking_last.php">
                <i class="bi bi-truck"></i> Mes livraisons
              </a>
              <a href="historique.php">
                <i class="bi bi-clock-history"></i> Historique d'activité
              </a>
              <hr>
              <a href="../backoffice/logout.php" class="logout-item">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
              </a>
            </div>
          </div>

        <?php else: ?>
          <a href="connexion.php" style="color:#4f46e5; font-weight:600;">Connexion</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <div class="container">
    <div class="details-container">
      <div>
        <img src="../../resources/<?= htmlspecialchars($jeu->getSrcImg()) ?>" alt="<?= htmlspecialchars($jeu->getTitre()) ?>" class="game-image">
      </div>

      <div class="game-info">
        <h1><?= htmlspecialchars($jeu->getTitre()) ?></h1>
        <div class="game-category">
          Catégorie : <?= htmlspecialchars($jeu->nom_categorie ?? 'Non classé') ?>
        </div>

        <div class="game-price">
          <?= number_format($jeu->getPrix(), 2) ?> TND
        </div>

        <?php if ($jeu->getDescription()): ?>
          <div class="game-description">
            <h3>Description</h3>
            <p><?= nl2br(htmlspecialchars($jeu->getDescription())) ?></p>
          </div>
        <?php endif; ?>

        <a href="shipping.php?id=<?= $jeu->getIdJeu() ?>" class="btn-acheter">
          <i class="bi bi-cart-plus"></i> Acheter maintenant
        </a>
      </div>
    </div>
  </div>

  <footer style="background-color:black; position:relative; z-index:2;">
    <!-- Ton footer -->
  </footer>
</body>
</html>