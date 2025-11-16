<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <h1 class="page-title">Contactez-Nous</h1>
            
            <div class="contact-content">
                <div class="contact-form-container">
                    <h2>Envoyez-nous un Message</h2>
                    <form id="contactForm">
                        <div class="form-group">
                            <label>Nom *</label>
                            <input type="text" id="contactName" required>
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" id="contactEmail" required>
                        </div>
                        <div class="form-group">
                            <label>Sujet *</label>
                            <select id="contactSubject" required>
                                <option value="">Sélectionnez un sujet</option>
                                <option value="support">Support Technique</option>
                                <option value="donation">Question sur les Dons</option>
                                <option value="order">Commande</option>
                                <option value="other">Autre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Message *</label>
                            <textarea id="contactMessage" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </form>
                </div>
                
                <div class="contact-info">
                    <h2>Informations de Contact</h2>
                    <div class="info-item">
                        <h3>Email</h3>
                        <p>contact@nextgen.com</p>
                    </div>
                    <div class="info-item">
                        <h3>Support</h3>
                        <p>support@nextgen.com</p>
                    </div>
                    <div class="info-item">
                        <h3>Heures d'Ouverture</h3>
                        <p>Lundi - Vendredi: 9h - 18h</p>
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

