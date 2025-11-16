<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css?v=<?php echo time(); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-logo">
                <a href="index.php">
                    <img src="../../public/images/logo.png" alt="NextGen Logo" class="logo-img">
                    NextGen
                </a>
            </div>
            <h1>Créer un compte</h1>
            <form id="registerForm">
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" id="prenom" name="prenom" required>
                    <span class="error-message" id="prenomError"></span>
                </div>
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" id="nom" name="nom" required>
                    <span class="error-message" id="nomError"></span>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" id="email" name="email" required>
                    <span class="error-message" id="emailError"></span>
                </div>
                <div class="form-group">
                    <label>Mot de passe *</label>
                    <input type="password" id="password" name="password" required>
                    <div class="password-requirements">
                        Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.
                    </div>
                    <span class="error-message" id="passwordError"></span>
                </div>
                <div class="form-group">
                    <label>Confirmer le mot de passe *</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                    <span class="error-message" id="confirmPasswordError"></span>
                </div>
                <div class="form-group" style="margin-top: 15px;">
                    <label style="display: flex; align-items: flex-start;">
                        <input type="checkbox" id="acceptTerms" style="width: auto; margin-right: 8px; margin-top: 3px;" required>
                        <span>J'accepte les <a href="terms.php" style="color: var(--primary-color);">conditions d'utilisation</a> et la <a href="privacy.php" style="color: var(--primary-color);">politique de confidentialité</a> *</span>
                    </label>
                </div>
                <button type="submit" class="btn btn-primary btn-block">S'inscrire</button>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <a href="index.php" class="btn btn-secondary btn-block" style="text-decoration: none; display: block; text-align: center;">
                        Accéder sans compte
                    </a>
                </div>
                <p style="text-align: center; margin-top: 20px; color: var(--text-light);">
                    Déjà un compte ? <a href="login.php" style="color: var(--primary-color); text-decoration: none;">Se connecter</a>
                </p>
            </form>
        </div>
    </div>
    <script>
        function validatePassword(password) {
            // At least 8 characters, one uppercase, one lowercase, one number
            const minLength = password.length >= 8;
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasNumber = /[0-9]/.test(password);
            
            return minLength && hasUpperCase && hasLowerCase && hasNumber;
        }
        
        function showError(fieldId, message) {
            const errorEl = document.getElementById(fieldId + 'Error');
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.style.display = 'block';
                errorEl.style.color = 'red';
            }
        }
        
        function clearError(fieldId) {
            const errorEl = document.getElementById(fieldId + 'Error');
            if (errorEl) {
                errorEl.textContent = '';
                errorEl.style.display = 'none';
            }
        }
        
        function clearAllErrors() {
            ['prenom', 'nom', 'email', 'password', 'confirmPassword'].forEach(field => {
                clearError(field);
            });
        }
        
        // Real-time validation
        document.getElementById('password').addEventListener('blur', function() {
            const password = this.value;
            if (password && !validatePassword(password)) {
                showError('password', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.');
            } else {
                clearError('password');
            }
        });
        
        document.getElementById('confirmPassword').addEventListener('blur', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            if (confirmPassword && password !== confirmPassword) {
                showError('confirmPassword', 'Les mots de passe ne correspondent pas.');
            } else {
                clearError('confirmPassword');
            }
        });
        
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                showError('email', 'Veuillez entrer un email valide.');
            } else {
                clearError('email');
            }
        });
        
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            clearAllErrors();
            
            const prenom = document.getElementById('prenom').value.trim();
            const nom = document.getElementById('nom').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const acceptTerms = document.getElementById('acceptTerms').checked;
            
            let hasError = false;
            
            // Validation
            if (!prenom) {
                showError('prenom', 'Le prénom est requis.');
                hasError = true;
            }
            
            if (!nom) {
                showError('nom', 'Le nom est requis.');
                hasError = true;
            }
            
            if (!email) {
                showError('email', 'L\'email est requis.');
                hasError = true;
            } else {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showError('email', 'Veuillez entrer un email valide.');
                    hasError = true;
                }
            }
            
            if (!password) {
                showError('password', 'Le mot de passe est requis.');
                hasError = true;
            } else if (!validatePassword(password)) {
                showError('password', 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre.');
                hasError = true;
            }
            
            if (!confirmPassword) {
                showError('confirmPassword', 'Veuillez confirmer le mot de passe.');
                hasError = true;
            } else if (password !== confirmPassword) {
                showError('confirmPassword', 'Les mots de passe ne correspondent pas.');
                hasError = true;
            }
            
            if (!acceptTerms) {
                alert('Veuillez accepter les conditions d\'utilisation.');
                hasError = true;
            }
            
            if (hasError) {
                return;
            }
            
            const formData = new FormData();
            formData.append('prenom', prenom);
            formData.append('nom', nom);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('confirmPassword', confirmPassword);
            
            fetch('../../api/auth.php?action=register', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    // Show error in appropriate field
                    if (data.message.includes('email')) {
                        showError('email', data.message);
                    } else if (data.message.includes('mot de passe')) {
                        showError('password', data.message);
                    } else {
                        showError('email', data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('email', 'Une erreur est survenue. Veuillez réessayer.');
            });
        });
    </script>
    <style>
        /* Ensure register styles are applied */
        .login-container {
            min-height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.9) 0%, rgba(234, 88, 12, 0.9) 100%),
                        url('../../public/images/background_gaming1.jpg') !important;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            background-attachment: fixed !important;
            padding: 20px !important;
        }
        
        .login-box {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px) !important;
            padding: 50px 40px !important;
            border-radius: 16px !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3),
                        0 0 40px rgba(37, 99, 235, 0.2) !important;
            width: 100% !important;
            max-width: 500px !important;
        }
        
        .login-box h1 {
            text-align: center !important;
            margin-bottom: 30px !important;
            color: var(--primary-color) !important;
            font-size: 32px !important;
            font-weight: 700 !important;
        }
        
        .form-group {
            margin-bottom: 20px !important;
        }
        
        .form-group label {
            display: block !important;
            margin-bottom: 8px !important;
            font-weight: 500 !important;
            color: var(--text-dark) !important;
        }
        
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: 100% !important;
            padding: 12px !important;
            border: 2px solid var(--border-color) !important;
            border-radius: 8px !important;
            font-size: 16px !important;
            transition: all 0.3s ease !important;
            box-sizing: border-box !important;
        }
        
        .form-group input[type="checkbox"] {
            width: auto !important;
            margin-right: 8px !important;
        }
        
        .form-group input:focus {
            outline: none !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
        }
        
        .password-requirements {
            font-size: 12px !important;
            color: var(--text-light) !important;
            margin-top: 8px !important;
            line-height: 1.5 !important;
        }
        
        .error-message {
            display: block;
            color: red;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</body>
</html>

