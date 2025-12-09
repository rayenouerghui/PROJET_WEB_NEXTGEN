<?php
// views/backoffice/ajouter_jeux.php

require_once '../../controller/jeuController.php';
require_once '../../controller/CategorieController.php';

$jeuController = new JeuController();
$categorieController = new CategorieController();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $prix = $_POST['prix'] ?? '';
    $id_categorie = (int)($_POST['id_categorie'] ?? 0);
    $src_img = '';
    $description = trim($_POST['description'] ?? '');

    // Image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed) && $file['size'] <= 5 * 1024 * 1024) {
            $upload_dir = '../../resources/';
            $filename = 'jeu_' . time() . '_' . uniqid() . '.' . $ext;
            $filepath = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                $src_img = $filename;
            } else {
                $error = "Erreur lors de l'upload de l'image.";
            }
        } else {
            $error = "Image invalide ou trop lourde (max 5 Mo).";
        }
    } else {
        $error = "L'image est obligatoire.";
    }

    if (!$error && $titre && is_numeric($prix) && $prix >= 0 && $id_categorie && $src_img) {
        $jeu = new Jeu($titre, (float)$prix, $src_img, $id_categorie, null, $description);
        $jeuController->ajouterJeu($jeu);
        $_SESSION['success_message'] = "Le jeu « $titre » a été ajouté avec succès !";
        header('Location: admin_jeux.php');
        exit;
    } else {
        $error = $error ?: "Tous les champs sont obligatoires et doivent être valides.";
    }
}

$categories = $categorieController->listeCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un Jeu – NextGen Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../frontoffice/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <style>
    .error-text { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
    a { text-decoration: none !important; }
  </style>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">

  <aside class="admin-sidebar">
    <h2>NextGen Admin</h2>
    <nav class="sidebar-menu">
      <a href="admin_jeux.php" class="item">Gestion des Jeux</a>
      <a href="ajouter_jeux.php" class="item active">Ajouter un Jeu</a>
    </nav>
    <div class="sidebar-actions">
      <a href="../frontoffice/index.php" class="site">Voir le Site</a>
      <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>
    </div>
  </aside>

  <main class="admin-main">
    <h1 class="page-title">Ajouter un Jeu</h1>

    <?php if ($error): ?>
      <div class="alert alert-danger mt-4">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="form-container">
      <form id="addGameForm" action="" method="POST" enctype="multipart/form-data" novalidate>
        
        <div class="form-group">
          <label for="titre">Titre du jeu</label>
          <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
          <small class="error-text" id="titreError"></small>
        </div>

        <div class="form-group">
          <label for="prix">Prix (TND)</label>
          <input type="text" id="prix" name="prix" value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>" placeholder="29.99">
          <small class="error-text" id="prixError"></small>
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="5" class="form-control" placeholder="Entrez une description du jeu..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
          <small class="text-muted">Optionnel mais recommandé</small>
        </div>

        <div class="form-group">
          <label for="image">Image du jeu <span style="color:#dc3545;">*</span></label>
          <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
          <small class="error-text" id="imageError"></small>
        </div>

        <div class="form-group">
          <label for="id_categorie">Catégorie</label>
          <select id="id_categorie" name="id_categorie" class="form-select">
            <option value="">-- Choisir une catégorie --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat->getIdCategorie() ?>" <?= ($_POST['id_categorie'] ?? '') == $cat->getIdCategorie() ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat->getNomCategorie()) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <small class="error-text" id="categorieError"></small>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Ajouter le jeu</button>
          <a href="admin_jeux.php" class="btn-cancel">Annuler</a>
        </div>
      </form>
    </div>
  </main>

  <script>
    document.getElementById('addGameForm').addEventListener('submit', function(e) {
      let hasError = false;
      document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

      // Titre
      const titre = document.getElementById('titre').value.trim();
      if (!titre) {
        document.getElementById('titreError').textContent = 'Le titre est obligatoire';
        hasError = true;
      }

      // Prix
      const prix = document.getElementById('prix').value.trim();
      if (!prix || isNaN(prix) || parseFloat(prix) < 0) {
        document.getElementById('prixError').textContent = 'Prix invalide (doit être un nombre positif)';
        hasError = true;
      }

      // Image
      const image = document.getElementById('image').files[0];
      if (!image) {
        document.getElementById('imageError').textContent = 'L\'image est obligatoire';
        hasError = true;
      }

      // Catégorie
      const categorie = document.getElementById('id_categorie').value;
      if (!categorie) {
        document.getElementById('categorieError').textContent = 'Veuillez choisir une catégorie';
        hasError = true;
      }

      if (hasError) e.preventDefault();
    });
  </script>
</body>
</html>