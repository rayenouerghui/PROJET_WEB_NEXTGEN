<?php
// projett/index.php - Routeur principal
session_start();

// Inclure la configuration de la base de données
require_once 'config/database.php';

// Inclure les classes de base du modèle MVC (intégrées)
require_once 'View.php';
require_once 'controllers/Controller.php';

// Routeur simple
$controller = $_GET['c'] ?? 'categorie';
$action = $_GET['a'] ?? 'index';

// Inclure le contrôleur demandé
switch($controller) {
    case 'admin':
        require_once 'controllers/AdminC.php';
        $controller = new AdminC();
        break;
    case 'categorie':
        require_once 'controllers/CategorieC.php';
        $controller = new CategorieC();
        break;
    case 'evenement':
        require_once 'controllers/EvenementC.php';
        $controller = new EvenementC();
        break;
    case 'reservation':
        require_once 'controllers/ReservationC.php';
        $controller = new ReservationC();
        break;
    case 'front':
        require_once 'controllers/FrontC.php';
        $controller = new FrontC();
        break;
    default:
        // Par défaut, afficher la page d'accueil
        require_once 'controllers/FrontC.php';
        $controller = new FrontC();
        $action = 'index';
        break;
}

// Vérifier que l'action existe
if (!method_exists($controller, $action)) {
    die('Action non trouvée: ' . $action);
}

// Exécuter l'action
$controller->$action();
?>