<?php
require_once __DIR__ . "/_partials/header.php";
require_once __DIR__ . "/../Controllers/frontoffice/GameController.php";
require_once __DIR__ . "/../Controllers/frontoffice/AuthController.php";

$authController = new AuthController();
$authController->requireAuth();

$gameId = $_GET['id'] ?? 0;
$gameController = new GameController();
$currentUser = $authController->getCurrentUser();

$game = $gameController->getGameById($gameId);
if (!$game) {
    header('Location: catalog.php');
    exit;
}

$isPurchased = $gameController->isGamePurchased($currentUser['id'], $gameId);
if (!$isPurchased) {
    header('Location: game-details.php?id=' . $gameId);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jouer - <?php echo htmlspecialchars($game['titre']); ?> - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <section class="play-section">
        <div class="container">
            <h1><?php echo htmlspecialchars($game['titre']); ?></h1>
            <div class="play-area">
                <p>Zone de jeu - À implémenter</p>
                <?php if ($game['lien_externe']): ?>
                    <iframe src="<?php echo htmlspecialchars($game['lien_externe']); ?>" 
                            style="width: 100%; height: 600px; border: none;"></iframe>
                <?php else: ?>
                    <p>Le jeu sera disponible ici prochainement.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <style>
        .play-section {
            padding: 40px 0;
            min-height: 70vh;
        }
        .play-area {
            background: var(--bg-white);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
    </style>
</body>
</html>

