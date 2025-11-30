<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>Créer un historique</h2>

<?php if(!empty($_SESSION['errors'])): ?>
  <div class="errors">
    <?php foreach($_SESSION['errors'] as $error): ?>
      <div><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <?php unset($_SESSION['errors']); ?>
  </div>
<?php endif; ?>

<form action="/user_nextgen/history/store" method="POST" id="historyForm">
  <label>
    Utilisateur
    <select name="user_id" id="hist_user" required>
      <option value="">-- Sélectionner un utilisateur --</option>
      <?php foreach ($users as $u): ?>
        <option value="<?= $u['id_user'] ?>" <?= ($_SESSION['old']['user_id'] ?? '') == $u['id_user'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($u['nom']) ?> <?= htmlspecialchars($u['prenom']) ?> (<?= htmlspecialchars($u['email']) ?>)
        </option>
      <?php endforeach; ?>
    </select>
    <span class="error-message" id="user_error"></span>
  </label>

  <label>
    Action
    <input type="text" name="action" id="hist_action" value="<?= htmlspecialchars($_SESSION['old']['action'] ?? '') ?>">
    <span class="error-message" id="action_error"></span>
  </label>
  
  <label>
    Description
    <textarea name="note" id="hist_note" rows="4"><?= htmlspecialchars($_SESSION['old']['note'] ?? '') ?></textarea>
  </label>
  
  <button type="submit">Créer</button>
  <a href="/user_nextgen/history" style="display: inline-block; margin-top: 1rem;">Retour</a>
</form>

<script src="/user_nextgen/assets/js/history-validation.js"></script>

<?php 
unset($_SESSION['old']);
require_once __DIR__ . '/../layouts/footer.php'; 
?>
