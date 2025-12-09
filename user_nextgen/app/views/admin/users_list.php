<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Gestion des Utilisateurs</h2>

<?php if(!empty($_SESSION['success'])): ?>
  <div class="success">
    <?= htmlspecialchars($_SESSION['success']) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<!-- Formulaire de recherche et filtres -->
<form method="GET" action="/user_nextgen/admin/users" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
  <div style="display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 1rem; align-items: end;">
    <label style="margin: 0;">
      Rechercher
      <input type="text" name="search" placeholder="Nom, prénom ou email..." value="<?= htmlspecialchars($search) ?>" style="width: 100%;">
    </label>
    
    <label style="margin: 0;">
      Rôle
      <select name="role" style="width: 100%;">
        <option value="">Tous</option>
        <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>User</option>
        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
      </select>
    </label>
    
    <label style="margin: 0;">
      Statut
      <select name="statut" style="width: 100%;">
        <option value="">Tous</option>
        <option value="actif" <?= $statut === 'actif' ? 'selected' : '' ?>>Actif</option>
        <option value="suspendu" <?= $statut === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
        <option value="banni" <?= $statut === 'banni' ? 'selected' : '' ?>>Banni</option>
      </select>
    </label>
    
    <div style="display: flex; gap: 0.5rem;">
      <button type="submit" style="padding: 0.75rem 1.5rem; white-space: nowrap;">🔍 Rechercher</button>
      <a href="/user_nextgen/admin/users" style="padding: 0.75rem 1rem; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">↻</a>
    </div>
  </div>
</form>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
  <p><strong><?= $total ?></strong> utilisateur(s) trouvé(s)</p>
  <a href="/user_nextgen/admin/users/export" style="padding: 0.5rem 1rem; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">📥 Export CSV</a>
</div>

<?php if (empty($users)): ?>
  <p>Aucun utilisateur trouvé.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Photo</th>
        <th>Nom</th>
        <th>Email</th>
        <th>Rôle</th>
        <th>Crédit</th>
        <th>Statut</th>
        <th>Inscription</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($users as $u): ?>
        <tr>
          <td>
            <?php if (!empty($u['photo_profile'])): ?>
              <img src="/user_nextgen/uploads/avatars/<?= htmlspecialchars($u['photo_profile']) ?>" 
                   alt="Photo" 
                   style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
            <?php else: ?>
              <div style="width: 40px; height: 40px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white;">
                <?= strtoupper(substr($u['nom'], 0, 1)) ?>
              </div>
            <?php endif; ?>
          </td>
          <td><strong><?= htmlspecialchars($u['nom']) ?> <?= htmlspecialchars($u['prenom']) ?></strong></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><span style="padding: 0.25rem 0.5rem; background: <?= $u['role'] === 'admin' ? '#ffc107' : '#28a745' ?>; color: white; border-radius: 4px; font-size: 0.75rem;"><?= htmlspecialchars($u['role']) ?></span></td>
          <td><?= htmlspecialchars($u['credit'] ?? '0') ?> TND</td>
          <td>
            <?php 
            $statusColors = ['actif' => '#28a745', 'suspendu' => '#ffc107', 'banni' => '#dc3545'];
            $statusColor = $statusColors[$u['statut'] ?? 'actif'] ?? '#6c757d';
            ?>
            <span style="padding: 0.25rem 0.5rem; background: <?= $statusColor ?>; color: white; border-radius: 4px; font-size: 0.75rem;">
              <?= htmlspecialchars($u['statut'] ?? 'actif') ?>
            </span>
          </td>
          <td><?= date('d/m/Y', strtotime($u['date_inscription'] ?? 'now')) ?></td>
          <td>
            <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
              <a href="/user_nextgen/admin/users/view?id=<?= $u['id_user'] ?>" style="padding: 0.25rem 0.5rem; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; font-size: 0.75rem;">Voir</a>
              <a href="/user_nextgen/admin/users/edit?id=<?= $u['id_user'] ?>" style="padding: 0.25rem 0.5rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px; font-size: 0.75rem;">Éditer</a>
              
              <?php if ($u['id_user'] !== $_SESSION['user']['id_user']): ?>
                <form action="/user_nextgen/admin/users/delete" method="POST" style="display: inline; margin: 0;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                  <input type="hidden" name="user_id" value="<?= $u['id_user'] ?>">
                  <button type="submit" class="btn-small btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">Supprimer</button>
                </form>
              <?php else: ?>
                <span style="color: #999; font-size: 0.75rem;">Vous</span>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Pagination -->
  <?php if ($totalPages > 1): ?>
    <div style="margin-top: 2rem; display: flex; justify-content: center; gap: 0.5rem;">
      <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>&statut=<?= urlencode($statut) ?>" 
           style="padding: 0.5rem 1rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">← Précédent</a>
      <?php endif; ?>
      
      <span style="padding: 0.5rem 1rem; background: #f8f9fa; border-radius: 4px;">
        Page <?= $page ?> sur <?= $totalPages ?>
      </span>
      
      <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>&statut=<?= urlencode($statut) ?>" 
           style="padding: 0.5rem 1rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">Suivant →</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
