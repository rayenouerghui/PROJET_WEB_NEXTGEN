<?php
require_once __DIR__ . "/_partials/header.php";
require_once __DIR__ . "/../Controllers/frontoffice/GameController.php";
require_once __DIR__ . "/../Controllers/frontoffice/AuthController.php";

$gameId = $_GET['id'] ?? 0;
$gameController = new GameController();
$authController = new AuthController();
$currentUser = $authController->getCurrentUser();

$game = $gameController->getGameById($gameId);
if (!$game) {
    header('Location: catalog.php');
    exit;
}

$isPurchased = $currentUser ? $gameController->isGamePurchased($currentUser['id'], $gameId) : false;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($game['titre']); ?> - NextGen</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
</head>
<body>
    <?php include __DIR__ . "/_partials/header.php"; ?>

    <section class="game-details-section">
        <div class="container">
            <div class="game-details-content">
                <div class="game-details-image">
                    <img src="<?php echo htmlspecialchars($game['src_img'] ? '../../public/images/' . $game['src_img'] : '../../public/images/default-game.jpg'); ?>"
                         alt="<?php echo htmlspecialchars($game['titre']); ?>"
                         onerror="this.src='../../public/images/default-game.jpg'">
                </div>
                <div class="game-details-info">
                    <h1><?php echo htmlspecialchars($game['titre']); ?></h1>
                    <p class="game-category"><?php echo htmlspecialchars($game['nom_categorie'] ?: 'Non catégorisé'); ?></p>
                    <p class="game-price"><?php echo $game['est_gratuit'] ? 'Gratuit' : $game['prix'] . ' TND'; ?></p>
                    
                    <?php if ($isPurchased): ?>
                        <a href="play.php?id=<?php echo $gameId; ?>" class="btn btn-primary btn-large">Jouer</a>
                    <?php else: ?>
                        <button class="btn btn-primary btn-large" id="purchaseBtn" data-game-id="<?php echo $gameId; ?>">Acheter</button>
                    <?php endif; ?>
                    
                    <div class="game-description">
                        <h3>Description</h3>
                        <p>Détails du jeu à venir...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        const purchaseBtn = document.getElementById('purchaseBtn');
        if (purchaseBtn) {
            purchaseBtn.addEventListener('click', function() {
                const gameId = this.getAttribute('data-game-id');
                const currentUserId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
                
                if (!currentUserId) {
                    window.location.href = 'login.php';
                    return;
                }
                
                const formData = new FormData();
                formData.append('gameId', gameId);
                
                fetch('../../api/games.php?action=purchase', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Replace button with play button
                        purchaseBtn.outerHTML = '<a href="play.php?id=' + gameId + '" class="btn btn-primary btn-large">Jouer</a>';
                        alert('Achat réussi! Votre crédit restant: ' + data.new_credit + ' TND');
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue lors de l\'achat.');
                });
            });
        }
    </script>
    <style>
        .game-details-section {
            padding: 40px 0;
        }
        .game-details-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
        }
        .game-details-image img {
            width: 100%;
            border-radius: 8px;
        }
        .game-details-info h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .game-price {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 20px 0;
        }
        .btn-large {
            padding: 15px 30px;
            font-size: 18px;
        }
        .game-description {
            margin-top: 30px;
        }
    </style>
</body>
</html>

