<?php
// Chemin corrigé - remonter de 3 niveaux pour atteindre app/Controllers/
require_once __DIR__ . '/../../../app/Controllers/BlogController.php';

// Initialiser le contrôleur
$blogController = new BlogController();

// Récupérer tous les articles
$articles = $blogController->index();
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
                    🛒
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

        <?php if (empty($articles)): ?>
            <div class="no-articles">
                <p>Aucun article disponible pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="blog-grid" id="blog-list">
                <?php foreach ($articles as $article): ?>
                    <article class="blog-card" onclick="openArticlePopup(<?php echo $article['id_article']; ?>)">
                        <div class="blog-image">
                            <img src="<?php echo $article['image']; ?>" alt="<?php echo $article['titre']; ?>">
                        </div>
                        <div class="blog-content">
                            <h2><?php echo $article['titre']; ?></h2>
                            <p><?php echo $article['content']; ?></p>
                            <div class="article-meta">
                                <small>Publié le <?php echo $article['date_publication']; ?></small>
                                <span class="category-badge"><?php echo $article['categorie']; ?></span>
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
        <span class="close-article-popup" onclick="closeArticlePopup(event)">&times;</span>
        <div id="article-popup-content-inner">
            <!-- Le contenu de l'article et des commentaires sera inséré ici dynamiquement -->
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
    // Données des commentaires (simulées)
    const commentsData = {
        1: [
            { name: "Emma Stone", date: "12 days ago", text: "If you really wanted to do that, then why wouldn't you do that? Instead you do this.", avatar: "https://i.pravatar.cc/60?img=12" },
            { name: "John Doe", date: "15 days ago", text: "This game looks so exciting! Can't wait to try it with my friends.", avatar: "https://i.pravatar.cc/60?img=5" }
        ],
        2: [
            { name: "Lucas Martin", date: "10 days ago", text: "Virtual reality is the next step for true immersion. Excellent article!", avatar: "https://i.pravatar.cc/60?img=20" }
        ],
        3: [
            { name: "Sophie Laurent", date: "5 days ago", text: "J'ai adoré cet article ! Les jeux vidéo sont effectivement bien plus qu'un simple divertissement.", avatar: "https://i.pravatar.cc/60?img=32" }
        ],
        4: [
            { name: "Thomas Bernard", date: "3 days ago", text: "NextGen a vraiment créé une communauté incroyable. Félicitations !", avatar: "https://i.pravatar.cc/60?img=45" }
        ]
    };

    // Fonction pour ouvrir la popup avec les données de l'article
    function openArticlePopup(articleId) {
        const articlesData = {
        <?php foreach ($articles as $article): ?>
        <?php echo $article['id_article']; ?>: {
            title: "<?php echo addslashes($article['titre']); ?>",
                image: "<?php echo $article['image']; ?>",
                content: `<?php echo addslashes(nl2br($article['full_content'] ?? $article['content'])); ?>`,
                date_publication: "<?php echo $article['date_publication']; ?>",
                categorie: "<?php echo $article['categorie']; ?>"
        },
        <?php endforeach; ?>
    };

        const article = articlesData[articleId];
        if (!article) {
            alert('Article non trouvé');
            return;
        }

        const popup = document.getElementById('article-popup');
        const content = document.getElementById('article-popup-content-inner');

        // Récupérer les commentaires pour cet article
        const comments = commentsData[articleId] || [];

        // Générer le HTML de l'article et des commentaires
        let articleHTML = `
            <img src="${article.image}" alt="${article.title}" class="article-popup-image">
            <h2 class="article-popup-title">${article.title}</h2>
            <div class="article-popup-meta">
                <small>Publié le ${article.date_publication}</small>
                <span class="category-badge">${article.categorie}</span>
            </div>
            <div class="article-popup-text">
                ${article.content}
            </div>

            <div class="article-popup-comments">
                <h3>Commentaires (${comments.length})</h3>
        `;

        // Afficher les commentaires existants
        if (comments.length === 0) {
            articleHTML += `<div class="no-comments">Aucun commentaire pour le moment. Soyez le premier à commenter !</div>`;
        } else {
            comments.forEach(comment => {
                articleHTML += `
                    <div class="comment">
                        <img src="${comment.avatar}" alt="${comment.name}" class="comment-avatar">
                        <div class="comment-content">
                            <h4 class="comment-author">${comment.name}</h4>
                            <small class="comment-date">${comment.date}</small>
                            <p class="comment-text">${comment.text}</p>
                        </div>
                    </div>
                `;
            });
        }

        // Ajouter le formulaire d'ajout de commentaire
        articleHTML += `
                <form class="add-comment-form" onsubmit="addComment(event, ${articleId})">
                    <h4>Ajouter un commentaire</h4>
                    <input type="text" class="comment-input" placeholder="Votre nom..." required>
                    <textarea class="comment-input comment-textarea" rows="4" placeholder="Écrire votre commentaire..." required></textarea>
                    <button type="submit" class="submit-comment-btn">Publier le commentaire</button>
                </form>
            </div>
        `;

        content.innerHTML = articleHTML;
        popup.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeArticlePopup(event) {
        if (event) {
            event.stopPropagation();
        }
        document.getElementById('article-popup').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    // Fonction pour ajouter un commentaire
    function addComment(event, articleId) {
        event.preventDefault();

        const form = event.target;
        const nameInput = form.querySelector('input[type="text"]');
        const textInput = form.querySelector('textarea');

        const name = nameInput.value;
        const text = textInput.value;

        if (!name || !text) {
            alert('Veuillez remplir tous les champs');
            return;
        }

        // Ajouter le nouveau commentaire aux données
        if (!commentsData[articleId]) {
            commentsData[articleId] = [];
        }

        commentsData[articleId].push({
            name: name,
            date: "À l'instant",
            text: text,
            avatar: `https://i.pravatar.cc/60?img=${Math.floor(Math.random() * 70)}`
        });

        // Fermer et rouvrir la popup pour afficher le nouveau commentaire
        closeArticlePopup();
        openArticlePopup(articleId);

        alert('Votre commentaire a été ajouté avec succès !');
    }

    // Fermer la popup en cliquant en dehors
    document.getElementById('article-popup').addEventListener('click', function(e) {
        if (e.target === this) {
            closeArticlePopup();
        }
    });

    // Fermer la popup avec la touche Échap
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeArticlePopup();
        }
    });
</script>

<style>
    /* Ajouter un curseur pointer sur les cartes d'articles */
    .blog-card {
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .blog-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Animation de pulsation subtile sur hover */
    .blog-card:active {
        transform: translateY(-5px);
    }

    /* Supprimer la section article-actions */
    .article-actions {
        display: none;
    }
</style>
</body>
</html>