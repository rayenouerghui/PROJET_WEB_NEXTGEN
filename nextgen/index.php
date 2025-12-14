<?php
// nextgen/index.php - Main Router for Integrated Application
session_start();

// Include configuration
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/paths.php';
require_once __DIR__ . '/config/session.php';

// Include base classes
require_once __DIR__ . '/View.php';
require_once __DIR__ . '/controller/Controller.php';

// Get controller and action from URL
$controller = $_GET['c'] ?? 'front';
$action = $_GET['a'] ?? 'index';

// Route to appropriate controller
switch($controller) {
    // Event module controllers
    case 'admin':
        require_once __DIR__ . '/controller/AdminC.php';
        $controllerInstance = new AdminC();
        break;
    case 'categorie':
        require_once __DIR__ . '/controller/CategorieC.php';
        $controllerInstance = new CategorieC();
        break;
    case 'evenement':
        require_once __DIR__ . '/controller/EvenementC.php';
        $controllerInstance = new EvenementC();
        break;
    case 'reservation':
        require_once __DIR__ . '/controller/ReservationC.php';
        $controllerInstance = new ReservationC();
        break;
    case 'front':
        require_once __DIR__ . '/controller/FrontC.php';
        $controllerInstance = new FrontC();
        break;
    
    // Blog module controllers (if needed for routing)
    case 'blog':
        require_once __DIR__ . '/controller/BlogController.php';
        $controllerInstance = new BlogController();
        break;
    
    // Default: show front page
    default:
        require_once __DIR__ . '/controller/FrontC.php';
        $controllerInstance = new FrontC();
        $action = 'index';
        break;
}

// Check if action exists
if (!method_exists($controllerInstance, $action)) {
    http_response_code(404);
    require_once __DIR__ . '/views/errors/404.php';
    exit;
}

// Execute the action
try {
    $controllerInstance->$action();
} catch (Exception $e) {
    error_log("Error executing action {$action} in {$controller}: " . $e->getMessage());
    http_response_code(500);
    echo "An error occurred. Please try again later.";
}
?>

