<?php
/**
 * Diagnostic Script - Check Database Connection and Article Retrieval
 * Save as: app/Views/frontoffice/diagnostic.php
 * Access: http://localhost/PROJET_WEB_NEXTGEN/app/Views/frontoffice/diagnostic.php
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../app/Controllers/BlogController.php';

echo "<h1>🔍 Blog Diagnostic Report</h1>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px; font-size: 12px;'>";

// 1. Check Database Connection
echo "\n========== 1. DATABASE CONNECTION ==========\n";
try {
    $database = Database::getInstance();
    $pdo = $database->getConnection();

    if ($pdo) {
        echo "✅ PDO Connection: SUCCESS\n";
        echo "PDO Type: " . get_class($pdo) . "\n";
    } else {
        echo "❌ PDO Connection: FAILED - Connection is NULL\n";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Database Exception: " . $e->getMessage() . "\n";
    exit;
}

// 2. Check article table exists
echo "\n========== 2. TABLE EXISTENCE ==========\n";
try {
    $query = "SHOW TABLES LIKE 'article'";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo "✅ Table 'article': EXISTS\n";
    } else {
        echo "❌ Table 'article': NOT FOUND\n";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Error checking table: " . $e->getMessage() . "\n";
}

// 3. Count articles
echo "\n========== 3. ARTICLE COUNT ==========\n";
try {
    $query = "SELECT COUNT(*) as count FROM article";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'] ?? 0;

    echo "Total articles in database: " . $count . "\n";

    if ($count === 0) {
        echo "⚠️  WARNING: No articles found in database!\n";
    }
} catch (Exception $e) {
    echo "❌ Error counting articles: " . $e->getMessage() . "\n";
}

// 4. Sample raw article data
echo "\n========== 4. RAW ARTICLE DATA ==========\n";
try {
    $query = "SELECT * FROM article LIMIT 3";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($articles)) {
        echo "❌ No articles retrieved\n";
    } else {
        echo "✅ Retrieved " . count($articles) . " sample articles:\n\n";
        foreach ($articles as $idx => $article) {
            echo "Article " . ($idx + 1) . ":\n";
            foreach ($article as $key => $value) {
                echo "  $key: " . ($value ?: 'NULL') . "\n";
            }
            echo "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error retrieving articles: " . $e->getMessage() . "\n";
}

// 5. Check JOIN query
echo "\n========== 5. JOIN QUERY TEST ==========\n";
try {
    $query = "SELECT a.*, c.nom as categorie_nom, c.slug as categorie_slug
             FROM article a
             LEFT JOIN categorie_article c ON a.id_categorie = c.id_categorie
             ORDER BY a.date_publication DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "✅ JOIN Query: SUCCESS\n";
    echo "Articles returned: " . count($articles) . "\n";

    if (count($articles) > 0) {
        echo "\nFirst article with JOIN:\n";
        foreach ($articles[0] as $key => $value) {
            echo "  $key: " . ($value ?: 'NULL') . "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ JOIN Query Error: " . $e->getMessage() . "\n";
    echo "Error Info: " . implode(", ", $pdo->errorInfo()) . "\n";
}

// 6. Test BlogController
echo "\n========== 6. BLOG CONTROLLER TEST ==========\n";
try {
    $blogController = new BlogController();
    $articles = $blogController->index();

    echo "✅ BlogController::index(): SUCCESS\n";
    echo "Articles returned: " . count($articles) . "\n";

    if (count($articles) > 0) {
        echo "\nFirst formatted article:\n";
        $first = $articles[0];
        foreach ($first as $key => $value) {
            if (is_array($value)) {
                echo "  $key: [ARRAY]\n";
            } else {
                echo "  $key: " . substr((string)$value, 0, 50) . "\n";
            }
        }
    } else {
        echo "⚠️  WARNING: No articles returned from controller!\n";
    }
} catch (Exception $e) {
    echo "❌ BlogController Error: " . $e->getMessage() . "\n";
}

// 7. Check category table
echo "\n========== 7. CATEGORY TABLE ==========\n";
try {
    $query = "SELECT COUNT(*) as count FROM categorie_article";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['count'] ?? 0;

    echo "✅ Total categories: " . $count . "\n";

    if ($count > 0) {
        $query = "SELECT * FROM categorie_article LIMIT 5";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo "Sample categories:\n";
        foreach ($categories as $cat) {
            echo "  - " . $cat['nom'] . " (ID: " . $cat['id_categorie'] . ")\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error checking categories: " . $e->getMessage() . "\n";
}

echo "\n========== END OF DIAGNOSTIC ==========\n";
echo "</pre>";

// Add some styling
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1 { color: #333; }
    pre { color: #333; line-height: 1.6; }
</style>";
?>