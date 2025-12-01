<?php
/**
 * Vue : blog.php
 * Blog avec filtrage par catégories (Design amélioré)
 */

require_once __DIR__ . '/../../../app/Controllers/BlogController.php';
require_once __DIR__ . '/../../../app/Controllers/CommentaireController.php';
require_once __DIR__ . '/../../../app/Controllers/CategoryController.php';

$blogController = new BlogController();
$commentController = new CommentController();
$categoryController = new CategoryController();

$articles = $blogController->index();
$categories = $categoryController->getAllCategories();

$errors = [];
$field_errors = ['nom_visiteur' => '', 'contenu' => ''];
$old_values = ['nom_visiteur' => '', 'contenu' => ''];
$action = $_POST['action'] ?? '';

// ===== CRÉATION DE COMMENTAIRE =====
if ($action === 'add_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_article = (int)($_POST['id_article'] ?? 0);
    $nom_visiteur = trim($_POST['nom_visiteur'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');

    $old_values['nom_visiteur'] = htmlspecialchars($nom_visiteur);
    $old_values['contenu'] = htmlspecialchars($contenu);

    $result = $commentController->create($id_article, $nom_visiteur, $contenu);

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}

// ===== MISE À JOUR DE COMMENTAIRE =====
if ($action === 'update_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_commentaire = (int)($_POST['id_commentaire'] ?? 0);
    $contenu = trim($_POST['contenu'] ?? '');

    header('Content-Type: application/json');
    if ($id_commentaire <= 0 || empty($contenu)) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit;
    }

    $result = $commentController->update($id_commentaire, $contenu);
    echo json_encode($result);
    exit;
}

// ===== SUPPRESSION DE COMMENTAIRE =====
if ($action === 'delete_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_commentaire = (int)($_POST['id_commentaire'] ?? 0);

    header('Content-Type: application/json');
    if ($id_commentaire <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        exit;
    }

    $result = $commentController->delete($id_commentaire);
    echo json_encode($result);
    exit;
}

// ===== RÉCUPÉRATION DES COMMENTAIRES EN JSON =====
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_comments'])) {
    $id_article = (int)($_GET['id_article'] ?? 0);
    header('Content-Type: application/json');
    if ($id_article <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID article invalide', 'comments' => []]);
        exit;
    }

    $result = $commentController->getByArticleJSON($id_article);
    echo json_encode($result);
    exit;
}

// ===== RÉCUPÉRATION DES ARTICLES PAR CATÉGORIE =====
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_articles_by_category'])) {
    $id_categorie = (int)($_GET['id_categorie'] ?? 0);
    header('Content-Type: application/json');

    if ($id_categorie <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID catégorie invalide', 'articles' => []]);
        exit;
    }

    $articles = $blogController->getByCategory($id_categorie);
    echo json_encode(['success' => true, 'articles' => $articles]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog - NextGen</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../../public/css/front.css">
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/style.css" />
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/frontoffice.css" />
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/blog.css" />

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">

    <style>
        .blog-card { cursor: pointer; transition: all 0.3s; }
        .blog-card:hover { transform: translateY(-8px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }

        .error-message {
            color: #dc3545;
            font-size: 14px;
            font-weight: 500;
            display: block;
            margin-top: 5px;
            margin-bottom: 8px;
        }

        .comment-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .comment-input:focus {
            border-color: #007bff;
            outline: none;
        }

        .comment-input.error {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .comment-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .submit-comment-btn {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: background 0.3s;
        }

        .submit-comment-btn:hover {
            background: #0056b3;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group label::after {
            content: " *";
            color: #dc3545;
        }

        .add-comment-form h4 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        /* ===== CATEGORY FILTER SECTION STYLES ===== */
        .category-filter-section {
            background: linear-gradient(135deg, #1a1a3e 0%, #16213e 100%);
            padding: 30px 0 20px 0;
            margin: 0;
        }

        .category-filter-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
            text-align: center;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .category-filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin-bottom: 0;
        }

        .category-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .category-btn:hover {
            background: rgba(102, 126, 234, 0.3);
            border-color: #00d4ff;
            transform: translateY(-2px);
        }

        .category-btn.active {
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            border-color: #00d4ff;
            color: #1a1a3e;
            font-weight: 700;
        }

        .active-filter-info {
            text-align: center;
            color: #00d4ff;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
            display: none;
        }

        .active-filter-info.show {
            display: block;
        }

        .blog-section {
            background: linear-gradient(135deg, #1a1a3e 0%, #16213e 100%);
            padding: 40px 0;
            min-height: 50vh;
        }

        .page-title {
            text-align: center;
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 40px;
        }

        .no-articles {
            text-align: center;
            color: #888;
            padding: 60px 20px;
            font-size: 16px;
        }
    </style>
</head>

<body>
<!-- ===== HEADER ===== -->
<header>
    <div class="container nav">
        <div class="left">
            <a href="index.php" class="logo">NextGen</a>
            <nav class="menu">
                <a href="index.php">Accueil</a>
                <a href="catalog.php">Produits</a>
                <a href="blog.php" class="active">Blog</a>
                <a href="apropos.php">À Propos</a>
            </nav>
        </div>
        <div>
            <a href="admin.php" style="color:#4f46e5;font-weight:700;">Administration</a>
        </div>
    </div>
</header>

<!-- ===== CATEGORY FILTER SECTION ===== -->
<section class="category-filter-section">
    <div class="container">
        <h2 class="category-filter-title">Filtrer par Catégorie</h2>

        <?php if (!empty($categories)): ?>
            <div class="category-filter-buttons" id="category-buttons">
                <!-- All Articles Button -->
                <button class="category-btn active" onclick="showAllArticles()">
                    Tous les Articles
                </button>

                <!-- Category Buttons -->
                <?php foreach ($categories as $category): ?>
                    <button class="category-btn" onclick="filterByCategory(<?php echo $category['id_categorie']; ?>, '<?php echo htmlspecialchars($category['nom']); ?>')">
                        <?php echo htmlspecialchars($category['nom']); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="active-filter-info" id="active-filter-info">
            Affichage de la catégorie: <span id="filter-category-name"></span>
        </div>
    </div>
</section>

<!-- ===== LISTE DES ARTICLES ===== -->
<section class="blog-section">
    <div class="container">
        <h1 class="page-title" id="articles-title">Articles</h1>

        <?php if (empty($articles) || isset($articles['error'])): ?>
            <div class="no-articles">
                <p>Aucun article disponible pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="blog-grid" id="blog-list">
                <?php foreach ($articles as $article): ?>
                    <article class="blog-card" onclick="openArticlePopup(<?php echo $article['id_article']; ?>)">
                        <div class="blog-image">
                            <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['titre']); ?>">
                        </div>
                        <div class="blog-content">
                            <h2><?php echo htmlspecialchars($article['titre']); ?></h2>
                            <p><?php echo htmlspecialchars($article['content']); ?></p>
                            <div class="article-meta">
                                <small>Publié le <?php echo htmlspecialchars($article['date_publication']); ?></small>
                                <span class="category-badge"><?php echo htmlspecialchars($article['categorie']); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===== POPUP ARTICLE COMPLET ===== -->
<div id="article-popup" class="article-popup">
    <div class="article-popup-content">
        <span class="close-article-popup" onclick="closeArticlePopup(event)">×</span>
        <div id="article-popup-content-inner"></div>
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="container">
        <p>© 2025 NextGen. Tous droits réservés.</p>
    </div>
</footer>

<script>
    const articlesData = <?php echo json_encode(array_column($articles ?? [], null, 'id_article')); ?>;
    const allArticles = <?php echo json_encode($articles ?? []); ?>;
    let currentCategoryId = null;

    function filterByCategory(categoryId, categoryName) {
        currentCategoryId = categoryId;

        // Update UI
        document.getElementById('articles-title').textContent = categoryName;
        document.getElementById('active-filter-info').classList.add('show');
        document.getElementById('filter-category-name').textContent = categoryName;

        // Update button states - find all buttons and update them
        const allButtons = document.querySelectorAll('.category-btn');
        allButtons.forEach((btn, index) => {
            // Skip first button (Tous les Articles) and check if this is the clicked category
            if (index > 0 && btn.textContent.trim() === categoryName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        // Fetch articles by category ID
        fetch(`blog.php?get_articles_by_category=1&id_categorie=${categoryId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.articles.length > 0) {
                    displayArticles(data.articles);
                } else {
                    document.getElementById('blog-list').innerHTML = '<div class="no-articles"><p>Aucun article trouvé pour cette catégorie.</p></div>';
                }
            })
            .catch(err => {
                console.error('Erreur:', err);
                document.getElementById('blog-list').innerHTML = '<div class="no-articles"><p>Erreur lors du chargement.</p></div>';
            });
    }

    function showAllArticles() {
        currentCategoryId = null;

        document.getElementById('articles-title').textContent = 'Articles';
        document.getElementById('active-filter-info').classList.remove('show');

        // Remove active from all buttons
        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Add active to first button (Tous les Articles)
        const allButtons = document.querySelectorAll('.category-btn');
        if (allButtons.length > 0) {
            allButtons[0].classList.add('active');
        }

        displayArticles(allArticles);
    }

    function displayArticles(articles) {
        const blogList = document.getElementById('blog-list');

        if (articles.length === 0) {
            blogList.innerHTML = '<div class="no-articles"><p>Aucun article disponible.</p></div>';
            return;
        }

        let html = '';
        articles.forEach(article => {
            html += `
                <article class="blog-card" onclick="openArticlePopup(${article.id_article})">
                    <div class="blog-image">
                        <img src="${escapeHtml(article.image)}" alt="${escapeHtml(article.titre)}">
                    </div>
                    <div class="blog-content">
                        <h2>${escapeHtml(article.titre)}</h2>
                        <p>${escapeHtml(article.content)}</p>
                        <div class="article-meta">
                            <small>Publié le ${escapeHtml(article.date_publication)}</small>
                            <span class="category-badge">${escapeHtml(article.categorie)}</span>
                        </div>
                    </div>
                </article>
            `;
        });

        blogList.innerHTML = html;
    }

    function openArticlePopup(articleId) {
        const article = articlesData[articleId];
        if (!article) {
            alert('Article non trouvé');
            return;
        }

        const popup = document.getElementById('article-popup');
        const content = document.getElementById('article-popup-content-inner');

        fetch(`blog.php?get_comments=1&id_article=${articleId}`)
            .then(res => res.json())
            .then(data => {
                const comments = data.comments || [];

                let html = `
                    <img src="${escapeHtml(article.image)}" alt="${escapeHtml(article.titre)}" class="article-popup-image">
                    <h2 class="article-popup-title">${escapeHtml(article.titre)}</h2>
                    <div class="article-popup-meta">
                        <small>Publié le ${escapeHtml(article.date_publication)}</small>
                        <span class="category-badge">${escapeHtml(article.categorie)}</span>
                    </div>
                    <div class="article-popup-text">
                        ${escapeHtml(article.full_content)}
                    </div>

                    <div class="article-popup-comments">
                        <h3>Commentaires (${comments.length})</h3>
                `;

                if (comments.length === 0) {
                    html += `<div class="no-comments">Aucun commentaire pour le moment. Soyez le premier à commenter !</div>`;
                } else {
                    comments.forEach(c => {
                        html += `
                            <div class="comment" data-comment-id="${c.id_commentaire}">
                                <img src="${escapeHtml(c.avatar)}" alt="Avatar" class="comment-avatar">
                                <div class="comment-content" style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 10px;">
                                        <div style="flex: 1;">
                                            <h4 class="comment-author">${escapeHtml(c.nom_visiteur)}</h4>
                                            <small class="comment-date">${escapeHtml(c.date_commentaire)}</small>
                                        </div>
                                        <div class="comment-menu">
                                            <button class="comment-menu-btn" onclick="toggleCommentMenu(event, ${c.id_commentaire})">⋮</button>
                                            <div class="comment-dropdown" data-menu="${c.id_commentaire}">
                                                <button class="comment-dropdown-item edit" onclick="editComment(event, ${c.id_commentaire})">
                                                    <i class="bi bi-pencil"></i> Modifier
                                                </button>
                                                <button class="comment-dropdown-item delete" onclick="deleteComment(event, ${c.id_commentaire}, ${articleId})">
                                                    <i class="bi bi-trash"></i> Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="comment-text" data-original-content="${escapeHtml(c.contenu)}">${escapeHtml(c.contenu)}</p>
                                </div>
                            </div>
                        `;
                    });
                }

                html += `
                    <form class="add-comment-form" onsubmit="addComment(event, ${articleId})">
                        <h4>Ajouter un commentaire</h4>

                        <div class="form-group">
                            <label>Votre nom</label>
                            <input type="text" name="nom_visiteur" class="comment-input" placeholder="Entrez votre nom..." style="background: #ffffff; color: #333; padding: 14px 16px;">
                            <span class="error-message" id="error-nom" style="display: none;"></span>
                        </div>

                        <div class="form-group">
                            <label>Votre commentaire</label>
                            <textarea name="contenu" class="comment-input comment-textarea" rows="4" placeholder="Écrivez votre commentaire..." style="background: #ffffff; color: #333; padding: 14px 16px;"></textarea>
                            <span class="error-message" id="error-contenu" style="display: none;"></span>
                        </div>

                        <button type="submit" class="submit-comment-btn">Publier le commentaire</button>
                    </form>
                    </div>
                `;

                content.innerHTML = html;
                popup.classList.add('active');
                document.body.style.overflow = 'hidden';
            })
            .catch(err => {
                console.error(err);
                content.innerHTML = `<p style="color:red;">Erreur de chargement des commentaires.</p>`;
                popup.classList.add('active');
            });
    }

    function closeArticlePopup(e) {
        if (e) e.stopPropagation();
        document.getElementById('article-popup').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function toggleCommentMenu(e, commentId) {
        e.stopPropagation();
        const dropdown = document.querySelector(`[data-menu="${commentId}"]`);
        document.querySelectorAll('.comment-dropdown.active').forEach(menu => {
            if (menu !== dropdown) menu.classList.remove('active');
        });
        dropdown.classList.toggle('active');
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.comment-menu')) {
            document.querySelectorAll('.comment-dropdown.active').forEach(menu => {
                menu.classList.remove('active');
            });
        }
    });

    function editComment(e, commentId) {
        e.stopPropagation();
        e.preventDefault();

        const commentDiv = document.querySelector(`[data-comment-id="${commentId}"]`);
        const commentText = commentDiv.querySelector('.comment-text');
        const originalContent = commentText.getAttribute('data-original-content');

        const editForm = document.createElement('div');
        editForm.className = 'edit-comment-form';
        editForm.innerHTML = `
            <textarea class="edit-comment-textarea" style="width: 100%; padding: 12px; border: 2px solid #00d4ff; border-radius: 8px; background: rgba(0, 212, 255, 0.08); color: #ffffff; font-family: 'Roboto', sans-serif; font-size: 0.95rem; resize: vertical; min-height: 80px; margin-bottom: 12px;">${originalContent}</textarea>
            <div style="display: flex; gap: 10px;">
                <button class="edit-save-btn" style="flex: 1; padding: 10px 16px; background: linear-gradient(135deg, #00d4ff 0%, #00a0cc 100%); color: #1a1a3e; border: none; border-radius: 6px; cursor: pointer; font-weight: 700; font-family: 'Montserrat', sans-serif; transition: all 0.3s ease;" data-comment-id="${commentId}">Enregistrer</button>
                <button class="edit-cancel-btn" style="flex: 1; padding: 10px 16px; background: rgba(255, 107, 157, 0.2); color: #ff6b9d; border: 2px solid #ff6b9d; border-radius: 6px; cursor: pointer; font-weight: 700; font-family: 'Montserrat', sans-serif; transition: all 0.3s ease;">Annuler</button>
            </div>
        `;

        const parentDiv = commentText.parentElement;
        commentText.style.display = 'none';
        parentDiv.insertBefore(editForm, commentText.nextSibling);

        document.querySelector(`[data-menu="${commentId}"]`).classList.remove('active');

        editForm.querySelector('.edit-save-btn').addEventListener('click', (event) => {
            const textarea = editForm.querySelector('.edit-comment-textarea');
            const newContent = textarea.value.trim();

            if (!newContent || newContent.length < 3 || newContent.length > 1000) {
                alert('Validation échouée');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'update_comment');
            formData.append('id_commentaire', commentId);
            formData.append('contenu', newContent);

            fetch('blog.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        commentText.textContent = newContent;
                        commentText.setAttribute('data-original-content', newContent);
                        commentText.style.display = 'block';
                        editForm.remove();
                    }
                });
        });

        editForm.querySelector('.edit-cancel-btn').addEventListener('click', () => {
            commentText.style.display = 'block';
            editForm.remove();
        });
    }

    function deleteComment(e, commentId, articleId) {
        e.stopPropagation();
        if (!confirm('Êtes-vous sûr?')) return;

        const formData = new FormData();
        formData.append('action', 'delete_comment');
        formData.append('id_commentaire', commentId);

        fetch('blog.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeArticlePopup();
                    openArticlePopup(articleId);
                }
            });
    }

    function showFieldErrors(form, errors) {
        form.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        form.querySelectorAll('.comment-input').forEach(input => {
            input.classList.remove('error');
        });

        errors.forEach(msg => {
            if (msg.includes('nom')) {
                const errorElement = form.querySelector('#error-nom');
                const inputElement = form.querySelector('input[name="nom_visiteur"]');
                if (errorElement && inputElement) {
                    errorElement.textContent = msg;
                    errorElement.style.display = 'block';
                    inputElement.classList.add('error');
                }
            }
            if (msg.includes('commentaire') || msg.includes('contenu')) {
                const errorElement = form.querySelector('#error-contenu');
                const inputElement = form.querySelector('textarea[name="contenu"]');
                if (errorElement && inputElement) {
                    errorElement.textContent = msg;
                    errorElement.style.display = 'block';
                    inputElement.classList.add('error');
                }
            }
        });
    }

    function addComment(e, articleId) {
        e.preventDefault();
        const form = e.target;

        form.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        form.querySelectorAll('.comment-input').forEach(input => input.classList.remove('error'));

        const formData = new FormData(form);
        formData.append('action', 'add_comment');
        formData.append('id_article', articleId);

        const nom_visiteur = formData.get('nom_visiteur').trim();
        const contenu = formData.get('contenu').trim();

        let clientErrors = [];

        if (!nom_visiteur || nom_visiteur.length < 2 || nom_visiteur.length > 100) {
            clientErrors.push('Nom invalide');
        }

        if (!contenu || contenu.length < 3 || contenu.length > 1000) {
            clientErrors.push('Commentaire invalide');
        }

        if (clientErrors.length) {
            showFieldErrors(form, clientErrors);
            return;
        }

        fetch('blog.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    form.reset();
                    closeArticlePopup();
                    openArticlePopup(articleId);
                } else {
                    // Map PHP validation errors to inline field messages under each label
                    if (Array.isArray(data.errors) && data.errors.length) {
                        showFieldErrors(form, data.errors);
                    } else if (data.message) {
                        // Fallback: show the message under the comment content
                        showFieldErrors(form, [String(data.message)]);
                    }
                }
            })
            .catch(() => {
                // Network/server error fallback
                showFieldErrors(form, ['Erreur de connexion au serveur']);
            });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    document.getElementById('article-popup').addEventListener('click', e => {
        if (e.target === e.currentTarget) closeArticlePopup();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeArticlePopup();
    });
</script>
</body>
</html>