
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../controller/jeuController.php';
$controller = new JeuController();
$jeux = $controller->afficherJeux();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>NextGen – Catalogue</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="styles.css">

  <!-- Polices gaming pour le style de ta capture -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Rajdhani:wght@600;800&display=swap" rel="stylesheet">

  <style>
    /* === USER DROPDOWN MENU (intact) === */
    .user-dropdown { position: relative; display: inline-block; }
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
      background: url('../../resources/bg-jeux.gif') no-repeat center top fixed !  important;
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

    header, .catalogue, footer {
      position: relative;
      z-index: 2;
    }

    /* STYLE GAMING EXACT DE TA CAPTURE */
    .catalogue h1 {
      color: white !important;
      font-size: 3rem;
      text-shadow: 0 0 20px rgba(255,255,255,0.5);
    }

    .game-card {
      background: linear-gradient(135deg, rgba(62, 37, 255, 0.9), rgba(59, 7, 100, 0.8));
      border-radius: 1.5rem;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0,0,0,0.5);
      transition: all 0.4s ease;
      border: 1px solid rgba(139, 92, 246, 0.6);
    }

    .game-card:hover {
      transform: translateY(-15px);
      box-shadow: 0 25px 50px rgba(139, 92, 246, 0.6);
    }

    .game-card img {
      width: 100%;
      height: 220px;
      object-fit: cover;
    }

    .game-card h3 {
      color: white;
      font-size: 1.5rem;
      padding: 1rem;
      font-weight: 700;
    }

    .game-card p:not(.price) {
      color: #e0d4ff;
      padding: 0 1rem;
      font-size: 1.1rem;
    }

    .price {
      color: #00ffc3;
      font-size: 2rem;
      font-weight: 900;
      padding: 0.5rem 1rem;
      text-shadow: 0 0 20px #00ffc3;
    }

    .btn-buy {
      display: block;
      margin: 1rem;
      padding: 1rem;
      background: linear-gradient(45deg, #8b5cf6, #ec4899);
      color: white;
      text-align: center;
      border-radius: 50px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 0 25px rgba(139, 92, 246, 0.6);
      transition: all 0.3s;
    }

    .btn-buy:hover {
      background: linear-gradient(45deg, #d946ef, #f43f5e);
      transform: translateY(-5px);
      box-shadow: 0 0 40px rgba(236, 72, 153, 0.8);
    }
    .search-bar input {
      position: relative;
      left: -380px;
      max-width: 400px;
      margin: 0 auto;
      display: block;
    }
        /* Fix dropdown cliquable + search bar alignée */
    .user-dropdown {
      z-index: 9999 !important;
    }
    .dropdown-menu {
      z-index: 9999 !important;
    }
    header {
      z-index: 1000;
    }
    .search-bar {
      z-index: 2;
      position: relative;
      left:450px;
    }
    /* AMÉLIORATIONS VISUELLES – sans toucher header ni contenu des cartes */
.catalogue {
  padding: 4rem 0;
}

.catalogue .container {
  max-width: 1400px;
}

/* Titre + search plus élégant et centré */
.catalogue > .container > div:first-child {
  justify-content: center !important;
  gap: 3rem;
  margin-bottom: 4rem !important;
}

.catalogue h1 {
  position: relative;
  left: 15px;
  font-size: 2rem !important;
  font-weight: 700;
  letter-spacing: 2px;
  background: linear-gradient(90deg, #8b5cf6, #ec4899);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-shadow: 0 0 30px rgba(139, 92, 246, 0.5);
}

/* Search bar plus stylée */
.search-bar input {
  width: 380px !important;
  max-width: 90vw;
  padding: 1rem 1.5rem;
  border-radius: 50px;
  border: 2px solid rgba(139, 92, 246, 0.4);
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(12px);
  color: white;
  font-size: 1.1rem;
  box-shadow: 0 8px 32px rgba(0,0,0,0.3);
  transition: all 0.3s ease;
}

.search-bar input:focus {
  outline: none;
  border-color: #ec4899;
  box-shadow: 0 0 30px rgba(236, 72, 153, 0.5);
  transform: scale(1.02);
}

.search-bar input::placeholder {
  color: rgba(255,255,255,0.7);
}

/* Grid plus aérée et centrée */
.games-grid {
  justify-content: center;
  gap: 2.5rem;
  padding: 0 1rem;
}

/* Petit glow sur les cartes au repos */
.game-card {
  box-shadow: 0 15px 40px rgba(0,0,0,0.4);
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
 

  <section class="catalogue">
  <div class="container">
    <form method="GET" style="all:unset;">
      <!-- Titre + Search bar sur la même ligne – positions EXACTES comme avant -->
      <div style="display:flex;flex-wrap:wrap; ">
        <h1 style="color:white;">Liste des produits</h1>
        
        <div class="search-bar">
          <input type="search" name="q" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" style="width:300px; max-width:100%;">
        </div>
      </div>

      <div class="games-grid">
        <?php 
        $search = trim($_GET['q'] ?? '');
        $hasResults = false;
        foreach ($jeux as $jeu): 
          if ($search !== '' && stripos($jeu->getTitre(), $search) === false) continue;
          $hasResults = true;
        ?>
          <div class="game-card">
            <img src="../../resources/<?= $jeu->getSrcImg() ?>" alt="<?= htmlspecialchars($jeu->getTitre()) ?>">
            <h3><?= htmlspecialchars($jeu->getTitre()) ?></h3>
            <p><?= htmlspecialchars($jeu->nom_categorie ?? '') ?></p>
            <p style="color:#00ffc3 !important;" class="price"><?= number_format($jeu->getPrix(), 2) ?> TND</p>
            <a href="catalogue_details.php?id=<?= $jeu->getIdJeu() ?>" class="btn-buy">Détails</a>
          </div>
        <?php endforeach; ?>

        <?php if (!$hasResults && $search !== ''): ?>
          <p class="empty">Aucun jeu trouvé pour "<?= htmlspecialchars($search) ?>"</p>
        <?php elseif (empty($jeux)): ?>
          <p class="empty">Aucun jeu disponible.</p>
        <?php endif; ?>
      </div>
    </form>
  </div>
</section>
</body>
</html>