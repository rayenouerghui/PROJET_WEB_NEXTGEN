<?php
// Fichier : app/Models/ArticleRatingModel.php

require_once __DIR__ . '/../config/db.php';

class ArticleRatingModel {
    private $pdo;
    private $table = 'article_rating';

    public function __construct() {
        $database = Database::getInstance();
        $this->pdo = $database->getConnection();
    }

    // Sauvegarde ou met à jour la notation
    public function saveRating(int $id_article, int $rating_value, string $user_identifier): bool {
        $sql = "INSERT INTO {$this->table} (id_article, user_identifier, rating_value) 
                VALUES (:id_article, :user_identifier, :rating_value)
                ON DUPLICATE KEY UPDATE rating_value = :rating_value_update, rating_date = NOW()";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_article' => $id_article,
            ':user_identifier' => $user_identifier,
            ':rating_value' => $rating_value,
            ':rating_value_update' => $rating_value,
        ]);
    }

    // Récupère les stats : moyenne et total
    public function getRatingStats(int $id_article): array {
        $sql = "SELECT 
                    COALESCE(AVG(rating_value), 0) as average_rating, 
                    COUNT(id_rating) as total_votes,
                    COALESCE(ROUND(AVG(rating_value), 1), 0) as average_rating_rounded
                FROM {$this->table} 
                WHERE id_article = :id_article";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id_article' => $id_article]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: [
            'average_rating' => 0,
            'total_votes' => 0,
            'average_rating_rounded' => '0.0'
        ];
    }

    // Récupère la note donnée par un utilisateur spécifique
    public function getUserRating(int $id_article, string $user_identifier): int {
        $sql = "SELECT rating_value FROM {$this->table} 
                WHERE id_article = :id_article 
                AND user_identifier = :user_identifier";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id_article' => $id_article,
            ':user_identifier' => $user_identifier
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['rating_value'] : 0;
    }

    // Supprime la notation d'un utilisateur
    public function removeRating(int $id_article, string $user_identifier): bool {
        $sql = "DELETE FROM {$this->table} 
                WHERE id_article = :id_article 
                AND user_identifier = :user_identifier";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':id_article' => $id_article,
            ':user_identifier' => $user_identifier
        ]);
    }

    // Récupère les articles les mieux notés
    public function getTopRatedArticles(int $limit = 5): array {
        $sql = "SELECT 
                    a.id_article,
                    a.titre,
                    a.content,
                    a.image,
                    a.date_publication,
                    COALESCE(AVG(ar.rating_value), 0) as avg_rating,
                    COUNT(ar.id_rating) as total_votes
                FROM article a
                LEFT JOIN {$this->table} ar ON a.id_article = ar.id_article
                GROUP BY a.id_article
                HAVING COUNT(ar.id_rating) >= 1
                ORDER BY avg_rating DESC, total_votes DESC
                LIMIT :limit";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>