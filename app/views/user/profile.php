<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>Mon Profil</h2>

<div style="margin: 2rem 0;">
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
      <?php if (isset($user['credit'])): ?>
      <tr>
        <td style="font-weight: bold;">Crédit</td>
        <td><?= htmlspecialchars($user['credit']) ?> TND</td>
      </tr>
      <?php endif; ?>
      <?php if (isset($user['statut'])): ?>
      <tr>
        <td style="font-weight: bold;">Statut</td>
        <td><?= htmlspecialchars($user['statut']) ?></td>
      </tr>
      <?php endif; ?>
      <tr>
        <td style="font-weight: bold;">Date d'inscription</td>
        <td><?= htmlspecialchars($user['date_inscription'] ?? 'N/A') ?></td>
      </tr>
      <?php if (isset($user['photo_profil']) && $user['photo_profil']): ?>
      <tr>
        <td style="font-weight: bold;">Photo de profil</td>
        <td>
          <img src="/user_nextgen/uploads/avatars/<?= htmlspecialchars($user['photo_profil']) ?>" 
               alt="Photo de profil" 
               style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid #007bff;">
        </td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<div style="margin: 2rem 0; display: flex; gap: 1rem;">
  <a href="/user_nextgen/profile/edit" style="padding: 0.75rem 1.5rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px;">✏️ Modifier mon profil</a>
  <a href="/user_nextgen/" style="padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">🏠 Retour à l'accueil</a>
</div>

<h3 style="margin-top: 3rem;">Mon Historique Récent</h3>

<div style="margin: 1rem 0;">
  <a href="/user_nextgen/history" style="padding: 0.5rem 1rem; background: #17a2b8; color: white; text-decoration: none; border-radius: 4px; margin-right: 0.5rem;">📋 Voir tout l'historique</a>
  <a href="/user_nextgen/history/create" style="padding: 0.5rem 1rem; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">➕ Ajouter une action</a>
</div>

<?php if (empty($histories)): ?>
  <p>Aucun historique pour le moment.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Action</th>
        <th>Description</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (array_slice($histories, 0, 5) as $h): ?>
        <tr>
          <td><?= htmlspecialchars($h['type_action']) ?></td>
          <td><?= htmlspecialchars($h['description'] ?? '-') ?></td>
          <td><?= htmlspecialchars($h['date_action'] ?? '-') ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if (count($histories) > 5): ?>
    <p style="margin-top: 1rem;"><em>Affichage des 5 dernières actions. <a href="/user_nextgen/history">Voir tout</a></em></p>
  <?php endif; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
