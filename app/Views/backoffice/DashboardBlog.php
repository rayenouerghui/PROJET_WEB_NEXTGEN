<?php
// Chemin corrigé - remonter de 3 niveaux pour atteindre app/Controllers/
require_once __DIR__ . '/../../../app/Controllers/BlogController.php';

// Initialiser le contrôleur
$blogController = new BlogController();

// Récupérer tous les articles
$articles = $blogController->index();

// Traitement des actions
$action = $_POST['action'] ?? '';
$message = '';
$errors = [];

if ($action === 'create_article' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation des champs
    $titre = trim($_POST['titre'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');

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

    if (empty($categorie)) {
        $errors['categorie'] = 'La catégorie est obligatoire';
    } elseif (!in_array($categorie, ['Gaming', 'VR', 'Esport', 'Communauté'])) {
        $errors['categorie'] = 'Catégorie invalide';
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
        $result = $blogController->create($_POST, $_FILES);
        if ($result['success']) {
            // Redirection pour éviter la resoumission du formulaire
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=create');
            exit();
        } else {
            $message = '<div class="alert alert-danger">' . $result['message'] . '</div>';
        }
    }
}

// Traitement de la modification
if ($action === 'update_article' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleId = (int)$_POST['id_article'];

    // Validation des champs
    $titre = trim($_POST['titre'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $categorie = trim($_POST['categorie'] ?? '');

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

    if (empty($categorie)) {
        $errors['categorie'] = 'La catégorie est obligatoire';
    } elseif (!in_array($categorie, ['Gaming', 'VR', 'Esport', 'Communauté'])) {
        $errors['categorie'] = 'Catégorie invalide';
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
        $result = $blogController->update($articleId, $_POST, $_FILES);
        if ($result['success']) {
            // Redirection pour éviter la resoumission du formulaire
            header('Location: ' . $_SERVER['PHP_SELF'] . '?success=update');
            exit();
        } else {
            $message = '<div class="alert alert-danger">' . $result['message'] . '</div>';
        }
    }
}

// Traitement de la suppression
if (isset($_POST['delete_article'])) {
    $articleId = (int)$_POST['delete_article'];
    $result = $blogController->delete($articleId);
    if ($result['success']) {
        // Redirection pour éviter la resoumission du formulaire
        header('Location: ' . $_SERVER['PHP_SELF'] . '?success=delete');
        exit();
    } else {
        $message = '<div class="alert alert-danger">' . $result['message'] . '</div>';
    }
}

// Afficher les messages de succès après redirection
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'create':
            $message = '<div class="alert alert-success">Article créé avec succès !</div>';
            break;
        case 'update':
            $message = '<div class="alert alert-success">Article mis à jour avec succès !</div>';
            break;
        case 'delete':
            $message = '<div class="alert alert-success">Article supprimé avec succès !</div>';
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
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/common.css" />
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/frontoffice.css" />
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/blog.css" />

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
                    <img src="/PROJET_WEB_NEXTGEN/public/images/logo.png" alt="NextGen Logo" class="logo-img">
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

    <!-- Afficher les erreurs de validation -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <strong>Erreurs de validation :</strong>
            <ul style="margin: 10px 0 0 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- ===== FORMULAIRE DE CRÉATION ===== -->
    <div class="admin-panel">
        <h3>Créer un nouvel article</h3>
        <form method="POST" enctype="multipart/form-data" class="admin-form" novalidate>
            <input type="hidden" name="action" value="create_article">

            <div class="form-group">
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" class="form-control" value="<?php echo htmlspecialchars($_POST['titre'] ?? ''); ?>">
                <?php if (isset($errors['titre'])): ?>
                    <span class="error-message"><?php echo $errors['titre']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="content">Contenu *</label>
                <textarea id="content" name="content" class="form-control" rows="6"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                <?php if (isset($errors['content'])): ?>
                    <span class="error-message"><?php echo $errors['content']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="categorie">Catégorie *</label>
                <select id="categorie" name="categorie" class="form-control">
                    <option value="">Choisir une catégorie</option>
                    <option value="Gaming" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'Gaming') ? 'selected' : ''; ?>>Gaming</option>
                    <option value="VR" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'VR') ? 'selected' : ''; ?>>VR</option>
                    <option value="Esport" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'Esport') ? 'selected' : ''; ?>>Esport</option>
                    <option value="Communauté" <?php echo (isset($_POST['categorie']) && $_POST['categorie'] === 'Communauté') ? 'selected' : ''; ?>>Communauté</option>
                </select>
                <?php if (isset($errors['categorie'])): ?>
                    <span class="error-message"><?php echo $errors['categorie']; ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="image">Image (JPG, PNG, GIF, WebP - Max 5MB)</label>
                <div class="file-input-wrapper">
                    <span class="file-input-button">Choisir une image</span>
                    <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this, 'preview-create')">
                </div>
                <div id="file-name-create" class="file-name"></div>
                <img id="preview-create" class="preview-image" alt="Aperçu">
                <?php if (isset($errors['image'])): ?>
                    <span class="error-message"><?php echo $errors['image']; ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary">Créer l'article</button>
        </form>
    </div>

    <!-- ===== TABLEAU DES ARTICLES ===== -->
    <div class="admin-panel">
        <h3>Liste des articles (<?php echo count($articles); ?>)</h3>

        <?php if (empty($articles)): ?>
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
                                    <img src="<?php echo $article['image']; ?>" alt="<?php echo $article['titre']; ?>" class="article-image">
                                </td>
                                <td>
                                    <div class="article-title"><?php echo $article['titre']; ?></div>
                                    <div class="article-excerpt"><?php echo substr($article['content'], 0, 100) . '...'; ?></div>
                                </td>
                                <td>
                                    <span class="category-badge"><?php echo $article['categorie']; ?></span>
                                </td>
                                <td>
                                    <div class="article-meta"><?php echo $article['date_publication']; ?></div>
                                </td>
                                <td class="actions-cell">
                                    <button class="btn btn-edit" onclick="editArticle(<?php echo $article['id_article']; ?>)">
                                        Modifier
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="delete_article" value="<?php echo $article['id_article']; ?>">
                                        <button type="submit" class="btn btn-delete" onclick="return confirm(' Êtes-vous sûr de vouloir supprimer cet article ? Cette action est irréversible.')">
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

<!-- ===== MODAL DE MODIFICATION ===== -->
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
    // Données des articles pour JavaScript
    const articlesData = {
    <?php foreach ($articles as $article): ?>
    <?php $rawArticle = $blogController->show($article['id_article']); ?>
    <?php echo $article['id_article']; ?>: {
        titre: "<?php echo addslashes($rawArticle['titre']); ?>",
            content: `<?php echo addslashes($rawArticle['content']); ?>`,
            categorie: "<?php echo $rawArticle['categorie']; ?>",
            image: "<?php echo $rawArticle['image']; ?>"
    },
    <?php endforeach; ?>
    };

    // Prévisualisation de l'image
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const fileNameDiv = document.getElementById('file-name-' + previewId.split('-')[1]);

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Afficher le nom du fichier
            if (fileNameDiv) {
                fileNameDiv.textContent = '📎 Fichier sélectionné : ' + file.name;
            }

            // Prévisualiser l'image
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Modifier un article
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
                    <input type="text" id="edit_titre" name="titre" class="form-control" value="${article.titre}">
                </div>

                <div class="form-group">
                    <label for="edit_content">Contenu *</label>
                    <textarea id="edit_content" name="content" class="form-control" rows="6">${article.content}</textarea>
                </div>

                <div class="form-group">
                    <label for="edit_categorie">Catégorie *</label>
                    <select id="edit_categorie" name="categorie" class="form-control">
                        <option value="Gaming" ${article.categorie === 'Gaming' ? 'selected' : ''}>Gaming</option>
                        <option value="VR" ${article.categorie === 'VR' ? 'selected' : ''}>VR</option>
                        <option value="Esport" ${article.categorie === 'Esport' ? 'selected' : ''}>Esport</option>
                        <option value="Communauté" ${article.categorie === 'Communauté' ? 'selected' : ''}>Communauté</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Image actuelle</label>
                    <br>
                    <img src="${article.image}" class="current-image" alt="Image actuelle">
                </div>

                <div class="form-group">
                    <label for="edit_image">Nouvelle image (laisser vide pour garder l'actuelle)</label>
                    <div class="file-input-wrapper">
                        <span class="file-input-button"> Choisir une nouvelle image</span>
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

    function closeEditModal() {
        const modal = document.getElementById('edit-modal');
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Fermer le modal avec Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEditModal();
        }
    });

    // Fermer le modal en cliquant en dehors
    document.getElementById('edit-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
</script>
</body>
</html>