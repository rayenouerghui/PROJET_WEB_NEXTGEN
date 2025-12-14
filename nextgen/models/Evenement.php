<?php

class Evenement
{
    private int $id_evenement;
    private string $titre;
    private string $description;
    private string $date_evenement;
    private string $lieu;
    private int $id_categorie;
    private int $places_disponibles;

    public function __construct($titre = '', $description = '', $date_evenement = '', $lieu = '', $id_categorie = 0, $id_evenement = 0, $places_disponibles = 0)
    {
        $this->titre = $titre;
        $this->description = $description;
        $this->date_evenement = $date_evenement;
        $this->lieu = $lieu;
        $this->id_categorie = $id_categorie;
        $this->id_evenement = $id_evenement;
        $this->places_disponibles = $places_disponibles;
    }

    // ==================== GETTERS ====================

    public function getIdEvenement(): int
    {
        return $this->id_evenement;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDateEvenement(): string
    {
        return $this->date_evenement;
    }

    public function getLieu(): string
    {
        return $this->lieu;
    }

    public function getIdCategorie(): int
    {
        return $this->id_categorie;
    }

    public function getPlacesDisponibles(): int
    {
        return $this->places_disponibles;
    }

    // ==================== SETTERS ====================

    public function setTitre(string $titre): void
    {
        $this->titre = $titre;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setDateEvenement(string $date_evenement): void
    {
        $this->date_evenement = $date_evenement;
    }

    public function setLieu(string $lieu): void
    {
        $this->lieu = $lieu;
    }

    public function setIdCategorie(int $id_categorie): void
    {
        $this->id_categorie = $id_categorie;
    }

    public function setPlacesDisponibles(int $places_disponibles): void
    {
        $this->places_disponibles = $places_disponibles;
    }

    public function setIdEvenement(int $id_evenement): void
    {
        $this->id_evenement = $id_evenement;
    }
}
?>
