<?php
require_once __DIR__ . '/../Models/BlogModel.php';

class BlogController {
    private $blogModel;
    private $uploadDir = __DIR__ . '/../../public/uploads/articles/';
    private $uploadUrl = '/PROJET_WEB_NEXTGEN/public/uploads/articles/';

    public function __construct() {
        $this->blogModel = new BlogModel();

        // Créer le dossier uploads s'il n'existe pas
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

            // Transformer les données pour le frontend
            $formattedArticles = [];
            foreach ($articles as $article) {
                $formattedArticles[] = [
                    'id_article' => $article['id_article'],
                    'titre' => htmlspecialchars($article['titre']),
                    'content' => $this->truncateContent($article['content'], 150),
                    'full_content' => $article['content'],
                    'date_publication' => $this->formatDate($article['date_publication']),
                    'categorie' => htmlspecialchars($article['categorie']),
                    'image' => $article['image'] ?: $this->getDefaultImage($article['categorie']),
                    'id_auteur' => $article['id_auteur']
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

            // Formater les données
            $formattedArticle = [
                'id_article' => $article['id_article'],
                'titre' => htmlspecialchars($article['titre']),
                'content' => $article['content'],
                'date_publication' => $this->formatDate($article['date_publication']),
                'categorie' => htmlspecialchars($article['categorie']),
                'image' => $article['image'] ?: $this->getDefaultImage($article['categorie']),
                'id_auteur' => $article['id_auteur']
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
            // Validation des données
            $errors = $this->validateArticleData($data);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Gérer l'upload de l'image
            $imagePath = '';
            if ($files && isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadImage($files['image']);
                if ($uploadResult['success']) {
                    $imagePath = $uploadResult['path'];
                } else {
                    return ['success' => false, 'message' => $uploadResult['message']];
                }
            }

            // Préparer les données
            $articleData = [
                'titre' => trim($data['titre']),
                'content' => trim($data['content']),
                'date_publication' => date('Y-m-d H:i:s'),
                'categorie' => trim($data['categorie']),
                'image' => $imagePath,
                'id_auteur' => $data['id_auteur'] ?? 1
            ];

            $result = $this->createArticleDB($articleData);

            if ($result) {
                return ['success' => true, 'message' => 'Article créé avec succès'];
            } else {
                // Supprimer l'image si l'insertion échoue
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
            // Vérifier si l'article existe
            $existingArticle = $this->getArticleById($id_article);
            if (!$existingArticle) {
                return ['success' => false, 'message' => 'Article non trouvé'];
            }

            // Validation des données
            $errors = $this->validateArticleData($data);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Gérer l'upload de la nouvelle image
            $imagePath = $existingArticle['image'];
            if ($files && isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
                $uploadResult = $this->uploadImage($files['image']);
                if ($uploadResult['success']) {
                    // Supprimer l'ancienne image si elle existe
                    if ($existingArticle['image'] && file_exists($this->uploadDir . basename($existingArticle['image']))) {
                        unlink($this->uploadDir . basename($existingArticle['image']));
                    }
                    $imagePath = $uploadResult['path'];
                } else {
                    return ['success' => false, 'message' => $uploadResult['message']];
                }
            }

            // Préparer les données
            $articleData = [
                'titre' => trim($data['titre']),
                'content' => trim($data['content']),
                'date_publication' => $existingArticle['date_publication'],
                'categorie' => trim($data['categorie']),
                'image' => $imagePath,
                'id_auteur' => $data['id_auteur'] ?? $existingArticle['id_auteur']
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
     * Supprimer un article, ses commentaires et son image
     */
    public function delete($id_article) {
        try {
            // Vérifier si l'article existe
            $existingArticle = $this->getArticleById($id_article);
            if (!$existingArticle) {
                return ['success' => false, 'message' => 'Article non trouvé'];
            }

            // Supprimer les commentaires associés d'abord
            $commentsDeleted = $this->deleteCommentsByArticle($id_article);

            if (!$commentsDeleted) {
                error_log("Avertissement: Impossible de supprimer les commentaires de l'article ID: " . $id_article);
            }

            // Supprimer l'image associée
            if ($existingArticle['image'] && file_exists($this->uploadDir . basename($existingArticle['image']))) {
                unlink($this->uploadDir . basename($existingArticle['image']));
            }

            $result = $this->deleteArticleDB($id_article);

            if ($result) {
                return ['success' => true, 'message' => 'Article et ses commentaires supprimés avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la suppression de l\'article'];
            }
        } catch (Exception $e) {
            error_log("Erreur dans BlogController::delete: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
        }
    }

    /**
     * Récupérer les articles par catégorie
     */
    public function getByCategory($categorie) {
        try {
            $articles = $this->getArticlesByCategory($categorie);

            $formattedArticles = [];
            foreach ($articles as $article) {
                $formattedArticles[] = [
                    'id_article' => $article['id_article'],
                    'titre' => htmlspecialchars($article['titre']),
                    'content' => $this->truncateContent($article['content'], 150),
                    'full_content' => $article['content'],
                    'date_publication' => $this->formatDate($article['date_publication']),
                    'categorie' => htmlspecialchars($article['categorie']),
                    'image' => $article['image'] ?: $this->getDefaultImage($article['categorie']),
                    'id_auteur' => $article['id_auteur']
                ];
            }

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
                    'categorie' => htmlspecialchars($article['categorie']),
                    'image' => $article['image'] ?: $this->getDefaultImage($article['categorie']),
                    'id_auteur' => $article['id_auteur']
                ];
            }

            return $formattedArticles;
        } catch (Exception $e) {
            error_log("Erreur dans BlogController::getRecent: " . $e->getMessage());
            return [];
        }
    }

    // ===== REQUÊTES SQL (DÉPLACÉES DU MODEL) =====

    /**
     * Récupérer tous les articles
     */
    private function getAllArticles() {
        try {
            $pdo = $this->blogModel->getPDO();
            $table = $this->blogModel->getTableName();
            $query = "SELECT * FROM {$table} ORDER BY date_publication DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des articles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer un article par son ID
     */
    private function getArticleById($id_article) {
        try {
            $pdo = $this->blogModel->getPDO();
            $table = $this->blogModel->getTableName();
            $query = "SELECT * FROM {$table} WHERE id_article = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_article, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
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
            $stmt->bindParam(':categorie', $data['categorie']);
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
            $stmt->bindParam(':categorie', $data['categorie']);
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
     * Supprimer tous les commentaires d'un article
     */
    private function deleteCommentsByArticle($id_article) {
        try {
            $pdo = $this->blogModel->getPDO();
            $query = "DELETE FROM commentaire WHERE id_article = :id_article";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression des commentaires: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupérer les articles par catégorie
     */
    private function getArticlesByCategory($categorie) {
        try {
            $pdo = $this->blogModel->getPDO();
            $table = $this->blogModel->getTableName();

            $query = "SELECT * FROM {$table} WHERE categorie = :categorie ORDER BY date_publication DESC";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':categorie', $categorie);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des articles par catégorie: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les derniers articles (pour la page d'accueil)
     */
    private function getRecentArticles($limit = 3) {
        try {
            $pdo = $this->blogModel->getPDO();
            $table = $this->blogModel->getTableName();

            $query = "SELECT * FROM {$table} ORDER BY date_publication DESC LIMIT :limit";
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
     * Gérer l'upload d'image
     */
    private function uploadImage($file) {
        // Vérifier les erreurs d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Erreur lors de l\'upload du fichier'];
        }

        // Vérifier la taille (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Le fichier est trop volumineux (max 5MB)'];
        }

        // Vérifier le type MIME
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Type de fichier non autorisé. Utilisez JPG, PNG, GIF ou WebP'];
        }

        // Générer un nom de fichier unique
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('article_', true) . '.' . $extension;
        $filepath = $this->uploadDir . $filename;

        // Déplacer le fichier uploadé
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

        if (empty(trim($data['titre']))) {
            $errors['titre'] = 'Le titre est obligatoire';
        }

        if (empty(trim($data['content']))) {
            $errors['content'] = 'Le contenu est obligatoire';
        }

        if (empty(trim($data['categorie']))) {
            $errors['categorie'] = 'La catégorie est obligatoire';
        }

        return $errors;
    }

    /**
     * Tronquer le contenu (pour l'aperçu)
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