<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>Historique Global (Groupé par Utilisateur)</h2>

<?php if(!empty($_SESSION['success'])): ?>
  <div class="success">
    <?= htmlspecialchars($_SESSION['success']) ?>
    <?php unset($_SESSION['success']); ?>
  </div>
<?php endif; ?>

<?php 
// DEBUG - À supprimer après
echo "<!-- DEBUG: historiesByUser count = " . count($historiesByUser) . " -->";
if (!empty($historiesByUser)) {
    echo "<!-- DEBUG: Utilisateurs avec historiques: " . implode(', ', array_keys($historiesByUser)) . " -->";
}
?>

<?php if (empty($historiesByUser)): ?>
  <div style="background: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0;">
    <h3>Aucun utilisateur trouvé</h3>
    <p>Vous devez d'abord créer des comptes utilisateurs.</p>
    <p><a href="/user_nextgen/register" style="color: #007bff; font-weight: bold;">Créer un compte</a></p>
  </div>
<?php else: ?>
  
  <?php foreach ($historiesByUser as $userData): ?>
    <?php $hasHistories = !empty($userData['histories']); ?>
    <div style="margin-bottom: 3rem; border: 2px solid #007bff; border-radius: 8px; overflow: hidden;">
      <!-- En-tête utilisateur -->
      <div style="background: #007bff; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
          <h3 style="margin: 0; font-size: 1.2rem;">
            <?= htmlspecialchars($userData['user']['nom']) ?> <?= htmlspecialchars($userData['user']['prenom']) ?>
          </h3>
          <p style="margin: 0.25rem 0 0 0; opacity: 0.9;">
            Email: <?= htmlspecialchars($userData['user']['email']) ?> | 
            ID: <?= htmlspecialchars($userData['user']['id']) ?>
          </p>
        </div>
        <div style="background: white; color: #007bff; padding: 0.5rem 1rem; border-radius: 4px; font-weight: bold;">
          <?= count($userData['histories']) ?> action(s)
        </div>
      </div>

      <!-- Tableau des historiques de cet utilisateur -->
      <div style="padding: 1rem; overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin: 0; background: white;">
          <thead>
            <tr style="background: #007bff; color: white;">
              <th style="padding: 14px; text-align: left; border: 1px solid #0056b3; font-weight: 700; font-size: 0.95rem; font-family: 'Arial', sans-serif;">ID Historique</th>
              <th style="padding: 14px; text-align: left; border: 1px solid #0056b3; font-weight: 700; font-size: 0.95rem; font-family: 'Arial', sans-serif;">Type d'action</th>
              <th style="padding: 14px; text-align: left; border: 1px solid #0056b3; font-weight: 700; font-size: 0.95rem; font-family: 'Arial', sans-serif;">Description</th>
              <th style="padding: 14px; text-align: left; border: 1px solid #0056b3; font-weight: 700; font-size: 0.95rem; font-family: 'Arial', sans-serif;">Données actions</th>
              <th style="padding: 14px; text-align: left; border: 1px solid #0056b3; font-weight: 700; font-size: 0.95rem; font-family: 'Arial', sans-serif;">Date action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($userData['histories'])): ?>
              <tr>
                <td colspan="5" style="text-align: center; color: #6c757d; padding: 2rem; border: 1px solid #dee2e6;">
                  Aucun historique pour cet utilisateur
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($userData['histories'] as $h): ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                  <td style="padding: 12px; border: 1px solid #dee2e6;"><?= htmlspecialchars($h['id_historique']) ?></td>
                  <td style="padding: 12px; border: 1px solid #dee2e6;"><strong><?= htmlspecialchars($h['type_action']) ?></strong></td>
                  <td style="padding: 12px; border: 1px solid #dee2e6;"><?= htmlspecialchars($h['description'] ?? '-') ?></td>
                  <td style="padding: 12px; border: 1px solid #dee2e6;">
                    <?php if (!empty($h['donnees_action'])): ?>
                      <code style="background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.85rem;">
                        <?= htmlspecialchars(substr($h['donnees_action'], 0, 50)) ?><?= strlen($h['donnees_action']) > 50 ? '...' : '' ?>
                      </code>
                    <?php else: ?>
                      -
                    <?php endif; ?>
                  </td>
                  <td style="padding: 12px; border: 1px solid #dee2e6;"><?= date('d/m/Y à H:i', strtotime($h['date_action'] ?? 'now')) ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
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
