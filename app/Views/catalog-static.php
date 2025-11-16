<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <!-- Catalog Section -->
    <section class="catalog-section">
        <div class="container">
            <h1 class="page-title">Catalogue de Jeux</h1>
            
            <!-- Search and Sort -->
            <div class="catalog-filters">
                <div class="filter-group" style="flex: 2;">
                    <input type="text" id="searchInput" placeholder="Rechercher un jeu...">
                </div>
                <div class="filter-group">
                    <label>Trier par:</label>
                    <select id="sortSelect">
                        <option value="name">Nom (A-Z)</option>
                        <option value="price-asc">Prix (Croissant)</option>
                        <option value="price-desc">Prix (Décroissant)</option>
                        <option value="category">Catégorie</option>
                    </select>
                </div>
            </div>

            <!-- Games Grid -->
            <div class="games-grid" id="gamesGrid">
                <!-- Games will be loaded dynamically -->
            </div>

            <!-- Pagination -->
            <div class="pagination" id="pagination">
                <!-- Pagination will be generated dynamically -->
            </div>
        </div>
    </section>

    <!-- AI Recommendations -->
    <section class="recommendations-section">
        <div class="container">
            <h2 class="section-title">Recommandations pour Vous</h2>
            <div class="games-grid" id="recommendedGames">
                <!-- AI recommendations will be loaded here -->
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>NextGen</h3>
                    <p>Plateforme de vente de jeux vidéo à vocation solidaire</p>
                </div>
                <div class="footer-section">
                    <h4>Liens Utiles</h4>
                    <ul>
                        <li><a href="catalog.php">Catalogue</a></li>
                        <li><a href="about.php">À Propos</a></li>
                        <li><a href="donations.php">Nos Dons</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="returns.php">Retours</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 NextGen. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

</body>
</html>

