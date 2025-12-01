<?php
require_once __DIR__ . '/../../Controllers/CategoryController.php';

$categoryController = new CategoryController();
$categories = $categoryController->getAllCategories();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
    <title>Toutes les Catégories</title>
</head>
<body>
<div class="wrapper">
    <?php include('partials/topbar.php'); ?>
    <?php include('partials/sidenav.php'); ?>

    <div class="page-content">
        <div class="page-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Toutes les Catégories</h3>
                <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="ri-arrow-left-line me-1"></i> Retour au tableau de bord</a>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered table-nowrap mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Slug</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($categories)): ?>
                                <tr><td colspan="4" class="text-center py-4">Aucune catégorie trouvée</td></tr>
                            <?php else: ?>
                                <?php foreach ($categories as $c): ?>
                                    <?php
                                    $id = htmlspecialchars((string)($c['id_categorie'] ?? ''));
                                    $nom = htmlspecialchars($c['nom'] ?? '');
                                    $desc = htmlspecialchars($c['description'] ?? '');
                                    $slug = htmlspecialchars($c['slug'] ?? '');
                                    ?>
                                    <tr>
                                        <td><?= $id ?></td>
                                        <td><?= $nom ?></td>
                                        <td class="text-muted" style="max-width:520px;" title="<?= $desc ?>"><?= $desc !== '' ? $desc : '—' ?></td>
                                        <td><code><?= $slug !== '' ? $slug : '—' ?></code></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <?php include('partials/footer.php'); ?>
    </div>
</div>

<?php include('partials/footer-scripts.php'); ?>
</body>
</html>
