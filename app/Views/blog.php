<?php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog - NextGen</title>

    <!-- Liens CSS -->
    <link rel="stylesheet" href="../../public/css/common.css" />
    <link rel="stylesheet" href="../../../public/css/frontoffice.css" />
    <link rel="stylesheet" href="../../../public/css/blog.css" />

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
                    <img src="../../../public/images/logo.png" alt="NextGen Logo" class="logo-img">
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
                    <li><a href="blog.html">Blog</a></li>
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
<section class="blog-section" id="blog-list">
    <div class="container">
        <h1 class="page-title">Nos Articles</h1>
        <div class="blog-grid">
            <article class="blog-card" onclick="openArticle(1)">
                <div class="blog-image">
                    <img src="https://via.placeholder.com/600x350/6B5BFF/ffffff?text=Gaming+Power" alt="">
                </div>
                <div class="blog-content">
                    <h2>My secret powers will only cost you twenty dollars.</h2>
                    <p>Découvrez comment le jeu peut devenir un levier d'impact social positif et solidaire.</p>
                </div>
            </article>

            <article class="blog-card" onclick="openArticle(2)">
                <div class="blog-image">
                    <img src="https://via.placeholder.com/600x350/00B8A9/ffffff?text=VR+Gaming" alt="">
                </div>
                <div class="blog-content">
                    <h2>VR gaming is changing the future of play.</h2>
                    <p>Plongez dans le monde fascinant de la réalité virtuelle et découvrez les nouvelles tendances.</p>
                </div>
            </article>

            <article class="blog-card" onclick="openArticle(3)">
                <div class="blog-image">
                    <img src="https://via.placeholder.com/600x350/FF7A5A/ffffff?text=Pro+Gamer" alt="">
                </div>
                <div class="blog-content">
                    <h2>You're not really a gamer until you've played our game.</h2>
                    <p>Les jeux vidéo sont bien plus qu'un divertissement : ce sont des histoires à vivre.</p>
                </div>
            </article>

            <article class="blog-card" onclick="openArticle(4)">
                <div class="blog-image">
                    <img src="https://via.placeholder.com/600x350/3A86FF/ffffff?text=NextGen+Community" alt="">
                </div>
                <div class="blog-content">
                    <h2>This is your one-stop shop for all things gaming.</h2>
                    <p>Bienvenue dans la communauté NextGen : là où la passion rencontre la solidarité.</p>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- ===== ARTICLES DÉTAILLÉS ===== -->
<section id="article-1" class="article-detail">
    <div class="article-full">
        <span class="back-btn" onclick="goBack()">⬅ Retour au blog</span>
        <img src="https://via.placeholder.com/800x400/6B5BFF/ffffff?text=Gaming+Power" alt="">
        <h2>My secret powers will only cost you twenty dollars.</h2>
        <p>Think you can be a templines ninja? Ninja were mercenaries hired by many lords...</p>
        <p>We have created a game called templines. In the game you will have to find the most dangerous ninja fighter to complete the mission.</p>

        <button class="view-comments-btn" onclick="openComments(1)">Voir les commentaires</button>
    </div>
</section>

<section id="article-2" class="article-detail">
    <div class="article-full">
        <span class="back-btn" onclick="goBack()">⬅ Retour au blog</span>
        <img src="https://via.placeholder.com/800x400/00B8A9/ffffff?text=VR+Gaming" alt="">
        <h2>VR gaming is changing the future of play.</h2>
        <p>The future of gaming lies in immersion...</p>

        <button class="view-comments-btn" onclick="openComments(2)">Voir les commentaires</button>
    </div>
</section>

<section id="article-3" class="article-detail">
    <div class="article-full">
        <span class="back-btn" onclick="goBack()">⬅ Retour au blog</span>
        <img src="https://via.placeholder.com/800x400/FF7A5A/ffffff?text=Pro+Gamer" alt="">
        <h2>You're not really a gamer until you've played our game.</h2>
        <p>Les jeux vidéo sont bien plus qu'un divertissement : ce sont des histoires à vivre.</p>

        <button class="view-comments-btn" onclick="openComments(3)">Voir les commentaires</button>
    </div>
</section>

<section id="article-4" class="article-detail">
    <div class="article-full">
        <span class="back-btn" onclick="goBack()">⬅ Retour au blog</span>
        <img src="https://via.placeholder.com/800x400/3A86FF/ffffff?text=NextGen+Community" alt="">
        <h2>This is your one-stop shop for all things gaming.</h2>
        <p>Bienvenue dans la communauté NextGen : là où la passion rencontre la solidarité.</p>

        <button class="view-comments-btn" onclick="openComments(4)">Voir les commentaires</button>
    </div>
</section>

<!-- ===== POPUP DES COMMENTAIRES ===== -->
<div id="comments-popup" class="comments-popup">
    <div class="popup-content">
        <span class="close-popup" onclick="closeComments()">&times;</span>
        <div id="popup-content-inner">
            <!-- Le contenu des commentaires sera inséré ici dynamiquement -->
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
    // Données des commentaires pour chaque article
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

    function openArticle(num) {
        document.getElementById('blog-list').style.display = 'none';
        document.getElementById('article-' + num).classList.add('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function goBack() {
        document.querySelectorAll('.article-detail').forEach(el => el.classList.remove('active'));
        document.getElementById('blog-list').style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function openComments(articleId) {
        const popup = document.getElementById('comments-popup');
        const content = document.getElementById('popup-content-inner');

        // Récupérer les commentaires pour cet article
        const comments = commentsData[articleId] || [];

        // Générer le HTML des commentaires
        let commentsHTML = `
            <h3>Commentaires</h3>
            <div class="comments-section">
        `;

        if (comments.length === 0) {
            commentsHTML += `<p>Aucun commentaire pour le moment. Soyez le premier à commenter !</p>`;
        } else {
            comments.forEach(comment => {
                commentsHTML += `
                    <div class="comment">
                        <img src="${comment.avatar}" alt="${comment.name}">
                        <div class="comment-content">
                            <h4>${comment.name}</h4>
                            <small>${comment.date}</small>
                            <p>${comment.text}</p>
                        </div>
                    </div>
                `;
            });
        }

        // Ajouter le formulaire de commentaire
        commentsHTML += `
            <form class="add-comment" onsubmit="addComment(event, ${articleId})">
                <input type="text" placeholder="Votre nom..." required>
                <textarea rows="3" placeholder="Écrire un commentaire..." required></textarea>
                <button type="submit">Envoyer</button>
            </form>
        </div>
        `;

        content.innerHTML = commentsHTML;
        popup.classList.add('active');
    }

    function closeComments() {
        document.getElementById('comments-popup').classList.remove('active');
    }

    function addComment(event, articleId) {
        event.preventDefault();

        const form = event.target;
        const nameInput = form.querySelector('input[type="text"]');
        const textInput = form.querySelector('textarea');

        const name = nameInput.value;
        const text = textInput.value;

        if (!name || !text) return;

        // Ajouter le nouveau commentaire aux données
        if (!commentsData[articleId]) {
            commentsData[articleId] = [];
        }

        commentsData[articleId].unshift({
            name: name,
            date: "À l'instant",
            text: text,
            avatar: `https://i.pravatar.cc/60?img=${Math.floor(Math.random() * 70)}`
        });

        // Fermer et rouvrir la popup pour afficher le nouveau commentaire
        closeComments();
        openComments(articleId);

        // Réinitialiser le formulaire
        form.reset();
    }

    // Fermer la popup en cliquant en dehors
    document.getElementById('comments-popup').addEventListener('click', function(e) {
        if (e.target === this) {
            closeComments();
        }
    });
</script>
</body>
</html>