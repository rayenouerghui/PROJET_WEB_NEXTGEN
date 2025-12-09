<?php
// views/backoffice/modifier_users.php

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

require_once '../../config/config.php';
require_once '../../controller/userController.php';

$controller = new userController();

if (!isset($_GET['id'])) {
    header('Location: admin_users.php');
    exit;
}

$user = $controller->getUserById((int)$_GET['id']);
if (!$user) die('Utilisateur non trouvé');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->setPrenom(trim($_POST['prenom']));
    $user->setNom(trim($_POST['nom']));
    $user->setEmail(trim($_POST['email']));
    $user->setTelephone(trim($_POST['telephone']));
    $user->setRole($_POST['role']);

    // Hash password only if filled
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 8) {
            $error = "Le mot de passe doit contenir au moins 8 caractères.";
        } else {
            $user->setMdp(password_hash($_POST['password'], PASSWORD_DEFAULT));
        }
    }

    if (!$error) {
        $controller->updateUser($user);
        $_SESSION['success_message'] = "L'utilisateur « {$user->getPrenom()} {$user->getNom()} » a été modifié avec succès !";
        header('Location: admin_users.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier l'utilisateur – NextGen Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel="stylesheet" href="../frontoffice/styles.css">



  <style>
    a { text-decoration: none !important; }
    .error-text { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">

  <!-- Success Toast -->
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
    <h1 class="page-title">Modifier l'utilisateur #<?= $user->getId() ?></h1>

    <?php if ($error): ?>
      <div class="alert alert-danger mt-4">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="form-container">
      <form id="modifyForm" method="POST" novalidate>

        <div class="form-group">
          <label for="prenom">Prénom</label>
          <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($user->getPrenom()) ?>" required>
          <small class="error-text" id="prenomError"></small>
        </div>

        <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($user->getNom()) ?>" required>
          <small class="error-text" id="nomError"></small>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
          <small class="error-text" id="emailError"></small>
        </div>

        <div class="form-group">
          <label for="telephone">Téléphone</label>
          <input type="text" id="telephone" name="telephone" maxlength="8"
                 value="<?= htmlspecialchars($user->getTelephone()) ?>" required>
          <small class="error-text" id="telephoneError"></small>
        </div>

        <div class="form-group">
          <label for="password">Nouveau mot de passe <small>(laisser vide pour garder l'ancien)</small></label>
          <input type="password" id="password" name="password">
          <small class="error-text" id="passwordError"></small>
        </div>

        <div class="form-group">
          <label>Rôle actuel</label>
          <div class="mt-2">
            <span class="badge <?= $user->getRole() === 'admin' ? 'bg-danger' : 'bg-primary' ?> fw-bold">
              <?= $user->getRole() === 'admin' ? 'Administrateur' : 'Utilisateur' ?>
            </span>
          </div>
        </div>

        <div class="form-group">
          <label for="role">Changer le rôle</label>
          <select id="role" name="role" class="form-select">
            <option value="user" <?= $user->getRole() === 'user' ? 'selected' : '' ?>>Utilisateur normal</option>
            <option value="admin" <?= $user->getRole() === 'admin' ? 'selected' : '' ?>>Administrateur</option>
          </select>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Sauvegarder les modifications</button>
          <a href="admin_users.php" class="btn-cancel">Annuler</a>
        </div>
      </form>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toast auto-show
    <?php if (isset($_SESSION['success_message'])): ?>
      const toastEl = document.getElementById('successToast');
      const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
      toast.show();
    <?php endif; ?>

    // Client-side validation
    document.getElementById('modifyForm').addEventListener('submit', function(e) {
      let hasError = false;
      document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

      const prenom = document.getElementById('prenom').value.trim();
      if (!prenom) {
        document.getElementById('prenomError').textContent = 'Le prénom est obligatoire';
        hasError = true;
      }

      const nom = document.getElementById('nom').value.trim();
      if (!nom) {
        document.getElementById('nomError').textContent = 'Le nom est obligatoire';
        hasError = true;
      }

      const email = document.getElementById('email').value.trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email || !emailRegex.test(email)) {
        document.getElementById('emailError').textContent = 'Email invalide';
        hasError = true;
      }

      const tel = document.getElementById('telephone').value.trim();
      if (!/^\d{8}$/.test(tel)) {
        document.getElementById('telephoneError').textContent = 'Doit contenir exactement 8 chiffres';
        hasError = true;
      }

      const pwd = document.getElementById('password').value;
      if (pwd !== '' && pwd.length < 8) {
        document.getElementById('passwordError').textContent = 'Minimum 8 caractères';
        hasError = true;
      }

      if (hasError) e.preventDefault();
    });
  </script>
</body>
</html>