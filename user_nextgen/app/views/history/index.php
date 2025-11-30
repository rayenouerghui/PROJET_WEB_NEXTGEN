<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>📋 Historique Global (Groupé par Utilisateur)</h2>

<?php if(!empty($_SESSION['success'])): ?>
  <div class="success">
    <?= htmlspecialchars($_SESSION['success']) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<div style="margin-bottom: 2rem;">
  <a href="/user_nextgen/history/create" style="padding: 0.75rem 1.5rem; background: #28a745; color: white; text-decoration: none; border-radius: 4px;">➕ Créer un historique</a>
</div>

<?php if (empty($historiesByUser)): ?>
  <p>Aucun utilisateur trouvé.</p>
<?php else: ?>
  
  <?php foreach ($historiesByUser as $userData): ?>
    <?php $hasHistories = !empty($userData['histories']); ?>
    <div style="margin-bottom: 3rem; border: 2px solid #007bff; border-radius: 8px; overflow: hidden;">
      <!-- En-tête utilisateur -->
      <div style="background: #007bff; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="margin: 0; font-size: 1.2rem;">
            👤 <?= htmlspecialchars($userData['user']['nom']) ?> <?= htmlspecialchars($userData['user']['prenom']) ?>
          </h3>
          <p style="margin: 0.25rem 0 0 0; opacity: 0.9;">
            📧 <?= htmlspecialchars($userData['user']['email']) ?> | 
            🆔 ID: <?= htmlspecialchars($userData['user']['id']) ?>
          </p>
        </div>
        <div style="background: white; color: #007bff; padding: 0.5rem 1rem; border-radius: 4px; font-weight: bold;">
          <?= count($userData['histories']) ?> action(s)
        </div>
      </div>

      <!-- Tableau des historiques de cet utilisateur -->
      <div style="padding: 1rem;">
        <table style="margin: 0;">
          <thead>
            <tr style="background: #f8f9fa;">
              <th>ID</th>
              <th>Type d'action</th>
              <th>Description</th>
              <th>Données actions</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($userData['histories'] as $h): ?>
              <tr>
                <td><?= htmlspecialchars($h['id_historique']) ?></td>
                <td><strong><?= htmlspecialchars($h['type_action']) ?></strong></td>
                <td><?= htmlspecialchars($h['description'] ?? '-') ?></td>
                <td>
                  <?php if (!empty($h['donnees_actions'])): ?>
                    <code style="background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">
                      <?= htmlspecialchars(substr($h['donnees_actions'], 0, 50)) ?><?= strlen($h['donnees_actions']) > 50 ? '...' : '' ?>
                    </code>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($h['date_action'] ?? 'now')) ?></td>
                <td>
                  <div style="display: flex; gap: 0.25rem;">
                    <a href="/user_nextgen/history/edit?id=<?= $h['id_historique'] ?>" 
                       style="padding: 0.25rem 0.5rem; background: #007bff; color: white; text-decoration: none; border-radius: 4px; font-size: 0.75rem;">
                      ✏️ Modifier
                    </a>
                    <form action="/user_nextgen/history/delete" method="POST" style="display: inline; margin: 0;" 
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet historique ?');">
                      <input type="hidden" name="id" value="<?= $h['id_historique'] ?>">
                      <button type="submit" class="btn-small btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                        🗑️ Supprimer
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php endforeach; ?>

  <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px; text-align: center;">
    <strong>Total: <?= count($historiesByUser) ?> utilisateur(s)</strong> avec 
    <strong><?= array_sum(array_map(fn($u) => count($u['histories']), $historiesByUser)) ?> action(s)</strong>
  </div>

<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
