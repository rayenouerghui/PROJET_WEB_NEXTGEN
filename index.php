<?php
// index.php
session_start();

// Autoloader simple
spl_autoload_register(function($class) {
    if (file_exists(__DIR__ . '/models/' . $class . '.php')) {
        require_once __DIR__ . '/models/' . $class . '.php';
    }
    if (file_exists(__DIR__ . '/controllers/' . $class . '.php')) {
        require_once __DIR__ . '/controllers/' . $class . '.php';
    }
});

require_once 'config/Database.php';

// Routing
$page = $_GET['page'] ?? 'front';
$action = $_GET['action'] ?? 'index';

try {
    switch($page) {
        case 'front':
            $controller = new FrontController();
            break;
        case 'admin':
            $controller = new AdminController();
            break;
        default:
            throw new Exception('Page non trouvée');
    }
    
    if (!method_exists($controller, $action)) {
        throw new Exception('Action non trouvée');
    }
    
    $controller->$action();
    
} catch (Exception $e) {
    http_response_code(404);
    include 'views/errors/404.php';
    exit;
}
?>