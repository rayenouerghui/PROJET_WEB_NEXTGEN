<?php
// Fichier : app/Controllers/ArticleRatingController.php

require_once __DIR__ . '/../Models/ArticleRatingModel.php';

class ArticleRatingController {
    private $model;

    public function __construct() {
        $this->model = new ArticleRatingModel();
    }

    private function getUserIdentifier(): string {
        // Pour les invités, utilisez l'adresse IP
        return $_SERVER['REMOTE_ADDR'] ?? 'guest_no_ip';
    }

    /**
     * Ajoute ou met à jour une notation
     */
    public function addRating(int $id_article, int $rating_value): array {
        if ($id_article <= 0 || $rating_value < 1 || $rating_value > 5) {
            return [
                'success' => false,
                'message' => 'Note invalide (doit être entre 1 et 5).'
            ];
        }

        $user_identifier = $this->getUserIdentifier();
        $success = $this->model->saveRating($id_article, $rating_value, $user_identifier);

        if ($success) {
            // Récupérer les stats mises à jour
            $stats = $this->model->getRatingStats($id_article);
            return [
                'success' => true,
                'message' => 'Notation enregistrée avec succès.',
                'stats' => $stats,
                'user_rating' => $rating_value
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement de la notation.'
            ];
        }
    }

    /**
     * Supprime la notation d'un utilisateur
     */
    public function removeRating(int $id_article): array {
        if ($id_article <= 0) {
            return [
                'success' => false,
                'message' => 'ID article invalide.'
            ];
        }

        $user_identifier = $this->getUserIdentifier();
        $success = $this->model->removeRating($id_article, $user_identifier);

        if ($success) {
            // Récupérer les stats mises à jour
            $stats = $this->model->getRatingStats($id_article);
            return [
                'success' => true,
                'message' => 'Votre note a été supprimée.',
                'stats' => $stats
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la suppression de la notation.'
            ];
        }
    }

    /**
     * Récupère les statistiques de notation
     */
    public function getRatingStats(int $id_article): array {
        $user_identifier = $this->getUserIdentifier();
        $stats = $this->model->getRatingStats($id_article);
        $user_rating = $this->model->getUserRating($id_article, $user_identifier);

        return [
            'success' => true,
            'stats' => $stats,
            'user_rating' => $user_rating
        ];
    }

    /**
     * Récupère les articles les mieux notés
     */
    public function getTopRatedArticles(int $limit = 5): array {
        try {
            $articles = $this->model->getTopRatedArticles($limit);
            return [
                'success' => true,
                'articles' => $articles
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération des articles.',
                'articles' => []
            ];
        }
    }
}
?>