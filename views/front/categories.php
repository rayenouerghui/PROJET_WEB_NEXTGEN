<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEXTGEN - Catégories</title>
    <link rel="stylesheet" href="/projet/public/css/style.css">
    <style>
        /* Tu peux supprimer tout ce style car il est maintenant dans style.css */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>NEXTGEN</h1>
            <p>Choisissez le type d'événement qui vous intéresse</p>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $cat): ?>
                <a href="?page=front&action=events&category_id=<?= $cat['id_categorie'] ?>" class="category-card">
                    <div class="category-icon">
                        <?php 
                        $icons = [
                            'Concerts' => '🎵',
                            'Conférences' => '💬',
                            'Ateliers' => '🎨',
                            'Sport' => '⚽',
                            'Festivals' => '🎪'
                        ];
                        echo $icons[$cat['nom_categorie']] ?? '📅';
                        ?>
                    </div>
                    <div class="category-name"><?= htmlspecialchars($cat['nom_categorie']) ?></div>
                    <div class="category-desc"><?= htmlspecialchars($cat['description_categorie']) ?></div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>