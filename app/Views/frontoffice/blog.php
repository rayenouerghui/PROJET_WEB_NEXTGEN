<?php
/**
 * Vue : blog.php
 * Blog avec filtrage par catégories + Système de likes + Réponses aux commentaires + Notation d'articles
 */

require_once __DIR__ . '/../../../app/Controllers/BlogController.php';
require_once __DIR__ . '/../../../app/Controllers/CommentaireController.php';
require_once __DIR__ . '/../../../app/Controllers/CategoryController.php';
require_once __DIR__ . '/../../../app/Controllers/ArticleRatingController.php';
require_once __DIR__ . '/../../../app/Controllers/AISummaryController.php'; // NOUVEAU

$blogController = new BlogController();
$commentController = new CommentController();
$categoryController = new CategoryController();
$ratingController = new ArticleRatingController();
$aiController = new AISummaryController(); // NOUVEAU

$articles = $blogController->index();
$categories = $categoryController->getAllCategories();

$errors = [];
$field_errors = ['nom_visiteur' => '', 'contenu' => ''];
$old_values = ['nom_visiteur' => '', 'contenu' => ''];
$action = $_POST['action'] ?? '';

// ===== CRÉATION DE COMMENTAIRE OU RÉPONSE =====
if ($action === 'add_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_article = (int)($_POST['id_article'] ?? 0);
    $nom_visiteur = trim($_POST['nom_visiteur'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $id_parent = isset($_POST['id_parent']) ? (int)$_POST['id_parent'] : null;

    $old_values['nom_visiteur'] = htmlspecialchars($nom_visiteur);
    $old_values['contenu'] = htmlspecialchars($contenu);

    $result = $commentController->create($id_article, $nom_visiteur, $contenu, $id_parent);

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}

// ===== MISE À JOUR DE COMMENTAIRE =====
if ($action === 'update_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_commentaire = (int)($_POST['id_commentaire'] ?? 0);
    $contenu = trim($_POST['contenu'] ?? '');

    header('Content-Type: application/json');
    if ($id_commentaire <= 0 || empty($contenu)) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit;
    }

    $result = $commentController->update($id_commentaire, $contenu);
    echo json_encode($result);
    exit;
}

// ===== SUPPRESSION DE COMMENTAIRE =====
if ($action === 'delete_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_commentaire = (int)($_POST['id_commentaire'] ?? 0);

    header('Content-Type: application/json');
    if ($id_commentaire <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        exit;
    }

    $result = $commentController->delete($id_commentaire);
    echo json_encode($result);
    exit;
}

// ===== AJOUT DE LIKE =====
if ($action === 'like_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_commentaire = (int)($_POST['id_commentaire'] ?? 0);

    header('Content-Type: application/json');
    if ($id_commentaire <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        exit;
    }

    $result = $commentController->addLike($id_commentaire);
    echo json_encode($result);
    exit;
}

// ===== RETRAIT DE LIKE =====
if ($action === 'unlike_comment' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_commentaire = (int)($_POST['id_commentaire'] ?? 0);

    header('Content-Type: application/json');
    if ($id_commentaire <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID invalide']);
        exit;
    }

    $result = $commentController->removeLike($id_commentaire);
    echo json_encode($result);
    exit;
}

// ===== AJOUT/MODIFICATION DE NOTATION =====
if ($action === 'add_rating' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_article = (int)($_POST['id_article'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);

    header('Content-Type: application/json');
    if ($id_article <= 0 || $rating < 1 || $rating > 5) {
        echo json_encode(['success' => false, 'message' => 'Données invalides']);
        exit;
    }

    try {
        $result = $ratingController->addRating($id_article, $rating);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
        ]);
    }
    exit;
}

// ===== SUPPRESSION DE NOTATION =====
if ($action === 'remove_rating' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_article = (int)($_POST['id_article'] ?? 0);

    header('Content-Type: application/json');
    if ($id_article <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID article invalide']);
        exit;
    }

    try {
        $result = $ratingController->removeRating($id_article);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
        ]);
    }
    exit;
}

// ===== RÉCUPÉRATION DES STATS DE NOTATION =====
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_rating_stats'])) {
    $id_article = (int)($_GET['id_article'] ?? 0);

    header('Content-Type: application/json');
    if ($id_article <= 0) {
        echo json_encode([
                'success' => false,
                'message' => 'ID article invalide',
                'stats' => [
                        'average_rating' => 0,
                        'total_votes' => 0,
                        'average_rating_rounded' => '0.0'
                ],
                'user_rating' => 0
        ]);
        exit;
    }

    try {
        $result = $ratingController->getRatingStats($id_article);

        // Vérifier et corriger les données
        if (!isset($result['stats']) || $result['stats'] === null) {
            $result['stats'] = [
                    'average_rating' => 0,
                    'total_votes' => 0,
                    'average_rating_rounded' => '0.0'
            ];
        }

        if (!isset($result['user_rating'])) {
            $result['user_rating'] = 0;
        }

        $result['success'] = true;

        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
                'stats' => [
                        'average_rating' => 0,
                        'total_votes' => 0,
                        'average_rating_rounded' => '0.0'
                ],
                'user_rating' => 0
        ]);
    }
    exit;
}

// ===== RÉCUPÉRATION DES ARTICLES LES MIEUX NOTÉS =====
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_top_rated'])) {
    $limit = (int)($_GET['limit'] ?? 5);

    header('Content-Type: application/json');
    try {
        $result = $ratingController->getTopRatedArticles($limit);
        echo json_encode($result);
    } catch (Exception $e) {
        echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage(),
                'articles' => []
        ]);
    }
    exit;
}

// ===== GÉNÉRATION DE RÉSUMÉ IA =====
if ($action === 'generate_ai_summary' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $articleText = $_POST['article_text'] ?? '';
    $articleId = (int)($_POST['article_id'] ?? 0);

    if (empty($articleText) || $articleId <= 0) {
        echo json_encode([
                'success' => false,
                'message' => 'Texte ou ID article invalide'
        ]);
        exit;

    }



    try {
        // Générer le résumé
        $result = $aiController->generateSummary($articleText);

        // Ajouter les informations de quota
        $result['remaining'] = 999;
        $result['used_today'] = 0;

        echo json_encode($result);

    } catch (Exception $e) {
        echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la génération du résumé',

        ]);
    }
    exit;
}

// ===== RÉCUPÉRATION DES STATS DE NOTATION =====
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_rating_stats'])) {
    $id_article = (int)($_GET['id_article'] ?? 0);

    header('Content-Type: application/json');
    if ($id_article <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID article invalide']);
        exit;
    }

    $result = $ratingController->getRatingStats($id_article);
    echo json_encode($result);
    exit;
}

// ===== RÉCUPÉRATION DES ARTICLES LES MIEUX NOTÉS =====
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_top_rated'])) {
    $limit = (int)($_GET['limit'] ?? 5);

    header('Content-Type: application/json');
    $result = $ratingController->getTopRatedArticles($limit);
    echo json_encode($result);
    exit;
}

// ===== RÉCUPÉRATION DES COMMENTAIRES EN JSON =====
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_comments'])) {
    $id_article = (int)($_GET['id_article'] ?? 0);
    header('Content-Type: application/json');
    if ($id_article <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID article invalide', 'comments' => []]);
        exit;
    }

    $result = $commentController->getByArticleJSON($id_article);
    echo json_encode($result);
    exit;
}

// ===== RÉCUPÉRATION DES ARTICLES PAR CATÉGORIE =====
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_articles_by_category'])) {
    $id_categorie = (int)($_GET['id_categorie'] ?? 0);
    header('Content-Type: application/json');

    if ($id_categorie <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID catégorie invalide', 'articles' => []]);
        exit;
    }

    $articles = $blogController->getByCategory($id_categorie);
    echo json_encode(['success' => true, 'articles' => $articles]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog - NextGen</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../../public/css/front.css">
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/style.css" />
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/frontoffice.css" />
    <link rel="stylesheet" href="/PROJET_WEB_NEXTGEN/public/css/blog.css" />

    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">

    <style>
        .blog-card { cursor: pointer; transition: all 0.3s; }
        .blog-card:hover { transform: translateY(-8px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }

        .error-message {
            color: #dc3545;
            font-size: 14px;
            font-weight: 500;
            display: block;
            margin-top: 5px;
            margin-bottom: 8px;
        }

        .comment-input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .comment-input:focus {
            border-color: #007bff;
            outline: none;
        }

        .comment-input.error {
            border-color: #dc3545;
            background-color: #fff5f5;
        }

        .comment-textarea {
            resize: vertical;
            min-height: 100px;
        }

        .submit-comment-btn {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: background 0.3s;
        }

        .submit-comment-btn:hover {
            background: #0056b3;
        }

        .submit-reply-btn {
            background: #17a2b8;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.3s;
        }

        .submit-reply-btn:hover {
            background: #0a7f8b;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-group label::after {
            content: " *";
            color: #dc3545;
        }

        .add-comment-form h4 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .comment-actions {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-top: 10px;
        }

        .like-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
            color: #666;
            font-size: 13px;
            font-weight: 600;
        }

        .like-btn:hover {
            background: rgba(0, 212, 255, 0.1);
            color: #00d4ff;
        }

        .like-btn.liked {
            background: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
        }

        .like-count {
            display: inline-block;
            min-width: 20px;
            text-align: center;
        }

        .reply-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px 12px;
            border-radius: 4px;
            transition: all 0.3s ease;
            color: #666;
            font-size: 13px;
            font-weight: 600;
        }

        .reply-btn:hover {
            background: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }

        .replies-container {
            margin-top: 20px;
            margin-left: 40px;
            border-left: 3px solid #17a2b8;
            padding-left: 20px;
        }

        .reply-comment {
            background: rgba(23, 162, 184, 0.05);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .reply-form {
            background: rgba(23, 162, 184, 0.08);
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
        }

        .reply-form.show {
            display: block;
        }

        .reply-form.show input,
        .reply-form.show textarea {
            background: #ffffff !important;
            color: #333 !important;
        }

        .cancel-reply-btn {
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            margin-left: 10px;
        }

        .cancel-reply-btn:hover {
            background: #5a6268;
        }

        /* ===== CATEGORY FILTER SECTION STYLES ===== */
        .category-filter-section {
            background: linear-gradient(135deg, #1a1a3e 0%, #16213e 100%);
            padding: 30px 0 20px 0;
            margin: 0;
        }

        .category-filter-title {
            font-size: 20px;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
            text-align: center;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .category-filter-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin-bottom: 0;
        }

        .category-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s ease;
        }

        .category-btn:hover {
            background: rgba(102, 126, 234, 0.3);
            border-color: #00d4ff;
            transform: translateY(-2px);
        }

        .category-btn.active {
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            border-color: #00d4ff;
            color: #1a1a3e;
            font-weight: 700;
        }

        .active-filter-info {
            text-align: center;
            color: #00d4ff;
            font-size: 12px;
            font-weight: 600;
            margin-top: 10px;
            display: none;
        }

        .active-filter-info.show {
            display: block;
        }

        .blog-section {
            background: linear-gradient(135deg, #1a1a3e 0%, #16213e 100%);
            padding: 40px 0;
            min-height: 50vh;
        }

        .page-title {
            text-align: center;
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 40px;
        }

        .no-articles {
            text-align: center;
            color: #888;
            padding: 60px 20px;
            font-size: 16px;
        }

        /* ===== RATING SYSTEM STYLES ===== */
        .rating-container {
            margin: 30px 0;
            padding: 25px;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.08) 0%, rgba(0, 154, 204, 0.05) 100%);
            border-radius: 15px;
            border: 1px solid rgba(0, 212, 255, 0.2);
            text-align: center;
        }

        .rating-title {
            font-size: 20px;
            font-weight: 700;
            color: #00d4ff;
            margin-bottom: 15px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .rating-stars {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .rating-star {
            font-size: 36px;
            color: #555;
            cursor: pointer;
            transition: all 0.3s ease;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
        }

        .rating-star:hover,
        .rating-star.hovered {
            color: #ffc107;
            transform: scale(1.3);
        }

        .rating-star.active {
            color: #ffc107;
            text-shadow: 0 0 15px rgba(255, 193, 7, 0.6);
        }

        .rating-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .average-rating {
            font-size: 28px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            padding: 10px 20px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .star-rating-display {
            display: inline-flex;
            gap: 3px;
        }

        .star-filled {
            color: #ffc107;
            font-size: 20px;
        }

        .star-empty {
            color: rgba(255, 255, 255, 0.5);
            font-size: 20px;
        }

        .total-votes {
            font-size: 16px;
            color: #aaa;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px 15px;
            border-radius: 20px;
        }

        .your-rating {
            font-size: 16px;
            color: #00d4ff;
            font-weight: 700;
            margin-bottom: 15px;
            display: none;
        }

        .rating-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .rating-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Montserrat', sans-serif;
        }

        .submit-rating-btn {
            background: linear-gradient(135deg, #00d4ff 0%, #0099cc 100%);
            color: #1a1a3e;
        }

        .submit-rating-btn:hover:not(:disabled) {
            background: linear-gradient(135deg, #00b8e6 0%, #0082a6 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
        }

        .remove-rating-btn {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
            border: 2px solid #ff6b6b;
        }

        .remove-rating-btn:hover {
            background: rgba(255, 107, 107, 0.2);
            transform: translateY(-2px);
        }

        .rating-message {
            margin-top: 10px;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            display: none;
            text-align: center;
        }

        .rating-message.success {
            display: block;
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid #28a745;
        }

        .rating-message.error {
            display: block;
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid #dc3545;
        }

        .rating-section {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.05) 0%, rgba(0, 154, 204, 0.02) 100%);
            padding: 20px;
            border-radius: 12px;
            margin-top: 25px;
        }

        .top-rated-btn {
            margin-top: 10px;
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #1a1a3e;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .top-rated-btn:hover {
            background: linear-gradient(135deg, #ffb300 0%, #f57c00 100%);
            transform: translateY(-2px);
        }

        /* ===== AI SUMMARY STYLES ===== */
        .ai-summary-section {
            margin: 40px 0 30px 0;
            padding: 30px;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.08) 0%, rgba(117, 90, 162, 0.05) 100%);
            border-radius: 15px;
            border: 1px solid rgba(102, 126, 234, 0.15);
            position: relative;
            overflow: hidden;
        }

        .ai-summary-section::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(102, 126, 234, 0.1) 0%, transparent 70%);
            z-index: 0;
        }

        .ai-summary-section > * {
            position: relative;
            z-index: 1;
        }

        .ai-summary-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .ai-summary-btn:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .ai-summary-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .ai-summary-btn-loading .ai-summary-icon {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .ai-summary-result {
            animation: slideDown 0.5s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .ai-source-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ai-source-huggingface {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
            border: 1px solid rgba(102, 126, 234, 0.3);
        }

        .ai-source-local {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
            border: 1px solid rgba(108, 117, 125, 0.3);
        }
    </style>
</head>

<body>
<!-- ===== HEADER ===== -->
<header>
    <div class="container nav">
        <div class="left">
            <a href="index.php" class="logo">NextGen</a>
            <nav class="menu">
                <a href="index.php">Accueil</a>
                <a href="catalog.php">Produits</a>
                <a href="blog.php" class="active">Blog</a>
                <a href="apropos.php">À Propos</a>
            </nav>
        </div>
        <div>
            <a href="admin.php" style="color:#4f46e5;font-weight:700;">Administration</a>
        </div>
    </div>
</header>

<!-- ===== CATEGORY FILTER SECTION ===== -->
<section class="category-filter-section">
    <div class="container">
        <h2 class="category-filter-title">Filtrer par Catégorie</h2>

        <?php if (!empty($categories)): ?>
            <div class="category-filter-buttons" id="category-buttons">
                <!-- All Articles Button -->
                <button class="category-btn active" onclick="showAllArticles()">
                    Tous les Articles
                </button>

                <!-- Category Buttons -->
                <?php foreach ($categories as $category): ?>
                    <button class="category-btn" onclick="filterByCategory(<?php echo $category['id_categorie']; ?>, '<?php echo htmlspecialchars($category['nom']); ?>')">
                        <?php echo htmlspecialchars($category['nom']); ?>
                    </button>
                <?php endforeach; ?>

                <!-- Top Rated Articles Button -->
                <button class="category-btn" onclick="showTopRatedArticles()">
                    <i class="bi bi-star-fill"></i> Les mieux notés
                </button>
            </div>
        <?php endif; ?>

        <div class="active-filter-info" id="active-filter-info">
            Affichage de la catégorie: <span id="filter-category-name"></span>
        </div>
    </div>
</section>

<!-- ===== LISTE DES ARTICLES ===== -->
<section class="blog-section">
    <div class="container">
        <h1 class="page-title" id="articles-title">Articles</h1>

        <?php if (empty($articles) || isset($articles['error'])): ?>
            <div class="no-articles">
                <p>Aucun article disponible pour le moment.</p>
            </div>
        <?php else: ?>
            <div class="blog-grid" id="blog-list">
                <?php foreach ($articles as $article): ?>
                    <article class="blog-card" onclick="openArticlePopup(<?php echo $article['id_article']; ?>)">
                        <div class="blog-image">
                            <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['titre']); ?>">
                        </div>
                        <div class="blog-content">
                            <h2><?php echo htmlspecialchars($article['titre']); ?></h2>
                            <p><?php echo htmlspecialchars($article['content']); ?></p>
                            <div class="article-meta">
                                <small>Publié le <?php echo htmlspecialchars($article['date_publication']); ?></small>
                                <span class="category-badge"><?php echo htmlspecialchars($article['categorie']); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- ===== POPUP ARTICLE COMPLET ===== -->
<div id="article-popup" class="article-popup">
    <div class="article-popup-content">
        <span class="close-article-popup" onclick="closeArticlePopup(event)">×</span>
        <div id="article-popup-content-inner"></div>
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="container">
        <p>© 2025 NextGen. Tous droits réservés.</p>
    </div>
</footer>

<script>
    const articlesData = <?php echo json_encode(array_column($articles ?? [], null, 'id_article')); ?>;
    const allArticles = <?php echo json_encode($articles ?? []); ?>;
    let currentCategoryId = null;
    const likedComments = new Set();
    let currentRating = 0;
    let hoverRating = 0;
    let isSubmittingRating = false;

    function filterByCategory(categoryId, categoryName) {
        currentCategoryId = categoryId;
        document.getElementById('articles-title').textContent = categoryName;
        document.getElementById('active-filter-info').classList.add('show');
        document.getElementById('filter-category-name').textContent = categoryName;

        const allButtons = document.querySelectorAll('.category-btn');
        allButtons.forEach((btn, index) => {
            if (index > 0 && btn.textContent.trim() === categoryName) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });

        fetch(`blog.php?get_articles_by_category=1&id_categorie=${categoryId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success && data.articles.length > 0) {
                    displayArticles(data.articles);
                } else {
                    document.getElementById('blog-list').innerHTML = '<div class="no-articles"><p>Aucun article trouvé pour cette catégorie.</p></div>';
                }
            })
            .catch(err => {
                console.error('Erreur:', err);
                document.getElementById('blog-list').innerHTML = '<div class="no-articles"><p>Erreur lors du chargement.</p></div>';
            });
    }

    function showAllArticles() {
        currentCategoryId = null;
        document.getElementById('articles-title').textContent = 'Articles';
        document.getElementById('active-filter-info').classList.remove('show');

        document.querySelectorAll('.category-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        const allButtons = document.querySelectorAll('.category-btn');
        if (allButtons.length > 0) {
            allButtons[0].classList.add('active');
        }

        displayArticles(allArticles);
    }

    function showTopRatedArticles() {
        fetch('blog.php?get_top_rated=1&limit=10')
            .then(res => res.json())
            .then(data => {
                if (data.success && data.articles && data.articles.length > 0) {
                    document.getElementById('articles-title').textContent = 'Articles les mieux notés';
                    document.getElementById('active-filter-info').classList.add('show');
                    document.getElementById('filter-category-name').textContent = 'Les mieux notés';

                    // Mettre à jour les boutons actifs
                    document.querySelectorAll('.category-btn').forEach(btn => {
                        btn.classList.remove('active');
                        if (btn.innerHTML.includes('bi-star-fill')) {
                            btn.classList.add('active');
                        }
                    });

                    displayArticles(data.articles);
                } else {
                    document.getElementById('blog-list').innerHTML = '<div class="no-articles"><p>Aucun article noté pour le moment.</p></div>';
                }
            })
            .catch(err => {
                console.error('Erreur:', err);
                document.getElementById('blog-list').innerHTML = '<div class="no-articles"><p>Erreur lors du chargement.</p></div>';
            });
    }

    function displayArticles(articles) {
        const blogList = document.getElementById('blog-list');

        if (articles.length === 0) {
            blogList.innerHTML = '<div class="no-articles"><p>Aucun article disponible.</p></div>';
            return;
        }

        let html = '';
        articles.forEach(article => {
            html += `
                <article class="blog-card" onclick="openArticlePopup(${article.id_article})">
                    <div class="blog-image">
                        <img src="${escapeHtml(article.image)}" alt="${escapeHtml(article.titre)}">
                    </div>
                    <div class="blog-content">
                        <h2>${escapeHtml(article.titre)}</h2>
                        <p>${escapeHtml(article.content)}</p>
                        <div class="article-meta">
                            <small>Publié le ${escapeHtml(article.date_publication)}</small>
                            <span class="category-badge">${escapeHtml(article.categorie)}</span>
                        </div>
                    </div>
                </article>
            `;
        });

        blogList.innerHTML = html;
    }

    function openArticlePopup(articleId) {
        const article = articlesData[articleId];
        if (!article) {
            alert('Article non trouvé');
            return;
        }

        const popup = document.getElementById('article-popup');
        const content = document.getElementById('article-popup-content-inner');

        // Afficher un message de chargement
        content.innerHTML = `
            <div style="text-align: center; padding: 40px; color: white;">
                <div class="loading" style="width: 40px; height: 40px; border: 4px solid rgba(255,255,255,.3); border-top-color: #fff; border-radius: 50%; margin: 0 auto 20px; animation: spin 1s linear infinite;"></div>
                <p>Chargement de l'article...</p>
            </div>
        `;

        popup.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Charger les commentaires et les stats de rating
        Promise.all([
            fetch(`blog.php?get_comments=1&id_article=${articleId}`).then(res => res.json()),
            fetch(`blog.php?get_rating_stats=1&id_article=${articleId}`).then(res => res.json())
        ])
            .then(([commentsData, ratingData]) => {
                const comments = commentsData.comments || [];

                let html = `
                <img src="${escapeHtml(article.image)}" alt="${escapeHtml(article.titre)}" class="article-popup-image">
                <h2 class="article-popup-title">${escapeHtml(article.titre)}</h2>
                <div class="article-popup-meta">
                    <small>Publié le ${escapeHtml(article.date_publication)}</small>
                    <span class="category-badge">${escapeHtml(article.categorie)}</span>
                </div>
                <div class="article-popup-text">
                    ${escapeHtml(article.full_content)}
                </div>

                <!-- RATING SYSTEM -->
                <div class="rating-container">
                    <h3 class="rating-title">Noter cet article</h3>

                    <div class="rating-stars" id="rating-stars-${articleId}">
                        <button class="rating-star" data-value="1">★</button>
                        <button class="rating-star" data-value="2">★</button>
                        <button class="rating-star" data-value="3">★</button>
                        <button class="rating-star" data-value="4">★</button>
                        <button class="rating-star" data-value="5">★</button>
                    </div>

                    <div class="rating-info">
                        <div class="average-rating" id="average-rating-${articleId}">
    ${ratingData && ratingData.stats && ratingData.stats.average_rating_rounded ? parseFloat(ratingData.stats.average_rating_rounded).toFixed(1) + ' ★' : '0.0 ★'}
</div>
                        <div class="total-votes" id="total-votes-${articleId}">
    ${ratingData && ratingData.stats ? (ratingData.stats.total_votes || 0) + ' votes' : '0 votes'}
</div>
                    </div>

                    <div class="your-rating" id="current-user-rating-${articleId}" style="display: ${ratingData.success && ratingData.user_rating > 0 ? 'block' : 'none'}">
                        ${ratingData.success && ratingData.user_rating > 0 ? `Votre note: <strong>${ratingData.user_rating}/5</strong>` : ''}
                    </div>

                    <div class="rating-actions">
                        <button class="rating-btn submit-rating-btn" id="submit-rating-btn-${articleId}" onclick="submitRating(${articleId})" ${ratingData.success && ratingData.user_rating > 0 ? 'disabled' : ''}>Soumettre la note</button>
                        <button class="rating-btn remove-rating-btn" id="remove-rating-btn-${articleId}" onclick="removeRating(${articleId})" style="display: ${ratingData.success && ratingData.user_rating > 0 ? 'inline-block' : 'none'}">Supprimer ma note</button>
                    </div>

                    <div class="rating-message" id="rating-message-${articleId}"></div>
                </div>

                <!-- AI SUMMARY SECTION -->
                <div class="ai-summary-section">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px;">
                            🤖
                        </div>
                        <div>
                            <h3 style="margin: 0; color: #333; font-size: 20px; font-weight: 700;">Résumé Express par IA</h3>
                            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Obtenez un résumé intelligent en moins de 30 secondes</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap; margin-bottom: 20px;">
                        <button id="ai-summary-btn-${articleId}" onclick="generateAISummary(${articleId})"
                                class="ai-summary-btn">
                            <span class="ai-summary-icon" style="font-size: 20px;">🤖</span>
                            <span class="ai-summary-text">Générer le résumé IA</span>
                        </button>


                    </div>

                    <div id="ai-summary-result-${articleId}" class="ai-summary-result" style="display: none;">
                        <!-- Le résumé apparaîtra ici -->
                    </div>


                </div>

                <div class="article-popup-comments">
                    <h3>Commentaires (${comments.length})</h3>
            `;

                if (comments.length === 0) {
                    html += `<div class="no-comments">Aucun commentaire pour le moment. Soyez le premier à commenter !</div>`;
                } else {
                    comments.forEach(c => {
                        html += renderComment(c, articleId);
                    });
                }

                html += `
                <form class="add-comment-form" onsubmit="addComment(event, ${articleId})">
                    <h4>Ajouter un commentaire</h4>

                    <div class="form-group">
                        <label>Votre nom</label>
                        <input type="text" name="nom_visiteur" class="comment-input" placeholder="Entrez votre nom..." style="background: #ffffff; color: #333; padding: 14px 16px;">
                        <span class="error-message" id="error-nom" style="display: none;"></span>
                    </div>

                    <div class="form-group">
                        <label>Votre commentaire</label>
                        <textarea name="contenu" class="comment-input comment-textarea" rows="4" placeholder="Écrivez votre commentaire..." style="background: #ffffff; color: #333; padding: 14px 16px;"></textarea>
                        <span class="error-message" id="error-contenu" style="display: none;"></span>
                    </div>

                    <button type="submit" class="submit-comment-btn">Publier le commentaire</button>
                </form>
                </div>
            `;

                content.innerHTML = html;

                // Initialiser le système de rating pour cet article
                initializeRatingSystem(articleId, ratingData);
            })
            .catch(err => {
                console.error(err);
                content.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #ff6b6b;">
                        <h3>Erreur de chargement</h3>
                        <p>Impossible de charger les données de l'article.</p>
                        <button onclick="closeArticlePopup()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-top: 20px;">Fermer</button>
                    </div>
                `;
            });
    }

    function initializeRatingSystem(articleId, ratingData) {
        currentRating = ratingData.success ? ratingData.user_rating : 0;
        hoverRating = 0;
        isSubmittingRating = false;

        const starsContainer = document.getElementById(`rating-stars-${articleId}`);
        if (!starsContainer) return;

        const stars = starsContainer.querySelectorAll('.rating-star');

        // Mettre à jour l'affichage initial des étoiles
        updateStarsDisplay(articleId);

        // Ajouter les événements aux étoiles
        stars.forEach((star, index) => {
            star.addEventListener('mouseenter', () => {
                if (isSubmittingRating) return;
                hoverRating = index + 1;
                updateStarsDisplay(articleId);
            });

            star.addEventListener('mouseleave', () => {
                if (isSubmittingRating) return;
                hoverRating = 0;
                updateStarsDisplay(articleId);
            });

            star.addEventListener('click', () => {
                if (isSubmittingRating) return;
                currentRating = index + 1;
                updateStarsDisplay(articleId);

                // Activer le bouton de soumission
                const submitBtn = document.getElementById(`submit-rating-btn-${articleId}`);
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            });
        });
    }

    function updateStarsDisplay(articleId) {
        const starsContainer = document.getElementById(`rating-stars-${articleId}`);
        if (!starsContainer) return;

        const stars = starsContainer.querySelectorAll('.rating-star');
        const displayRating = hoverRating || currentRating;

        stars.forEach((star, index) => {
            star.classList.remove('active', 'hovered');

            if (displayRating >= index + 1) {
                star.classList.add(hoverRating ? 'hovered' : 'active');
            }
        });
    }

    function submitRating(articleId) {
        if (isSubmittingRating) return;

        if (currentRating < 1 || currentRating > 5) {
            showRatingMessage(articleId, 'Veuillez sélectionner une note entre 1 et 5 étoiles', 'error');
            return;
        }

        // Désactiver le bouton pendant l'envoi
        isSubmittingRating = true;
        const submitBtn = document.getElementById(`submit-rating-btn-${articleId}`);
        const originalText = submitBtn ? submitBtn.innerHTML : 'Soumettre la note';

        if (submitBtn) {
            submitBtn.innerHTML = '<div class="loading" style="display: inline-block; margin-right: 8px;"></div> Envoi...';
            submitBtn.disabled = true;
        }

        const formData = new FormData();
        formData.append('action', 'add_rating');
        formData.append('id_article', articleId);
        formData.append('rating', currentRating);

        fetch('blog.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            }
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`Erreur HTTP ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('Réponse notation:', data);

                if (data.success) {
                    showRatingMessage(articleId, data.message, 'success');

                    // Mettre à jour l'affichage
                    if (data.stats) {
                        updateRatingDisplay(articleId, data.stats, currentRating);
                    }

                    // Afficher la note de l'utilisateur et le bouton de suppression
                    const userRatingEl = document.getElementById(`current-user-rating-${articleId}`);
                    const removeBtn = document.getElementById(`remove-rating-btn-${articleId}`);

                    if (userRatingEl) {
                        userRatingEl.innerHTML = `Votre note: <strong>${currentRating}/5</strong>`;
                        userRatingEl.style.display = 'block';
                    }

                    if (removeBtn) {
                        removeBtn.style.display = 'inline-block';
                    }

                    // Désactiver le bouton de soumission
                    if (submitBtn) {
                        submitBtn.disabled = true;
                    }
                } else {
                    showRatingMessage(articleId, data.message || 'Erreur inconnue', 'error');

                    // Réactiver le bouton
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                }
            })
            .catch(err => {
                console.error('Erreur complète:', err);
                showRatingMessage(articleId, 'Erreur de connexion au serveur. Veuillez réessayer.', 'error');

                // Réactiver le bouton
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
            })
            .finally(() => {
                isSubmittingRating = false;
                if (submitBtn) {
                    submitBtn.innerHTML = originalText;
                }
            });
    }

    function removeRating(articleId) {
        if (isSubmittingRating) return;

        if (!confirm('Êtes-vous sûr de vouloir supprimer votre note ?')) return;

        // Désactiver le bouton pendant l'envoi
        isSubmittingRating = true;
        const removeBtn = document.getElementById(`remove-rating-btn-${articleId}`);
        const originalText = removeBtn ? removeBtn.innerHTML : 'Supprimer ma note';

        if (removeBtn) {
            removeBtn.innerHTML = '<div class="loading" style="display: inline-block; margin-right: 8px;"></div> Suppression...';
            removeBtn.disabled = true;
        }

        const formData = new FormData();
        formData.append('action', 'remove_rating');
        formData.append('id_article', articleId);

        fetch('blog.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Cache-Control': 'no-cache'
            }
        })
            .then(res => {
                if (!res.ok) {
                    throw new Error(`Erreur HTTP ${res.status}`);
                }
                return res.json();
            })
            .then(data => {
                console.log('Réponse suppression:', data);

                if (data.success) {
                    showRatingMessage(articleId, data.message, 'success');

                    // Réinitialiser
                    currentRating = 0;
                    updateStarsDisplay(articleId);

                    // Mettre à jour l'affichage
                    if (data.stats) {
                        updateRatingDisplay(articleId, data.stats, 0);
                    }

                    // Masquer la note de l'utilisateur et le bouton de suppression
                    const userRatingEl = document.getElementById(`current-user-rating-${articleId}`);

                    if (userRatingEl) {
                        userRatingEl.style.display = 'none';
                    }

                    if (removeBtn) {
                        removeBtn.style.display = 'none';
                    }

                    // Réactiver le bouton de soumission
                    const submitBtn = document.getElementById(`submit-rating-btn-${articleId}`);
                    if (submitBtn) {
                        submitBtn.disabled = false;
                    }
                } else {
                    showRatingMessage(articleId, data.message || 'Erreur inconnue', 'error');

                    // Réactiver le bouton
                    if (removeBtn) {
                        removeBtn.disabled = false;
                    }
                }
            })
            .catch(err => {
                console.error('Erreur complète:', err);
                showRatingMessage(articleId, 'Erreur de connexion au serveur. Veuillez réessayer.', 'error');

                // Réactiver le bouton
                if (removeBtn) {
                    removeBtn.disabled = false;
                }
            })
            .finally(() => {
                isSubmittingRating = false;
                if (removeBtn) {
                    removeBtn.innerHTML = originalText;
                    removeBtn.disabled = false;
                }
            });
    }

    function updateRatingDisplay(articleId, stats, userRating) {
        // Mettre à jour la note moyenne
        const avgRating = document.getElementById(`average-rating-${articleId}`);
        if (avgRating && stats) {
            avgRating.textContent = `${stats.average_rating_rounded || '0.0'} ★`;
        }

        // Mettre à jour le nombre de votes
        const totalVotes = document.getElementById(`total-votes-${articleId}`);
        if (totalVotes && stats) {
            totalVotes.textContent = `${stats.total_votes || 0} votes`;
        }
    }

    function showRatingMessage(articleId, message, type) {
        const messageEl = document.getElementById(`rating-message-${articleId}`);
        if (messageEl) {
            messageEl.textContent = message;
            messageEl.className = `rating-message ${type}`;
            messageEl.style.display = 'block';

            // Masquer après 5 secondes
            setTimeout(() => {
                messageEl.style.display = 'none';
            }, 5000);
        }
    }

    // ===== FONCTIONS RÉSUMÉ IA =====
    async function generateAISummary(articleId) {
        console.log('Génération résumé IA pour article:', articleId);

        // Récupérer les éléments
        const btn = document.getElementById(`ai-summary-btn-${articleId}`);
        const resultDiv = document.getElementById(`ai-summary-result-${articleId}`);
        const icon = btn.querySelector('.ai-summary-icon');
        const text = btn.querySelector('.ai-summary-text');

        // Récupérer le texte de l'article
        const articleText = document.querySelector('.article-popup-text').innerText;

        if (!articleText || articleText.length < 50) {
            alert('Texte trop court pour générer un résumé.');
            return;
        }

        // Sauvegarder l'état original
        const originalText = text.textContent;
        const originalIcon = icon.textContent;

        // Mettre en état de chargement
        btn.disabled = true;
        btn.classList.add('ai-summary-btn-loading');
        icon.textContent = '⏳';
        text.textContent = 'Analyse en cours...';

        try {
            // Préparer les données
            const formData = new FormData();
            formData.append('action', 'generate_ai_summary');
            formData.append('article_id', articleId);
            formData.append('article_text', articleText);

            console.log('Envoi requête IA...');

            // Envoyer la requête
            const response = await fetch('blog.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            console.log('Réponse reçue, status:', response.status);

            if (!response.ok) {
                throw new Error(`Erreur HTTP ${response.status}`);
            }

            const data = await response.json();
            console.log('Données reçues:', data);

            if (data.success) {
                // Afficher le résultat
                displayAISummaryResult(articleId, data);

                // Mettre à jour le bouton
                icon.textContent = '🔄';
                text.textContent = 'Regénérer';

            } else {
                // Afficher erreur
                showAIError(articleId, data.message || 'Erreur inconnue');
            }

        } catch (error) {
            console.error('Erreur complète:', error);
            showAIError(articleId, 'Erreur de connexion au serveur');

        } finally {
            // Réactiver le bouton
            btn.disabled = false;
            btn.classList.remove('ai-summary-btn-loading');

            // Si toujours en chargement, réinitialiser
            if (text.textContent === 'Analyse en cours...') {
                icon.textContent = originalIcon;
                text.textContent = originalText;
            }
        }
    }

    function displayAISummaryResult(articleId, data) {
        const resultDiv = document.getElementById(`ai-summary-result-${articleId}`);

        // Déterminer la source
        const sourceText = data.source === 'huggingface' ? 'IA Avancée' : 'Algorithme Local';
        const sourceClass = data.source === 'huggingface' ? 'ai-source-huggingface' : 'ai-source-local';

        resultDiv.innerHTML = `
            <div style="background: rgba(255, 255, 255, 0.95); padding: 25px; border-radius: 12px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12); border: 1px solid rgba(0, 0, 0, 0.08); margin-top: 20px;">
                <!-- En-tête -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                            ✅
                        </div>
                        <div>
                            <h4 style="margin: 0; color: #333; font-size: 18px; font-weight: 700;">Résumé généré</h4>
                            <p style="margin: 3px 0 0 0; color: #666; font-size: 13px;">Par notre intelligence artificielle</p>
                        </div>
                    </div>
                    <span class="ai-source-badge ${sourceClass}" style="margin-top: 5px;">
                        ${sourceText}
                    </span>
                </div>

                <!-- Résumé -->
                <div style="color: #333; font-size: 16px; line-height: 1.7; margin-bottom: 25px; padding: 20px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #28a745; position: relative;">
                    <div style="position: absolute; top: -10px; left: -10px; background: #28a745; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                        ✨
                    </div>
                    ${escapeHtml(data.summary)}
                </div>

                <!-- Infos et actions -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 1px solid #e9ecef;">
                    <!-- Quota -->
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #555;">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <i class="bi bi-lightning-charge" style="color: #ffc107;"></i>
                                <span><strong style="color: #333;">${data.remaining || 4}</strong> résumés restants</span>
                            </div>
                            <span style="color: #ddd;">•</span>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <i class="bi bi-calendar-check" style="color: #17a2b8;"></i>
                                <span>Aujourd'hui</span>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div style="display: flex; gap: 10px;">
                        <button onclick="copyAISummary('${escapeHtml(data.summary)}')"
                                style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.3s;">
                            <i class="bi bi-clipboard"></i>
                            Copier
                        </button>
                        <button onclick="shareAISummary(${articleId}, '${encodeURIComponent(data.summary)}')"
                                style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: all 0.3s;">
                            <i class="bi bi-share"></i>
                            Partager
                        </button>
                    </div>
                </div>

                <!-- Note -->
                <div style="margin-top: 15px; padding: 12px; background: #fff3cd; border-radius: 8px; border: 1px solid #ffeaa7; display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-info-circle" style="color: #856404; font-size: 16px;"></i>
                    <span style="color: #856404; font-size: 13px;">
                        Ce résumé est généré automatiquement. Il peut contenir des imperfections.
                    </span>
                </div>
            </div>
        `;

        resultDiv.style.display = 'block';
    }

    function showAIError(articleId, message) {
        const resultDiv = document.getElementById(`ai-summary-result-${articleId}`);

        resultDiv.innerHTML = `
            <div style="background: #fff5f5; padding: 25px; border-radius: 12px; border: 1px solid #f8d7da; margin-top: 20px;">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <div style="background: #dc3545; color: white; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                        ⚠️
                    </div>
                    <div>
                        <h4 style="margin: 0; color: #721c24; font-size: 18px;">Service temporairement indisponible</h4>
                        <p style="margin: 5px 0 0 0; color: #856404; font-size: 14px;">${message}</p>
                    </div>
                </div>

                <div style="margin-top: 20px; padding: 15px; background: white; border-radius: 8px; border: 1px solid #e9ecef;">
                    <p style="margin: 0 0 10px 0; color: #555; font-size: 14px;">
                        <strong>Solutions :</strong>
                    </p>
                    <ul style="margin: 0; padding-left: 20px; color: #666; font-size: 14px;">
                        <li>Vérifiez votre connexion internet</li>
                        <li>Réessayez dans quelques minutes</li>
                        <li>Utilisez la fonction de résumé manuel ci-dessous</li>
                    </ul>
                </div>

                <!-- Fallback manuel -->
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e9ecef;">
                    <button onclick="showManualSummary(${articleId})"
                            style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 8px; margin: 0 auto;">
                        <i class="bi bi-journal-text"></i>
                        Afficher un résumé manuel
                    </button>
                </div>
            </div>
        `;

        resultDiv.style.display = 'block';
    }

    function copyAISummary(text) {
        navigator.clipboard.writeText(text).then(() => {
            // Afficher notification
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: #28a745;
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                z-index: 10000;
                animation: slideInRight 0.3s ease-out;
            `;
            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Résumé copié dans le presse-papier !</span>
                </div>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }).catch(err => {
            console.error('Erreur copie:', err);
            alert('Impossible de copier le texte');
        });
    }

    function shareAISummary(articleId, summary) {
        const decodedSummary = decodeURIComponent(summary);
        const url = window.location.href.split('?')[0];

        if (navigator.share) {
            navigator.share({
                title: `Résumé de l'article #${articleId}`,
                text: decodedSummary,
                url: url
            });
        } else {
            // Fallback : copier dans le presse-papier
            const text = `Résumé de l'article #${articleId}:\n\n${decodedSummary}\n\n${url}`;
            copyAISummary(text);
        }
    }

    function showManualSummary(articleId) {
        const articleText = document.querySelector('.article-popup-text').innerText;
        const sentences = articleText.split(/[.!?]+/).filter(s => s.trim().length > 20);

        let manualSummary = '';
        if (sentences.length >= 3) {
            manualSummary = sentences.slice(0, 3).join('. ') + '.';
        } else if (sentences.length > 0) {
            manualSummary = sentences[0] + '.';
        } else {
            manualSummary = articleText.substring(0, 200) + '...';
        }

        alert(`Résumé manuel:\n\n${manualSummary}\n\n(Copié dans le presse-papier)`);
        copyAISummary(manualSummary);
    }

    // ===== FONCTIONS EXISTANTES =====
    function renderComment(c, articleId) {
        let repliesHtml = '';
        if (c.replies && c.replies.length > 0) {
            repliesHtml = '<div class="replies-container">';
            c.replies.forEach(reply => {
                repliesHtml += `
                    <div class="reply-comment" data-comment-id="${reply.id_commentaire}">
                        <img src="${escapeHtml(reply.avatar)}" alt="Avatar" class="comment-avatar" style="width:32px; height:32px; border-radius:50%; margin-right:10px;">
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: start; gap: 10px;">
                                <div style="flex: 1;">
                                    <h4 class="comment-author">${escapeHtml(reply.nom_visiteur)}</h4>
                                    <small class="comment-date">${escapeHtml(reply.date_commentaire)}</small>
                                </div>
                                <div class="comment-menu">
                                    <button class="comment-menu-btn" onclick="toggleCommentMenu(event, ${reply.id_commentaire})">⋮</button>
                                    <div class="comment-dropdown" data-menu="${reply.id_commentaire}">
                                        <button class="comment-dropdown-item edit" onclick="editComment(event, ${reply.id_commentaire})">
                                            <i class="bi bi-pencil"></i> Modifier
                                        </button>
                                        <button class="comment-dropdown-item delete" onclick="deleteComment(event, ${reply.id_commentaire}, ${articleId})">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <p class="comment-text" data-original-content="${escapeHtml(reply.contenu)}" style="margin-top:8px;">${escapeHtml(reply.contenu)}</p>

                            <div class="comment-actions">
                                <button class="like-btn" onclick="toggleLike(event, ${reply.id_commentaire})" data-liked="false">
                                    <i class="bi bi-heart"></i>
                                    <span class="like-count">${reply.likes || 0}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            repliesHtml += '</div>';
        }

        return `
            <div class="comment" data-comment-id="${c.id_commentaire}">
                <img src="${escapeHtml(c.avatar)}" alt="Avatar" class="comment-avatar">
                <div class="comment-content" style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 10px;">
                        <div style="flex: 1;">
                            <h4 class="comment-author">${escapeHtml(c.nom_visiteur)}</h4>
                            <small class="comment-date">${escapeHtml(c.date_commentaire)}</small>
                        </div>
                        <div class="comment-menu">
                            <button class="comment-menu-btn" onclick="toggleCommentMenu(event, ${c.id_commentaire})">⋮</button>
                            <div class="comment-dropdown" data-menu="${c.id_commentaire}">
                                <button class="comment-dropdown-item edit" onclick="editComment(event, ${c.id_commentaire})">
                                    <i class="bi bi-pencil"></i> Modifier
                                </button>
                                <button class="comment-dropdown-item delete" onclick="deleteComment(event, ${c.id_commentaire}, ${articleId})">
                                    <i class="bi bi-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="comment-text" data-original-content="${escapeHtml(c.contenu)}">${escapeHtml(c.contenu)}</p>

                    <div class="comment-actions">
                        <button class="like-btn" onclick="toggleLike(event, ${c.id_commentaire})" data-liked="false">
                            <i class="bi bi-heart"></i>
                            <span class="like-count">${c.likes || 0}</span>
                        </button>
                        <button class="reply-btn" onclick="toggleReplyForm(event, ${c.id_commentaire}, '${escapeHtml(c.nom_visiteur)}')">
                            <i class="bi bi-chat-left"></i> Répondre
                        </button>
                    </div>

                    <div class="reply-form" id="reply-form-${c.id_commentaire}">
                        <div class="form-group">
                            <input type="text" class="comment-input" placeholder="Votre nom..." style="background: #ffffff; color: #333; padding: 10px 14px;" data-reply-name="${c.id_commentaire}">
                        </div>
                        <div class="form-group">
                            <textarea class="comment-input" rows="3" placeholder="Écrivez votre réponse..." style="background: #ffffff; color: #333; padding: 10px 14px;" data-reply-content="${c.id_commentaire}"></textarea>
                        </div>
                        <button class="submit-reply-btn" onclick="submitReply(event, ${c.id_commentaire}, ${articleId})">Envoyer</button>
                        <button class="cancel-reply-btn" onclick="cancelReply(${c.id_commentaire})">Annuler</button>
                    </div>
                </div>
            </div>
            ${repliesHtml}
        `;
    }

    function toggleReplyForm(e, commentId, authorName) {
        e.stopPropagation();
        const form = document.getElementById(`reply-form-${commentId}`);
        form.classList.toggle('show');
        if (form.classList.contains('show')) {
            form.querySelector(`[data-reply-name="${commentId}"]`).focus();
        }
    }

    function cancelReply(commentId) {
        const form = document.getElementById(`reply-form-${commentId}`);
        form.classList.remove('show');
    }

    function submitReply(e, parentId, articleId) {
        e.preventDefault();
        const form = e.target.closest('.reply-form');
        const nomInput = form.querySelector(`[data-reply-name="${parentId}"]`);
        const contenuInput = form.querySelector(`[data-reply-content="${parentId}"]`);

        const nom = nomInput.value.trim();
        const contenu = contenuInput.value.trim();

        if (!nom || nom.length < 2 || nom.length > 100) {
            alert('Veuillez entrer un nom valide (2-100 caractères)');
            return;
        }

        if (!contenu || contenu.length < 3 || contenu.length > 1000) {
            alert('La réponse doit contenir entre 3 et 1000 caractères');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'add_comment');
        formData.append('id_article', articleId);
        formData.append('nom_visiteur', nom);
        formData.append('contenu', contenu);
        formData.append('id_parent', parentId);

        fetch('blog.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeArticlePopup();
                    openArticlePopup(articleId);
                } else {
                    alert(data.message || 'Erreur lors de l\'envoi de la réponse');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Erreur de connexion');
            });
    }

    function toggleLike(e, commentId) {
        e.stopPropagation();
        const btn = e.currentTarget;
        const isLiked = btn.getAttribute('data-liked') === 'true';
        const action = isLiked ? 'unlike_comment' : 'like_comment';

        const formData = new FormData();
        formData.append('action', action);
        formData.append('id_commentaire', commentId);

        fetch('blog.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const likeCount = btn.querySelector('.like-count');
                    likeCount.textContent = data.likes;
                    btn.setAttribute('data-liked', !isLiked);
                    btn.classList.toggle('liked');
                }
            })
            .catch(err => console.error('Erreur like:', err));
    }

    function closeArticlePopup(e) {
        if (e) e.stopPropagation();
        document.getElementById('article-popup').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function toggleCommentMenu(e, commentId) {
        e.stopPropagation();
        const dropdown = document.querySelector(`[data-menu="${commentId}"]`);
        document.querySelectorAll('.comment-dropdown.active').forEach(menu => {
            if (menu !== dropdown) menu.classList.remove('active');
        });
        dropdown.classList.toggle('active');
    }

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.comment-menu')) {
            document.querySelectorAll('.comment-dropdown.active').forEach(menu => {
                menu.classList.remove('active');
            });
        }
    });

    function editComment(e, commentId) {
        e.stopPropagation();
        e.preventDefault();

        const commentDiv = document.querySelector(`[data-comment-id="${commentId}"]`);
        const commentText = commentDiv.querySelector('.comment-text');
        const originalContent = commentText.getAttribute('data-original-content');

        const editForm = document.createElement('div');
        editForm.className = 'edit-comment-form';
        editForm.innerHTML = `
            <textarea class="edit-comment-textarea" style="width: 100%; padding: 12px; border: 2px solid #00d4ff; border-radius: 8px; background: rgba(0, 212, 255, 0.08); color: #ffffff; font-family: 'Roboto', sans-serif; font-size: 0.95rem; resize: vertical; min-height: 80px; margin-bottom: 12px;">${originalContent}</textarea>
            <div style="display: flex; gap: 10px;">
                <button class="edit-save-btn" style="flex: 1; padding: 10px 16px; background: linear-gradient(135deg, #00d4ff 0%, #00a0cc 100%); color: #1a1a3e; border: none; border-radius: 6px; cursor: pointer; font-weight: 700; font-family: 'Montserrat', sans-serif; transition: all 0.3s ease;" data-comment-id="${commentId}">Enregistrer</button>
                <button class="edit-cancel-btn" style="flex: 1; padding: 10px 16px; background: rgba(255, 107, 157, 0.2); color: #ff6b9d; border: 2px solid #ff6b9d; border-radius: 6px; cursor: pointer; font-weight: 700; font-family: 'Montserrat', sans-serif; transition: all 0.3s ease;">Annuler</button>
            </div>
        `;

        const parentDiv = commentText.parentElement;
        commentText.style.display = 'none';
        parentDiv.insertBefore(editForm, commentText.nextSibling);

        document.querySelector(`[data-menu="${commentId}"]`).classList.remove('active');

        editForm.querySelector('.edit-save-btn').addEventListener('click', (event) => {
            const textarea = editForm.querySelector('.edit-comment-textarea');
            const newContent = textarea.value.trim();

            if (!newContent || newContent.length < 3 || newContent.length > 1000) {
                alert('Validation échouée');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'update_comment');
            formData.append('id_commentaire', commentId);
            formData.append('contenu', newContent);

            fetch('blog.php', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        commentText.textContent = newContent;
                        commentText.setAttribute('data-original-content', newContent);
                        commentText.style.display = 'block';
                        editForm.remove();
                    }
                });
        });

        editForm.querySelector('.edit-cancel-btn').addEventListener('click', () => {
            commentText.style.display = 'block';
            editForm.remove();
        });
    }

    function deleteComment(e, commentId, articleId) {
        e.stopPropagation();
        if (!confirm('Êtes-vous sûr?')) return;

        const formData = new FormData();
        formData.append('action', 'delete_comment');
        formData.append('id_commentaire', commentId);

        fetch('blog.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    closeArticlePopup();
                    openArticlePopup(articleId);
                }
            });
    }

    function showFieldErrors(form, errors) {
        form.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        form.querySelectorAll('.comment-input').forEach(input => {
            input.classList.remove('error');
        });

        errors.forEach(msg => {
            if (msg.includes('nom')) {
                const errorElement = form.querySelector('#error-nom');
                const inputElement = form.querySelector('input[name="nom_visiteur"]');
                if (errorElement && inputElement) {
                    errorElement.textContent = msg;
                    errorElement.style.display = 'block';
                    inputElement.classList.add('error');
                }
            }
            if (msg.includes('commentaire') || msg.includes('contenu')) {
                const errorElement = form.querySelector('#error-contenu');
                const inputElement = form.querySelector('textarea[name="contenu"]');
                if (errorElement && inputElement) {
                    errorElement.textContent = msg;
                    errorElement.style.display = 'block';
                    inputElement.classList.add('error');
                }
            }
        });
    }

    function addComment(e, articleId) {
        e.preventDefault();
        const form = e.target;

        form.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.style.display = 'none';
        });

        form.querySelectorAll('.comment-input').forEach(input => input.classList.remove('error'));

        const formData = new FormData(form);
        formData.append('action', 'add_comment');
        formData.append('id_article', articleId);

        const nom_visiteur = formData.get('nom_visiteur').trim();
        const contenu = formData.get('contenu').trim();

        let clientErrors = [];

        if (!nom_visiteur || nom_visiteur.length < 2 || nom_visiteur.length > 100) {
            clientErrors.push('Nom invalide');
        }

        if (!contenu || contenu.length < 3 || contenu.length > 1000) {
            clientErrors.push('Commentaire invalide');
        }

        if (clientErrors.length) {
            showFieldErrors(form, clientErrors);
            return;
        }

        fetch('blog.php', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    form.reset();
                    closeArticlePopup();
                    openArticlePopup(articleId);
                } else {
                    if (Array.isArray(data.errors) && data.errors.length) {
                        showFieldErrors(form, data.errors);
                    } else if (data.message) {
                        showFieldErrors(form, [String(data.message)]);
                    }
                }
            })
            .catch(() => {
                showFieldErrors(form, ['Erreur de connexion au serveur']);
            });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    document.getElementById('article-popup').addEventListener('click', e => {
        if (e.target === e.currentTarget) closeArticlePopup();
    });

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeArticlePopup();
    });

    // Ajouter les animations CSS pour l'IA
    const aiStyles = document.createElement('style');
    aiStyles.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(aiStyles);
</script>
</body>
</html>