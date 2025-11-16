<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../Controllers/frontoffice/AuthController.php";

$authController = new AuthController();
$currentUser = $authController->getCurrentUser();
$isLoggedIn = $currentUser !== null;
$isAdmin = $isLoggedIn && $currentUser['role'] === 'admin';
$userName = $isLoggedIn ? ($currentUser['prenom'] . ' ' . $currentUser['nom']) : 'Guest';
$userCredit = $isLoggedIn ? $currentUser['credit'] : 0;
?>
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="index.html">
                    <img src="../../public/images/logo.png" alt="NextGen Logo" class="logo-img">
                    NextGen
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="catalog.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'catalog.html' || basename($_SERVER['PHP_SELF']) == 'catalog.php') ? 'active' : ''; ?>">Jeux</a></li>
                    <li><a href="about.html">À Propos</a></li>
                    <li><a href="donations.html">Nos Dons</a></li>
                    <li><a href="returns.html">Retours et Réclamations</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="cart.html" class="cart-icon" title="Panier">
                    🛒
                    <span class="cart-count">0</span>
                </a>
                <?php if ($isAdmin): ?>
                    <a href="dashboard.php" class="btn-dashboard" style="margin-right: 10px; padding: 8px 16px; background: var(--primary-color); color: white; text-decoration: none; border-radius: 4px; font-size: 14px;">Dashboard</a>
                <?php endif; ?>
                <div class="profile-dropdown">
                    <div class="profile-picture" id="profilePicture" style="cursor: pointer;">
                        <img src="data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2740%27 height=%2740%27%3E%3Ccircle cx=%2720%27 cy=%2720%27 r=%2718%27 fill=%27%233D37B1%27/%3E%3Ctext x=%2750%25%27 y=%2750%27 text-anchor=%27middle%27 dy=%27.3em%27 fill=%27white%27 font-size=%2714%27 font-weight=%27bold%27%3E<?php echo strtoupper(substr($userName, 0, 1)); ?>%3C/text%3E%3C/svg%3E" alt="Profile" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                    </div>
                    <div class="profile-dropdown-menu" id="profileDropdown" style="display: none;">
                        <div class="profile-info">
                            <div class="profile-name"><?php echo htmlspecialchars($userName); ?></div>
                            <?php if ($isLoggedIn): ?>
                                <div class="profile-credit">Crédit: <?php echo $userCredit; ?> TND</div>
                            <?php else: ?>
                                <div class="profile-credit">Invité</div>
                            <?php endif; ?>
                        </div>
                        <div class="dropdown-divider"></div>
                        <?php if ($isLoggedIn): ?>
                            <a href="account.html" class="dropdown-item">Mon Compte</a>
                            <a href="settings.php" class="dropdown-item">Modifier les informations</a>
                            <a href="#" class="dropdown-item" id="logoutBtn">Déconnexion</a>
                        <?php else: ?>
                            <a href="login.php" class="dropdown-item">Se connecter</a>
                            <a href="register.php" class="dropdown-item">S'inscrire</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profilePicture = document.getElementById('profilePicture');
    const profileDropdown = document.getElementById('profileDropdown');
    
    if (profilePicture && profileDropdown) {
        profilePicture.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.style.display = profileDropdown.style.display === 'none' ? 'block' : 'none';
        });
        
        document.addEventListener('click', function() {
            profileDropdown.style.display = 'none';
        });
        
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
        const logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                fetch('../../api/auth.php?action=logout')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = 'index.html';
                        }
                    });
            });
        }
});
</script>
<style>
.profile-dropdown {
    position: relative;
}

.profile-picture {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    border: 2px solid var(--primary-color);
}

.profile-dropdown-menu {
    position: absolute;
    top: 50px;
    right: 0;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    min-width: 200px;
    z-index: 1000;
    overflow: hidden;
}

.profile-info {
    padding: 12px 16px;
    background: var(--primary-color);
    color: white;
}

.profile-name {
    font-weight: 600;
    margin-bottom: 4px;
}

.profile-credit {
    font-size: 12px;
    opacity: 0.9;
}

.dropdown-divider {
    height: 1px;
    background: var(--border-color);
}

.dropdown-item {
    display: block;
    padding: 12px 16px;
    color: var(--text-dark);
    text-decoration: none;
    transition: background 0.2s;
}

.dropdown-item:hover {
    background: var(--bg-light);
}
</style>

