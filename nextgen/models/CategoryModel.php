<?php
// ===== CategoryModel.php =====

require_once __DIR__ . '/../config/db.php';

class CategoryModel {
    private $pdo;
    private $table = 'categorie_article';

    // Properties
    private $id_categorie;
    private $nom;
    private $description;
    private $slug;
    private $created_at;
    private $updated_at;

    public function __construct() {
        $database = Database::getInstance();
        $this->pdo = $database->getConnection();
    }

    // ===== GETTERS =====
    public function getId() {
        return $this->id_categorie;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function getCreatedAt() {
        return $this->created_at;
    }

    public function getUpdatedAt() {
        return $this->updated_at;
    }

    public function getPDO() {
        return $this->pdo;
    }

    public function getTableName() {
        return $this->table;
    }

    // ===== SETTERS =====
    public function setId($id_categorie) {
        $this->id_categorie = $id_categorie;
        return $this;
    }

    public function setNom($nom) {
        $this->nom = trim($nom);
        return $this;
    }

    public function setDescription($description) {
        $this->description = trim($description);
        return $this;
    }

    public function setSlug($slug) {
        $this->slug = trim($slug);
        return $this;
    }

    public function setCreatedAt($created_at) {
        $this->created_at = $created_at;
        return $this;
    }

    public function setUpdatedAt($updated_at) {
        $this->updated_at = $updated_at;
        return $this;
    }

    // ===== LOAD FROM ARRAY =====
    public function loadFromArray($data) {
        if (isset($data['id_categorie'])) $this->setId($data['id_categorie']);
        if (isset($data['nom'])) $this->setNom($data['nom']);
        if (isset($data['description'])) $this->setDescription($data['description']);
        if (isset($data['slug'])) $this->setSlug($data['slug']);
        if (isset($data['created_at'])) $this->setCreatedAt($data['created_at']);
        if (isset($data['updated_at'])) $this->setUpdatedAt($data['updated_at']);
        return $this;
    }

    // ===== TO ARRAY =====
    public function toArray() {
        return [
            'id_categorie' => $this->id_categorie,
            'nom' => $this->nom,
            'description' => $this->description,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
?>