<?php require __DIR__ . '/../layouts/header_admin.php'; ?>

<h2 class="back-title" style="text-align: center; color: #667eea; margin-bottom: 30px;">Dashboard Administrateur</h2>

<?php if(!empty($_SESSION['success'])): ?>
  <div class="success">
    <?= htmlspecialchars($_SESSION['success']) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<!-- Statistiques rapides -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-number"><?= count($users) ?></div>
    <div class="stat-label">Utilisateurs</div>
  </div>
  <div class="stat-card" style="background: linear-gradient(135deg, #28a745, #20c997);">
    <div class="stat-number"><?= count($histories) ?></div>
    <div class="stat-label">Historiques</div>
  </div>
  <div class="stat-card" style="background: linear-gradient(135deg, #ffc107, #ff9800);">
    <div class="stat-number"><?= count(array_filter($users, fn($u) => $u['role'] === 'admin')) ?></div>
    <div class="stat-label">Admins</div>
  </div>
  <div class="stat-card" style="background: linear-gradient(135deg, #17a2b8, #138496);">
    <div class="stat-number"><?= count(array_filter($users, fn($u) => ($u['statut'] ?? 'actif') === 'actif')) ?></div>
    <div class="stat-label">Actifs</div>
  </div>
</div>

<div style="text-align: center; margin: 40px 0;">
  <a href="/user_nextgen/admin/users" class="btn-primary" style="font-size: 1.2rem; padding: 15px 40px;">
    Gestion Complète des Utilisateurs
  </a>
</div>

<h3 style="color: #667eea; margin-top: 40px; margin-bottom: 20px;">Aperçu des Utilisateurs</h3>

<?php if (empty($users)): ?>
  <p style="text-align: center; color: #999;">Aucun utilisateur trouvé.</p>
<?php else: ?>
  <table class="table">
    <thead>
      <tr>
        <th>Photo</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Crédit</th>
        <th>Statut</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach(array_slice($users, 0, 5) as $u): ?>
        <tr>
          <td>
            <?php if (!empty($u['photo_profile'])): ?>
              <img src="/user_nextgen/uploads/avatars/<?= htmlspecialchars($u['photo_profile']) ?>" 
                   alt="Photo" 
                   class="profile-img">
            <?php else: ?>
              <div class="profile-initial">
                <?= strtoupper(substr($u['nom'], 0, 1)) ?>
              </div>
            <?php endif; ?>
          </td>
          <td><strong><?= htmlspecialchars($u['nom']) ?> <?= htmlspecialchars($u['prenom']) ?></strong></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="badge badge-<?= $u['role'] ?>"><?= htmlspecialchars($u['role']) ?></span></td>
          <td><strong><?= htmlspecialchars($u['credit'] ?? '0') ?> TND</strong></td>
          <td><span class="badge badge-<?= $u['statut'] ?? 'actif' ?>"><?= htmlspecialchars($u['statut'] ?? 'actif') ?></span></td>
          <td>
            <div class="action-buttons">
              <a href="/user_nextgen/admin/users/view?id=<?= $u['id_user'] ?>" class="btn-view">Voir</a>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if (count($users) > 5): ?>
    <p style="text-align: center; margin-top: 20px;">
      <a href="/user_nextgen/admin/users" class="btn-primary">Voir tous les utilisateurs (<?= count($users) ?>)</a>
    </p>
  <?php endif; ?>
<?php endif; ?>

<h3 style="color: #667eea; margin-top: 40px; margin-bottom: 20px;">Historique Global (avec jointure)</h3>

<?php if (empty($histories)): ?>
  <p style="text-align: center; color: #999;">Aucun historique trouvé.</p>
<?php else: ?>
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Utilisateur</th>
        <th>Email</th>
        <th>Action</th>
        <th>Description</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach(array_slice($histories, 0, 10) as $h): ?>
        <tr>
          <td><?= htmlspecialchars($h['id_historique']) ?></td>
          <td><strong><?= htmlspecialchars($h['user_nom'] ?? 'N/A') ?> <?= htmlspecialchars($h['user_prenom'] ?? '') ?></strong></td>
          <td><?= htmlspecialchars($h['user_email'] ?? 'N/A') ?></td>
          <td><?= htmlspecialchars($h['type_action']) ?></td>
          <td><?= htmlspecialchars($h['description'] ?? '-') ?></td>
          <td><?= date('d/m/Y H:i', strtotime($h['date_action'] ?? 'now')) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if (count($histories) > 10): ?>
    <p style="text-align: center; margin-top: 20px;">
      <a href="/user_nextgen/history" class="btn-primary">Voir tout l'historique (<?= count($histories) ?>)</a>
    </p>
  <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer_admin.php'; ?>
