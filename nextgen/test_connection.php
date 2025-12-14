<?php
/**
 * NextGen Connection Test Script
 * Use this to verify database connections and table existence
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>NextGen Connection Test</title>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;} .error{color:red;}</style></head><body>";
echo "<h1>🔍 NextGen Connection Test</h1>";

// Test config.php
echo "<h2>Testing config.php</h2>";
try {
    require_once 'config/config.php';
    $pdo1 = config::getConnexion();
    echo "<span class='ok'>✅ config.php connection: OK</span><br>";
    echo "Database: nextgen_db<br>";
} catch (Exception $e) {
    echo "<span class='error'>❌ config.php connection: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}

// Test database.php
echo "<h2>Testing database.php</h2>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    $pdo2 = $db->getConnection();
    echo "<span class='ok'>✅ database.php connection: OK</span><br>";
} catch (Exception $e) {
    echo "<span class='error'>❌ database.php connection: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}

// Test db.php
echo "<h2>Testing db.php</h2>";
try {
    require_once 'config/db.php';
    $db2 = Database::getInstance();
    $pdo3 = $db2->getConnection();
    echo "<span class='ok'>✅ db.php connection: OK</span><br>";
} catch (Exception $e) {
    echo "<span class='error'>❌ db.php connection: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}

// Test tables
echo "<h2>Testing Database Tables</h2>";
try {
    $pdo = config::getConnexion();
    $tables = [
        // Base module
        'users', 'categorie', 'jeu', 'jeux_owned', 'livraisons', 
        'trajets', 'reclamation', 'traitement', 'historique', 'password_resets',
        // Blog module
        'article', 'categorie_article', 'commentaire', 'article_rating',
        // Event module
        'evenement', 'categoriev', 'reservation'
    ];
    
    echo "<table border='1' cellpadding='5'><tr><th>Table</th><th>Status</th></tr>";
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            // Get row count
            $countStmt = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $countStmt->fetch()['count'];
            echo "<tr><td>$table</td><td class='ok'>✅ EXISTS ($count rows)</td></tr>";
        } else {
            echo "<tr><td>$table</td><td class='error'>❌ NOT FOUND</td></tr>";
        }
    }
    echo "</table>";
} catch (Exception $e) {
    echo "<span class='error'>❌ Table check error: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}

// Test paths
echo "<h2>Testing Path Configuration</h2>";
try {
    require_once 'config/paths.php';
    echo "<span class='ok'>✅ Paths loaded</span><br>";
    echo "ROOT_PATH: " . ROOT_PATH . "<br>";
    echo "WEB_ROOT: " . WEB_ROOT . "<br>";
    echo "CONFIG_PATH: " . CONFIG_PATH . "<br>";
} catch (Exception $e) {
    echo "<span class='error'>❌ Paths error: " . htmlspecialchars($e->getMessage()) . "</span><br>";
}

echo "<hr>";
echo "<h2>Quick Links</h2>";
echo "<ul>";
echo "<li><a href='view/frontoffice/index.php'>Base Module - Home</a></li>";
echo "<li><a href='view/frontoffice/blog.php'>Blog Module - Front</a></li>";
echo "<li><a href='index.php?c=front&a=events'>Event Module - Events</a></li>";
echo "<li><a href='view/backoffice/accueil.php'>Admin Dashboard</a></li>";
echo "</ul>";

echo "</body></html>";
?>

