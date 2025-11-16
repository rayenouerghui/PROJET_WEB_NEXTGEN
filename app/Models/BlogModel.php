<?php
require_once __DIR__.'/../../config/db.php';

class BlogModel {
    private $pdo;
    private $table = 'article';

    public function __construct() {
        $database = Database::getInstance();
        $this->pdo = $database->getConnection();
    }

    // Récupérer tous les articles
    public function getAllArticles() {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY date_publication DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des articles: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer un article par son ID
    public function getArticleById($id_article) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE id_article = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id_article, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'article: " . $e->getMessage());
            return false;
        }
    }

    // Créer un nouvel article
    public function createArticle($data) {
        try {
            $query = "INSERT INTO {$this->table} (titre, content, date_publication, categorie, image, id_auteur) 
                     VALUES (:titre, :content, :date_publication, :categorie, :image, :id_auteur)";

            $stmt = $this->pdo->prepare($query);
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

    // Mettre à jour un article
    public function updateArticle($id_article, $data) {
        try {
            $query = "UPDATE {$this->table} SET 
                     titre = :titre, 
                     content = :content, 
                     date_publication = :date_publication, 
                     categorie = :categorie, 
                     image = :image, 
                     id_auteur = :id_auteur 
                     WHERE id_article = :id";

            $stmt = $this->pdo->prepare($query);
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

    // Supprimer un article
    public function deleteArticle($id_article) {
        try {
            $query = "DELETE FROM {$this->table} WHERE id_article = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id_article, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de l'article: " . $e->getMessage());
            return false;
        }
    }

    // Récupérer les articles par catégorie
    public function getArticlesByCategory($categorie) {
        try {
            $query = "SELECT * FROM {$this->table} WHERE categorie = :categorie ORDER BY date_publication DESC";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':categorie', $categorie);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des articles par catégorie: " . $e->getMessage());
            return [];
        }
    }

    // Récupérer les derniers articles (pour la page d'accueil)
    public function getRecentArticles($limit = 3) {
        try {
            $query = "SELECT * FROM {$this->table} ORDER BY date_publication DESC LIMIT :limit";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des articles récents: " . $e->getMessage());
            return [];
        }
    }
}
?>