<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du code - NextGen</title>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;900&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/user_nextgen/assets/css/auth.css?v=<?= time() ?>">
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            NextGen
        </div>

        <h1 class="auth-title">Vérification du code</h1>
        <p class="auth-subtitle">Entrez le code à 6 chiffres envoyé à<br><strong><?= htmlspecialchars($_SESSION['reset_email'] ?? '') ?></strong></p>

        <?php if(!empty($_SESSION['errors'])): ?>
            <div class="errors">
                <?php foreach($_SESSION['errors'] as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <form action="/user_nextgen/reset-password/verify-code" method="POST" id="verifyCodeForm">
            <div class="form-group">
                <label class="form-label">Code de vérification</label>
                <input type="text" 
                       name="code" 
                       id="verify_code" 
                       class="form-control" 
                       placeholder="000000"
                       maxlength="6"
                       style="text-align: center; font-size: 24px; letter-spacing: 5px;">
                <span class="error-message" id="code_error"></span>
                <div class="password-hint">Le code expire dans 15 secondes</div>
            </div>

            <button type="submit" class="btn-auth">
                Vérifier le code
            </button>
        </form>

        <div class="auth-link">
            <a href="/user_nextgen/forgot-password">← Renvoyer un nouveau code</a>
        </div>
        <div class="auth-link" style="margin-top: 0.5rem;">
            <a href="/user_nextgen/reset-password/cancel" style="color: #e74c3c;">Annuler</a>
        </div>
    </div>

    <script src="/user_nextgen/assets/js/verify-code-validation.js"></script>
</body>
</html>
