<?php
require_once __DIR__ . '/../Models/CommentaireModel.php';

class CommentController {
    private $commentModel;

    public function __construct() {
        $this->commentModel = new CommentModel();
    }

    /**
     * Récupérer tous les commentaires (pour le backoffice)
     */
    public function getAllComments() {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            $stmt = $pdo->query("SELECT * FROM {$table} ORDER BY date_commentaire DESC");
            if (!$stmt) {
                error_log('CommentController::getAllComments -> échec de la préparation de la requête');
                return [];
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Exception $e) {
            error_log('CommentController::getAllComments error: ' . $e->getMessage());
            return [];
        }
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
                // Récupérer les réponses du commentaire
                $replies = $this->getRepliesByCommentId($comment['id_commentaire']);
                $formattedReplies = [];
                foreach ($replies as $reply) {
                    $formattedReplies[] = [
                        'id_commentaire' => $reply['id_commentaire'],
                        'nom_visiteur' => htmlspecialchars($reply['nom_visiteur']),
                        'contenu' => htmlspecialchars($reply['contenu']),
                        'date_commentaire' => $this->formatDate($reply['date_commentaire']),
                        'likes' => (int)($reply['likes'] ?? 0),
                        'avatar' => $this->getDefaultAvatar()
                    ];
                }

                $formattedComments[] = [
                    'id_commentaire' => $comment['id_commentaire'],
                    'id_article' => $comment['id_article'],
                    'nom_visiteur' => htmlspecialchars($comment['nom_visiteur']),
                    'contenu' => htmlspecialchars($comment['contenu']),
                    'date_commentaire' => $this->formatDate($comment['date_commentaire']),
                    'likes' => (int)($comment['likes'] ?? 0),
                    'avatar' => $this->getDefaultAvatar(),
                    'replies' => $formattedReplies
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
                'message' => 'Erreur serveur',
                'comments' => []
            ];
        }
    }

    /**
     * Créer un nouveau commentaire ou une réponse
     */
    public function create($id_article, $nom_visiteur, $contenu, $id_parent = null) {
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
                'date_commentaire' => date('Y-m-d H:i:s'),
                'likes' => 0,
                'id_parent' => $id_parent ? (int)$id_parent : null
            ];

            $result = $this->createCommentDB($commentData);

            if ($result) {
                return ['success' => true, 'message' => 'Commentaire créé avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la création du commentaire'];
            }
        } catch (Exception $e) {
            error_log("Erreur dans CommentController::create: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
        }
    }

    /**
     * Mettre à jour un commentaire
     */
    public function update($id_commentaire, $contenu) {
        try {
            if (!$id_commentaire || empty(trim($contenu))) {
                return ['success' => false, 'message' => 'Données invalides'];
            }

            $contenu = trim($contenu);

            // Validation du contenu
            if (strlen($contenu) < 3) {
                return ['success' => false, 'message' => 'Le commentaire doit contenir au moins 3 caractères'];
            }

            if (strlen($contenu) > 1000) {
                return ['success' => false, 'message' => 'Le commentaire ne doit pas dépasser 1000 caractères'];
            }

            // Vérifier que le commentaire existe
            $existingComment = $this->getCommentById($id_commentaire);
            if (!$existingComment) {
                return ['success' => false, 'message' => 'Commentaire non trouvé'];
            }

            // Mettre à jour le commentaire
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();
            $query = "UPDATE {$table} SET contenu = :contenu WHERE id_commentaire = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':contenu', $contenu);
            $stmt->bindParam(':id', $id_commentaire, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Commentaire mis à jour avec succès'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de la mise à jour'];
            }

        } catch (Exception $e) {
            error_log("Erreur dans CommentController::update: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
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

    /**
     * Ajouter un like à un commentaire
     */
    public function addLike($id_commentaire) {
        try {
            if (!$id_commentaire) {
                return ['success' => false, 'message' => 'ID commentaire invalide'];
            }

            $existingComment = $this->getCommentById($id_commentaire);
            if (!$existingComment) {
                return ['success' => false, 'message' => 'Commentaire non trouvé'];
            }

            $result = $this->incrementLikesDB($id_commentaire);

            if ($result) {
                $updatedComment = $this->getCommentById($id_commentaire);
                return [
                    'success' => true,
                    'message' => 'Like ajouté avec succès',
                    'likes' => (int)($updatedComment['likes'] ?? 0)
                ];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de l\'ajout du like'];
            }
        } catch (Exception $e) {
            error_log("Erreur dans CommentController::addLike: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
        }
    }

    /**
     * Retirer un like d'un commentaire
     */
    public function removeLike($id_commentaire) {
        try {
            if (!$id_commentaire) {
                return ['success' => false, 'message' => 'ID commentaire invalide'];
            }

            $existingComment = $this->getCommentById($id_commentaire);
            if (!$existingComment) {
                return ['success' => false, 'message' => 'Commentaire non trouvé'];
            }

            $result = $this->decrementLikesDB($id_commentaire);

            if ($result) {
                $updatedComment = $this->getCommentById($id_commentaire);
                return [
                    'success' => true,
                    'message' => 'Like retiré avec succès',
                    'likes' => (int)($updatedComment['likes'] ?? 0)
                ];
            } else {
                return ['success' => false, 'message' => 'Erreur lors du retrait du like'];
            }
        } catch (Exception $e) {
            error_log("Erreur dans CommentController::removeLike: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur serveur'];
        }
    }

    // ===== REQUÊTES SQL =====

    /**
     * Récupérer tous les commentaires d'un article (sans réponses)
     */
    private function getAllCommentsByArticle($id_article) {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            $query = "SELECT * FROM {$table}
                     WHERE id_article = :id_article AND id_parent IS NULL
                     ORDER BY date_commentaire DESC";

            $stmt = $pdo->prepare($query);

            if (!$stmt) {
                error_log("Failed to prepare statement: " . implode(", ", $pdo->errorInfo()));
                return [];
            }

            $stmt->bindParam(':id_article', $id_article, PDO::PARAM_INT);
            $executed = $stmt->execute();

            if (!$executed) {
                error_log("Failed to execute query: " . implode(", ", $stmt->errorInfo()));
                return [];
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des commentaires: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupérer les réponses d'un commentaire
     */
    private function getRepliesByCommentId($id_parent) {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            $query = "SELECT * FROM {$table}
                     WHERE id_parent = :id_parent
                     ORDER BY date_commentaire ASC";

            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id_parent', $id_parent, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des réponses: " . $e->getMessage());
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
     * Créer un nouveau commentaire en base de données
     */
    private function createCommentDB($data) {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            $query = "INSERT INTO {$table} (id_article, nom_visiteur, contenu, date_commentaire, likes, id_parent) 
                     VALUES (:id_article, :nom_visiteur, :contenu, :date_commentaire, :likes, :id_parent)";

            $stmt = $pdo->prepare($query);

            if (!$stmt) {
                error_log("Failed to prepare insert statement: " . implode(", ", $pdo->errorInfo()));
                return false;
            }

            $stmt->bindParam(':id_article', $data['id_article'], PDO::PARAM_INT);
            $stmt->bindParam(':nom_visiteur', $data['nom_visiteur']);
            $stmt->bindParam(':contenu', $data['contenu']);
            $stmt->bindParam(':date_commentaire', $data['date_commentaire']);
            $stmt->bindParam(':likes', $data['likes'], PDO::PARAM_INT);
            $stmt->bindParam(':id_parent', $data['id_parent'], PDO::PARAM_INT);

            $executed = $stmt->execute();

            if (!$executed) {
                error_log("Failed to execute insert: " . implode(", ", $stmt->errorInfo()));
                return false;
            }

            return $executed;
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du commentaire: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprimer un commentaire et ses réponses
     */
    private function deleteCommentDB($id_commentaire) {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            // Supprimer les réponses du commentaire
            $query1 = "DELETE FROM {$table} WHERE id_parent = :id";
            $stmt1 = $pdo->prepare($query1);
            $stmt1->bindParam(':id', $id_commentaire, PDO::PARAM_INT);
            $stmt1->execute();

            // Supprimer le commentaire lui-même
            $query2 = "DELETE FROM {$table} WHERE id_commentaire = :id";
            $stmt2 = $pdo->prepare($query2);
            $stmt2->bindParam(':id', $id_commentaire, PDO::PARAM_INT);

            return $stmt2->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du commentaire: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Incrémenter le nombre de likes
     */
    private function incrementLikesDB($id_commentaire) {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            $query = "UPDATE {$table} SET likes = likes + 1 WHERE id_commentaire = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_commentaire, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de l'incrémentation des likes: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Décrémenter le nombre de likes
     */
    private function decrementLikesDB($id_commentaire) {
        try {
            $pdo = $this->commentModel->getPDO();
            $table = $this->commentModel->getTableName();

            $query = "UPDATE {$table} SET likes = CASE WHEN likes > 0 THEN likes - 1 ELSE 0 END WHERE id_commentaire = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_commentaire, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la décrémentation des likes: " . $e->getMessage());
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