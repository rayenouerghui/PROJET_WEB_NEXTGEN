<?php require __DIR__ . '/../layouts/header.php'; ?>

<h2>Éditer l'utilisateur</h2>

<?php if(!empty($_SESSION['errors'])): ?>
  <div class="errors">
    <?php foreach($_SESSION['errors'] as $error): ?>
      <div><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
    <?php unset($_SESSION['errors']); ?>
  </div>
<?php endif; ?>

<form action="/user_nextgen/admin/users/update" method="POST" id="editUserForm">
  <input type="hidden" name="id" value="<?= htmlspecialchars($user['id_user']) ?>">
  
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

  <label>
    Rôle
    <select name="role">
      <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Utilisateur</option>
      <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
    </select>
  </label>

  <label>
    Statut
    <select name="statut">
      <option value="actif" <?= ($user['statut'] ?? 'actif') === 'actif' ? 'selected' : '' ?>>Actif</option>
      <option value="suspendu" <?= ($user['statut'] ?? '') === 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
      <option value="banni" <?= ($user['statut'] ?? '') === 'banni' ? 'selected' : '' ?>>Banni</option>
    </select>
  </label>

  <label>
    Crédit (TND)
    <input type="number" name="credit" step="0.01" value="<?= htmlspecialchars($user['credit'] ?? '0') ?>">
  </label>

  <button type="submit">Enregistrer les modifications</button>
  <a href="/user_nextgen/admin/users/view?id=<?= $user['id_user'] ?>" style="display: inline-block; margin-top: 1rem; padding: 0.75rem 1.5rem; background: #6c757d; color: white; text-decoration: none; border-radius: 4px;">Annuler</a>
</form>

<script>
document.getElementById('editUserForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Reset errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    document.querySelectorAll('input').forEach(el => el.classList.remove('error-field'));
    
    // Nom validation
    const nom = document.getElementById('edit_nom');
    if (nom.value.trim() === '') {
        document.getElementById('nom_error').textContent = 'Nom requis';
        nom.classList.add('error-field');
        isValid = false;
    }
    
    // Email validation
    const email = document.getElementById('edit_email');
    const emailValue = email.value.trim();
    if (emailValue === '') {
        document.getElementById('email_error').textContent = 'Email requis';
        email.classList.add('error-field');
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
        document.getElementById('email_error').textContent = 'Email invalide';
        email.classList.add('error-field');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
