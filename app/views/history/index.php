<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>Historique</h2>

<?php if(!empty($_SESSION['success'])): ?>
  <div class="success">
    <?= htmlspecialchars($_SESSION['success']) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<a href="/user_nextgen/history/create" style="display: inline-block; padding: 0.5rem 1rem; background: #28a745; color: white; text-decoration: none; border-radius: 4px; margin-bottom: 1rem;">Créer un historique</a>

<?php if (empty($histories)): ?>
  <p>Aucun historique trouvé.</p>
<?php else: ?>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
          <th>Utilisateur</th>
        <?php endif; ?>
        <th>Action</th>
        <th>Description</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($histories as $h): ?>
        <tr>
          <td><?= htmlspecialchars($h['id_historique']) ?></td>
          <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <td><?= htmlspecialchars($h['user_nom'] ?? 'N/A') ?> <?= htmlspecialchars($h['user_prenom'] ?? '') ?></td>
          <?php endif; ?>
          <td><?= htmlspecialchars($h['type_action']) ?></td>
          <td><?= htmlspecialchars($h['description'] ?? '-') ?></td>
          <td><?= htmlspecialchars($h['date_action'] ?? '-') ?></td>
          <td>
            <a href="/user_nextgen/history/edit?id=<?= $h['id_historique'] ?>" style="padding: 0.25rem 0.5rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px; font-size: 0.875rem;">Modifier</a>
            <form action="/user_nextgen/history/delete" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet historique ?');">
              <input type="hidden" name="id" value="<?= $h['id_historique'] ?>">
              <button type="submit" class="btn-small btn-danger">Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
