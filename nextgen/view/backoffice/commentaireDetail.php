<?php
require_once __DIR__ . '/../../Controllers/CommentaireController.php';
require_once __DIR__ . '/../../Controllers/BlogController.php';

$commentController = new CommentController();
$blogController = new BlogController();

// Récupérer toutes les données nécessaires
try {
    $comments = $commentController->getAllComments();
} catch (Exception $e) {
    error_log('Failed to load comments: ' . $e->getMessage());
    $comments = [];
}

// Charger les articles pour mapper les titres
$articles = $blogController->index();
$articlesById = [];
foreach ($articles as $a) {
    if (isset($a['id_article'])) {
        $articlesById[$a['id_article']] = $a;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
    <title>Tous les Commentaires</title>
    <style>
        .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body>
<div class="wrapper">
    <?php include('partials/topbar.php'); ?>
    <?php include('partials/sidenav.php'); ?>

    <div class="page-content">
        <div class="page-container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Tous les Commentaires</h3>
                <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="ri-arrow-left-line me-1"></i> Retour au tableau de bord</a>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered table-nowrap mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Auteur</th>
                                <th>Article</th>
                                <th>Commentaire</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (empty($comments)): ?>
                                <tr><td colspan="5" class="text-center py-4">Aucun commentaire trouvé</td></tr>
                            <?php else: ?>
                                <?php foreach ($comments as $c): ?>
                                    <?php
                                    $id = htmlspecialchars((string)($c['id_commentaire'] ?? ''));
                                    $nom = htmlspecialchars($c['nom_visiteur'] ?? 'Anonyme');
                                    $contenu = htmlspecialchars($c['contenu'] ?? '');
                                    $date = htmlspecialchars($c['date_commentaire'] ?? '');
                                    $articleId = (int)($c['id_article'] ?? 0);
                                    $articleTitle = isset($articlesById[$articleId]['titre']) ? htmlspecialchars($articlesById[$articleId]['titre']) : ('Article #'.$articleId);
                                    $excerpt = (strlen($contenu) > 140) ? (substr($contenu, 0, 140) . '...') : $contenu;
                                    ?>
                                    <tr>
                                        <td><?= $id ?></td>
                                        <td><?= $nom ?></td>
                                        <td class="text-truncate" style="max-width:260px" title="<?= $articleTitle ?>"><?= $articleTitle ?></td>
                                        <td class="text-muted text-truncate-2" style="max-width:520px" title="<?= $contenu ?>"><?= $excerpt ?></td>
                                        <td><span class="text-muted fs-12"><?= $date ?></span></td>
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
