<?php
require_once __DIR__.'/../../config/db.php';

class CommentModel {
    private $pdo;
    private $table = 'commentaire';

    // Propriétés du commentaire
    private $id_commentaire;
    private $id_article;
    private $nom_visiteur;
    private $contenu;
    private $date_commentaire;
    private $likes;
    private $id_parent;

    public function __construct() {
        $database = Database::getInstance();
        $this->pdo = $database->getConnection();
    }

    // ===== GETTERS =====
    public function getId() {
        return $this->id_commentaire;
    }

    public function getIdArticle() {
        return $this->id_article;
    }

    public function getNomVisiteur() {
        return $this->nom_visiteur;
    }

    public function getContenu() {
        return $this->contenu;
    }

    public function getDateCommentaire() {
        return $this->date_commentaire;
    }

    public function getLikes() {
        return $this->likes ?? 0;
    }

    public function getIdParent() {
        return $this->id_parent;
    }

    public function getPDO() {
        return $this->pdo;
    }

    public function getTableName() {
        return $this->table;
    }

    // ===== SETTERS =====
    public function setId($id_commentaire) {
        $this->id_commentaire = $id_commentaire;
        return $this;
    }

    public function setIdArticle($id_article) {
        $this->id_article = $id_article;
        return $this;
    }

    public function setNomVisiteur($nom_visiteur) {
        $this->nom_visiteur = trim($nom_visiteur);
        return $this;
    }

    public function setContenu($contenu) {
        $this->contenu = trim($contenu);
        return $this;
    }

    public function setDateCommentaire($date_commentaire) {
        $this->date_commentaire = $date_commentaire;
        return $this;
    }

    public function setLikes($likes) {
        $this->likes = (int)$likes;
        return $this;
    }

    public function setIdParent($id_parent) {
        $this->id_parent = $id_parent ? (int)$id_parent : null;
        return $this;
    }

    // ===== MÉTHODE POUR CHARGER LES DONNÉES DANS LES PROPRIÉTÉS =====
    public function loadFromArray($data) {
        if (isset($data['id_commentaire'])) $this->setId($data['id_commentaire']);
        if (isset($data['id_article'])) $this->setIdArticle($data['id_article']);
        if (isset($data['nom_visiteur'])) $this->setNomVisiteur($data['nom_visiteur']);
        if (isset($data['contenu'])) $this->setContenu($data['contenu']);
        if (isset($data['date_commentaire'])) $this->setDateCommentaire($data['date_commentaire']);
        if (isset($data['likes'])) $this->setLikes($data['likes']);
        if (isset($data['id_parent'])) $this->setIdParent($data['id_parent']);
        return $this;
    }

    // ===== MÉTHODE POUR OBTENIR LES DONNÉES SOUS FORME DE TABLEAU =====
    public function toArray() {
        return [
            'id_commentaire' => $this->id_commentaire,
            'id_article' => $this->id_article,
            'nom_visiteur' => $this->nom_visiteur,
            'contenu' => $this->contenu,
            'date_commentaire' => $this->date_commentaire,
            'likes' => $this->likes ?? 0,
            'id_parent' => $this->id_parent
        ];
    }
}
?>