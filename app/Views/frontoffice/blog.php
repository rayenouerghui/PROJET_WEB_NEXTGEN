<?php
/**
 * Vue : blog.php
 * Point d'entrée + Affichage des articles avec commentaires
 */

require_once __DIR__ . '/../../../app/Controllers/BlogController.php';
require_once __DIR__ . '/../../../app/Controllers/CommentaireController.php';

// Initialiser les contrôleurs
$blogController = new BlogController();
$commentController = new CommentController();

// Récupérer tous les articles
$articles = $blogController->index();

// Variables pour les erreurs de formulaire
$errors = [];
$field_errors = [
        'nom_visiteur' => '',
        'contenu' => ''
];
$old_values = [
        'nom_visiteur' => '',
        'contenu' => ''
];

// Traitement des actions pour les commentaires
$action = $_POST['action'] ?? '';

// ===== CRÉATION DE COMMENTAIRE =====
if ($action === 'add_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_article = (int)($_POST['id_article'] ?? 0);
    $nom_visiteur = trim($_POST['nom_visiteur'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');

    // Sauvegarder les anciennes valeurs pour la réaffichage
    $old_values['nom_visiteur'] = htmlspecialchars($nom_visiteur);
    $old_values['contenu'] = htmlspecialchars($contenu);

    $result = $commentController->create($id_article, $nom_visiteur, $contenu);

    // Si c'est une requête AJAX, retourner le résultat en JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } else {
        // Si ce n'est pas AJAX, traiter les erreurs pour l'affichage PHP
        if (!$result['success']) {
            $errors = $result['errors'] ?? [];

            // Organiser les erreurs par champ
            foreach ($errors as $error) {
                if (strpos(strtolower($error), 'nom') !== false) {
                    $field_errors['nom_visiteur'] = $error;
                } elseif (strpos(strtolower($error), 'commentaire') !== false || strpos(strtolower($error), 'contenu') !== false) {
                    $field_errors['contenu'] = $error;
                }
            }
        }
    }
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog - NextGen</title>

    <!-- Liens CSS -->
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/style.css" />
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/frontoffice.css" />
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/blog.css" />

    <!-- Polices -->
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
    </style>
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
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="catalog.html">Jeux</a></li>
                    <li><a href="about.html">À Propos</a></li>
                    <li><a href="donations.html">Nos Dons</a></li>
                    <li><a href="returns.html">Retours et Réclamations</a></li>
                    <li><a href="blog.php">Blog</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="cart.html" class="cart-icon" title="Panier">
                    <span class="cart-count">0</span>
                </a>
                <a href="account.html" class="account-icon">Mon Compte</a>
            </div>
        </div>
    </div>
</header>

<!-- ===== LISTE DES ARTICLES ===== -->
<section class="blog-section">
    <div class="container">
        <h1 class="page-title">Nos Articles</h1>

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
        <div id="article-popup-content-inner">
            <!-- Contenu chargé dynamiquement -->
        </div>
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="container">
        <p>© 2025 NextGen. Tous droits réservés.</p>
    </div>
</footer>

<script>
    // Données des articles depuis PHP
    const articlesData = <?php echo json_encode(array_column($articles ?? [], null, 'id_article')); ?>;

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
                            <div class="comment">
                                <img src="${escapeHtml(c.avatar)}" alt="Avatar" class="comment-avatar">
                                <div class="comment-content">
                                    <h4 class="comment-author">${escapeHtml(c.nom_visiteur)}</h4>
                                    <small class="comment-date">${escapeHtml(c.date_commentaire)}</small>
                                    <p class="comment-text">${escapeHtml(c.contenu)}</p>
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
                            <input type="text" name="nom_visiteur" class="comment-input" placeholder="Entrez votre nom...">
                            <span class="error-message" id="error-nom" style="display: none;"></span>
                        </div>

                        <div class="form-group">
                            <label>Votre commentaire</label>
                            <textarea name="contenu" class="comment-input comment-textarea" rows="4" placeholder="Écrivez votre commentaire..."></textarea>
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

    // Fonction pour afficher les erreurs sous les champs (comme dans le dashboard)
    function showFieldErrors(form, errors) {
        // Réinitialiser tous les messages d'erreur et bordures
        const errorElements = form.querySelectorAll('.error-message');
        errorElements.forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        const inputs = form.querySelectorAll('.comment-input');
        inputs.forEach(input => {
            input.classList.remove('error');
        });

        // Afficher les erreurs spécifiques sous chaque champ
        errors.forEach(msg => {
            if (msg.includes('nom') || msg.includes('Nom')) {
                const errorElement = form.querySelector('#error-nom');
                const inputElement = form.querySelector('input[name="nom_visiteur"]');

                if (errorElement && inputElement) {
                    errorElement.textContent = msg;
                    errorElement.style.display = 'block';
                    inputElement.classList.add('error');
                }
            }
            if (msg.includes('commentaire') || msg.includes('contenu') || msg.includes('Commentaire')) {
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

    // Ajouter un commentaire avec affichage des erreurs dans le formulaire
    function addComment(e, articleId) {
        e.preventDefault();
        const form = e.target;

        // Réinitialiser les erreurs
        const errorElements = form.querySelectorAll('.error-message');
        errorElements.forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        const inputs = form.querySelectorAll('.comment-input');
        inputs.forEach(input => input.classList.remove('error'));

        const formData = new FormData(form);
        formData.append('action', 'add_comment');
        formData.append('id_article', articleId);

        // Récupérer les valeurs pour la validation côté client
        const nom_visiteur = formData.get('nom_visiteur').trim();
        const contenu = formData.get('contenu').trim();

        // Validation côté client basique
        let hasErrors = false;
        let clientErrors = [];

        if (!nom_visiteur) {
            clientErrors.push('Le nom est obligatoire');
            hasErrors = true;
        } else if (nom_visiteur.length < 2) {
            clientErrors.push('Le nom doit contenir au moins 2 caractères');
            hasErrors = true;
        } else if (nom_visiteur.length > 100) {
            clientErrors.push('Le nom ne doit pas dépasser 100 caractères');
            hasErrors = true;
        }

        if (!contenu) {
            clientErrors.push('Le commentaire ne peut pas être vide');
            hasErrors = true;
        } else if (contenu.length < 3) {
            clientErrors.push('Le commentaire doit contenir au moins 3 caractères');
            hasErrors = true;
        } else if (contenu.length > 1000) {
            clientErrors.push('Le commentaire ne doit pas dépasser 1000 caractères');
            hasErrors = true;
        }

        // Si erreurs côté client, les afficher directement
        if (hasErrors) {
            showFieldErrors(form, clientErrors);
            return;
        }

        // Envoyer la requête au serveur
        fetch('blog.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Succès : vider le formulaire et recharger l'article
                    form.reset();
                    closeArticlePopup();
                    openArticlePopup(articleId);
                } else if (data.errors && Array.isArray(data.errors)) {
                    // Afficher les erreurs de validation du serveur sous chaque champ
                    showFieldErrors(form, data.errors);
                } else {
                    // Erreur générale
                    showFieldErrors(form, [data.message || 'Une erreur est survenue']);
                }
            })
            .catch(() => {
                showFieldErrors(form, ['Erreur réseau. Veuillez réessayer.']);
            });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Fermer en cliquant dehors ou avec Échap
    document.getElementById('article-popup').addEventListener('click', e => {
        if (e.target === e.currentTarget) closeArticlePopup();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeArticlePopup();
    });
</script>
</body>
</html>