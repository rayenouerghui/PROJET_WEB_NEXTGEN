<?php
// index.php - Point d'entrée unique de l'application
session_start();

// Autoloader simple
spl_autoload_register(function ($class) {
    $paths = [
        __DIR__ . '/config/' . $class . '.php',
        __DIR__ . '/app/models/' . $class . '.php',
        __DIR__ . '/app/controllers/' . $class . '.php'
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Récupérer l'URL demandée
$request = $_SERVER['REQUEST_URI'];
$request = str_replace('/user_nextgen', '', $request);
$request = strtok($request, '?'); // Enlever les query strings

// Nettoyer la requête
if ($request === '' || $request === '/index.php') {
    $request = '/';
}

// Router
switch ($request) {
    case '/':
    case '/home':
        $controller = new HomeController();
        $controller->index();
        break;

    case '/login':
        $controller = new AuthController();
        $controller->showLogin();
        break;

    case '/login/post':
        $controller = new AuthController();
        $controller->login();
        break;

    case '/register':
        $controller = new AuthController();
        $controller->showRegister();
        break;

    case '/register/post':
        $controller = new AuthController();
        $controller->register();
        break;

    case '/logout':
        $controller = new AuthController();
        $controller->logout();
        break;

    case '/profile':
        $controller = new UserController();
        $controller->profile();
        break;

    case '/profile/edit':
        $controller = new UserController();
        $controller->edit();
        break;

    case '/profile/update':
        $controller = new UserController();
        $controller->update();
        break;

    case '/admin/dashboard':
        $controller = new UserController();
        $controller->adminDashboard();
        break;

    case '/admin/users/delete':
        $controller = new UserController();
        $controller->deleteUser();
        break;

    case '/history':
        $controller = new HistoryController();
        $controller->index();
        break;

    case '/history/create':
        $controller = new HistoryController();
        $controller->create();
        break;

    case '/history/store':
        $controller = new HistoryController();
        $controller->store();
        break;

    case '/history/edit':
        $controller = new HistoryController();
        $controller->edit();
        break;

    case '/history/update':
        $controller = new HistoryController();
        $controller->update();
        break;

    case '/history/delete':
        $controller = new HistoryController();
        $controller->delete();
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Page non trouvée</h1>";
        break;
}
