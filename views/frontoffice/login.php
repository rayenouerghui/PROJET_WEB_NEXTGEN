<?php
// Démarrer la session et inclure les contrôleurs
session_start();
require_once '../controllers/AuthController.php';

$error = '';
if (isset($_GET['error'])) {
    $error = "Email ou mot de passe incorrect";
}
if (isset($_GET['success'])) {
    $success = "Inscription réussie! Connectez-vous.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>NextGen - Connexion</title>
    <style>
        /* VOTRE CSS EXISTANT */
        .error-message { color: #ff6b6b; text-align: center; margin: 10px 0; }
        .success-message { color: #51cf66; text-align: center; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2 style="text-align: center; margin-bottom: 30px;">Connexion</h2>
        
        <?php if ($error): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success-message"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST" action="../controllers/AuthController.php">
            <input type="hidden" name="action" value="login">
            
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="text" id="email" name="email" placeholder="votre@email.com">
                <div class="error-message" id="emailError">Veuillez entrer un email valide</div>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" placeholder="Votre mot de passe">
                <div class="error-message" id="passwordError">Le mot de passe doit contenir au moins 6 caractères</div>
            </div>
            
            <button type="submit" class="btn">Se connecter</button>
        </form>

        <!-- VOTRE JAVASCRIPT EXISTANT POUR LES CONTRÔLES -->
        <script>
            // VOTRE CODE JS EXISTANT POUR LA VALIDATION
        </script>
    </div>
</body>
</html>