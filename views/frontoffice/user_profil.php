<?php
session_start();
require_once '../controllers/UserController.php';

$userController = new UserController();
$user = $userController->getUserProfile();

if (!$user) {
    header('Location: login.php');
    exit();
}

$success = '';
$error = '';
if (isset($_GET['success'])) {
    $success = "Profil mis à jour avec succès!";
}
if (isset($_GET['error'])) {
    $error = "Erreur lors de la mise à jour";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>NextGen - Mon Profil</title>
    <style>
        /* VOTRE CSS EXISTANT */
    </style>
</head>
<body>
    <!-- VOTRE HTML EXISTANT -->
    
    <div class="profile-container">
        <div class="profile-section">
            <h3>📝 Informations personnelles</h3>
            
            <?php if ($success): ?>
                <div class="success-message"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" action="../controllers/UserController.php">
                <input type="hidden" name="action" value="updateProfile">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="profile_prenom">Prénom :</label>
                        <input type="text" id="profile_prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="profile_nom">Nom :</label>
                        <input type="text" id="profile_nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="profile_email">Email :</label>
                    <input type="text" id="profile_email" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                </div>

                <button type="submit" class="btn">💾 Enregistrer les modifications</button>
            </form>
        </div>
    </div>

    <!-- VOTRE JAVASCRIPT EXISTANT -->
</body>
</html>