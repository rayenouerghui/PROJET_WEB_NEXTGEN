<?php
require_once __DIR__ . '/../Models/CommentaireModel.php';

class CommentController {
    private $commentModel;

    public function __construct() {
        $this->commentModel = new CommentModel();
    }

    /**
     * Récupérer tous les commentaires d'un article (API JSON)
     */
    public function getByArticleJSON($id_article) {
        try {
            $comments = $this->getAllCommentsByArticle($id_article);

            // Transformer les données pour le frontend
            $formattedComments = [];
            foreach ($comments as $comment) {
                $formattedComments[] = [
                    'id_commentaire' => $comment['id_commentaire'],
                    'id_article' => $comment['id_article'],
                    'nom_visiteur' => htmlspecialchars($comment['nom_visiteur']),
                    'contenu' => htmlspecialchars($comment['contenu']),
                    'date_commentaire' => $this->formatDate($comment['date_commentaire']),
                    'avatar' => $this->getDefaultAvatar()
                ];
            }

            return [
                'success' => true,
                'count' => count($formattedComments),
                'comments' => $formattedComments
            ];
        } catch (Exception $e) {
            error_log("Erreur dans CommentController::getByArticleJSON: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage(),
                'comments' => []
            ];
        }
    }

    /**
     * Créer un nouveau commentaire
     */
    public function create($id_article, $nom_visiteur, $contenu) {
        try {
            // Validation des données
            $errors = $this->validateCommentData($nom_visiteur, $contenu);
            if (!empty($errors)) {
                return ['success' => false, 'errors' => $errors];
            }

            // Préparer les données
            $commentData = [
                'id_article' => (int)$id_article,
                'nom_visiteur' => trim($nom_visiteur),
                'contenu' => trim($contenu),
                'date_commentaire' => date('Y-m-d H:i:s')
            ];

            $result = $this->createCommentDB($commentData);

            if ($result) {
                return ['success' => true, 'message' => 'Commentaire créé avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la création du commentaire'];
            }
        } catch (Exception $e) {
            error_log("Erreur dans CommentController::create: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()];
        }
    }

    /**
     * Supprimer un commentaire
     */
    public function delete($id_commentaire) {
        try {
            // Vérifier si le commentaire existe
            $existingComment = $this->getCommentById($id_commentaire);
            if (!$existingComment) {
                return ['success' => false, 'message' => 'Commentaire non trouvé'];
            }

            $result = $this->deleteCommentDB($id_commentaire);

            if ($result) {
                return ['success' => true, 'message' => 'Commentaire supprimé avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la suppression du commentaire'];
            }
        } catch (Exception $e) {
            error_log("Erreur dans CommentController::delete: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
        }
    }

    // ===== REQUÊTES SQL =====

    /**
     * Récupérer tous les commentaires d'un article
     */
    private function getAllCommentsByArticle($id_article) {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            $query = "SELECT * FROM {$table}
                     WHERE id_article = :id_article
                     ORDER BY date_commentaire DESC";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer un commentaire par son ID
     */
    private function getCommentById($id_commentaire) {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            $query = "SELECT * FROM {$table}
                     WHERE id_commentaire = :id";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_commentaire, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du commentaire: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Créer un nouveau commentaire
     */
    private function createCommentDB($data) {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            $query = "INSERT INTO {$table} (id_article, nom_visiteur, contenu, date_commentaire) 
                     VALUES (:id_article, :nom_visiteur, :contenu, :date_commentaire)";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id_article', $data['id_article'], PDO::PARAM_INT);
            $stmt->bindParam(':nom_visiteur', $data['nom_visiteur']);
            $stmt->bindParam(':contenu', $data['contenu']);
            $stmt->bindParam(':date_commentaire', $data['date_commentaire']);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du commentaire: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un commentaire
     */
    private function deleteCommentDB($id_commentaire) {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            $query = "DELETE FROM {$table} WHERE id_commentaire = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_commentaire, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du commentaire: " . $e->getMessage());
            return false;
        }
    }

    // ===== MÉTHODES UTILITAIRES =====

    /**
     * Valider les données d'un commentaire
     */
    private function validateCommentData($nom_visiteur, $contenu) {
        $errors = [];

        if (empty(trim($nom_visiteur))) {
            $errors[] = 'Le nom est obligatoire';
        } elseif (strlen(trim($nom_visiteur)) < 2) {
            $errors[] = 'Le nom doit contenir au moins 2 caractères';
        } elseif (strlen(trim($nom_visiteur)) > 100) {
            $errors[] = 'Le nom ne doit pas dépasser 100 caractères';
        }

        if (empty(trim($contenu))) {
            $errors[] = 'Le commentaire ne peut pas être vide';
        } elseif (strlen(trim($contenu)) < 3) {
            $errors[] = 'Le commentaire doit contenir au moins 3 caractères';
        } elseif (strlen(trim($contenu)) > 1000) {
            $errors[] = 'Le commentaire ne doit pas dépasser 1000 caractères';
        }

        return $errors;
    }

    /**
     * Formater la date de façon relative
     */
    private function formatDate($date) {
        $timestamp = strtotime($date);
        $now = time();
        $diff = $now - $timestamp;

        if ($diff < 60) {
            return 'À l\'instant';
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return 'il y a ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '');
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return 'il y a ' . $hours . ' heure' . ($hours > 1 ? 's' : '');
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return 'il y a ' . $days . ' jour' . ($days > 1 ? 's' : '');
        } else {
            return date('d/m/Y à H:i', $timestamp);
        }
    }

    /**
     * Obtenir un avatar par défaut
     */
    private function getDefaultAvatar() {
        $randomId = rand(1, 70);
        return 'https://i.pravatar.cc/60?img=' . $randomId;
    }
}
?>