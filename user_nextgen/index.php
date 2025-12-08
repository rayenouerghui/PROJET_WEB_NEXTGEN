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

    // ========== AUTHENTIFICATION ==========
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

    // ========== PROFIL UTILISATEUR ==========
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

    // ========== ADMINISTRATION ==========
    case '/admin/dashboard':
        $controller = new UserController();
        $controller->adminDashboard();
        break;

    case '/admin/users':
        $controller = new UserController();
        $controller->listUsers();
        break;

    case '/admin/users/view':
        $controller = new UserController();
        $controller->viewUser();
        break;

    case '/admin/users/edit':
        $controller = new UserController();
        $controller->editUserAdmin();
        break;

    case '/admin/users/update':
        $controller = new UserController();
        $controller->updateUserAdmin();
        break;

    case '/admin/users/suspend':
        $controller = new UserController();
        $controller->suspendUser();
        break;

    case '/admin/users/delete':
        $controller = new UserController();
        $controller->deleteUser();
        break;

    case '/admin/users/export':
        $controller = new UserController();
        $controller->exportUsers();
        break;

    // ========== HISTORIQUE ==========
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

    // ========== RÉINITIALISATION MOT DE PASSE ==========
    case '/forgot-password':
        $controller = new PasswordResetController();
        $controller->showForgotForm();
        break;

    case '/forgot-password/send':
        $controller = new PasswordResetController();
        $controller->sendResetCode();
        break;

    case '/reset-password/verify':
        $controller = new PasswordResetController();
        $controller->showVerifyForm();
        break;

    case '/reset-password/verify-code':
        $controller = new PasswordResetController();
        $controller->verifyCode();
        break;

    case '/reset-password/new':
        $controller = new PasswordResetController();
        $controller->showNewPasswordForm();
        break;

    case '/reset-password/update':
        $controller = new PasswordResetController();
        $controller->updatePassword();
        break;

    case '/reset-password/cancel':
        $controller = new PasswordResetController();
        $controller->cancel();
        break;

    // ========== PAGE 404 ==========
    default:
        http_response_code(404);
        echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>404 - Page non trouvée</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .container {
            text-align: center;
        }
        h1 {
            font-size: 120px;
            margin: 0;
        }
        p {
            font-size: 24px;
        }
        a {
            color: white;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>404</h1>
        <p>Page non trouvée</p>
        <a href='/user_nextgen/'>← Retour à l'accueil</a>
    </div>
</body>
</html>";
        break;
}
