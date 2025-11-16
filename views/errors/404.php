<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page non trouvée</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 50px; background: #f5f7fb; 
               display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .error-container { text-align: center; background: white; padding: 50px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .error-code { font-size: 5em; color: #e74c3c; margin: 0; }
        .error-message { font-size: 1.5em; color: #333; margin: 20px 0; }
        .btn { display: inline-block; padding: 12px 25px; background: #667eea; color: white; 
               text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-code">404</h1>
        <p class="error-message">Page non trouvée</p>
        <p>La page que vous recherchez n'existe pas ou a été déplacée.</p>
        <a href="?page=front&action=index" class="btn">🏠 Retour à l'accueil</a>
    </div>
</body>
</html>