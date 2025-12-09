<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - NextGen</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;900&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/user_nextgen/assets/css/auth.css?v=<?= time() ?>">
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            NextGen
        </div>

        <h1 class="auth-title">Mot de passe oublié</h1>
        <p class="auth-subtitle">Entrez votre email pour recevoir un code de réinitialisation</p>

        <?php if(!empty($_SESSION['success'])): ?>
            <div class="success">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($_SESSION['errors'])): ?>
            <div class="errors">
                <?php foreach($_SESSION['errors'] as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <form action="/user_nextgen/forgot-password/send" method="POST" id="forgotPasswordForm">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="text" 
                       name="email" 
                       id="forgot_email" 
                       class="form-control" 
                       placeholder="votre@email.com"
                       value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">
                <span class="error-message" id="email_error"></span>
            </div>

            <button type="submit" class="btn-auth">
                Envoyer le code
            </button>
        </form>

        <div class="auth-link">
            Vous vous souvenez de votre mot de passe ? <a href="/user_nextgen/login">Se connecter</a>
        </div>
        <div class="auth-link" style="margin-top: 0.5rem;">
            <a href="/user_nextgen/" style="color: #6c757d;">← Retour à l'accueil</a>
        </div>
    </div>

    <script src="/user_nextgen/assets/js/forgot-password-validation.js"></script>
</body>
</html>
<?php unset($_SESSION['old']); ?>
