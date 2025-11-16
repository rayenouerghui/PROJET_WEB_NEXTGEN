<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="container">
            <h1 class="page-title">Questions Fréquentes</h1>
            
            <div class="faq-content">
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Comment fonctionne le système de dons ?</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>20% de chaque achat est automatiquement reversé à la Maison des Orphelins. Vous recevrez un reçu par email confirmant votre contribution.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Comment puis-je accéder à mes jeux ?</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Après l'achat, vos jeux sont disponibles instantanément dans votre compte. Vous pouvez y accéder depuis n'importe quel appareil sans téléchargement.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Puis-je retourner un jeu ?</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Oui, vous pouvez demander un retour dans les 14 jours suivant l'achat. Consultez notre page Retours pour plus d'informations.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Comment fonctionne le chatbot ?</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Notre chatbot IA peut vous aider avec vos questions sur les achats, les dons et le fonctionnement du site. Tapez "aide" pour un manuel rapide.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <h3>Comment sont sélectionnés les partenaires ONG ?</h3>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Toutes nos ONG partenaires sont vérifiées légalement et financièrement pour garantir la bonne utilisation des fonds collectés.</p>
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

