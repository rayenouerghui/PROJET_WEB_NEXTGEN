<?php
// Shared Navigation Component
// Include this file in all front office pages for consistent navigation
if (!defined('WEB_ROOT')) {
    require_once __DIR__ . '/../../../config/paths.php';
}

$current_page = basename($_SERVER['PHP_SELF']);
$is_logged_in = isset($_SESSION['user']);
$is_admin = isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
?>
<header>
    <div class="container nav">
      <div class="left">
      <a href="<?php echo WEB_ROOT; ?>/view/frontoffice/index.php" class="logo">
        <img src="<?php echo WEB_ROOT; ?>/resources/nextgen.png" alt="NextGen Logo" class="nav-logo" style="height: 44px; width: auto; max-height: 44px; object-fit: contain;">
      </a>
        
        <nav class="menu">
          <a href="<?php echo WEB_ROOT; ?>/view/frontoffice/index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Accueil</a>
          <a href="<?php echo WEB_ROOT; ?>/view/frontoffice/catalogue.php" class="<?php echo ($current_page == 'catalogue.php') ? 'active' : ''; ?>">Produits</a>
          <a href="<?php echo WEB_ROOT; ?>/view/frontoffice/blog.php" class="<?php echo ($current_page == 'blog.php') ? 'active' : ''; ?>">
            <i class="bi bi-journal-text"></i> Blog
          </a>
          <a href="<?php echo WEB_ROOT; ?>/index.php?c=front&a=events" class="<?php echo (isset($_GET['c']) && $_GET['c'] == 'front' && $_GET['a'] == 'events') ? 'active' : ''; ?>">
            <i class="bi bi-calendar-event"></i> Événements
          </a>
          <a href="<?php echo WEB_ROOT; ?>/view/livraison.php" class="<?php echo ($current_page == 'livraison.php') ? 'active' : ''; ?>">
            <i class="bi bi-truck"></i> Livraison
          </a>
          <a href="apropos.html">À Propos</a>
        </nav>
      </div>

      <div style="display:flex; gap:1rem; align-items:center;">
        <?php if ($is_admin): ?>
          <a class="dashboard-btn" href="<?php echo WEB_ROOT; ?>/view/backoffice/admin_users.php">
            <i class="bi bi-person-gear"></i> Administration
          </a>
        <?php endif; ?>

        <?php if ($is_logged_in): 
          $photo = !empty($_SESSION['user']['photo_profil']) 
              ? WEB_ROOT . '/resources/' . $_SESSION['user']['photo_profil'] 
              : WEB_ROOT . '/resources/default.jpg';
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
              <a href="<?php echo WEB_ROOT; ?>/view/livraison.php">
                <i class="bi bi-truck"></i> Mes livraisons
              </a>
              <a href="index.php?show_history=1">
                <i class="bi bi-clock-history"></i> Historique d'activité
              </a>
              <a href="reclamation.php">
                <i class="bi bi-exclamation-triangle"></i> Passer une réclamation
              </a>
              <a href="mes_reclamations.php">
                <i class="fas fa-exclamation-triangle"></i> Mes réclamations
              </a>
              <hr>
              <a href="<?php echo WEB_ROOT; ?>/view/backoffice/logout.php" class="logout-item">
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

