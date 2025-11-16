<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À Propos - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <!-- About Section -->
    <section class="about-section">
        <div class="container">
            <h1 class="page-title">À Propos de NextGen</h1>
            
            <div class="about-content">
                <div class="about-intro">
                    <h2>Notre Mission</h2>
                    <p>NextGen est une plateforme de vente de jeux vidéo où chaque achat contribue à une cause humanitaire. Notre objectif est d'associer le plaisir du jeu à la solidarité et à l'espoir, transformant chaque achat en un acte de générosité.</p>
                </div>
                
                <div class="about-values">
                    <h2>Nos Valeurs</h2>
                    <div class="values-grid">
                        <div class="value-card">
                            <h3>Impact Social Local</h3>
                            <p>Collaboration avec des ONG locales vérifiées pour un impact mesurable</p>
                        </div>
                        <div class="value-card">
                            <h3>Transparence Totale</h3>
                            <p>Suivi en temps réel des dons et reçus automatiques pour chaque contribution</p>
                        </div>
                        <div class="value-card">
                            <h3>Durabilité</h3>
                            <p>Modèle 100% digital, écologique, sans support matériel</p>
                        </div>
                        <div class="value-card">
                            <h3>Accessibilité</h3>
                            <p>Jeux disponibles instantanément depuis n'importe quel appareil</p>
                        </div>
                    </div>
                </div>
                
                <div class="about-innovation">
                    <h2>Innovation & IA</h2>
                    <div class="innovation-features">
                        <div class="innovation-card">
                            <h3>🤖 Chatbot d'Assistance</h3>
                            <p>Aide instantanée pour vos questions sur les achats, dons et fonctionnement du site</p>
                        </div>
                        <div class="innovation-card">
                            <h3>🎮 Recommandations Intelligentes</h3>
                            <p>Système de suggestion basé sur vos préférences et achats précédents</p>
                        </div>
                    </div>
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

