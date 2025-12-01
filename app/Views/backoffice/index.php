<?php
// Include controllers
require_once __DIR__ . '/../../Controllers/BlogController.php';
require_once __DIR__ . '/../../Controllers/CategoryController.php';
require_once __DIR__ . '/../../Controllers/CommentaireController.php';

// Handle AJAX CREATE/UPDATE requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');

    $blogController = new BlogController();
    $categoryController = new CategoryController();
    $commentController = new CommentController();

    if ($_POST['ajax_action'] === 'create_article') {
        $result = $blogController->create($_POST, $_FILES);
        echo json_encode($result);
        exit;
    }

    if ($_POST['ajax_action'] === 'create_category') {
        $result = $categoryController->create($_POST);
        echo json_encode($result);
        exit;
    }

    // Update article
    if ($_POST['ajax_action'] === 'update_article') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID article invalide']);
            exit;
        }
        $data = [
            'titre' => $_POST['titre'] ?? '',
            'content' => $_POST['content'] ?? '',
            'id_categorie' => $_POST['id_categorie'] ?? '',
            'id_auteur' => $_POST['id_auteur'] ?? 1,
        ];
        $result = $blogController->update($id, $data, $_FILES ?? null);
        echo json_encode($result);
        exit;
    }

    // Update category
    if ($_POST['ajax_action'] === 'update_category') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID catégorie invalide']);
            exit;
        }
        $data = [
            'nom' => $_POST['nom'] ?? '',
            'description' => $_POST['description'] ?? '',
        ];
        $result = $categoryController->update($id, $data);
        echo json_encode($result);
        exit;
    }

    // Update comment
    if ($_POST['ajax_action'] === 'update_comment') {
        $id = (int)($_POST['id'] ?? 0);
        $contenu = $_POST['contenu'] ?? '';
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID commentaire invalide']);
            exit;
        }
        $result = $commentController->update($id, $contenu);
        echo json_encode($result);
        exit;
    }
}

// Handle DELETE requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');

    $blogController = new BlogController();
    $categoryController = new CategoryController();
    $commentController = new CommentController();

    if ($_POST['ajax_action'] === 'delete_article' && isset($_POST['id'])) {
        $result = $blogController->delete($_POST['id']);
        echo json_encode($result);
        exit;
    }

    if ($_POST['ajax_action'] === 'delete_category' && isset($_POST['id'])) {
        $result = $categoryController->delete($_POST['id']);
        echo json_encode($result);
        exit;
    }

    if ($_POST['ajax_action'] === 'delete_comment' && isset($_POST['id'])) {
        $result = $commentController->delete($_POST['id']);
        echo json_encode($result);
        exit;
    }
}

// Initialize controllers
$blogController = new BlogController();
$categoryController = new CategoryController();
$commentController = new CommentController();

// Lightweight GET JSON endpoints
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ajax_get'])) {
    header('Content-Type: application/json');
    if ($_GET['ajax_get'] === 'article') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'ID article invalide']); exit; }
        $data = $blogController->show($id);
        echo json_encode(['success' => empty($data['error']), 'article' => $data]);
        exit;
    }
    if ($_GET['ajax_get'] === 'category') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'ID catégorie invalide']); exit; }
        $data = $categoryController->getCategoryById($id);
        echo json_encode(['success' => $data !== false, 'category' => $data]);
        exit;
    }
    if ($_GET['ajax_get'] === 'comment') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) { echo json_encode(['success' => false, 'message' => 'ID commentaire invalide']); exit; }
        // Pas de méthode publique dédiée; utiliser getAllComments et filtrer (fallback léger)
        $all = $commentController->getAllComments();
        $found = null;
        foreach ($all as $cm) {
            if ((int)($cm['id_commentaire'] ?? 0) === $id) { $found = $cm; break; }
        }
        echo json_encode(['success' => $found !== null, 'comment' => $found]);
        exit;
    }
}

// Get data for dashboard
$articles = $blogController->index();
$categories = $categoryController->getAllCategories();

// Calculate statistics
$totalArticles = count($articles);
$totalCategories = count($categories);

// Get recent articles
$recentArticles = array_slice($articles, 0, 5);

// Get all comments for "Commentaires Récents" section (via controller)
// Map articles by id for quick lookup of titles
$articlesById = [];
foreach ($articles as $a) {
    if (isset($a['id_article'])) {
        $articlesById[$a['id_article']] = $a;
    }
}

// Use CommentController to fetch comments (MVC separation)
try {
    $allComments = $commentController->getAllComments();
} catch (Exception $e) {
    error_log('Failed to load comments for dashboard: ' . $e->getMessage());
    $allComments = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('partials/title-meta.php'); ?>
    <?php include('partials/head-css.php'); ?>
</head>

<body>
<div class="wrapper">
    <?php include('partials/topbar.php'); ?>
    <?php include('partials/sidenav.php'); ?>

    <div class="page-content">
        <div class="page-container">

            <!-- Alert Section -->
            <div class="alert alert-info d-flex align-items-center d-none d-md-flex" role="alert">
                <iconify-icon icon="solar:help-bold-duotone" class="fs-24 me-1"></iconify-icon>
                <div><strong>Tableau de bord Admin - </strong> Gérez efficacement vos articles, catégories et commentaires de blog.</div>
            </div>

            <!-- Statistics Cards -->
            <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1">
                <!-- Total Articles -->
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 justify-content-between">
                                <div>
                                    <h5 class="text-muted fs-13 fw-bold text-uppercase">Total Articles</h5>
                                    <h3 class="my-2 py-1 fw-bold"><?php echo $totalArticles; ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Articles publiés</span>
                                    </p>
                                </div>
                                <div class="avatar-xl flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle text-primary rounded-circle fs-42">
                                            <iconify-icon icon="solar:document-text-bold-duotone"></iconify-icon>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Categories -->
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 justify-content-between">
                                <div>
                                    <h5 class="text-muted fs-13 fw-bold text-uppercase">Catégories</h5>
                                    <h3 class="my-2 py-1 fw-bold"><?php echo $totalCategories; ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Catégories actives</span>
                                    </p>
                                </div>
                                <div class="avatar-xl flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle text-success rounded-circle fs-42">
                                            <iconify-icon icon="solar:folder-bold-duotone"></iconify-icon>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Articles (Last 7 Days) -->
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 justify-content-between">
                                <div>
                                    <h5 class="text-muted fs-13 fw-bold text-uppercase">Articles Récents</h5>
                                    <h3 class="my-2 py-1 fw-bold"><?php echo count($recentArticles); ?></h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Derniers articles</span>
                                    </p>
                                </div>
                                <div class="avatar-xl flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle text-warning rounded-circle fs-42">
                                            <iconify-icon icon="solar:star-bold-duotone"></iconify-icon>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Comments -->
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center gap-2 justify-content-between">
                                <div>
                                    <h5 class="text-muted fs-13 fw-bold text-uppercase">Commentaires</h5>
                                    <h3 class="my-2 py-1 fw-bold" id="totalComments">0</h3>
                                    <p class="mb-0 text-muted">
                                        <span class="text-nowrap">Tous les commentaires</span>
                                    </p>
                                </div>
                                <div class="avatar-xl flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle text-info rounded-circle fs-42">
                                            <iconify-icon icon="solar:chat-round-dots-bold-duotone"></iconify-icon>
                                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row">
                <!-- Articles by Category Chart -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Articles par Catégorie</h4>
                        </div>
                        <div class="card-body">
                            <div dir="ltr">
                                <div id="category-chart" class="apex-charts" data-colors="#6B5BFF,#00B8A9,#FF7A5A,#3A86FF,#F8AB37"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Chart -->
                <div class="col-xl-6">
                    <div class="card">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Activité de Publication</h4>
                        </div>
                        <div class="card-body">
                            <div dir="ltr">
                                <div id="activity-chart" class="apex-charts" data-colors="#0acf97,#45bbe0"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Management Tables -->
            <div class="row">
                <!-- Articles Management -->
                <div class="col-xxl-6">
                    <div class="card">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Gestion des Articles</h4>
                            <button type="button" class="btn btn-sm btn-primary" onclick="openArticleModal()">
                                <i class="ri-add-line me-1"></i> Nouvel Article
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="bg-light bg-opacity-50 py-1 text-center">
                                <p class="m-0"><b><?php echo $totalArticles; ?></b> Articles au Total</p>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-centered table-sm table-nowrap table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Titre</th>
                                        <th>Catégorie</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($recentArticles)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-3">Aucun article trouvé</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentArticles as $article): ?>
                                            <tr>
                                                <td>
                                                    <h5 class="fs-14 fw-semibold mb-0"><?php echo substr($article['titre'], 0, 40); ?>...</h5>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary-subtle text-primary"><?php echo $article['categorie']; ?></span>
                                                </td>
                                                <td>
                                                    <span class="text-muted fs-12"><?php echo $article['date_publication']; ?></span>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-link text-muted p-0 border-0"
                                                                type="button"
                                                                data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                            <i class="ri-more-2-fill fs-18"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <button type="button"
                                                                        class="dropdown-item"
                                                                        onclick="editArticle(<?php echo $article['id_article']; ?>)">
                                                                    <i class="ri-edit-line me-1"></i> Modifier
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <button type="button"
                                                                        class="dropdown-item text-danger"
                                                                        onclick="deleteArticle(<?php echo $article['id_article']; ?>)">
                                                                    <i class="ri-delete-bin-line me-1"></i> Supprimer
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>


                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="articleDetail.php" class="btn btn-sm btn-light w-100">Voir Tous les Articles</a>
                        </div>
                    </div>
                </div>

                <!-- Categories Management -->
                <div class="col-xxl-6">
                    <div class="card">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Gestion des Catégories</h4>
                            <button type="button" class="btn btn-sm btn-primary" onclick="openCategoryModal()">
                                <i class="ri-add-line me-1"></i> Nouvelle Catégorie
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="bg-light bg-opacity-50 py-1 text-center">
                                <p class="m-0"><b><?php echo $totalCategories; ?></b> Catégories au Total</p>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-custom table-centered table-sm table-nowrap table-hover mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Nom</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($categories)): ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-3">Aucune catégorie trouvée</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td>
                                                    <h5 class="fs-14 fw-semibold mb-0"><?php echo htmlspecialchars($category['nom']); ?></h5>
                                                </td>
                                                <td>
                                                            <span class="text-muted fs-12">
                                                                <?php echo !empty($category['description']) ? substr(htmlspecialchars($category['description']), 0, 30) . '...' : 'Aucune description'; ?>
                                                            </span>
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-link text-muted p-0 border-0"
                                                                type="button"
                                                                data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                            <i class="ri-more-2-fill fs-18"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <button type="button"
                                                                        class="dropdown-item"
                                                                        onclick="editCategory(<?php echo $category['id_categorie']; ?>)">
                                                                    <i class="ri-edit-line me-1"></i> Modifier
                                                                </button>
                                                            </li>
                                                            <li>
                                                                <button type="button"
                                                                        class="dropdown-item text-danger"
                                                                        onclick="deleteCategory(<?php echo $category['id_categorie']; ?>)">
                                                                    <i class="ri-delete-bin-line me-1"></i> Supprimer
                                                                </button>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="categorieDetail.php" class="btn btn-sm btn-light w-100">Voir Toutes les Catégories</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Comments -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <h4 class="header-title">Commentaires Récents</h4>
                            <a href="commentaireDetail.php" class="btn btn-sm btn-light">Voir Tout</a>
                        </div>
                        <div class="card-body p-0">
                            <div id="commentsSection">
                                <?php if (empty($allComments)): ?>
                                    <div class="text-center py-4">
                                        <p class="text-muted">Aucun commentaire trouvé</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-centered table-nowrap table-hover mb-0">
                                            <thead class="table-light">
                                            <tr>
                                                <th>Auteur</th>
                                                <th>Article</th>
                                                <th>Commentaire</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php foreach ($allComments as $c): ?>
                                                <?php
                                                $nom = htmlspecialchars($c['nom_visiteur'] ?? 'Anonyme');
                                                $contenu = htmlspecialchars($c['contenu'] ?? '');
                                                $date = htmlspecialchars($c['date_commentaire'] ?? '');
                                                $articleId = (int)($c['id_article'] ?? 0);
                                                $articleTitle = isset($articlesById[$articleId]['titre']) ? htmlspecialchars($articlesById[$articleId]['titre']) : ('Article #'.$articleId);
                                                $excerpt = (strlen($contenu) > 120) ? (substr($contenu, 0, 120) . '...') : $contenu;
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar-title bg-primary-subtle text-primary rounded-circle me-2" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;">
                                                                <i class="ri-user-3-line"></i>
                                                            </span>
                                                            <div class="fw-semibold"><?= $nom ?></div>
                                                        </div>
                                                    </td>
                                                    <td class="text-truncate" style="max-width: 220px;" title="<?= $articleTitle ?>">
                                                        <?= $articleTitle ?>
                                                    </td>
                                                    <td class="text-muted text-truncate" style="max-width: 360px;" title="<?= $contenu ?>">
                                                        <?= $excerpt ?>
                                                    </td>
                                                    <td><span class="fs-12 text-muted"><?= $date ?></span></td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-link text-muted p-0 border-0"
                                                                    type="button"
                                                                    data-bs-toggle="dropdown"
                                                                    aria-expanded="false">
                                                                <i class="ri-more-2-fill fs-18"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li>
                                                                    <button type="button"
                                                                            class="dropdown-item"
                                                                            onclick="editComment(<?= (int)($c['id_commentaire'] ?? 0) ?>)">
                                                                        <i class="ri-edit-line me-1"></i> Modifier
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    <button type="button"
                                                                            class="dropdown-item text-danger"
                                                                            onclick="deleteComment(<?= (int)($c['id_commentaire'] ?? 0) ?>)">
                                                                        <i class="ri-delete-bin-line me-1"></i> Supprimer
                                                                    </button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <?php include('partials/footer.php'); ?>
    </div>
</div>

<!-- Add Article Modal -->
<div class="modal fade" id="addArticleModal" tabindex="-1" aria-labelledby="addArticleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addArticleModalLabel">Ajouter un Nouvel Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addArticleForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Titre de l'Article <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="titre" placeholder="Entrez le titre de l'article">
                        <div class="invalid-feedback d-block small" data-error-for="titre"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                        <select class="form-select" name="id_categorie">
                            <option value="">Sélectionnez une catégorie</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id_categorie']; ?>">
                                    <?php echo htmlspecialchars($cat['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback d-block small" data-error-for="id_categorie"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contenu <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" rows="6" placeholder="Écrivez le contenu de l'article..."></textarea>
                        <div class="invalid-feedback d-block small" data-error-for="content"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image de l'Article</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">Formats acceptés: JPG, PNG, GIF, WebP (Max 5MB)</small>
                        <div class="invalid-feedback d-block small" data-error-for="image"></div>
                    </div>
                    <div id="articleError" class="alert alert-danger d-none"></div>
                    <div id="articleSuccess" class="alert alert-success d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveArticle()">
                    <i class="ri-save-line me-1"></i> Publier l'Article
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Comment Modal -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCommentModalLabel">Modifier le Commentaire</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCommentForm">
                    <input type="hidden" name="id" id="edit_comment_id">
                    <div class="mb-3">
                        <label class="form-label">Contenu du commentaire <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="contenu" id="edit_comment_contenu" rows="4" placeholder="Éditez le commentaire..."></textarea>
                        <div class="invalid-feedback d-block small" data-error-for="contenu"></div>
                    </div>
                    <div id="editCommentError" class="alert alert-danger d-none"></div>
                    <div id="editCommentSuccess" class="alert alert-success d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveEditedComment()">
                    <i class="ri-save-line me-1"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
    
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Ajouter une Nouvelle Catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addCategoryForm">
                    <div class="mb-3">
                        <label class="form-label">Nom de la Catégorie <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nom" placeholder="Ex: Gaming, VR, Esport...">
                        <div class="invalid-feedback d-block small" data-error-for="nom"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Description de la catégorie (optionnel)"></textarea>
                        <div class="invalid-feedback d-block small" data-error-for="description"></div>
                    </div>
                    <div id="categoryError" class="alert alert-danger d-none"></div>
                    <div id="categorySuccess" class="alert alert-success d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveCategory()">
                    <i class="ri-save-line me-1"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Article Modal -->
<div class="modal fade" id="editArticleModal" tabindex="-1" aria-labelledby="editArticleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editArticleModalLabel">Modifier l'Article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editArticleForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="edit_article_id">
                    <div class="mb-3">
                        <label class="form-label">Titre de l'Article <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="titre" id="edit_article_titre" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catégorie <span class="text-danger">*</span></label>
                        <select class="form-select" name="id_categorie" id="edit_article_categorie" required>
                            <option value="">Sélectionnez une catégorie</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id_categorie']; ?>">
                                    <?php echo htmlspecialchars($cat['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contenu <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="content" id="edit_article_content" rows="6" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image actuelle</label>
                        <div class="mb-2">
                            <img id="editArticleImagePreview" src="" alt="aperçu" style="max-width:240px; display:none;"/>
                        </div>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">Laissez vide pour conserver l'image actuelle.</small>
                    </div>
                    <div id="editArticleError" class="alert alert-danger d-none"></div>
                    <div id="editArticleSuccess" class="alert alert-success d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitEditArticle()">
                    <i class="ri-save-line me-1"></i> Enregistrer les modifications
                </button>
            </div>
        </div>
    </div>

</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Modifier la Catégorie</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCategoryForm">
                    <input type="hidden" name="id" id="edit_category_id">
                    <div class="mb-3">
                        <label class="form-label">Nom de la Catégorie <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nom" id="edit_category_nom" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_category_description" rows="3"></textarea>
                    </div>
                    <div id="editCategoryError" class="alert alert-danger d-none"></div>
                    <div id="editCategorySuccess" class="alert alert-success d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitEditCategory()">
                    <i class="ri-save-line me-1"></i> Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>

<?php include('partials/customizer.php'); ?>
<?php include('partials/footer-scripts.php'); ?>

<!-- ApexCharts -->
<script src="../../../public/assets/vendor/apexcharts/apexcharts.min.js"></script>

<script>
    // Open modals manually - works even if Bootstrap isn't fully loaded
    function openArticleModal() {
        const modalEl = document.getElementById('addArticleModal');
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.setAttribute('aria-modal', 'true');
        modalEl.setAttribute('role', 'dialog');
        modalEl.removeAttribute('aria-hidden');

        // Add backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'articleModalBackdrop';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }

    function openEditArticleModal() {
        const modalEl = document.getElementById('editArticleModal');
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.setAttribute('aria-modal', 'true');
        modalEl.setAttribute('role', 'dialog');
        modalEl.removeAttribute('aria-hidden');

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'editArticleModalBackdrop';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }

    function openCategoryModal() {
        const modalEl = document.getElementById('addCategoryModal');
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.setAttribute('aria-modal', 'true');
        modalEl.setAttribute('role', 'dialog');
        modalEl.removeAttribute('aria-hidden');

        // Add backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'categoryModalBackdrop';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }

    function openEditCategoryModal() {
        const modalEl = document.getElementById('editCategoryModal');
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.setAttribute('aria-modal', 'true');
        modalEl.setAttribute('role', 'dialog');
        modalEl.removeAttribute('aria-hidden');

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'editCategoryModalBackdrop';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }

    // Open Edit Comment modal
    function openEditCommentModal() {
        const modalEl = document.getElementById('editCommentModal');
        modalEl.classList.add('show');
        modalEl.style.display = 'block';
        modalEl.setAttribute('aria-modal', 'true');
        modalEl.setAttribute('role', 'dialog');
        modalEl.removeAttribute('aria-hidden');

        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop fade show';
        backdrop.id = 'editCommentModalBackdrop';
        document.body.appendChild(backdrop);
        document.body.classList.add('modal-open');
    }

    // Close modal function
    function closeModal(modalId, backdropId) {
        const modalEl = document.getElementById(modalId);
        modalEl.classList.remove('show');
        modalEl.style.display = 'none';
        modalEl.setAttribute('aria-hidden', 'true');
        modalEl.removeAttribute('aria-modal');
        modalEl.removeAttribute('role');

        const backdrop = document.getElementById(backdropId);
        if (backdrop) {
            backdrop.remove();
        }
        document.body.classList.remove('modal-open');
    }

    // Add event listeners to close buttons
    document.addEventListener('DOMContentLoaded', function() {
        // Article modal close buttons
        const articleCloseButtons = document.querySelectorAll('#addArticleModal [data-bs-dismiss="modal"]');
        articleCloseButtons.forEach(btn => {
            btn.addEventListener('click', () => closeModal('addArticleModal', 'articleModalBackdrop'));
        });

        // Edit Article modal close buttons
        const editArticleCloseButtons = document.querySelectorAll('#editArticleModal [data-bs-dismiss="modal"]');
        editArticleCloseButtons.forEach(btn => {
            btn.addEventListener('click', () => closeModal('editArticleModal', 'editArticleModalBackdrop'));
        });

        // Category modal close buttons
        const categoryCloseButtons = document.querySelectorAll('#addCategoryModal [data-bs-dismiss="modal"]');
        categoryCloseButtons.forEach(btn => {
            btn.addEventListener('click', () => closeModal('addCategoryModal', 'categoryModalBackdrop'));
        });

        // Edit Category modal close buttons
        const editCategoryCloseButtons = document.querySelectorAll('#editCategoryModal [data-bs-dismiss="modal"]');
        editCategoryCloseButtons.forEach(btn => {
            btn.addEventListener('click', () => closeModal('editCategoryModal', 'editCategoryModalBackdrop'));
        });

        // Edit Comment modal close buttons
        const editCommentCloseButtons = document.querySelectorAll('#editCommentModal [data-bs-dismiss="modal"]');
        editCommentCloseButtons.forEach(btn => {
            btn.addEventListener('click', () => closeModal('editCommentModal', 'editCommentModalBackdrop'));
        });

        // Close on backdrop click
        document.addEventListener('click', function(e) {
            if (e.target.id === 'articleModalBackdrop') {
                closeModal('addArticleModal', 'articleModalBackdrop');
            }
            if (e.target.id === 'editArticleModalBackdrop') {
                closeModal('editArticleModal', 'editArticleModalBackdrop');
            }
            if (e.target.id === 'categoryModalBackdrop') {
                closeModal('addCategoryModal', 'categoryModalBackdrop');
            }
            if (e.target.id === 'editCategoryModalBackdrop') {
                closeModal('editCategoryModal', 'editCategoryModalBackdrop');
            }
            if (e.target.id === 'editCommentModalBackdrop') {
                closeModal('editCommentModal', 'editCommentModalBackdrop');
            }
        });
    });

    // Chart Data
    const categoryData = <?php echo json_encode(array_map(function($cat) use ($articles) {
        return [
                'name' => $cat['nom'],
                'count' => count(array_filter($articles, function($art) use ($cat) {
                    return $art['categorie'] == $cat['nom'];
                }))
        ];
    }, $categories)); ?>;

    // Category Distribution Chart
    if (document.querySelector("#category-chart")) {
        const categoryChart = new ApexCharts(document.querySelector("#category-chart"), {
            chart: {
                type: 'donut',
                height: 350
            },
            series: categoryData.map(c => c.count),
            labels: categoryData.map(c => c.name),
            colors: ['#6B5BFF', '#00B8A9', '#FF7A5A', '#3A86FF', '#F8AB37'],
            legend: {
                position: 'bottom'
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '65%'
                    }
                }
            }
        });
        categoryChart.render();
    }

    // Activity Chart
    if (document.querySelector("#activity-chart")) {
        const activityChart = new ApexCharts(document.querySelector("#activity-chart"), {
            chart: {
                type: 'bar',
                height: 350
            },
            series: [{
                name: 'Articles',
                data: categoryData.map(c => c.count)
            }],
            xaxis: {
                categories: categoryData.map(c => c.name)
            },
            colors: ['#0acf97'],
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false
                }
            }
        });
        activityChart.render();
    }

    // Save new article
    function saveArticle() {
        const form = document.getElementById('addArticleForm');
        const formData = new FormData(form);
        const errorDiv = document.getElementById('articleError');
        const successDiv = document.getElementById('articleSuccess');
        clearFieldErrors(form);

        // Hide previous messages
        errorDiv.classList.add('d-none');
        successDiv.classList.add('d-none');

        // Add action identifier
        formData.append('ajax_action', 'create_article');
        formData.append('id_auteur', 1);

        // Send AJAX request to the same page
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successDiv.textContent = 'Article créé avec succès !';
                    successDiv.classList.remove('d-none');
                    form.reset();

                    setTimeout(() => {
                        closeModal('addArticleModal', 'articleModalBackdrop');
                        location.reload();
                    }, 1500);
                } else {
                    if (data.errors) {
                        showFieldErrors(form, data.errors);
                    }
                    if (data.message) {
                        errorDiv.textContent = data.message;
                        errorDiv.classList.remove('d-none');
                    } else if (!data.errors) {
                        errorDiv.textContent = 'Erreur lors de la création de l\'article.';
                        errorDiv.classList.remove('d-none');
                    }
                }
            })
            .catch(error => {
                errorDiv.textContent = 'Erreur de connexion au serveur.';
                errorDiv.classList.remove('d-none');
                console.error('Error:', error);
            });
    }

    // Save category
    function saveCategory() {
        const form = document.getElementById('addCategoryForm');
        const formData = new FormData(form);
        const errorDiv = document.getElementById('categoryError');
        const successDiv = document.getElementById('categorySuccess');
        clearFieldErrors(form);

        // Hide previous messages
        errorDiv.classList.add('d-none');
        successDiv.classList.add('d-none');

        // Add action identifier
        formData.append('ajax_action', 'create_category');

        // Send AJAX request to the same page
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    successDiv.textContent = 'Catégorie créée avec succès !';
                    successDiv.classList.remove('d-none');
                    form.reset();

                    setTimeout(() => {
                        closeModal('addCategoryModal', 'categoryModalBackdrop');
                        location.reload();
                    }, 1500);
                } else {
                    if (data.errors) {
                        showFieldErrors(form, data.errors);
                    }
                    if (data.message) {
                        errorDiv.textContent = data.message;
                        errorDiv.classList.remove('d-none');
                    } else if (!data.errors) {
                        errorDiv.textContent = 'Erreur lors de la création de la catégorie.';
                        errorDiv.classList.remove('d-none');
                    }
                }
            })
            .catch(error => {
                errorDiv.textContent = 'Erreur de connexion au serveur.';
                errorDiv.classList.remove('d-none');
                console.error('Error:', error);
            });
    }

    // Helpers: field error handling
    function clearFieldErrors(form) {
        // Remove is-invalid from inputs/selects/textareas
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        // Clear inline error messages
        form.querySelectorAll('[data-error-for]').forEach(el => el.textContent = '');
    }

    function showFieldErrors(form, errors) {
        Object.keys(errors).forEach(key => {
            const msg = errors[key];
            const errEl = form.querySelector('[data-error-for="' + key + '"]');
            if (errEl) {
                errEl.textContent = msg;
            }
            const inputEl = form.querySelector('[name="' + key + '"]');
            if (inputEl) {
                inputEl.classList.add('is-invalid');
            }
        });
    }

    // Delete article
    function deleteArticle(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
            const formData = new FormData();
            formData.append('ajax_action', 'delete_article');
            formData.append('id', id);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Article supprimé avec succès !');
                        location.reload();
                    } else {
                        alert(data.message || 'Erreur lors de la suppression de l\'article.');
                    }
                })
                .catch(error => {
                    alert('Erreur de connexion au serveur.');
                    console.error('Error:', error);
                });
        }
    }

    // Delete category
    function deleteCategory(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')) {
            const formData = new FormData();
            formData.append('ajax_action', 'delete_category');
            formData.append('id', id);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Catégorie supprimée avec succès !');
                        location.reload();
                    } else {
                        alert(data.message || 'Erreur lors de la suppression de la catégorie.');
                    }
                })
                .catch(error => {
                    alert('Erreur de connexion au serveur.');
                    console.error('Error:', error);
                });
        }
    }

    // Delete comment
    function deleteComment(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
            const formData = new FormData();
            formData.append('ajax_action', 'delete_comment');
            formData.append('id', id);

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Commentaire supprimé avec succès !');
                        location.reload();
                    } else {
                        alert(data.message || 'Erreur lors de la suppression du commentaire.');
                    }
                })
                .catch(error => {
                    alert('Erreur de connexion au serveur.');
                    console.error('Error:', error);
                });
        }
    }

    // Edit comment: fetch and open modal
    function editComment(id) {
        const url = window.location.pathname + `?ajax_get=comment&id=${id}`;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.comment) {
                    alert(data.message || 'Impossible de charger le commentaire');
                    return;
                }
                const c = data.comment;
                document.getElementById('edit_comment_id').value = c.id_commentaire;
                document.getElementById('edit_comment_contenu').value = c.contenu || '';
                document.getElementById('editCommentError').classList.add('d-none');
                document.getElementById('editCommentSuccess').classList.add('d-none');
                const form = document.getElementById('editCommentForm');
                clearFieldErrors(form);
                openEditCommentModal();
            })
            .catch(() => alert('Erreur de connexion au serveur.'));
    }

    // Save edited comment
    function saveEditedComment() {
        const form = document.getElementById('editCommentForm');
        const formData = new FormData(form);
        const errorDiv = document.getElementById('editCommentError');
        const successDiv = document.getElementById('editCommentSuccess');
        clearFieldErrors(form);

        errorDiv.classList.add('d-none');
        successDiv.classList.add('d-none');

        formData.append('ajax_action', 'update_comment');

        fetch(window.location.href, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    successDiv.textContent = 'Commentaire mis à jour avec succès !';
                    successDiv.classList.remove('d-none');
                    setTimeout(() => {
                        closeModal('editCommentModal', 'editCommentModalBackdrop');
                        location.reload();
                    }, 1200);
                } else {
                    // Map server validation to field error if provided
                    if (data.errors) {
                        showFieldErrors(form, data.errors);
                    } else if (data.message) {
                        // Fallback: show message under content field or as alert
                        const errEl = form.querySelector('[data-error-for="contenu"]');
                        if (errEl) errEl.textContent = data.message;
                    }
                    if (data.message && !data.errors) {
                        errorDiv.textContent = data.message;
                        errorDiv.classList.remove('d-none');
                    }
                }
            })
            .catch(err => {
                errorDiv.textContent = 'Erreur de connexion au serveur.';
                errorDiv.classList.remove('d-none');
                console.error('Error:', err);
            });
    }

    // Edit category
    function editCategory(id) {
        const url = window.location.pathname + `?ajax_get=category&id=${id}`;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.category) {
                    alert(data.message || 'Impossible de charger la catégorie');
                    return;
                }
                const c = data.category;
                document.getElementById('edit_category_id').value = c.id_categorie;
                document.getElementById('edit_category_nom').value = c.nom || '';
                document.getElementById('edit_category_description').value = c.description || '';
                document.getElementById('editCategoryError').classList.add('d-none');
                document.getElementById('editCategorySuccess').classList.add('d-none');
                openEditCategoryModal();
            })
            .catch(() => alert('Erreur de connexion au serveur.'));
    }

    // Edit article
    function editArticle(id) {
        const url = window.location.pathname + `?ajax_get=article&id=${id}`;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (!data.success || !data.article) {
                    alert(data.message || "Impossible de charger l'article");
                    return;
                }
                const a = data.article;
                document.getElementById('edit_article_id').value = a.id_article;
                document.getElementById('edit_article_titre').value = a.titre || '';
                document.getElementById('edit_article_content').value = (a.full_content || a.content || '');
                const select = document.getElementById('edit_article_categorie');
                if (a.id_categorie) {
                    select.value = a.id_categorie;
                } else if (a.categorie) {
                    // fallback: some payloads expose category id under `categorie`
                    select.value = a.categorie;
                }
                const img = document.getElementById('editArticleImagePreview');
                if (a.image) {
                    img.src = a.image;
                    img.style.display = 'inline-block';
                } else {
                    img.removeAttribute('src');
                    img.style.display = 'none';
                }
                document.getElementById('editArticleError').classList.add('d-none');
                document.getElementById('editArticleSuccess').classList.add('d-none');
                openEditArticleModal();
            })
            .catch(() => alert('Erreur de connexion au serveur.'));
    }

    function submitEditArticle() {
        const form = document.getElementById('editArticleForm');
        const formData = new FormData(form);
        const err = document.getElementById('editArticleError');
        const ok = document.getElementById('editArticleSuccess');
        err.classList.add('d-none');
        ok.classList.add('d-none');

        if (!formData.get('titre') || !formData.get('content') || !formData.get('id_categorie')) {
            err.textContent = 'Veuillez remplir tous les champs obligatoires.';
            err.classList.remove('d-none');
            return;
        }

        formData.append('ajax_action', 'update_article');

        fetch(window.location.href, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    ok.textContent = 'Article mis à jour avec succès !';
                    ok.classList.remove('d-none');
                    setTimeout(() => { closeModal('editArticleModal', 'editArticleModalBackdrop'); location.reload(); }, 1000);
                } else {
                    err.textContent = data.message || 'Erreur lors de la mise à jour de l\'article.';
                    err.classList.remove('d-none');
                }
            })
            .catch(() => {
                err.textContent = 'Erreur de connexion au serveur.';
                err.classList.remove('d-none');
            });
    }

    function submitEditCategory() {
        const form = document.getElementById('editCategoryForm');
        const formData = new FormData(form);
        const err = document.getElementById('editCategoryError');
        const ok = document.getElementById('editCategorySuccess');
        err.classList.add('d-none');
        ok.classList.add('d-none');

        if (!formData.get('nom')) {
            err.textContent = 'Le nom de la catégorie est obligatoire.';
            err.classList.remove('d-none');
            return;
        }

        formData.append('ajax_action', 'update_category');

        fetch(window.location.href, { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    ok.textContent = 'Catégorie mise à jour avec succès !';
                    ok.classList.remove('d-none');
                    setTimeout(() => { closeModal('editCategoryModal', 'editCategoryModalBackdrop'); location.reload(); }, 1000);
                } else {
                    err.textContent = data.message || 'Erreur lors de la mise à jour de la catégorie.';
                    err.classList.remove('d-none');
                }
            })
            .catch(() => {
                err.textContent = 'Erreur de connexion au serveur.';
                err.classList.remove('d-none');
            });
    }
</script>
</body>
</html>