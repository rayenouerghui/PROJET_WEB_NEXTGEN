<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: ../frontoffice/connexion.php');
    exit;
}

require_once '../../controller/CategorieController.php';
$controller = new CategorieController();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $desc = trim($_POST['description'] ?? '');

    if ($nom !== '') {
        $categorie = new Categorie($nom, $desc ?: null);
        $controller->ajouterCategorie($categorie);
        $_SESSION['success_message'] = "Catégorie ajoutée avec succès !";
        header('Location: admin_categories.php');
        exit;
    } else {
        $error = "Le nom est obligatoire.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Ajouter Catégorie</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../frontoffice/styles.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">
  <aside class="admin-sidebar">
    <h2>NextGen Admin</h2>
    <nav class="sidebar-menu">
      <a href="admin_categories.php" class="item active">Gestion des Catégories</a>
    </nav>
  </aside>

  <main class="admin-main">
    <h1 class="page-title">Ajouter une Catégorie</h1>

    <?php if ($error): ?>
      <div style="background:#f8d7da;color:#721c24;padding:1rem;border-radius:.375rem;margin-bottom:1.5rem;">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="admin-form" style="max-width:600px;">
      <div class="form-group">
        <label>Nom <span style="color:#dc3545;">*</span></label>
        <input type="text" name="nom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Description</label>
        <textarea name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn-submit">Ajouter</button>
        <a href="admin_categories.php" class="btn-cancel">Annuler</a>
      </div>
    </form>
  </main>
</body>
</html>