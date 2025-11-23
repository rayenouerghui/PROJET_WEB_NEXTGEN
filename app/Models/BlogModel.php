<?php
require_once __DIR__.'/../../config/db.php';

class BlogModel {
    private $pdo;
    private $table = 'article';

    // Propriétés de l'article (attrs)
    private $id_article;
    private $titre;
    private $content;
    private $date_publication;
    private $categorie;
    private $image;
    private $id_auteur;

    public function __construct() {
        $database = Database::getInstance();
        $this->pdo = $database->getConnection();
    }

    // ===== GETTERS =====
    public function getId() {
        return $this->id_article;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getContent() {
        return $this->content;
    }

    public function getDatePublication() {
        return $this->date_publication;
    }

    public function getCategorie() {
        return $this->categorie;
    }

    public function getImage() {
        return $this->image;
    }

    public function getIdAuteur() {
        return $this->id_auteur;
    }

    public function getPDO() {
        return $this->pdo;
    }

    public function getTableName() {
        return $this->table;
    }

    // ===== SETTERS =====
    public function setId($id_article) {
        $this->id_article = $id_article;
        return $this;
    }

    public function setTitre($titre) {
        $this->titre = trim($titre);
        return $this;
    }

    public function setContent($content) {
        $this->content = trim($content);
        return $this;
    }

    public function setDatePublication($date_publication) {
        $this->date_publication = $date_publication;
        return $this;
    }

    public function setCategorie($categorie) {
        $this->categorie = trim($categorie);
        return $this;
    }

    public function setImage($image) {
        $this->image = $image;
        return $this;
    }

    public function setIdAuteur($id_auteur) {
        $this->id_auteur = $id_auteur;
        return $this;
    }

    // ===== MÉTHODE POUR CHARGER LES DONNÉES DANS LES PROPRIÉTÉS =====
    public function loadFromArray($data) {
        if (isset($data['id_article'])) $this->setId($data['id_article']);
        if (isset($data['titre'])) $this->setTitre($data['titre']);
        if (isset($data['content'])) $this->setContent($data['content']);
        if (isset($data['date_publication'])) $this->setDatePublication($data['date_publication']);
        if (isset($data['categorie'])) $this->setCategorie($data['categorie']);
        if (isset($data['image'])) $this->setImage($data['image']);
        if (isset($data['id_auteur'])) $this->setIdAuteur($data['id_auteur']);
        return $this;
    }

    // ===== MÉTHODE POUR OBTENIR LES DONNÉES SOUS FORME DE TABLEAU =====
    public function toArray() {
        return [
            'id_article' => $this->id_article,
            'titre' => $this->titre,
            'content' => $this->content,
            'date_publication' => $this->date_publication,
            'categorie' => $this->categorie,
            'image' => $this->image,
            'id_auteur' => $this->id_auteur
        ];
    }
}
?>