<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <!-- Checkout Section -->
    <section class="checkout-section">
        <div class="container">
            <h1 class="page-title">Finaliser la Commande</h1>
            
            <div class="checkout-content">
                <div class="checkout-form">
                    <h2>Informations de Livraison</h2>
                    <form id="checkoutForm">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" id="email" required>
                        </div>
                        <div class="form-group">
                            <label>Prénom *</label>
                            <input type="text" id="firstName" required>
                        </div>
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" id="lastName" required>
                        </div>
                        
                        <h2>Méthode de Paiement</h2>
                        <div class="payment-methods">
                            <div class="payment-option">
                                <input type="radio" name="payment" id="card" value="card" checked>
                                <label for="card">Carte Bancaire</label>
                            </div>
                            <div class="payment-option">
                                <input type="radio" name="payment" id="paypal" value="paypal">
                                <label for="paypal">PayPal</label>
                            </div>
                        </div>
                        
                        <div id="cardDetails" class="card-details">
                            <div class="form-group">
                                <label>Numéro de Carte *</label>
                                <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Date d'Expiration *</label>
                                    <input type="text" id="expiry" placeholder="MM/AA">
                                </div>
                                <div class="form-group">
                                    <label>CVV *</label>
                                    <input type="text" id="cvv" placeholder="123">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Confirmer le Paiement</button>
                    </form>
                </div>
                
                <div class="order-summary">
                    <h2>Résumé de la Commande</h2>
                    <div id="orderItems">
                        <!-- Order items will be loaded here -->
                    </div>
                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Sous-total:</span>
                            <span id="orderSubtotal">0,00 €</span>
                        </div>
                        <div class="summary-row">
                            <span>Don (20%):</span>
                            <span id="orderDonation" class="donation-highlight">0,00 €</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span id="orderTotal">0,00 €</span>
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

