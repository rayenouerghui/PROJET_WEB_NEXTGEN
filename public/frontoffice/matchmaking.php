<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Matchmaking - NextGen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Roboto:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/common.css">
    <link rel="stylesheet" href="../css/frontoffice/frontoffice.css">
</head>
<body>
    <div class="games-matchmaking-list">
        <div class="page-header">
            <h1 class="page-title">🎮 Trouver un Match</h1>
            <p class="page-subtitle">Sélectionnez un jeu pour trouver des partenaires de jeu</p>
        </div>

        <?php
        require_once __DIR__ . '/../../app/Controllers/frontoffice/MatchController.php';
        
        try {
            $controller = new MatchController();
            $controller->afficherPage();
        } catch (Exception $e) {
            echo '<div class="alert alert-error">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
        ?>
    </div>
</body>
</html>


