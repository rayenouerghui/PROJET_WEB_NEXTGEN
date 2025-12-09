<?php require_once __DIR__ . '/layouts/header.php'; ?>

<h1>Bienvenue sur NextGen</h1>
<p>Boutique gaming & accessoires — un pourcentage des ventes est reversé aux maisons d'orphelins.</p>

<?php if (empty($_SESSION['user'])): ?>
  <div style="margin-top: 2rem;">
    <h2>Commencez maintenant</h2>
    <p>Créez un compte ou connectez-vous pour accéder à toutes les fonctionnalités.</p>
    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
      <a href="/user_nextgen/register" style="padding: 1rem 2rem; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">S'inscrire</a>
      <a href="/user_nextgen/login" style="padding: 1rem 2rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">Se connecter</a>
    </div>
  </div>
<?php else: ?>
  <div style="margin-top: 2rem;">
    <h2>Bienvenue, <?= htmlspecialchars($_SESSION['user']['nom']) ?> <?= htmlspecialchars($_SESSION['user']['prenom']) ?>!</h2>
    <p>Vous êtes connecté en tant que <strong><?= htmlspecialchars($_SESSION['user']['role']) ?></strong>.</p>
    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
      <a href="/user_nextgen/profile" style="padding: 1rem 2rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">Mon Profil</a>
      <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="/user_nextgen/admin/users" style="padding: 1rem 2rem; background: #ffc107; color: #333; text-decoration: none; border-radius: 4px;">Gestion Utilisateurs</a>
        <a href="/user_nextgen/history" style="padding: 1rem 2rem; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px;">Historique</a>
      <?php endif; ?>
    </div>
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
