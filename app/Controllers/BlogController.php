<?php
require_once __DIR__ . '/../Models/BlogModel.php';
require_once __DIR__ . '/../Models/CategoryModel.php';
require_once __DIR__ . '/CategoryController.php';

class BlogController {
    private $blogModel;
    private $categoryModel;
    private $categoryController;
    private $uploadDir = __DIR__ . '/../../public/uploads/articles/';
    private $uploadUrl = '/PROJET_WEB_NEXTGEN/public/uploads/articles/';

    public function __construct() {
        $this->blogModel = new BlogModel();
        $this->categoryModel = new CategoryModel();
        $this->categoryController = new CategoryController();

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Afficher tous les articles (pour la page blog)
     */
    public function index() {
        try {
            $articles = $this->getAllArticles();
            error_log("BlogController::index - Retrieved " . count($articles) . " articles");

            $formattedArticles = [];
            foreach ($articles as $article) {
                $formattedArticles[] = [
                    'id_article' => $article['id_article'],
                    'titre' => htmlspecialchars($article['titre']),
                    'content' => $this->truncateContent($article['content'], 150),
                    'full_content' => $article['content'],
                    'date_publication' => $this->formatDate($article['date_publication']),
                    'id_categorie' => $article['id_categorie'] ?? '',
                    'categorie' => htmlspecialchars($article['categorie_nom']),
                    'categorie_nom' => htmlspecialchars($article['categorie_nom']),
                    'categorie_slug' => $article['categorie_slug'] ?? '',
                    'image' => $article['image'] ?: $this->getDefaultImage($article['categorie_nom']),
                    'id_auteur' => $article['id_auteur'] ?? 1
                ];
            }

            return $formattedArticles;
        } catch (Exception $e) {
            error_log("Erreur dans BlogController::index: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Afficher un article spécifique
     */
    public function show($id_article) {
        try {
            $article = $this->getArticleById($id_article);

            if (!$article) {
                return ['error' => 'Article non trouvé'];
            }

            $formattedArticle = [
                'id_article' => $article['id_article'],
                'titre' => htmlspecialchars($article['titre']),
                'content' => $article['content'],
                'date_publication' => $this->formatDate($article['date_publication']),
                'id_categorie' => $article['id_categorie'] ?? '',
                'categorie' => htmlspecialchars($article['categorie_nom']),
                'categorie_nom' => htmlspecialchars($article['categorie_nom']),
                'categorie_slug' => $article['categorie_slug'] ?? '',
                'image' => $article['image'] ?: $this->getDefaultImage($article['categorie_nom']),
                'id_auteur' => $article['id_auteur'] ?? 1
            ];

            return $formattedArticle;
        } catch (Exception $e) {
            error_log("Erreur dans BlogController::show: " . $e->getMessage());
            return ['error' => 'Erreur lors de la récupération de l\'article'];
        }
    }

    /**
     * Créer un nouvel article avec upload d'image
     */
    public function create($data, $files = null) {
        try {
            $errors = $this->validateArticleData($data);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            if (!$this->categoryExists($data['id_categorie'])) {
                return ['success' => false, 'message' => 'Catégorie invalide'];
            }

            $imagePath = '';
            if ($files && isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadImage($files['image']);
                if ($uploadResult['success']) {
                    $imagePath = $uploadResult['path'];
                } else {
                    return ['success' => false, 'message' => $uploadResult['message']];
                }
            }

            $articleData = [
                'titre' => trim($data['titre']),
                'content' => trim($data['content']),
                'date_publication' => date('Y-m-d H:i:s'),
                'id_categorie' => (int)$data['id_categorie'],
                'image' => $imagePath,
                'id_auteur' => $data['id_auteur'] ?? 1
            ];

            $result = $this->createArticleDB($articleData);

            if ($result) {
                return ['success' => true, 'message' => 'Article créé avec succès'];
            } else {
                if ($imagePath && file_exists($this->uploadDir . basename($imagePath))) {
                    unlink($this->uploadDir . basename($imagePath));
                }
                return ['success' => false, 'message' => 'Erreur lors de la création de l\'article'];
            }
        } catch (Exception $e) {
            error_log("Erreur dans BlogController::create: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
        }
    }

    /**
     * Mettre à jour un article avec upload d'image
     */
    public function update($id_article, $data, $files = null) {
        try {
            $existingArticle = $this->getArticleById($id_article);
            if (!$existingArticle) {
                return ['success' => false, 'message' => 'Article non trouvé'];
            }

            $errors = $this->validateArticleData($data);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            if (!$this->categoryExists($data['id_categorie'])) {
                return ['success' => false, 'message' => 'Catégorie invalide'];
            }

            $imagePath = $existingArticle['image'] ?? '';
            if ($files && isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadImage($files['image']);
                if ($uploadResult['success']) {
                    if ($existingArticle['image'] && file_exists($this->uploadDir . basename($existingArticle['image']))) {
                        unlink($this->uploadDir . basename($existingArticle['image']));
                    }
                    $imagePath = $uploadResult['path'];
                } else {
                    return ['success' => false, 'message' => $uploadResult['message']];
                }
            }

            $articleData = [
                'titre' => trim($data['titre']),
                'content' => trim($data['content']),
                'date_publication' => $existingArticle['date_publication'],
                'id_categorie' => (int)$data['id_categorie'],
                'image' => $imagePath,
                'id_auteur' => $data['id_auteur'] ?? ($existingArticle['id_auteur'] ?? 1)
            ];

            $result = $this->updateArticleDB($id_article, $articleData);

            if ($result) {
                return ['success' => true, 'message' => 'Article mis à jour avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour de l\'article'];
            }
        } catch (Exception $e) {
            error_log("Erreur dans BlogController::update: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
        }
    }

    /**
     * Delete article with CASCADE deletion of comments
     * When an article is deleted:
     * 1. Delete all comments associated with this article
     * 2. Delete the article's image file
     * 3. Delete the article itself
     */
    public function delete($id_article) {
        try {
            // Check if article exists
            $existingArticle = $this->getArticleById($id_article);
            if (!$existingArticle) {
                return ['success' => false, 'message' => 'Article non trouvé'];
            }

            $pdo = $this->blogModel->getPDO();

            // Start transaction for atomic operation
            $pdo->beginTransaction();

            try {
                // Step 1: Delete all comments associated with this article (CASCADE)
                $deletedCommentsCount = $this->deleteCommentsByArticle($id_article);

                if ($deletedCommentsCount === false) {
                    $pdo->rollBack();
                    return ['success' => false, 'message' => 'Erreur lors de la suppression des commentaires'];
                }

                // Step 2: Delete the article from database
                $articleDeleted = $this->deleteArticleDB($id_article);

                if (!$articleDeleted) {
                    $pdo->rollBack();
                    return ['success' => false, 'message' => 'Erreur lors de la suppression de l\'article'];
                }

                // Commit transaction
                $pdo->commit();

                // Step 3: Delete image file (outside transaction - best effort)
                if (!empty($existingArticle['image'])) {
                    $imagePath = $this->uploadDir . basename($existingArticle['image']);
                    if (file_exists($imagePath)) {
                        @unlink($imagePath);
                    }
                }

                $message = sprintf(
                    'Article supprimé avec succès. %d commentaire(s) supprimé(s).',
                    $deletedCommentsCount
                );

                return [
                    'success' => true,
                    'message' => $message,
                    'deleted_comments' => $deletedCommentsCount
                ];

            } catch (Exception $e) {
                // Rollback on any error
                $pdo->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Erreur dans BlogController::delete: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur lors de la suppression'];
        }
    }

    /**
     * Récupérer les articles par catégorie
     */
    public function getByCategory($id_categorie) {
        try {
            $articles = $this->getArticlesByCategoryId($id_categorie);

            $formattedArticles = [];
            foreach ($articles as $article) {
                $formattedArticles[] = [
                    'id_article' => $article['id_article'],
                    'titre' => htmlspecialchars($article['titre']),
                    'content' => $this->truncateContent($article['content'], 150),
                    'full_content' => $article['content'],
                    'date_publication' => $this->formatDate($article['date_publication']),
                    'id_categorie' => $article['categorie'] ?? '',
                    'categorie' => htmlspecialchars($article['categorie_nom']),
                    'categorie_nom' => htmlspecialchars($article['categorie_nom']),
                    'categorie_slug' => '',
                    'image' => $article['image'] ?: $this->getDefaultImage($article['categorie_nom']),
                    'id_auteur' => $article['id_auteur'] ?? 1
                ];
            }

            error_log("getByCategory returned " . count($formattedArticles) . " articles for category ID: " . $id_categorie);
            return $formattedArticles;
        } catch (Exception $e) {
            error_log("Erreur dans BlogController::getByCategory: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les articles récents
     */
    public function getRecent($limit = 3) {
        try {
            $articles = $this->getRecentArticles($limit);

            $formattedArticles = [];
            foreach ($articles as $article) {
                $formattedArticles[] = [
                    'id_article' => $article['id_article'],
                    'titre' => htmlspecialchars($article['titre']),
                    'content' => $this->truncateContent($article['content'], 150),
                    'full_content' => $article['content'],
                    'date_publication' => $this->formatDate($article['date_publication']),
                    'id_categorie' => $article['id_categorie'] ?? '',
                    'categorie' => htmlspecialchars($article['categorie_nom']),
                    'categorie_nom' => htmlspecialchars($article['categorie_nom']),
                    'categorie_slug' => $article['categorie_slug'] ?? '',
                    'image' => $article['image'] ?: $this->getDefaultImage($article['categorie_nom']),
                    'id_auteur' => $article['id_auteur'] ?? 1
                ];
            }

            return $formattedArticles;
        } catch (Exception $e) {
            error_log("Erreur dans BlogController::getRecent: " . $e->getMessage());
            return [];
        }
    }

    // ===== REQUÊTES SQL =====

    /**
     * Récupérer tous les articles
     */
    private function getAllArticles() {
        try {
            $pdo = $this->blogModel->getPDO();

            if (!$pdo) {
                error_log("PDO connection is null in getAllArticles");
                return [];
            }

            $query = "SELECT a.*, COALESCE(c.nom, 'Non catégorisé') AS categorie_nom
                      FROM article a
                      LEFT JOIN categorie_article c ON a.categorie = c.id_categorie
                      ORDER BY a.date_publication DESC";

            $stmt = $pdo->prepare($query);

            if (!$stmt) {
                error_log("Failed to prepare statement: " . implode(", ", $pdo->errorInfo()));
                return [];
            }

            $executed = $stmt->execute();

            if (!$executed) {
                error_log("Failed to execute query: " . implode(", ", $stmt->errorInfo()));
                return [];
            }

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            error_log("getAllArticles returned " . count($results) . " articles");

            return $results;

        } catch (PDOException $e) {
            error_log("PDOException in getAllArticles: " . $e->getMessage());
            return [];
        } catch (Exception $e) {
            error_log("Exception in getAllArticles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer un article par son ID
     */
    private function getArticleById($id_article) {
        try {
            $pdo = $this->blogModel->getPDO();
            $query = "SELECT a.*, COALESCE(c.nom, 'Non catégorisé') AS categorie_nom
                      FROM article a
                      LEFT JOIN categorie_article c ON a.categorie = c.id_categorie
                      WHERE a.id_article = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_article, PDO::PARAM_INT);
            $stmt->execute();

            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            return $article;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'article: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer un nouvel article
     */
    private function createArticleDB($data) {
        try {
            $pdo = $this->blogModel->getPDO();
            $table = $this->blogModel->getTableName();

            $query = "INSERT INTO {$table} (titre, content, date_publication, categorie, image, id_auteur) 
                     VALUES (:titre, :content, :date_publication, :categorie, :image, :id_auteur)";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':titre', $data['titre']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':date_publication', $data['date_publication']);
            $stmt->bindParam(':categorie', $data['id_categorie']);
            $stmt->bindParam(':image', $data['image']);
            $stmt->bindParam(':id_auteur', $data['id_auteur'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'article: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mettre à jour un article
     */
    private function updateArticleDB($id_article, $data) {
        try {
            $pdo = $this->blogModel->getPDO();
            $table = $this->blogModel->getTableName();

            $query = "UPDATE {$table} SET 
                     titre = :titre, 
                     content = :content, 
                     date_publication = :date_publication, 
                     categorie = :categorie, 
                     image = :image, 
                     id_auteur = :id_auteur 
                     WHERE id_article = :id";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_article, PDO::PARAM_INT);
            $stmt->bindParam(':titre', $data['titre']);
            $stmt->bindParam(':content', $data['content']);
            $stmt->bindParam(':date_publication', $data['date_publication']);
            $stmt->bindParam(':categorie', $data['id_categorie']);
            $stmt->bindParam(':image', $data['image']);
            $stmt->bindParam(':id_auteur', $data['id_auteur'], PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'article: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un article
     */
    private function deleteArticleDB($id_article) {
        try {
            $pdo = $this->blogModel->getPDO();
            $table = $this->blogModel->getTableName();

            $query = "DELETE FROM {$table} WHERE id_article = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_article, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de l'article: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete all comments associated with an article (CASCADE)
     * Returns the number of deleted comments or false on error
     */
    private function deleteCommentsByArticle($id_article) {
        try {
            $pdo = $this->blogModel->getPDO();
            $query = "DELETE FROM commentaire WHERE id_article = :id_article";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->rowCount(); // Return number of deleted comments
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression des commentaires: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les articles par ID de catégorie
     */
    private function getArticlesByCategoryId($id_categorie) {
        try {
            $pdo = $this->blogModel->getPDO();
            $query = "SELECT a.*, COALESCE(c.nom, 'Non catégorisé') AS categorie_nom
                      FROM article a
                      LEFT JOIN categorie_article c ON a.categorie = c.id_categorie
                      WHERE a.categorie = :id
                      ORDER BY a.date_publication DESC";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_categorie, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans getArticlesByCategoryId: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les derniers articles
     */
    private function getRecentArticles($limit = 3) {
        try {
            $pdo = $this->blogModel->getPDO();
            $query = "SELECT a.*, COALESCE(c.nom, 'Non catégorisé') AS categorie_nom
                      FROM article a
                      LEFT JOIN categorie_article c ON a.categorie = c.id_categorie
                      ORDER BY a.date_publication DESC LIMIT :limit";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des articles récents: " . $e->getMessage());
            return [];
        }
    }

    // ===== MÉTHODES PRIVÉES =====

    /**
     * Vérifier si une catégorie existe
     */
    private function categoryExists($id_categorie) {
        try {
            $category = $this->categoryController->getCategoryById($id_categorie);
            return $category !== false;
        } catch (Exception $e) {
            error_log("Erreur lors de la vérification de la catégorie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gérer l'upload d'image
     */
    private function uploadImage($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Erreur lors de l\'upload du fichier'];
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Le fichier est trop volumineux (max 5MB)'];
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WebP'];
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('article_', true) . '.' . $extension;
        $filepath = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return [
                'success' => true,
                'path' => $this->uploadUrl . $filename,
                'filename' => $filename
            ];
        } else {
            return ['success' => false, 'message' => 'Erreur lors de l\'enregistrement du fichier'];
        }
    }

    /**
     * Valider les données d'un article
     */
    private function validateArticleData($data) {
        $errors = [];

        if (empty(trim($data['titre'] ?? ''))) {
            $errors['titre'] = 'Le titre est obligatoire';
        } elseif (strlen(trim($data['titre'])) < 3) {
            $errors['titre'] = 'Le titre doit contenir au moins 3 caractères';
        }

        if (empty(trim($data['content'] ?? ''))) {
            $errors['content'] = 'Le contenu est obligatoire';
        } elseif (strlen(trim($data['content'])) < 10) {
            $errors['content'] = 'Le contenu doit contenir au moins 10 caractères';
        }

        if (empty($data['id_categorie'] ?? '')) {
            $errors['id_categorie'] = 'La catégorie est obligatoire';
        }

        return $errors;
    }

    /**
     * Tronquer le contenu
     */
    private function truncateContent($content, $length) {
        if (strlen($content) <= $length) {
            return $content;
        }

        $truncated = substr($content, 0, $length);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }

    /**
     * Formater la date
     */
    private function formatDate($date) {
        $timestamp = strtotime($date);
        return date('d/m/Y à H:i', $timestamp);
    }

    /**
     * Obtenir l'image par défaut selon la catégorie
     */
    private function getDefaultImage($categorie) {
        $images = [
            'Gaming' => 'https://via.placeholder.com/600x350/6B5BFF/ffffff?text=Gaming',
            'VR' => 'https://via.placeholder.com/600x350/00B8A9/ffffff?text=VR+Gaming',
            'Esport' => 'https://via.placeholder.com/600x350/FF7A5A/ffffff?text=Pro+Gamer',
            'Communauté' => 'https://via.placeholder.com/600x350/3A86FF/ffffff?text=NextGen+Community',
            'default' => 'https://via.placeholder.com/600x350/6c757d/ffffff?text=NextGen+Blog'
        ];

        return $images[$categorie] ?? $images['default'];
    }
}
?>