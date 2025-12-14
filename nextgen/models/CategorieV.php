<?php

class Categoriev
{
    private int $id_categoriev;
    private string $nom_categoriev;
    private string $description_categoriev;

    public function __construct($nom_categoriev = '', $description_categoriev = '', $id_categoriev = 0)
    {
        $this->nom_categoriev = $nom_categoriev;
        $this->description_categoriev = $description_categoriev;
        $this->id_categoriev = $id_categoriev;
    }

    // ==================== GETTERS ====================

    public function getIdCategoriev(): int
    {
        return $this->id_categoriev;
    }

    public function getNomCategoriev(): string
    {
        return $this->nom_categoriev;
    }

    public function getDescriptionCategoriev(): string
    {
        return $this->description_categoriev;
    }

    // ==================== SETTERS ====================

    public function setNomCategoriev(string $nom_categoriev): void
    {
        $this->nom_categoriev = $nom_categoriev;
    }

    public function setDescriptionCategoriev(string $description_categoriev): void
    {
        $this->description_categoriev = $description_categoriev;
    }

    public function setIdCategoriev(int $id_categoriev): void
    {
        $this->id_categoriev = $id_categoriev;
    }
}
?>
