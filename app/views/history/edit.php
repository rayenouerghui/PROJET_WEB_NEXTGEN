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
    Action
    <input type="text" name="action" id="hist_action" value="<?= htmlspecialchars($history['type_action']) ?>">
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
