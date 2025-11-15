<?php

if (!isset($_GET['controller'])) {
    $_GET['controller'] = 'matchmaking';
}

$originalPath = $_SERVER['REQUEST_URI'];
$_SERVER['REQUEST_URI'] = '/backoffice/matchmaking' . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');

require_once __DIR__ . '/../../../index.php';

$_SERVER['REQUEST_URI'] = $originalPath;

?>

