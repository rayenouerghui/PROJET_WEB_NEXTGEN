<?php
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    header('Location: connexion.php');
    exit;
}

require_once '../../controller/userController.php';
$controller = new userController();

$error = '';
$success = '';

$uploadDir = dirname(__DIR__, 2) . '/resources';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $password = $_POST['password'] ?? '';

    if (!empty($password) && strlen($password) < 8) {
        $error = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        $updatedUser = new User(
            $nom,
            $prenom,
            $email,
            $telephone,
            !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : '',
            $_SESSION['user']['role'],
            $_SESSION['user']['id']
        );

        // === GESTION PHOTO DE PROFIL === 
      if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
          $allowed = ['jpg', 'jpeg', 'png', 'webp'];
          $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

          if (!in_array($ext, $allowed)) {
              $error = "Format d'image non autorisé (JPG, PNG, WebP uniquement).";
          } elseif ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
              $error = "L'image ne doit pas dépasser 5 Mo.";
          } else {
              // Chemin absolu du dossier resources
              $uploadDir = dirname(__DIR__, 2) . '/resources/';
              $newName = 'user_' . $_SESSION['user']['id'] . '_' . time() . '.' . $ext;
              $destination = $uploadDir . $newName;

              if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                  // 1. Supprimer l'ancienne photo (sauf default.jpg)
                  if (!empty($_SESSION['user']['photo_profil']) && 
                      $_SESSION['user']['photo_profil'] !== 'default.jpg') {
                      $oldPath = $uploadDir . $_SESSION['user']['photo_profil'];
                      if (file_exists($oldPath)) {
                          unlink($oldPath);
                      }
                  }

                  // 2. Mettre à jour la base de données
                  $sql = "UPDATE users SET photo_profil = :photo WHERE id = :id";
                  $stmt = Config::getConnexion()->prepare($sql);
                  $stmt->execute([
                      ':photo' => $newName,
                      ':id'    => $_SESSION['user']['id']
                  ]);

                  // 3. CRUCIAL : Mettre à jour la session immédiatement
                  $_SESSION['user']['photo_profil'] = $newName;

                  // Optionnel : message de succès spécifique
                  $success = "Profil et photo mis à jour avec succès !";
              } else {
                  $error = "Erreur lors de l'upload de l'image.";
              }
          }
      }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gérer mon profil – NextGen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    a { text-decoration: none !important; }
    .error-text { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; display: block; }
    .current-avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 4px solid #4f46e5;
      box-shadow: 0 8px 25px rgba(79,70,229,0.25);
    }
    .avatar-preview { margin-top: 1rem; text-align: center; }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">

  <?php if ($success): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body fw-bold">
          <i class="bi bi-check-circle-fill me-2"></i><?= $success ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($error): ?>
  <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;">
    <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert">
      <div class="d-flex">
        <div class="toast-body fw-bold">
          <i class="bi bi-exclamation-triangle me-2"></i><?= $error ?>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <aside class="admin-sidebar">
    <h2>NextGen</h2>
    <nav class="sidebar-menu">
      <a href="../frontoffice/index.php" class="item">Accueil</a>
      <a href="../frontoffice/catalogue.php" class="item">Catalogue</a>
      <a href="../frontoffice/apropos.html" class="item">À Propos</a>
    </nav>
    <div class="sidebar-actions">
      <a href="profil.php" class="site active"><i class="bi bi-person-circle"></i> Mon Profil</a>
      <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>
    </div>
  </aside>

  <main class="admin-main">
    <h1 class="page-title">Gérer mon profil</h1>

    <div class="form-container">
      <form id="profileForm" method="POST" enctype="multipart/form-data" novalidate>

        <div class="form-group text-center mb-5">
          <label>Photo de profil actuelle</label><br>
        <?php 
          $photoPath = !empty($_SESSION['user']['photo_profil']) 
              ? '../../resources/' . $_SESSION['user']['photo_profil'] 
              : '../../resources/default.jpg';
        ?>
          <img src="<?= $photoPath ?>" alt="Photo de profil" class="current-avatar" id="currentAvatar"
               alt="Photo de profil" class="current-avatar" id="currentAvatar">
          <div class="avatar-preview" id="previewContainer" style="display:none;">
            <p><strong>Aperçu :</strong></p>
            <img id="previewImg" style="max-width:200px; border-radius:50%; border:4px solid #4f46e5;">
          </div>
        </div>

        <div class="form-group">
          <label for="photo">Changer la photo de profil <small>(JPG, PNG, WebP – max 5 Mo)</small></label>
          <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
          <small class="error-text" id="photoError"></small>
        </div>

        <hr class="my-5">

        <div class="form-group">
          <label for="prenom">Prénom</label>
          <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($_SESSION['user']['prenom'] ?? '') ?>" required>
          <small class="error-text" id="prenomError"></small>
        </div>

        <div class="form-group">
          <label for="nom">Nom</label>
          <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($_SESSION['user']['nom'] ?? '') ?>" required>
          <small class="error-text" id="nomError"></small>
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['user']['email'] ?? '') ?>" required>
          <small class="error-text" id="emailError"></small>
        </div>

        <div class="form-group">
          <label for="telephone">Téléphone</label>
          <input type="text" id="telephone" name="telephone" maxlength="8" 
                 value="<?= htmlspecialchars($_SESSION['user']['telephone'] ?? '') ?>" required>
          <small class="error-text" id="telephoneError"></small>
        </div>

        <div class="form-group">
          <label for="password">Nouveau mot de passe <small>(laisser vide pour ne pas changer)</small></label>
          <input type="password" id="password" name="password">
          <small class="error-text" id="passwordError"></small>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Sauvegarder les modifications</button>
          <a href="../frontoffice/index.php" class="btn-cancel">Annuler</a>
        </div>
      </form>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('photo').addEventListener('change', function(e) {
      const file = e.target.files[0];
      const previewContainer = document.getElementById('previewContainer');
      const previewImg = document.getElementById('previewImg');

      if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
          previewImg.src = ev.target.result;
          previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(file);
      } else {
        previewContainer.style.display = 'none';
      }
    });

    document.getElementById('profileForm').addEventListener('submit', function(e) {
      let hasError = false;
      document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

      const prenom = document.getElementById('prenom').value.trim();
      if (!prenom) { document.getElementById('prenomError').textContent = 'Le prénom est obligatoire'; hasError = true; }

      const nom = document.getElementById('nom').value.trim();
      if (!nom) { document.getElementById('nomError').textContent = 'Le nom est obligatoire'; hasError = true; }

      const email = document.getElementById('email').value.trim();
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email || !emailRegex.test(email)) { document.getElementById('emailError').textContent = 'Email invalide'; hasError = true; }

      const tel = document.getElementById('telephone').value.trim();
      if (!/^\d{8}$/.test(tel)) { document.getElementById('telephoneError').textContent = 'Doit contenir exactement 8 chiffres'; hasError = true; }

      const pwd = document.getElementById('password').value;
      if (pwd && pwd.length < 8) { document.getElementById('passwordError').textContent = 'Minimum 8 caractères'; hasError = true; }

      const photo = document.getElementById('photo').files[0];
      if (photo && photo.size > 5 * 1024 * 1024) {
        document.getElementById('photoError').textContent = 'L\'image ne doit pas dépasser 5 Mo';
        hasError = true;
      }

      if (hasError) e.preventDefault();
    });

    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.toast').forEach(toast => {
        new bootstrap.Toast(toast, { delay: 4000 }).show();
      });
    });
  </script>
</body>
</html>