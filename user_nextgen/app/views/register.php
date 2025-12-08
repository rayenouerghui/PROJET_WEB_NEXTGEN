<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - NextGen</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;900&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/user_nextgen/assets/css/auth.css?v=<?= time() ?>">
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            NextGen
        </div>

        <h1 class="auth-title">Inscription</h1>
        <p class="auth-subtitle">Créez votre compte en quelques secondes</p>

        <?php if(!empty($_SESSION['errors'])): ?>
            <div class="errors">
                <?php foreach($_SESSION['errors'] as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <form action="/user_nextgen/register/post" method="POST" id="registerForm">
            <div class="form-group">
                <label class="form-label">Nom</label>
                <input type="text" 
                       name="name" 
                       id="reg_name" 
                       class="form-control" 
                       placeholder="Votre nom"
                       value="<?= htmlspecialchars($_SESSION['old']['name'] ?? '') ?>">
                <span class="error-message" id="name_error"></span>
            </div>

            <div class="form-group">
                <label class="form-label">Prénom</label>
                <input type="text" 
                       name="prenom" 
                       id="reg_prenom" 
                       class="form-control" 
                       placeholder="Votre prénom"
                       value="<?= htmlspecialchars($_SESSION['old']['prenom'] ?? '') ?>">
                <span class="error-message" id="prenom_error"></span>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="text" 
                       name="email" 
                       id="reg_email" 
                       class="form-control" 
                       placeholder="votre@email.com"
                       value="<?= htmlspecialchars($_SESSION['old']['email'] ?? '') ?>">
                <span class="error-message" id="email_error"></span>
            </div>

            <div class="form-group">
                <label class="form-label">Rôle</label>
                <select name="role" id="reg_role" class="form-select">
                    <option value="user" <?= ($_SESSION['old']['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>Utilisateur</option>
                    <option value="admin" <?= ($_SESSION['old']['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                </select>
                <span class="error-message" id="role_error"></span>
            </div>

            <div class="form-group">
                <label class="form-label">Mot de passe</label>
                <input type="password" 
                       name="password" 
                       id="reg_password" 
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
                       id="reg_password_confirm" 
                       class="form-control" 
                       placeholder="••••••••">
                <span class="error-message" id="password_confirm_error"></span>
            </div>

            <button type="submit" class="btn-auth">
                Créer mon compte
            </button>
        </form>

        <div class="auth-link">
            Déjà un compte ? <a href="/user_nextgen/login">Se connecter</a>
        </div>
        <div class="auth-link" style="margin-top: 0.5rem;">
            <a href="/user_nextgen/" style="color: #6c757d;">← Retour à l'accueil</a>
        </div>
    </div>

    <script>
    // Password strength indicator
    document.getElementById('reg_password').addEventListener('input', function() {
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
    <script src="/user_nextgen/assets/js/register-validation.js"></script>
</body>
</html>
<?php unset($_SESSION['old']); ?>
