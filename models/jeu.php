<?php
// models/jeu.php

class Jeu
{
    private $id_jeu;
    private $titre;
    private $prix;
    private $src_img;
    private $id_categorie;
    private $description;  // ← NOUVEAU

    public function __construct($titre, $prix, $src_img, $id_categorie, $id_jeu = null, $description = null)
    {
        $this->id_jeu = $id_jeu;
        $this->titre = $titre;
        $this->prix = $prix;
        $this->src_img = $src_img;
        $this->id_categorie = $id_categorie;
        $this->description = $description;  // ← NOUVEAU
    }

    // Getters
    public function getIdJeu() { return $this->id_jeu; }
    public function getTitre() { return $this->titre; }
    public function getPrix() { return $this->prix; }
    public function getSrcImg() { return $this->src_img; }
    public function getIdCategorie() { return $this->id_categorie; }
    public function getDescription() { return $this->description; }  // ← NOUVEAU

    // Setters
    public function setTitre($titre) { $this->titre = $titre; }
    public function setPrix($prix) { $this->prix = $prix; }
    public function setSrcImg($src_img) { $this->src_img = $src_img; }
    public function setIdCategorie($id_categorie) { $this->id_categorie = $id_categorie; }
    public function setDescription($description) { $this->description = $description; }  // ← NOUVEAU
}