<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session de Match - NextGen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&family=Roboto:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../css/frontoffice.css">
    <style>
        .session-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }
        
        .session-container {
            max-width: 800px;
            width: 100%;
        }
        
        .session-not-found {
            background: white;
            border-radius: 24px;
            padding: 64px 32px;
            text-align: center;
            box-shadow: var(--shadow-xl);
        }
        
        .session-not-found-icon {
            font-size: 5rem;
            margin-bottom: 24px;
            opacity: 0.5;
        }
        
        .session-not-found-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 16px;
        }
        
        .session-not-found-text {
            color: var(--text-medium);
            font-size: 1.125rem;
            margin-bottom: 32px;
        }
        
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--gradient-primary);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }
    </style>
</head>
<body>
    <div class="session-page">
        <div class="session-container">
            <?php if (isset($session) && $session): ?>
                <div class="session-found">
                    <h2 class="session-title">🎮 Session de Match</h2>
                    <div class="session-info">
                        <div class="session-info-item">
                            <span class="session-info-label">🎮 Jeu:</span>
                            <span class="session-info-value"><?php echo htmlspecialchars($session['nom_jeu']); ?></span>
                        </div>
                        <div class="session-info-item">
                            <span class="session-info-label">📅 Date de création:</span>
                            <span class="session-info-value"><?php echo date('d/m/Y à H:i', strtotime($session['date_creation'])); ?></span>
                        </div>
                        <div class="session-info-item">
                            <span class="session-info-label">👥 Participants:</span>
                            <span class="session-info-value"><?php echo count($session['participants']); ?> joueur(s)</span>
                        </div>
                        <div class="session-info-item">
                            <span class="session-info-label">🔑 ID Session:</span>
                            <span class="session-info-value"><?php echo htmlspecialchars($session['id_session']); ?></span>
                        </div>
                    </div>
                    <div class="session-links">
                        <?php if (isset($lienDiscord) && $lienDiscord): ?>
                            <a href="<?php echo htmlspecialchars($lienDiscord); ?>" target="_blank" class="discord-link">
                                <span>💬</span>
                                <span>Rejoindre le Serveur Discord</span>
                            </a>
                        <?php endif; ?>
                        <a href="../matchmaking.php" class="session-link">
                            <span>🔙</span>
                            <span>Retour au Matchmaking</span>
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="session-not-found">
                    <div class="session-not-found-icon">❌</div>
                    <h2 class="session-not-found-title">Session introuvable</h2>
                    <p class="session-not-found-text">
                        La session que vous recherchez n'existe pas, a expiré ou a été annulée.
                    </p>
                    <a href="../matchmaking.php" class="back-link">
                        <span>←</span>
                        <span>Retour au Matchmaking</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

