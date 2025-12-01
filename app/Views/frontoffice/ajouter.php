<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter un Jeu – NextGen Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="back.css">
</head>
<body class="admin-layout">

<aside class="admin-sidebar">
  <h2>NextGen Admin</h2>
  <nav class="sidebar-menu">
    <a href="admin.html" class="item active">Gestion des Jeux</a>
  </nav>
  <div class="sidebar-actions">
    <a href="catalog.php" class="site">Voir le Catalogue</a>
    <a href="index.php" class="site">Voir le Site</a>
  </div>
</aside>

<main class="admin-main">
  <h1 class="page-title">Ajouter un Jeu</h1>

  <form class="admin-form">
    <div class="form-group">
      <label>Titre <span style="color:#dc3545;">*</span></label>
      <input type="text" placeholder="Ex: Cyber Rider 2077">
    </div>

    <div class="form-group">
      <label>Prix (TND) <span style="color:#dc3545;">*</span></label>
      <input type="number" step="0.01" placeholder="89.99">
    </div>

    <div class="form-group">
      <label>Description <span style="color:#dc3545;">*</span></label>
      <textarea rows="5" placeholder="Description du jeu..."></textarea>
    </div>

    <div class="form-group">
      <label>Image <span style="color:#dc3545;">*</span></label>
      <input type="file" accept="image/*">
    </div>

    <div class="form-group">
      <label>Vidéo hover (optionnel)</label>
      <input type="file" accept="video/mp4">
      <small>La vidéo sera jouée au survol dans le catalogue</small>
    </div>

    <div class="form-group">
      <label>Catégorie <span style="color:#dc3545;">*</span></label>
      <select>
        <option value="">Choisir une catégorie</option>
        <option>Action</option>
        <option>Aventure</option>
        <option>RPG</option>
        <option>Stratégie</option>
        <option>Sport</option>
      </select>
    </div>

    <div class="form-actions">
      <button type="button" class="btn-submit" onclick="alert('Jeu ajouté avec succès !')">Sauvegarder</button>
      <a href="admin.html" class="btn-cancel">Annuler</a>
    </div>
  </form>
</main>

</body>
</html>