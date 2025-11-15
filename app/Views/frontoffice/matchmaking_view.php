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
    <link rel="stylesheet" href="css/frontoffice.css">
</head>
<body>
    <div class="games-matchmaking-list">
        <div class="page-header">
            <h1 class="page-title">🎮 Trouver un Match</h1>
            <p class="page-subtitle">Sélectionnez un jeu pour trouver des partenaires de jeu</p>
        </div>

        <?php if (isset($message) && $message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($sessionActive) && $sessionActive): 
            $lienDiscord = isset($lienDiscord) ? $lienDiscord : null;
            $sessionPageUrl = isset($sessionPageUrl) ? $sessionPageUrl : '#';
        ?>
            <div class="session-found">
                <h2 class="session-title">🎉 Match Trouvé !</h2>
                <div class="session-info">
                    <div class="session-info-item">
                        <span class="session-info-label">🎮 Jeu:</span>
                        <span class="session-info-value"><?php echo htmlspecialchars($sessionActive['nom_jeu']); ?></span>
                    </div>
                    <div class="session-info-item">
                        <span class="session-info-label">📅 Date:</span>
                        <span class="session-info-value"><?php echo date('d/m/Y à H:i', strtotime($sessionActive['date_creation'])); ?></span>
                    </div>
                    <div class="session-info-item">
                        <span class="session-info-label">👥 Participants:</span>
                        <span class="session-info-value"><?php echo count($sessionActive['participants']); ?> joueur(s)</span>
                    </div>
                </div>
                <div class="session-links">
                    <?php if ($lienDiscord): ?>
                        <a href="<?php echo htmlspecialchars($lienDiscord); ?>" target="_blank" class="discord-link">
                            <span>💬</span>
                            <span>Rejoindre Discord</span>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo htmlspecialchars($sessionPageUrl); ?>" class="session-link">
                        <span>🎮</span>
                        <span>Voir la Session</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($jeux) && empty($jeux)): ?>
            <div class="empty-games">
                <div class="empty-games-icon">🎮</div>
                <h2 class="empty-games-title">Aucun jeu acheté</h2>
                <p class="empty-games-text">Vous devez acheter des jeux avant de pouvoir trouver un match.</p>
            </div>
        <?php elseif (isset($jeux) && !empty($jeux)): ?>
            <div class="games-grid">
                <?php foreach ($jeux as $jeu): ?>
                    <div class="game-card">
                        <div class="game-image">
                            <?php if (!empty($jeu['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($jeu['image_url']); ?>" alt="<?php echo htmlspecialchars($jeu['nom']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                                🎮
                            <?php endif; ?>
                        </div>
                        <div class="game-content">
                            <h3 class="game-title"><?php echo htmlspecialchars($jeu['nom']); ?></h3>
                            <span class="game-category"><?php echo htmlspecialchars($jeu['categorie']); ?></span>
                            <div class="game-price"><?php echo number_format($jeu['prix'], 2); ?> €</div>
                            <form method="POST" action="">
                                <input type="hidden" name="action" value="ajouter_attente">
                                <input type="hidden" name="id_utilisateur" value="<?php echo isset($idUtilisateur) ? $idUtilisateur : 1; ?>">
                                <input type="hidden" name="id_jeu" value="<?php echo $jeu['id_jeu']; ?>">
                                <button type="submit" class="match-button">🔍 Trouver un Match</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>


