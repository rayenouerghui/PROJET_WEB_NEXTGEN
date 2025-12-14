<?php
// Compat layer: some controllers expect models/Categorie.php and class Categorie.
// The project currently uses class `Categoriev` in `CategorieV.php`.
// This file ensures `Categorie` is available and extends `Categoriev` so existing calls work.
require_once __DIR__ . '/CategorieV.php';

if (!class_exists('Categorie') && class_exists('Categoriev')) {
    class Categorie extends Categoriev {
        public function getIdCategorie(): int
        {
            return $this->getIdCategoriev();
        }

        public function getNomCategorie(): string
        {
            return $this->getNomCategoriev();
        }

        public function getDescription(): string
        {
            return $this->getDescriptionCategoriev();
        }

        public function setIdCategorie(int $id): void
        {
            $this->setIdCategoriev($id);
        }

        public function setNomCategorie(string $nom): void
        {
            $this->setNomCategoriev($nom);
        }

        public function setDescription(string $description): void
        {
            $this->setDescriptionCategoriev($description);
        }
    }
}

?>
