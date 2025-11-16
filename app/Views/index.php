<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NextGen - Accueil</title>
    <link rel="stylesheet" href="../../public/css/common.css">
    <link rel="stylesheet" href="../../public/css/frontoffice.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Bienvenue sur NextGen</h1>
                <p>Jouer pour Espérer</p>
                <div class="hero-buttons">
                    <a href="catalog.php" class="btn btn-primary btn-large">Voir le Catalogue</a>
                    <a href="about.html" class="btn btn-secondary btn-large">En Savoir Plus</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Section -->
    <section class="impact-section">
        <div class="container">
            <h2 class="section-title">Notre Impact</h2>
            <div class="impact-stats">
                <div class="stat-card">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Jeux Disponibles</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">0</div>
                    <div class="stat-label">Utilisateurs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">0 TND</div>
                    <div class="stat-label">Dons Collectés</div>
                </div>
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
                        <li><a href="about.html">À Propos</a></li>
                        <li><a href="donations.html">Nos Dons</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="contact.html">Contact</a></li>
                        <li><a href="returns.html">Retours</a></li>
                        <li><a href="faq.html">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 NextGen. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script>
        // Redirect buttons to login if not logged in
        document.addEventListener('DOMContentLoaded', function() {
            const currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
            
            // Add click handlers to buttons that require login
            document.querySelectorAll('a[href="catalog.php"]').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!currentUserId) {
                        e.preventDefault();
                        window.location.href = 'login.php';
                    }
                });
            });
        });
    </script>
    <style>
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }
        .btn-large {
            padding: 15px 30px;
            font-size: 18px;
        }
    </style>
</body>
</html>

