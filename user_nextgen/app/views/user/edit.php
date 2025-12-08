<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Modifier profil</h2>

<?php if(!empty($_SESSION['errors'])): ?>
  <div class="errors">
    <?php foreach($_SESSION['errors'] as $error): ?>
      <div><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <?php unset($_SESSION['errors']); ?>
  </div>
<?php endif; ?>

<form action="/user_nextgen/profile/update" method="POST" id="profileForm">
  <label>
    Nom
    <input type="text" name="nom" id="edit_nom" value="<?= htmlspecialchars($user['nom']) ?>">
    <span class="error-message" id="nom_error"></span>
  </label>

  <label>
    Prénom
    <input type="text" name="prenom" id="edit_prenom" value="<?= htmlspecialchars($user['prenom']) ?>">
    <span class="error-message" id="prenom_error"></span>
  </label>

  <label>
    Email
    <input type="text" name="email" id="edit_email" value="<?= htmlspecialchars($user['email']) ?>">
    <span class="error-message" id="email_error"></span>
  </label>

  <button type="submit">Enregistrer</button>
  <a href="/user_nextgen/profile" style="display: inline-block; margin-top: 1rem;">Retour au profil</a>
</form>

<script src="/user_nextgen/assets/js/profile-validation.js"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
