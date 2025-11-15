<?php

require_once __DIR__ . '/../app/Models/backoffice/AttenteMatchModel.php';
require_once __DIR__ . '/../app/Models/backoffice/SessionMatchModel.php';
require_once __DIR__ . '/../app/Services/MatchService.php';
require_once __DIR__ . '/../config/db.php';

set_time_limit(0);

try {
    $matchService = new MatchService();
    $resultat = $matchService->verifierTousLesMatchs();
    
    echo "[" . date('Y-m-d H:i:s') . "] Vérification des matchs terminée.\n";
    echo "Matchs créés: " . $resultat['matchs_crees'] . "\n";
    
    if (isset($resultat['erreur'])) {
        echo "Erreur: " . $resultat['erreur'] . "\n";
    }
    
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

?>
