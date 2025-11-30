<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>👤 Profil de <?= htmlspecialchars($user['nom']) ?> <?= htmlspecialchars($user['prenom']) ?></h2>

<?php if(!empty($_SESSION['success'])): ?>
  <div class="success">
    <?= htmlspecialchars($_SESSION['success']) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<div style="display: flex; gap: 2rem; margin: 2rem 0;">
  <!-- Informations utilisateur -->
  <div style="flex: 1;">
    <table style="max-width: 600px;">
      <thead>
        <tr>
          <th colspan="2" style="background: #007bff; color: white; text-align: center; padding: 1rem;">
            Informations du compte
          </th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="font-weight: bold; width: 200px;">ID</td>
          <td><?= htmlspecialchars($user['id_user']) ?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Photo de profil</td>
          <td>
            <?php if (!empty($user['photo_profile'])): ?>
              <img src="/user_nextgen/uploads/avatars/<?= htmlspecialchars($user['photo_profile']) ?>" 
                   alt="Photo de profil" 
                   style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid #007bff;">
            <?php else: ?>
              <div style="width: 100px; height: 100px; background: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 2rem;">
                <?= strtoupper(substr($user['nom'], 0, 1)) ?>
              </div>
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Nom</td>
          <td><?= htmlspecialchars($user['nom']) ?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Prénom</td>
          <td><?= htmlspecialchars($user['prenom']) ?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Email</td>
          <td><?= htmlspecialchars($user['email']) ?></td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Rôle</td>
          <td><span style="padding: 0.25rem 0.5rem; background: <?= $user['role'] === 'admin' ? '#ffc107' : '#28a745' ?>; color: white; border-radius: 4px;"><?= htmlspecialchars($user['role']) ?></span></td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Crédit</td>
          <td><strong style="color: #28a745; font-size: 1.2rem;"><?= htmlspecialchars($user['credit'] ?? '0') ?> TND</strong></td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Statut</td>
          <td>
            <?php 
            $statusColors = ['actif' => '#28a745', 'suspendu' => '#ffc107', 'banni' => '#dc3545'];
            $statusColor = $statusColors[$user['statut'] ?? 'actif'] ?? '#6c757d';
            ?>
            <span style="padding: 0.5rem 1rem; background: <?= $statusColor ?>; color: white; border-radius: 4px;">
              <?= htmlspecialchars($user['statut'] ?? 'actif') ?>
            </span>
          </td>
        </tr>
        <tr>
          <td style="font-weight: bold;">Date d'inscription</td>
          <td><?= htmlspecialchars($user['date_inscription'] ?? 'N/A') ?></td>
        </tr>
      </tbody>
    </table>

    <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
      <a href="/user_nextgen/admin/users/edit?id=<?= $user['id_user'] ?>" style="padding: 0.75rem 1.5rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">✏️ Éditer</a>
      <a href="/user_nextgen/admin/users" style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">← Retour à la liste</a>
    </div>
  </div>
</div>

<!-- Historique de l'utilisateur -->
<h3 style="margin-top: 3rem;">📋 Historique des actions (<?= count($histories) ?>)</h3>

<?php if (empty($histories)): ?>
  <p>Aucun historique pour cet utilisateur.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Action</th>
        <th>Description</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($histories as $h): ?>
        <tr>
          <td><?= htmlspecialchars($h['id_historique']) ?></td>
          <td><?= htmlspecialchars($h['type_action']) ?></td>
          <td><?= htmlspecialchars($h['description'] ?? '-') ?></td>
          <td><?= htmlspecialchars($h['date_action'] ?? '-') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
