<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <!-- Cart Section -->
    <section class="cart-section">
        <div class="container">
            <h1 class="page-title">Mon Panier</h1>
            
            <div class="cart-content">
                <div class="cart-items" id="cartItems">
                    <!-- Game in cart - Only image -->
                    <div class="cart-images-grid">
                        <div class="cart-image-item">
                            <img src="https://via.placeholder.com/300x200?text=Jungle+Quest" alt="Jungle Quest">
                            <a href="cart.php?remove=1" class="remove-cart-item" title="Retirer">×</a>
                        </div>
                    </div>
                </div>
                
                <div class="cart-summary">
                    <h3>Résumé de la Commande</h3>
                    <div class="summary-row">
                        <span>Sous-total:</span>
                        <span>29,99 €</span>
                    </div>
                    <div class="summary-row">
                        <span>Don (20%):</span>
                        <span class="donation-highlight">6,00 €</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span>29,99 €</span>
                    </div>
                    <div class="donation-info">
                        <p>💝 20% de votre achat sera reversé à la Maison des Orphelins</p>
                    </div>
                    <a href="checkout.php" class="btn btn-primary btn-block">Procéder au Paiement</a>
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

