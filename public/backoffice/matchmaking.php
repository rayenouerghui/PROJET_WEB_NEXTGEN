<?php
$title = 'Gestion Matchmaking';
$extraCss = ['css/backoffice.css'];
require_once __DIR__ . '/../../app/Views/backoffice/_partials/header.php';

require_once __DIR__ . '/../../app/Controllers/backoffice/MatchmakingAdminController.php';

try {
    $controller = new MatchmakingAdminController();
    $controller->afficherPage();
} catch (Exception $e) {
    echo '<div class="alert alert-error">Erreur: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}
?>

<script src="../assets/js/validation.js"></script>
</main>
</body>
</html>
