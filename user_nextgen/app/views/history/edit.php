<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>Modifier l'historique</h2>

<?php if(!empty($_SESSION['errors'])): ?>
  <div class="errors">
    <?php foreach($_SESSION['errors'] as $error): ?>
      <div><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <?php unset($_SESSION['errors']); ?>
  </div>
<?php endif; ?>

<form action="/user_nextgen/history/update" method="POST" id="historyForm">
  <input type="hidden" name="id" value="<?= htmlspecialchars($history['id_historique']) ?>">
  
  <label>
    Type d'action
    <select name="action" id="hist_action" required>
      <option value="">-- Sélectionner un type d'action --</option>
      <option value="login" <?= $history['type_action'] === 'login' ? 'selected' : '' ?>>login</option>
      <option value="logout" <?= $history['type_action'] === 'logout' ? 'selected' : '' ?>>logout</option>
      <option value="inscription" <?= $history['type_action'] === 'inscription' ? 'selected' : '' ?>>inscription</option>
      <option value="edit_profile" <?= $history['type_action'] === 'edit_profile' ? 'selected' : '' ?>>edit_profile</option>
      <option value="edit_by_admin" <?= $history['type_action'] === 'edit_by_admin' ? 'selected' : '' ?>>edit_by_admin</option>
      <option value="status_change" <?= $history['type_action'] === 'status_change' ? 'selected' : '' ?>>status_change</option>
      <option value="achat" <?= $history['type_action'] === 'achat' ? 'selected' : '' ?>>achat</option>
      <option value="vente" <?= $history['type_action'] === 'vente' ? 'selected' : '' ?>>vente</option>
      <option value="autre" <?= $history['type_action'] === 'autre' ? 'selected' : '' ?>>autre</option>
    </select>
    <span class="error-message" id="action_error"></span>
  </label>
  
  <label>
    Description
    <textarea name="note" id="hist_note" rows="4"><?= htmlspecialchars($history['description'] ?? '') ?></textarea>
  </label>
  
  <button type="submit">Modifier</button>
  <a href="/user_nextgen/history" style="display: inline-block; margin-top: 1rem;">Retour</a>
</form>

<script src="/user_nextgen/assets/js/history-validation.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
