<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Article.php';

class ArticleController
{
    private $uploadDir = __DIR__ . '/../resources/';
    private $uploadUrl = '/resources/';

    public function __construct()
    {
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    // List all articles (latest first)
    public function listeArticles(): array
    {
        $sql = "SELECT a.*, 
                       COALESCE((SELECT COUNT(*) FROM commentaire c WHERE c.id_article = a.id_article), 0) AS comment_count
                FROM article a 
                ORDER BY a.date_publication DESC";

        $db = Config::getConnexion();
        try {
            $query = $db->query($sql);
            $articles = [];
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $article = new Article(
                    $row['titre'],
                    $row['content'],
                    $row['date_publication'],
                    $row['categorie'],
                    $row['id_auteur'],
                    $row['image'] ?? null,
                    $row['id_article'],
                    (int)$row['rating_count'],
                    (int)$row['rating_sum']
                );
                $article->comment_count = (int)$row['comment_count'];
                $articles[] = $article;
            }
            return $articles;
        } catch (Exception $e) {
            error_log('Erreur listeArticles: ' . $e->getMessage());
            return [];
        }
    }

    // Get single article
    public function getArticle(int $id): ?Article
    {
        $sql = "SELECT * FROM article WHERE id_article = :id";
        $db = Config::getConnexion();
        try {
            $query = $db->prepare($sql);
            $query->execute([':id' => $id]);
            $row = $query->fetch(PDO::FETCH_ASSOC);
            if (!$row) return null;

            return new Article(
                $row['titre'],
                $row['content'],
                $row['date_publication'],
                $row['categorie'],
                $row['id_auteur'],
                $row['image'] ?? null,
                $row['id_article'],
                (int)$row['rating_count'],
                (int)$row['rating_sum']
            );
        } catch (Exception $e) {
            error_log('Erreur getArticle: ' . $e->getMessage());
            return null;
        }
    }

    // Add article
    public function ajouterArticle(Article $article, ?array $file = null): bool
    {
        $db = Config::getConnexion();
        try {
            $db->beginTransaction();

            $imagePath = null;
            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->handleUpload($file);
                if ($imagePath) {
                    $article->setImage($imagePath);
                }
            }

            $sql = "INSERT INTO article 
                    (titre, content, date_publication, categorie, image, id_auteur, rating_count, rating_sum)
                    VALUES (:titre, :content, NOW(), :categorie, :image, :id_auteur, 0, 0)";

            $query = $db->prepare($sql);
            $success = $query->execute([
                ':titre' => $article->getTitre(),
                ':content' => $article->getContent(),
                ':categorie' => $article->getCategorie(),
                ':image' => $article->getImage(),
                ':id_auteur' => $article->getIdAuteur()
            ]);

            $db->commit();
            return $success;
        } catch (Exception $e) {
            $db->rollBack();
            error_log('Erreur ajouterArticle: ' . $e->getMessage());
            return false;
        }
    }

    // Update article
    public function modifierArticle(Article $article, ?array $file = null): bool
    {
        $db = Config::getConnexion();
        try {
            $db->beginTransaction();

            $oldArticle = $this->getArticle($article->getIdArticle());
            $currentImage = $oldArticle ? $oldArticle->getImage() : null;

            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $newImage = $this->handleUpload($file);
                if ($newImage) {
                    $article->setImage($newImage);
                    if ($currentImage && file_exists(__DIR__ . '/../' . $currentImage)) {
                        unlink(__DIR__ . '/../' . $currentImage);
                    }
                }
            } else {
                $article->setImage($currentImage);
            }

            $sql = "UPDATE article SET 
                    titre = :titre,
                    content = :content,
                    categorie = :categorie,
                    image = :image,
                    id_auteur = :id_auteur
                    WHERE id_article = :id";

            $query = $db->prepare($sql);
            $success = $query->execute([
                ':titre' => $article->getTitre(),
                ':content' => $article->getContent(),
                ':categorie' => $article->getCategorie(),
                ':image' => $article->getImage(),
                ':id_auteur' => $article->getIdAuteur(),
                ':id' => $article->getIdArticle()
            ]);

            $db->commit();
            return $success;
        } catch (Exception $e) {
            $db->rollBack();
            error_log('Erreur modifierArticle: ' . $e->getMessage());
            return false;
        }
    }

    // Delete article
    public function supprimerArticle(int $id): bool
    {
        $db = Config::getConnexion();
        try {
            $db->beginTransaction();

            $article = $this->getArticle($id);
            if ($article && $article->getImage() && file_exists(__DIR__ . '/../' . $article->getImage())) {
                unlink(__DIR__ . '/../' . $article->getImage());
            }

            $sql = "DELETE FROM article WHERE id_article = :id";
            $query = $db->prepare($sql);
            $query->execute([':id' => $id]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log('Erreur supprimerArticle: ' . $e->getMessage());
            return false;
        }
    }

    private function handleUpload(array $file): ?string
    {
        $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!isset($allowed[$ext]) || !in_array($file['type'], $allowed)) {
            return null;
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
            return null;
        }

        $filename = 'article_' . uniqid() . '.' . $ext;
        $target = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return $this->uploadUrl . $filename;
        }

        return null;
    }
}