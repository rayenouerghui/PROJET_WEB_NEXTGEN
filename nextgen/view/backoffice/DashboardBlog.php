<?php

require_once __DIR__ . '/../../controller/BlogController.php';
require_once __DIR__ . '/../../controller/CategoryController.php';

// Initialiser les contrôleurs
$blogController = new BlogController();
$categoryController = new CategoryController();

// Récupérer tous les articles
$articles = $blogController->index();

// Récupérer toutes les catégories
$categories = $categoryController->getAllCategories();

// Traitement des actions
$action = $_POST['action'] ?? '';
$message = '';
$errors = [];

// ===== CRÉATION DE CATÉGORIE =====
if ($action === 'create_category' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validation
    if (empty($nom)) {
        $errors['nom'] = 'Le nom de la catégorie est obligatoire';
    } elseif (strlen($nom) < 2) {
        $errors['nom'] = 'Le nom doit contenir au moins 2 caractères';
    }

    if (empty($errors)) {
        $result = $categoryController->create([
                'nom' => $nom,
                'description' => $description
        ]);

        if ($result['success']) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=create_category');
            exit();
        } else {
            $message = '<div class="alert alert-danger">' . htmlspecialchars($result['message']) . '</div>';
        }
    }
}

// ===== CRÉATION D'ARTICLE =====
if ($action === 'create_article' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $id_categorie = (int)($_POST['id_categorie'] ?? 0);

    // Validation
    if (empty($titre)) {
        $errors['titre'] = 'Le titre est obligatoire';
    } elseif (strlen($titre) < 3) {
        $errors['titre'] = 'Le titre doit contenir au moins 3 caractères';
    }

    if (empty($content)) {
        $errors['content'] = 'Le contenu est obligatoire';
    } elseif (strlen($content) < 10) {
        $errors['content'] = 'Le contenu doit contenir au moins 10 caractères';
    }

    if (empty($id_categorie)) {
        $errors['categorie'] = 'La catégorie est obligatoire';
    }

    // Validation de l'image
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors['image'] = 'Erreur lors de l\'upload du fichier';
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors['image'] = 'Le fichier est trop volumineux (max 5MB)';
        } else {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                $errors['image'] = 'Type de fichier non autorisé (JPG, PNG, GIF, WebP uniquement)';
            }
        }
    }

    if (empty($errors)) {
        // Préparer les données avec l'ID de catégorie
        $articleData = [
                'titre' => $titre,
                'content' => $content,
                'id_categorie' => $id_categorie,
                'id_auteur' => 1 // ID d'auteur par défaut
        ];

        $result = $blogController->create($articleData, $_FILES);
        if ($result['success']) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=create');
            exit();
        } else {
            $message = '<div class="alert alert-danger">' . htmlspecialchars($result['message']) . '</div>';
        }
    }
}

// ===== MODIFICATION D'ARTICLE =====
if ($action === 'update_article' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleId = (int)$_POST['id_article'];
    $titre = trim($_POST['titre'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $id_categorie = (int)($_POST['id_categorie'] ?? 0);

    // Validation
    if (empty($titre)) {
        $errors['titre'] = 'Le titre est obligatoire';
    } elseif (strlen($titre) < 3) {
        $errors['titre'] = 'Le titre doit contenir au moins 3 caractères';
    }

    if (empty($content)) {
        $errors['content'] = 'Le contenu est obligatoire';
    } elseif (strlen($content) < 10) {
        $errors['content'] = 'Le contenu doit contenir au moins 10 caractères';
    }

    if (empty($id_categorie)) {
        $errors['categorie'] = 'La catégorie est obligatoire';
    }

    // Validation de l'image si fournie
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors['image'] = 'Erreur lors de l\'upload du fichier';
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors['image'] = 'Le fichier est trop volumineux (max 5MB)';
        } else {
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedTypes)) {
                $errors['image'] = 'Type de fichier non autorisé (JPG, PNG, GIF, WebP uniquement)';
            }
        }
    }

    if (empty($errors)) {
        // Préparer les données avec l'ID de catégorie
        $articleData = [
                'titre' => $titre,
                'content' => $content,
                'id_categorie' => $id_categorie,
                'id_auteur' => 1 // ID d'auteur par défaut
        ];

        $result = $blogController->update($articleId, $articleData, $_FILES);
        if ($result['success']) {
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=update');
            exit();
        } else {
            $message = '<div class="alert alert-danger">' . htmlspecialchars($result['message']) . '</div>';
        }
    }
}

// ===== SUPPRESSION D'ARTICLE =====
if (isset($_POST['delete_article'])) {
    $articleId = (int)$_POST['delete_article'];
    $result = $blogController->delete($articleId);
    if ($result['success']) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=delete');
        exit;
    } else {
        $message = '<div class="alert alert-danger">' . htmlspecialchars($result['message']) . '</div>';
    }
}

// ===== MESSAGES DE SUCCÈS =====
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'create_category':
            $message = '<div class="alert alert-success">Catégorie créée avec succès !</div>';
            // Recharger les catégories
            $categories = $categoryController->getAllCategories();
            break;
        case 'create':
            $message = '<div class="alert alert-success">Article créé avec succès !</div>';
            // Recharger les articles
            $articles = $blogController->index();
            break;
        case 'update':
            $message = '<div class="alert alert-success">Article mis à jour avec succès !</div>';
            $articles = $blogController->index();
            break;
        case 'delete':
            $message = '<div class="alert alert-success">Article et ses commentaires supprimés avec succès !</div>';
            $articles = $blogController->index();
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Blog - NextGen</title>

    <!-- Liens CSS -->
    <?php require_once __DIR__ . '/../../config/paths.php'; ?>
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/public/css/style.css" />
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/public/css/frontoffice.css" />
    <link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/public/css/blog.css" />

    <!-- Polices -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
</head>

<body>
<!-- ===== HEADER ===== -->
<header class="header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="index.html">
                    <img src="<?php echo WEB_ROOT; ?>/public/images/logo.png" alt="NextGen Logo" class="logo-img">
                    NextGen
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="dashboardblog.php" style="color: #6B5BFF; font-weight: bold;">Blog</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="cart.html" class="cart-icon" title="Panier">
                    🛒
                    <span class="cart-count">0</span>
                </a>
                <a href="account.html" class="account-icon">Mon Compte</a>
            </div>
        </div>
    </div>
</header>

<!-- ===== DASHBOARD CONTENT ===== -->
<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Dashboard Blog - Gestion des Articles</h1>
        <p>Créez, modifiez et supprimez les articles du blog</p>
    </div>

    <?php echo $message; ?>

    <!-- ===== BOUTON POUR CRÉER UNE CATÉGORIE ===== -->
    <div class="admin-panel">
        <div class="panel-header">
            <h3>Gestion des Catégories</h3>
            <button class="btn btn-secondary" onclick="openCategoryModal()">
                + Nouvelle Catégorie
            </button>
        </div>
        <div class="categories-list">
            <?php if (!empty($categories)): ?>
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                        <div class="category-card">
                            <h4><?php echo htmlspecialchars($category['nom']); ?></h4>
                            <?php if (!empty($category['description'])): ?>
                                <p><?php echo htmlspecialchars($category['description']); ?></p>
                            <?php endif; ?>
                            <small>Slug: <?php echo htmlspecialchars($category['slug']); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-categories">Aucune catégorie disponible. Créez votre première catégorie !</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== FORMULAIRE DE CRÉATION D'ARTICLE ===== -->
    <div class="admin-panel">
        <h3>Créer un nouvel article</h3>
        <form method="POST" enctype="multipart/form-data" class="admin-form" novalidate>
            <input type="hidden" name="action" value="create_article">

            <div class="form-group">
                <label for="titre">Titre *</label>
                <?php if (isset($errors['titre'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['titre']); ?></span>
                <?php endif; ?>
                <input type="text" id="titre" name="titre" class="form-control" value="<?php echo htmlspecialchars($_POST['titre'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="content">Contenu *</label>
                <?php if (isset($errors['content'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['content']); ?></span>
                <?php endif; ?>
                <textarea id="content" name="content" class="form-control" rows="6"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label for="id_categorie">Catégorie *</label>
                <?php if (isset($errors['categorie'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['categorie']); ?></span>
                <?php endif; ?>
                <select id="id_categorie" name="id_categorie" class="form-control">
                    <option value="">Choisir une catégorie</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['id_categorie']); ?>"
                                <?php echo (isset($_POST['id_categorie']) && $_POST['id_categorie'] == $category['id_categorie']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category['nom']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Image (JPG, PNG, GIF, WebP - Max 5MB)</label>
                <?php if (isset($errors['image'])): ?>
                    <span class="error-message"><?php echo htmlspecialchars($errors['image']); ?></span>
                <?php endif; ?>
                <div class="file-input-wrapper">
                    <span class="file-input-button">Choisir une image</span>
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this, 'preview-create')">
                </div>
                <div id="file-name-create" class="file-name"></div>
                <img id="preview-create" class="preview-image" alt="Aperçu">
            </div>

            <button type="submit" class="btn btn-primary">Créer l'article</button>
        </form>
    </div>

    <!-- ===== TABLEAU DES ARTICLES ===== -->
    <div class="admin-panel">
        <h3>Liste des articles (<?php echo count($articles); ?>)</h3>

        <?php if (empty($articles) || isset($articles['error'])): ?>
            <div class="no-articles">
                <p>Aucun article disponible. Créez votre premier article !</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <div class="articles-table">
                    <table>
                        <thead>
                        <tr>
                            <th>Image</th>
                            <th>Article</th>
                            <th>Catégorie</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['titre']); ?>" class="article-image">
                                </td>
                                <td>
                                    <div class="article-title"><?php echo htmlspecialchars($article['titre']); ?></div>
                                    <div class="article-excerpt"><?php echo htmlspecialchars(substr($article['content'], 0, 100)) . '...'; ?></div>
                                </td>
                                <td>
                                    <span class="category-badge"><?php echo htmlspecialchars($article['categorie_nom']); ?></span>
                                </td>
                                <td>
                                    <div class="article-meta"><?php echo htmlspecialchars($article['date_publication']); ?></div>
                                </td>
                                <td class="actions-cell">
                                    <button class="btn btn-edit" onclick="editArticle(<?php echo $article['id_article']; ?>)">
                                        Modifier
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="delete_article" value="<?php echo $article['id_article']; ?>">
                                        <button type="submit" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ? Tous les commentaires associés seront également supprimés. Cette action est irréversible.')">
                                            Supprimer
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ===== MODAL DE CRÉATION DE CATÉGORIE ===== -->
<div id="category-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeCategoryModal()">&times;</span>
        <h2>Créer une nouvelle catégorie</h2>
        <form method="POST" class="admin-form" novalidate>
            <input type="hidden" name="action" value="create_category">

            <div class="form-group">
                <label for="category_nom">Nom de la catégorie *</label>
                <input type="text" id="category_nom" name="nom" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="category_description">Description</label>
                <textarea id="category_description" name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Créer la catégorie</button>
                <button type="button" class="btn btn-cancel" onclick="closeCategoryModal()">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== MODAL DE MODIFICATION D'ARTICLE ===== -->
<div id="edit-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeEditModal()">&times;</span>
        <div id="edit-modal-body">
            <!-- Le formulaire sera inséré ici dynamiquement -->
        </div>
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="container">
        <p>&copy; 2025 NextGen. Tous droits réservés.</p>
    </div>
</footer>

<!-- ===== SCRIPT JS ===== -->
<script>
    // ✅ Données des articles depuis PHP - PROPRE avec json_encode()
    const articlesData = <?php echo json_encode(array_column($articles, null, 'id_article')); ?>;

    /**
     * Ouvrir la modal de création de catégorie (void)
     */
    function openCategoryModal() {
        const modal = document.getElementById('category-modal');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Fermer la modal de création de catégorie (void)
     */
    function closeCategoryModal() {
        const modal = document.getElementById('category-modal');
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    /**
     * Prévisualisation de l'image (void)
     */
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const fileNameDiv = document.getElementById('file-name-' + previewId.split('-')[1]);

        if (input.files && input.files[0]) {
            const file = input.files[0];

            if (fileNameDiv) {
                fileNameDiv.textContent = '📁 Fichier sélectionné : ' + file.name;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    /**
     * Modifier un article (void)
     */
    function editArticle(articleId) {
        const article = articlesData[articleId];

        if (!article) {
            alert('Article non trouvé');
            return;
        }

        const modal = document.getElementById('edit-modal');
        const modalBody = document.getElementById('edit-modal-body');

        const formHTML = `
            <h2>Modifier l'article</h2>
            <form method="POST" enctype="multipart/form-data" class="admin-form" novalidate>
                <input type="hidden" name="action" value="update_article">
                <input type="hidden" name="id_article" value="${articleId}">

                <div class="form-group">
                    <label for="edit_titre">Titre *</label>
                    <input type="text" id="edit_titre" name="titre" class="form-control" value="${escapeHtml(article.titre)}">
                </div>

                <div class="form-group">
                    <label for="edit_content">Contenu *</label>
                    <textarea id="edit_content" name="content" class="form-control" rows="6">${escapeHtml(article.full_content)}</textarea>
                </div>

                <div class="form-group">
                    <label for="edit_id_categorie">Catégorie *</label>
                    <select id="edit_id_categorie" name="id_categorie" class="form-control">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['id_categorie']); ?>" ${article.id_categorie == <?php echo $category['id_categorie']; ?> ? 'selected' : ''}>
                                <?php echo htmlspecialchars($category['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Image actuelle</label>
                    <br>
                    <img src="${escapeHtml(article.image)}" class="current-image" alt="Image actuelle">
                </div>

                <div class="form-group">
                    <label for="edit_image">Nouvelle image (laisser vide pour garder l'actuelle)</label>
                    <div class="file-input-wrapper">
                        <span class="file-input-button">Choisir une nouvelle image</span>
                        <input type="file" id="edit_image" name="image" accept="image/*" onchange="previewImage(this, 'preview-edit')">
                    </div>
                    <div id="file-name-edit" class="file-name"></div>
                    <img id="preview-edit" class="preview-image" alt="Aperçu">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    <button type="button" class="btn btn-cancel" onclick="closeEditModal()">Annuler</button>
                </div>
            </form>
        `;

        modalBody.innerHTML = formHTML;
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Fermer la modal d'édition (void)
     */
    function closeEditModal() {
        const modal = document.getElementById('edit-modal');
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    /**
     * Échapper les caractères spéciaux HTML (string)
     */
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Fermer les modals avec Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
            closeCategoryModal();
        }
    });

    // Fermer les modals en cliquant en dehors
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                if (this.id === 'edit-modal') {
                    closeEditModal();
                } else if (this.id === 'category-modal') {
                    closeCategoryModal();
                }
            }
        });
    });
</script>

<style>
    /* Styles pour la gestion des catégories */
    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .category-card {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .category-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .category-card h4 {
        margin: 0 0 10px 0;
        color: #333;
        font-size: 1.1em;
    }

    .category-card p {
        margin: 0 0 10px 0;
        color: #666;
        font-size: 0.9em;
    }

    .category-card small {
        color: #888;
        font-size: 0.8em;
    }

    .no-categories {
        text-align: center;
        color: #666;
        font-style: italic;
        padding: 20px;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.3s;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }
</style>
</body>
</html>