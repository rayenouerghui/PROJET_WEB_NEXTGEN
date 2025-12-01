
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NextGen – Accueil</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../../../public/css/front.css">
</head>
<body>

<header>
    <div class="container nav">
        <div class="left">
            <a href="index.php" class="logo">NextGen</a>
            <nav class="menu">
                <a href="index.php" class="active">Accueil</a>
                <a href="catalog.php">Produits</a>
                <a href="blog.php">Blog</a>
                <a href="apropos.php">À Propos</a>

            </nav>
        </div>
        <div>
            <a href="admin.php" style="color:#4f46e5;font-weight:700;">Administration</a>
        </div>
    </div>
</header>

<section class="hero">
    <div class="container">
        <h1>Bienvenue sur NextGen</h1>
        <p>Jouer pour Espérer</p>
        <div>
            <a href="catalog.php" class="btn btn-primary">Voir le Catalogue</a>
            <a href="apropos.php" class="btn btn-secondary">En Savoir Plus</a>
        </div>
    </div>
</section>

<section class="impact">
    <div class="container">
        <h2>Notre Impact</h2>
        <div class="stats">
            <?php
            $stats = array(
                    array('num' => 48, 'label' => 'Jeux Disponibles'),
                    array('num' => 1337, 'label' => 'Utilisateurs'),
                    array('num' => '0 TND', 'label' => 'Dons Collectés')
            );

            foreach($stats as $stat) {
                echo '<div class="stat"><div class="num">' . $stat['num'] . '</div><div class="label">' . $stat['label'] . '</div></div>';
            }
            ?>
        </div>
    </div>
</section>

</body>
</html>
