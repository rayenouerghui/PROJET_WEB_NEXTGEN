<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - NextGen</title>
    <link rel="stylesheet" href="../../public/css/common.css">
    <link rel="stylesheet" href="../../public/css/frontoffice.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <a href="index.html">
                    <img src="../../public/images/logo.png" alt="NextGen Logo" class="logo-img">
                    NextGen
                </a>
            </div>
            <h1>Connexion</h1>
            <form id="loginForm">
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error-message" id="emailError"></span>
                </div>
                <div class="form-group">
                    <label>Mot de passe *</label>
                    <input type="password" id="password" name="password" required>
                    <span class="error-message" id="passwordError"></span>
                </div>
                <div class="form-group">
                    <a href="forgot-password.html" style="color: var(--primary-color); text-decoration: none; font-size: 14px;">Mot de passe oublié ?</a>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <a href="index.html" class="btn btn-secondary btn-block" style="text-decoration: none; display: block; text-align: center;">
                        Accéder sans connexion
                    </a>
                </div>
                <p style="text-align: center; margin-top: 20px; color: var(--text-light);">
                    Pas de compte ? <a href="register.html" style="color: var(--primary-color); text-decoration: none;">S'inscrire</a>
                </p>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            document.querySelectorAll('.error-message').forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            
            fetch('../../api/auth.php?action=login', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Login response:', data); // Debug
                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    // Show error in the email field
                    const emailError = document.getElementById('emailError');
                    if (emailError) {
                        emailError.textContent = data.message || 'Une erreur est survenue';
                        emailError.style.display = 'block';
                        emailError.style.color = 'red';
                        emailError.style.fontSize = '12px';
                        emailError.style.marginTop = '5px';
                    } else {
                        alert(data.message || 'Une erreur est survenue');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const emailError = document.getElementById('emailError');
                if (emailError) {
                    emailError.textContent = 'Erreur de connexion. Veuillez réessayer.';
                    emailError.style.display = 'block';
                    emailError.style.color = 'red';
                    emailError.style.fontSize = '12px';
                    emailError.style.marginTop = '5px';
                }
            });
        });
    </script>
    <style>
        .error-message {
            display: block;
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</body>
</html>

