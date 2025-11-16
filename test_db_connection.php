<?php
// Test database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Database Connection</h2>";

require_once __DIR__ . "/config/config.php";

try {
    $pdo = config::getConnexion();
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM utilisateur");
    $result = $stmt->fetch();
    echo "<p>Current users in database: " . $result['count'] . "</p>";
    
    // Test table structure
    $stmt = $pdo->query("DESCRIBE utilisateur");
    $columns = $stmt->fetchAll();
    echo "<h3>Table Structure:</h3><ul>";
    foreach ($columns as $col) {
        echo "<li>" . $col['Field'] . " (" . $col['Type'] . ")</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
?>

