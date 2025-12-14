<?php
// ===== CategoryController.php =====

require_once __DIR__ . '/../models/CategoryModel.php';

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        try {
            $categories = $this->getCategories();
            return $categories;
        } catch (Exception $e) {
            error_log("Erreur dans CategoryController::getAllCategories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get category by ID
     */
    public function getCategoryById($id_categorie) {
        try {
            $pdo = $this->categoryModel->getPDO();
            $table = $this->categoryModel->getTableName();
            $query = "SELECT * FROM {$table} WHERE id_categorie = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_categorie, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la catégorie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get category by slug
     */
    public function getCategoryBySlug($slug) {
        try {
            $pdo = $this->categoryModel->getPDO();
            $table = $this->categoryModel->getTableName();
            $query = "SELECT * FROM {$table} WHERE slug = :slug";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':slug', $slug);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la catégorie par slug: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get category by name
     */
    public function getCategoryByName($nom) {
        try {
            $pdo = $this->categoryModel->getPDO();
            $table = $this->categoryModel->getTableName();
            $query = "SELECT * FROM {$table} WHERE nom = :nom";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':nom', $nom);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la catégorie par nom: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new category
     */
    public function create($data) {
        try {
            // Validation
            $errors = $this->validateCategoryData($data);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Check if category already exists
            if ($this->categoryExists(trim($data['nom']))) {
                return ['success' => false, 'message' => 'Cette catégorie existe déjà'];
            }

            $slug = $this->generateSlug($data['nom']);

            $result = $this->createCategoryDB([
                'nom' => trim($data['nom']),
                'description' => trim($data['description'] ?? ''),
                'slug' => $slug
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Catégorie créée avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la création de la catégorie'];
            }
        } catch (Exception $e) {
            error_log("Erreur dans CategoryController::create: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
        }
    }

    /**
     * Update category
     */
    public function update($id_categorie, $data) {
        try {
            // Check if category exists
            $existingCategory = $this->getCategoryById($id_categorie);
            if (!$existingCategory) {
                return ['success' => false, 'message' => 'Catégorie non trouvée'];
            }

            // Validation
            $errors = $this->validateCategoryData($data);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Check if new name already exists (excluding current category)
            if ($data['nom'] !== $existingCategory['nom'] && $this->categoryExists(trim($data['nom']))) {
                return ['success' => false, 'message' => 'Cette catégorie existe déjà'];
            }

            $slug = $this->generateSlug($data['nom']);

            $result = $this->updateCategoryDB($id_categorie, [
                'nom' => trim($data['nom']),
                'description' => trim($data['description'] ?? ''),
                'slug' => $slug
            ]);

            if ($result) {
                return ['success' => true, 'message' => 'Catégorie mise à jour avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
            }
        } catch (Exception $e) {
            error_log("Erreur dans CategoryController::update: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
        }
    }

    /**
     * Delete category with CASCADE deletion of articles and comments
     * When a category is deleted:
     * 1. Get all articles in this category
     * 2. For each article, delete all its comments
     * 3. Delete all articles
     * 4. Delete the category
     */
    public function delete($id_categorie) {
        try {
            // Check if category exists
            $existingCategory = $this->getCategoryById($id_categorie);
            if (!$existingCategory) {
                return ['success' => false, 'message' => 'Catégorie non trouvée'];
            }

            $pdo = $this->categoryModel->getPDO();

            // Start transaction for atomic operation
            $pdo->beginTransaction();

            try {
                // Step 1: Get all articles in this category
                $articles = $this->getArticlesByCategory($id_categorie);
                $articleIds = [];
                $images = [];

                foreach ($articles as $article) {
                    if (isset($article['id_article'])) {
                        $articleIds[] = (int)$article['id_article'];
                    }
                    if (!empty($article['image'])) {
                        $images[] = $article['image'];
                    }
                }

                $deletedCommentsCount = 0;
                $deletedArticlesCount = 0;

                // Step 2: Delete comments for all articles in this category
                if (!empty($articleIds)) {
                    $deletedCommentsCount = $this->deleteCommentsByArticleIds($articleIds, $pdo);

                    if ($deletedCommentsCount === false) {
                        $pdo->rollBack();
                        return ['success' => false, 'message' => 'Échec de la suppression des commentaires liés'];
                    }

                    // Step 3: Delete all articles in this category
                    $deletedArticlesCount = $this->deleteArticlesByIds($articleIds, $pdo);

                    if ($deletedArticlesCount === false) {
                        $pdo->rollBack();
                        return ['success' => false, 'message' => 'Échec de la suppression des articles liés'];
                    }
                }

                // Step 4: Delete the category itself
                $categoryDeleted = $this->deleteCategoryDB($id_categorie);

                if (!$categoryDeleted) {
                    $pdo->rollBack();
                    return ['success' => false, 'message' => 'Erreur lors de la suppression de la catégorie'];
                }

                // Commit transaction
                $pdo->commit();

                // Step 5: Clean up image files (outside transaction - best effort)
                if (!empty($images)) {
                    $uploadsDir = __DIR__ . '/../../public/uploads/articles/';
                    foreach ($images as $imgPath) {
                        $basename = basename($imgPath);
                        $fullPath = $uploadsDir . $basename;
                        if (is_file($fullPath)) {
                            @unlink($fullPath);
                        }
                    }
                }

                $message = sprintf(
                    'Catégorie supprimée avec succès. %d article(s) et %d commentaire(s) supprimés.',
                    $deletedArticlesCount,
                    $deletedCommentsCount
                );

                return [
                    'success' => true,
                    'message' => $message,
                    'deleted_articles' => $deletedArticlesCount,
                    'deleted_comments' => $deletedCommentsCount
                ];

            } catch (Exception $e) {
                // Rollback on any error
                $pdo->rollBack();
                throw $e;
            }

        } catch (Exception $e) {
            error_log("Erreur dans CategoryController::delete: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur lors de la suppression'];
        }
    }

    /**
     * Search categories
     */
    public function search($keyword) {
        try {
            $pdo = $this->categoryModel->getPDO();
            $table = $this->categoryModel->getTableName();
            $keyword = "%{$keyword}%";

            $query = "SELECT * FROM {$table} 
                     WHERE nom LIKE :keyword OR description LIKE :keyword 
                     ORDER BY nom ASC";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':keyword', $keyword);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche de catégories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if category is in use
     */
    public function isInUse($id_categorie) {
        return $this->countArticlesByCategory($id_categorie) > 0;
    }

    // ===== PRIVATE DATABASE METHODS =====

    /**
     * Get all categories from database
     */
    private function getCategories() {
        try {
            $pdo = $this->categoryModel->getPDO();
            $table = $this->categoryModel->getTableName();
            $query = "SELECT * FROM {$table} ORDER BY nom ASC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des catégories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create category in database
     */
    private function createCategoryDB($data) {
        try {
            $pdo = $this->categoryModel->getPDO();
            $table = $this->categoryModel->getTableName();

            $query = "INSERT INTO {$table} (nom, description, slug) 
                     VALUES (:nom, :description, :slug)";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':slug', $data['slug']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de la catégorie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update category in database
     */
    private function updateCategoryDB($id_categorie, $data) {
        try {
            $pdo = $this->categoryModel->getPDO();
            $table = $this->categoryModel->getTableName();

            $query = "UPDATE {$table} SET 
                     nom = :nom, 
                     description = :description, 
                     slug = :slug,
                     updated_at = NOW()
                     WHERE id_categorie = :id";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_categorie, PDO::PARAM_INT);
            $stmt->bindParam(':nom', $data['nom']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':slug', $data['slug']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de la catégorie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete category from database
     */
    private function deleteCategoryDB($id_categorie) {
        try {
            $pdo = $this->categoryModel->getPDO();
            $table = $this->categoryModel->getTableName();

            $query = "DELETE FROM {$table} WHERE id_categorie = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_categorie, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de la catégorie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if category name exists
     */
    private function categoryExists($nom) {
        try {
            $pdo = $this->categoryModel->getPDO();
            $table = $this->categoryModel->getTableName();
            $query = "SELECT COUNT(*) as count FROM {$table} WHERE nom = :nom";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':nom', $nom);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de la catégorie: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Count articles in category
     */
    private function countArticlesByCategory($id_categorie) {
        try {
            $pdo = $this->categoryModel->getPDO();
            $query = "SELECT COUNT(*) as count FROM article WHERE categorie = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_categorie, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Erreur lors du comptage des articles: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get articles in category
     */
    private function getArticlesByCategory($id_categorie) {
        try {
            $pdo = $this->categoryModel->getPDO();
            $query = "SELECT * FROM article WHERE categorie = :id ORDER BY date_publication DESC";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_categorie, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des articles de la catégorie: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Delete comments for a list of article IDs (CASCADE)
     * Uses provided PDO to stay within the same transaction
     */
    private function deleteCommentsByArticleIds(array $articleIds, PDO $pdo) {
        if (empty($articleIds)) {
            return 0;
        }

        try {
            // Build IN clause with placeholders
            $placeholders = implode(',', array_fill(0, count($articleIds), '?'));
            $sql = "DELETE FROM commentaire WHERE id_article IN ($placeholders)";

            $stmt = $pdo->prepare($sql);

            // Bind each article ID
            foreach ($articleIds as $index => $id) {
                $stmt->bindValue($index + 1, (int)$id, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->rowCount();

        } catch (PDOException $e) {
            error_log('Erreur lors de la suppression en cascade des commentaires: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete articles by IDs (CASCADE)
     * Uses provided PDO to stay within the same transaction
     */
    private function deleteArticlesByIds(array $articleIds, PDO $pdo) {
        if (empty($articleIds)) {
            return 0;
        }

        try {
            // Build IN clause with placeholders
            $placeholders = implode(',', array_fill(0, count($articleIds), '?'));
            $sql = "DELETE FROM article WHERE id_article IN ($placeholders)";

            $stmt = $pdo->prepare($sql);

            // Bind each article ID
            foreach ($articleIds as $index => $id) {
                $stmt->bindValue($index + 1, (int)$id, PDO::PARAM_INT);
            }

            $stmt->execute();
            return $stmt->rowCount();

        } catch (PDOException $e) {
            error_log('Erreur lors de la suppression des articles liés: ' . $e->getMessage());
            return false;
        }
    }

    // ===== UTILITY METHODS =====

    /**
     * Validate category data
     */
    private function validateCategoryData($data) {
        $errors = [];

        if (empty(trim($data['nom'] ?? ''))) {
            $errors['nom'] = 'Le nom de la catégorie est obligatoire';
        } elseif (strlen(trim($data['nom'])) < 2) {
            $errors['nom'] = 'Le nom doit contenir au moins 2 caractères';
        } elseif (strlen(trim($data['nom'])) > 100) {
            $errors['nom'] = 'Le nom ne peut pas dépasser 100 caractères';
        }

        return $errors;
    }

    /**
     * Generate URL-friendly slug
     */
    private function generateSlug($text) {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^\w\s-]/u', '', $text);
        $text = preg_replace('/[\s_]+/', '-', $text);
        $text = preg_replace('/^-+|-+$/', '', $text);
        return $text;
    }
}
?>