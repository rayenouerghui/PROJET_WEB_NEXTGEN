<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog - NextGen</title>

    <!-- Liens CSS -->
    <link rel="stylesheet" href="../../public/css/style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="css/blog.css" />

    <!-- Polices -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
</head>

<body>
<?php include __DIR__ . "/_partials/header.php"; ?>

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
<!-- Exemple pour Article 1 -->
<section id="article-1" class="article-detail">
    <div class="article-full">
        <span class="back-btn" onclick="goBack()">⬅ Retour au blog</span>
        <img src="https://via.placeholder.com/800x400/6B5BFF/ffffff?text=Gaming+Power" alt="">
        <h2>My secret powers will only cost you twenty dollars.</h2>
        <p>Think you can be a templines ninja? Ninja were mercenaries hired by many lords...</p>
        <p>We have created a game called templines. In the game you will have to find the most dangerous ninja fighter to complete the mission.</p>

        <div class="comments-section">
            <h3>Commentaires</h3>
            <div class="comment">
                <img src="https://i.pravatar.cc/60?img=12" alt="">
                <div class="comment-content">
                    <h4>Emma Stone</h4>
                    <small>12 days ago</small>
                    <p>If you really wanted to do that, then why wouldn't you do that? Instead you do this.</p>
                </div>
            </div>
            <div class="comment">
                <img src="https://i.pravatar.cc/60?img=5" alt="">
                <div class="comment-content">
                    <h4>John Doe</h4>
                    <small>15 days ago</small>
                    <p>This game looks so exciting! Can't wait to try it with my friends.</p>
                </div>
            </div>

            <!-- Ajouter un commentaire -->
            <form class="add-comment">
                <input type="text" placeholder="Votre nom..." required>
                <textarea rows="3" placeholder="Écrire un commentaire..." required></textarea>
                <button type="submit">Envoyer</button>
            </form>
        </div>
    </div>
</section>

<!-- Répète la même structure pour les autres articles (2, 3, 4) -->
<!-- Je raccourcis pour la lisibilité -->
<section id="article-2" class="article-detail">
    <div class="article-full">
        <span class="back-btn" onclick="goBack()">⬅ Retour au blog</span>
        <img src="https://via.placeholder.com/800x400/00B8A9/ffffff?text=VR+Gaming" alt="">
        <h2>VR gaming is changing the future of play.</h2>
        <p>The future of gaming lies in immersion...</p>

        <div class="comments-section">
            <h3>Commentaires</h3>
            <div class="comment">
                <img src="https://i.pravatar.cc/60?img=20" alt="">
                <div class="comment-content">
                    <h4>Lucas Martin</h4>
                    <small>10 days ago</small>
                    <p>Virtual reality is the next step for true immersion. Excellent article!</p>
                </div>
            </div>

            <form class="add-comment">
                <input type="text" placeholder="Votre nom..." required>
                <textarea rows="3" placeholder="Écrire un commentaire..." required></textarea>
                <button type="submit">Envoyer</button>
            </form>
        </div>
    </div>
</section>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="container">
        <p>&copy; 2025 NextGen. Tous droits réservés.</p>
    </div>
</footer>

<!-- ===== SCRIPT JS ===== -->
<script>
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
</script>
</body>
</html>

