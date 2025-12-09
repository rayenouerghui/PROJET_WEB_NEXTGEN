<?php
// views/backoffice/modifier_jeux.php

require_once '../../controller/jeuController.php';
require_once '../../controller/CategorieController.php';

$jeuController = new JeuController();
$categorieController = new CategorieController();
$error = '';
$jeu = null;

$id_jeu = (int)($_GET['id'] ?? 0);
if (!$id_jeu || !($jeu = $jeuController->getJeu($id_jeu))) {
    header('Location: admin_jeux.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $prix = $_POST['prix'] ?? '';
    $id_categorie = (int)($_POST['id_categorie'] ?? 0);
    $src_img = $jeu->getSrcImg();
    $description = trim($_POST['description'] ?? '');

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($ext, $allowed) && $file['size'] <= 5 * 1024 * 1024) {
            $upload_dir = '../../resources/';
            $filename = 'jeu_' . time() . '_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                if ($src_img && file_exists($upload_dir . $src_img)) unlink($upload_dir . $src_img);
                $src_img = $filename;
            }
        } else {
            $error = "Image invalide.";
        }
    }

    if (!$error && $titre && is_numeric($prix) && $prix >= 0 && $id_categorie) {
        $jeu->setTitre($titre);
        $jeu->setPrix((float)$prix);
        $jeu->setIdCategorie($id_categorie);
        $jeu->setSrcImg($src_img);
        $jeu->setDescription($description);

        $jeuController->modifierJeu($jeu);
        $_SESSION['success_message'] = "Le jeu « $titre » a été modifié avec succès !";
        header('Location: admin_jeux.php');
        exit;
    } else {
        $error = $error ?: "Tous les champs sont obligatoires.";
    }
}

$categories = $categorieController->listeCategories();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier Jeu – NextGen Admin</title>
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
      <a href="admin_jeux.php" class="item active">Gestion des Jeux</a>
      <a href="ajouter_jeux.php" class="item">Ajouter un Jeu</a>
    </nav>
    <div class="sidebar-actions">
      <a href="../frontoffice/index.php" class="site">Voir le Site</a>
      <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>
    </div>
  </aside>

  <main class="admin-main">
    <h1 class="page-title">Modifier le Jeu #<?= $jeu->getIdJeu() ?></h1>

    <?php if ($error): ?>
      <div class="alert alert-danger mt-4">
        <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="form-container">
      <form id="editGameForm" action="" method="POST" enctype="multipart/form-data" novalidate>
        
        <div class="form-group">
          <label for="titre">Titre du jeu</label>
          <input type="text" id="titre" name="titre" value="<?= htmlspecialchars($jeu->getTitre()) ?>">
          <small class="error-text" id="titreError"></small>
        </div>

        <div class="form-group">
          <label for="prix">Prix (TND)</label>
          <input type="text" id="prix" name="prix" value="<?= number_format($jeu->getPrix(), 2, '.', '') ?>">
          <small class="error-text" id="prixError"></small>
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <textarea id="description" name="description" rows="5" class="form-control"><?= htmlspecialchars($jeu->getDescription() ?? '') ?></textarea>
        </div>

        <div class="form-group">
          <label>Image actuelle</label>
          <?php if ($jeu->getSrcImg()): ?>
            <div class="mb-3">
              <img src="../../resources/<?= htmlspecialchars($jeu->getSrcImg()) ?>" style="max-height:150px; border-radius:.5rem; box-shadow:0 4px 10px rgba(0,0,0,.1);">
            </div>
          <?php endif; ?>
          <label for="image">Nouvelle image (optionnel)</label>
          <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
          <small class="text-muted">Laisser vide pour garder l'image actuelle</small>
        </div>

        <div class="form-group">
          <label for="id_categorie">Catégorie</label>
          <select id="id_categorie" name="id_categorie" class="form-select">
            <option value="">-- Choisir --</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat->getIdCategorie() ?>" <?= $jeu->getIdCategorie() == $cat->getIdCategorie() ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat->getNomCategorie()) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <small class="error-text" id="categorieError"></small>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn-submit">Sauvegarder</button>
          <a href="admin_jeux.php" class="btn-cancel">Annuler</a>
        </div>
      </form>
    </div>
  </main>

  <script>
    document.getElementById('editGameForm').addEventListener('submit', function(e) {
      let hasError = false;
      document.querySelectorAll('.error-text').forEach(el => el.textContent = '');

      const titre = document.getElementById('titre').value.trim();
      if (!titre) {
        document.getElementById('titreError').textContent = 'Le titre est obligatoire';
        hasError = true;
      }

      const prix = document.getElementById('prix').value.trim();
      if (!prix || isNaN(prix) || parseFloat(prix) < 0) {
        document.getElementById('prixError').textContent = 'Prix invalide';
        hasError = true;
      }

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