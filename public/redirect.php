<?php
// Redirect script for HTML pages that need authentication
session_start();

$page = $_GET['page'] ?? 'index';
$allowedPages = ['index', 'catalog', 'about', 'donations', 'returns', 'contact', 'faq'];

if (!in_array($page, $allowedPages)) {
    $page = 'index';
}

// Check if page requires auth
$authRequired = ['catalog', 'account', 'settings', 'play', 'game-details'];
$isAuthRequired = in_array($page, $authRequired);

if ($isAuthRequired && !isset($_SESSION['user_id'])) {
    header('Location: ../app/Views/login.php');
    exit;
}

// Redirect to appropriate page
switch ($page) {
    case 'catalog':
        header('Location: ../app/Views/catalog.php');
        break;
    case 'index':
        header('Location: ../app/Views/index.php');
        break;
    default:
        header('Location: ../app/Views/' . $page . '.php');
        break;
}
exit;

?>

