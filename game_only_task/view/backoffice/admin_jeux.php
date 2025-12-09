<?php
// view/backoffice/admin_jeux.php

require_once '../../controller/jeuController.php';
require_once '../../controller/CategorieController.php';

$jeuController = new JeuController();

// Gestion de la suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $jeuController->supprimerJeu($id);
        header('Location: admin_jeux.php?success=delete');
        exit;
    } catch (Exception $e) {
        header('Location: admin_jeux.php?error=delete');
        exit;
    }
}

$jeux = $jeuController->afficherJeux();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Admin – NextGen</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link rel="stylesheet" href="../backoffice/styles.css">
  <script src="../frontoffice/script.js"></script>
  <link rel="stylesheet" href="../assets/green-theme.css">
</head>
<body class="admin-layout">

  <aside class="admin-sidebar">
    <h2>NextGen Admin</h2>
    <nav class="sidebar-menu">
      <a href="admin_jeux.php" class="item active">Gestion des Jeux</a>
      <a href="admin_users.php" class="item">Gestion des Utilisateurs</a>
      <a href="admin_categories.php" class="item">Gestion des Catégories</a>
      <a href="admin_livraisons.php" class="item">Gestion des Livraisons</a>
      <a href="admin_reclamations.php" class="item">Gestion des Réclamations</a>
    </nav>
    <div class="sidebar-actions">
      <!-- NEW BUTTON: Voir le Catalogue -->
      <a href="../frontoffice/catalogue.php" class="site" style=" margin-bottom: 0.75rem;">
        Voir le Catalogue
      </a>
      <a href="../frontoffice/index.php" class="site">
        Voir le Site
      </a>
      <button class="logout" onclick="location.href='logout.php'">Déconnexion</button>
    </div>
  </aside>

  <main class="admin-main">

    <!-- Header avec titre + bouton Ajouter -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
      <h1 class="page-title">Gestion des Jeux</h1>
      <a href="ajouter_jeux.php" class="btn-submit" style="font-size: 1rem; padding: 0.75rem 1.5rem;">
        + Ajouter un Jeu
      </a>
    </div>

    <!-- Messages de succès / erreur -->
    <?php if (isset($_GET['success'])): ?>
      <div style="background:#d4edda; color:#155724; padding:1rem; border-radius:0.375rem; margin-bottom:1.5rem;">
        <?php
          $successMessages = [
              'add'    => 'Jeu ajouté avec succès !',
              'edit'   => 'Jeu modifié avec succès !',
              'delete' => 'Jeu supprimé avec succès !'
          ];
          echo isset($successMessages[$_GET['success']]) ? $successMessages[$_GET['success']] : 'Opération réussie !';
        ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
      <div style="background:#f8d7da; color:#721c24; padding:1rem; border-radius:0.375rem; margin-bottom:1.5rem;">
        Une erreur est survenue.
      </div>
    <?php endif; ?>

    <div class="table-container">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Prix</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($jeux)): ?>
            <tr>
              <td colspan="6" style="text-align:center; padding:2rem; color:#666;">
                Aucun jeu trouvé.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($jeux as $jeu): ?>
              <tr>
                <td><?= $jeu->getIdJeu() ?></td>
                <td>
                  <?php if ($jeu->getSrcImg()): ?>
                    <img src="../../resources/<?= $jeu->getSrcImg() ?>" class="img-thumb" alt="<?= htmlspecialchars($jeu->getTitre()) ?>">
                  <?php else: ?>
                    <div class="img-thumb" style="background:#e5e7eb;"></div>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($jeu->getTitre()) ?></td>
                <td><?= htmlspecialchars($jeu->nom_categorie ?? '—') ?></td>
                <td><?= number_format($jeu->getPrix(), 2) ?> TND</td>
                <td class="actions">
                  <a href="modifier_jeux.php?id=<?= $jeu->getIdJeu() ?>" class="btn-edit">Modifier</a>
                  <a href="?delete=<?= $jeu->getIdJeu() ?>" 
                     class="btn-delete"
                     onclick="return confirm('Supprimer « <?= addslashes(htmlspecialchars($jeu->getTitre())) ?> » ?')">
                     Supprimer
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</body>
</html>