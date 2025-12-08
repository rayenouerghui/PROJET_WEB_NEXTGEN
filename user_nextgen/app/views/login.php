<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - NextGen</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;900&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/user_nextgen/assets/css/auth.css?v=<?= time() ?>">
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            NextGen
        </div>

        <h1 class="auth-title">Connexion</h1>
        <p class="auth-subtitle">Entrez vos identifiants pour continuer</p>

        <?php if(!empty($_SESSION['success'])): ?>
            <div class="success">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(!empty($_SESSION['errors'])): ?>
            <div class="errors">
                <?php foreach($_SESSION['errors'] as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
                <?php 
                $hasAccountError = false;
                foreach($_SESSION['errors'] as $error) {
                    if (strpos($error, 'créer un compte') !== false) {
                        $hasAccountError = true;
                        break;
                    }
                }
                if ($hasAccountError): ?>
                    <div style="margin-top: 10px;">
                        <a href="/user_nextgen/register" style="color: white; font-weight: bold; text-decoration: underline;">→ Créer un compte maintenant</a>
                    </div>
                <?php endif; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <form action="/user_nextgen/login/post" method="POST" id="loginForm">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="text" 
                       name="email" 
                       id="login_email" 
                       class="form-control" 
                       placeholder="votre@email.com"
                       value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">
                <span class="error-message" id="email_error"></span>
            </div>

            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password" 
                       name="password" 
                       id="login_password" 
                       class="form-control" 
                       placeholder="••••••••">
                <span class="error-message" id="password_error"></span>
            </div>

            <button type="submit" class="btn-auth">
                Se connecter
            </button>
        </form>

        <div class="auth-link">
            <a href="/user_nextgen/forgot-password">Mot de passe oublié ?</a>
        </div>
        <div class="auth-link" style="margin-top: 0.5rem;">
            Pas encore de compte ? <a href="/user_nextgen/register">Créer un compte</a>
        </div>
        <div class="auth-link" style="margin-top: 0.5rem;">
            <a href="/user_nextgen/" style="color: #6c757d;">← Retour à l'accueil</a>
        </div>
    </div>

    <script src="/user_nextgen/assets/js/login-validation.js"></script>
</body>
</html>
<?php unset($_SESSION['old']); ?>
