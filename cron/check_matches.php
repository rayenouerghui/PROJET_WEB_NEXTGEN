<?php

require_once __DIR__ . '/../backoffice/models/AttenteMatchModel.php';
require_once __DIR__ . '/../backoffice/models/SessionMatchModel.php';
require_once __DIR__ . '/../backoffice/services/MatchService.php';
require_once __DIR__ . '/../backoffice/config/db.php';

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
