<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe - NextGen</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;900&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/user_nextgen/assets/css/auth.css?v=<?= time() ?>">
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            NextGen
        </div>

        <h1 class="auth-title">Nouveau mot de passe</h1>
        <p class="auth-subtitle">Choisissez un nouveau mot de passe sécurisé</p>

        <?php if(!empty($_SESSION['errors'])): ?>
            <div class="errors">
                <?php foreach($_SESSION['errors'] as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <form action="/user_nextgen/reset-password/update" method="POST" id="newPasswordForm">
            <div class="form-group">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" 
                       name="password" 
                       id="new_password" 
                       class="form-control" 
                       placeholder="••••••••">
                <div class="password-strength">
                    <div class="password-strength-bar" id="strength-bar"></div>
                </div>
                <div class="password-hint">Minimum 6 caractères</div>
                <span class="error-message" id="password_error"></span>
            </div>

            <div class="form-group">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" 
                       name="password_confirm" 
                       id="new_password_confirm" 
                       class="form-control" 
                       placeholder="••••••••">
                <span class="error-message" id="password_confirm_error"></span>
            </div>

            <button type="submit" class="btn-auth">
                Réinitialiser le mot de passe
            </button>
        </form>

        <div class="auth-link">
            <a href="/user_nextgen/reset-password/cancel" style="color: #e74c3c;">Annuler</a>
        </div>
    </div>

    <script>
    // Password strength indicator
    document.getElementById('new_password').addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.getElementById('strength-bar');
        
        strengthBar.className = 'password-strength-bar';
        
        if (password.length === 0) {
            strengthBar.style.width = '0';
        } else if (password.length < 6) {
            strengthBar.classList.add('strength-weak');
        } else if (password.length < 10) {
            strengthBar.classList.add('strength-medium');
        } else {
            strengthBar.classList.add('strength-strong');
        }
    });
    </script>
    <script src="/user_nextgen/assets/js/new-password-validation.js"></script>
</body>
</html>
