<?php
// Compat layer: some controllers expect models/Categorie.php and class Categorie.
// The project currently uses class `Categoriev` in `CategorieV.php`.
// This file ensures `Categorie` is available and extends `Categoriev` so existing calls work.
require_once __DIR__ . '/CategorieV.php';

if (!class_exists('Categorie') && class_exists('Categoriev')) {
    class Categorie extends Categoriev {
        // empty - inherits all methods/properties from Categoriev
    }
}

?>
