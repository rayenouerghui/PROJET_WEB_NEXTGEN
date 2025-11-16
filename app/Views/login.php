<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - NextGen</title>
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
                    <a href="forgot-password.php" style="color: var(--primary-color); text-decoration: none; font-size: 14px;">Mot de passe oublié ?</a>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <a href="index.php" class="btn btn-secondary btn-block" style="text-decoration: none; display: block; text-align: center;">
                        Accéder sans connexion
                    </a>
                </div>
                <p style="text-align: center; margin-top: 20px; color: var(--text-light);">
                    Pas de compte ? <a href="register.php" style="color: var(--primary-color); text-decoration: none;">S'inscrire</a>
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
        /* Ensure login styles are applied */
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
            max-width: 450px !important;
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
        
        .form-group input {
            width: 100% !important;
            padding: 12px !important;
            border: 2px solid var(--border-color) !important;
            border-radius: 8px !important;
            font-size: 16px !important;
            transition: all 0.3s ease !important;
        }
        
        .form-group input:focus {
            outline: none !important;
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
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

