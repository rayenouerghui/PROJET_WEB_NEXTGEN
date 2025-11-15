<?php
require_once __DIR__ . '/../../app/Controllers/frontoffice/MatchController.php';

try {
    $controller = new MatchController();
    $controller->afficherSession();
} catch (Exception $e) {
    echo '<div class="alert alert-error">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>

