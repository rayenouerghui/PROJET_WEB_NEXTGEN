<?php
// views/backoffice/ajouter_users.php

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

require_once '../../controller/userController.php';
$userController = new userController();

$error = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom       = trim($_POST['nom'] ?? '');
    $prenom    = trim($_POST['prenom'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $role      = $_POST['role'] ?? 'user';

    if ($nom && $prenom && $email && $telephone && $password && in_array($role, ['user', 'admin'])) {
        $user = new User($nom, $prenom, $email, $telephone, $password, $role);
        if ($userController->addUser($user)) {
            $_SESSION['success_message'] = "L'utilisateur « $prenom $nom » a été ajouté avec succès !";
            header('Location: admin_users.php');
            exit;
        } else {
            $error = "Erreur : cet email est déjà utilisé.";
        }
    } else {
        $error = "Tous les champs sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un utilisateur – NextGen Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../frontoffice/styles.css">

  <!-- Bootstrap pour le toast -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    /* Petit style pour les messages d'erreur sous les champs */
    .error-text {
      color: #dc3545;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
    /* Supprime les soulignés sur tous les liens */
    a { text-decoration: none !important; }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">

  <!-- Toast de succès (même que dans admin_users.php) -->
  <?php if (isset($_SESSION['success_message'])): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body fw-bold">
          <i class="bi bi-check-circle-fill me-2"></i>
          <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
  <?php unset($_SESSION['success_message']); endif; ?>

  <aside class="admin-sidebar">
    <h2>NextGen Admin</h2>
    <nav class="sidebar-menu">
      <a href="admin_jeux.php" class="item">Gestion des Jeux</a>
      <a href="admin_users.php" class="item active">Gestion des Utilisateurs</a>
    </nav>
    <div class="sidebar-actions">
      <a href="../frontoffice/catalogue.php" class="site">Voir le Catalogue</a>
      <a href="../frontoffice/index.php" class="site">Voir le Site</a>
      <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>
    </div>
  </aside>

  <main class="admin-main">
    <h1 class="page-title">Ajouter un utilisateur</h1>

    <?php if ($error): ?>
      <div class="alert alert-danger mt-4">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="form-container">
      <form id="addUserForm" action="" method="POST" novalidate>
        
        <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
          <small class="error-text" id="nomError"></small>
        </div>

        <div class="form-group">
          <label for="prenom">Prénom</label>
          <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
          <small class="error-text" id="prenomError"></small>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
          <small class="error-text" id="emailError"></small>
        </div>

        <div class="form-group">
          <label for="telephone">Téléphone</label>
          <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($_POST['telephone'] ?? '') ?>" maxlength="8" required>
          <small class="error-text" id="telephoneError"></small>
        </div>

        <div class="form-group">
          <label for="password">Mot de passe</label>
          <input type="password" id="password" name="password" required>
          <small class="error-text" id="passwordError"></small>
        </div>

        <div class="form-group">
          <label for="role">Rôle</label>
          <select id="role" name="role" class="form-select">
            <option value="user" <?= ($_POST['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>Utilisateur normal</option>
            <option value="admin" <?= ($_POST['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
          </select>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Ajouter l'utilisateur</button>
          <a href="admin_users.php" class="btn-cancel">Annuler</a>
        </div>
      </form>
    </div>
  </main>

  <!-- Bootstrap JS pour le toast -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Affichage du toast si présent
    <?php if (isset($_SESSION['success_message'])): ?>
      const toastEl = document.getElementById('successToast');
      const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
      toast.show();
    <?php endif; ?>

    // Validation côté client (même que avant)
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
      let hasError = false;
      document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

      const allowedRegex = /^[A-Za-zÀ-ÖØ-öø-ÿ0-9\s'-]+$/;
      const hasLetter = /[A-Za-zÀ-ÖØ-öø-ÿ]/;

      // Nom
      const nom = document.getElementById('nom').value.trim();
      if (!nom) {
        document.getElementById('nomError').textContent = 'Le nom est obligatoire';
        hasError = true;
      } else if (!allowedRegex.test(nom) || !hasLetter.test(nom)) {
        document.getElementById('nomError').textContent = 'Nom invalide (lettres obligatoires, chiffres/espaces/-/\')';
        hasError = true;
      }

      // Prénom
      const prenom = document.getElementById('prenom').value.trim();
      if (!prenom) {
        document.getElementById('prenomError').textContent = 'Le prénom est obligatoire';
        hasError = true;
      } else if (!allowedRegex.test(prenom) || !hasLetter.test(prenom)) {
        document.getElementById('prenomError').textContent = 'Prénom invalide';
        hasError = true;
      }

      // Email
      const email = document.getElementById('email').value.trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email || !emailRegex.test(email)) {
        document.getElementById('emailError').textContent = 'Email invalide';
        hasError = true;
      }

      // Téléphone tunisien
      const tel = document.getElementById('telephone').value.trim();
      if (!/^[2459]\d{7}$/.test(tel)) {
        document.getElementById('telephoneError').textContent = 'Doit commencer par 2, 4, 5 ou 9 et faire 8 chiffres';
        hasError = true;
      }

      // Mot de passe
      const pwd = document.getElementById('password').value;
      if (!pwd || pwd.length < 4) {
        document.getElementById('passwordError').textContent = 'Minimum 4 caractères';
        hasError = true;
      }

      if (hasError) e.preventDefault();
    });
  </script>
</body>
</html>