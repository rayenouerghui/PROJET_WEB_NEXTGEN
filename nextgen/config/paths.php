<?php
/**
 * Global Path Constants
 * Include this file at the TOP of every controller/view
 */

define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CONTROLLERS_PATH', ROOT_PATH . '/controller');
define('MODELS_PATH', ROOT_PATH . '/models');
define('SERVICES_PATH', ROOT_PATH . '/services');
define('VIEWS_PATH', ROOT_PATH . '/view');
$__scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$__pos = strpos($__scriptName, '/nextgen/');
if ($__pos !== false) {
    $__webRoot = substr($__scriptName, 0, $__pos) . '/nextgen';
} else {
    $__pos2 = strpos($__scriptName, '/nextgen');
    if ($__pos2 !== false) {
        $__webRoot = substr($__scriptName, 0, $__pos2) . '/nextgen';
    } else {
        $__webRoot = '/nextgen';
    }
}

define('WEB_ROOT', $__webRoot);
// Base URL of the whole project (parent of /nextgen). Useful for linking to legacy modules.
$__webBase = preg_replace('#/nextgen$#', '', $__webRoot);
if ($__webBase === null || $__webBase === '') {
    $__webBase = '/';
}
define('WEB_BASE', $__webBase);
define('WEB_API', WEB_ROOT . '/view/api');

/**
 * Helper function for redirects
 */
function redirect($path) {
    header("Location: " . WEB_ROOT . '/' . ltrim($path, '/'));
    exit;
}
