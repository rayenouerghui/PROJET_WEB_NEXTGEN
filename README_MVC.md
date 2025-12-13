# Structure MVC du Projet

Ce projet utilise une architecture MVC (Model-View-Controller) propre et organisée.

## Structure des dossiers

```
projet/
├── config/          # Configuration (base de données, etc.)
├── core/            # Classes de base du framework MVC
│   ├── Controller.php   # Classe de base pour tous les contrôleurs
│   └── View.php         # Classe pour gérer le rendu des vues
├── models/          # Modèles (logique métier et accès aux données)
├── controllers/     # Contrôleurs (logique de traitement des requêtes)
├── views/           # Vues (présentation)
│   ├── admin/       # Vues de l'administration
│   ├── front/       # Vues du site public
│   └── errors/      # Pages d'erreur
└── public/          # Fichiers publics (CSS, images, JS)
```

## Architecture MVC

### Modèle (Models)
- Les modèles représentent les entités métier (Categorie, Evenement, Reservation)
- Ils gèrent l'accès aux données et la logique métier
- Exemple : `models/Reservation.php`

### Vue (Views)
- Les vues sont des fichiers PHP qui contiennent le HTML/XHTML
- Elles utilisent XHTML 1.0 Transitional (pas HTML5)
- Les vues sont incluses depuis les contrôleurs
- Exemple : `views/front/events.php`

### Contrôleur (Controllers)
- Les contrôleurs traitent les requêtes HTTP
- Ils utilisent les modèles pour récupérer les données
- Ils chargent les vues appropriées
- Exemple : `controllers/FrontC.php`

## Classe de base Controller

Tous les contrôleurs peuvent étendre la classe `Controller` qui fournit :

- `render($viewPath, $data)` : Charge une vue avec des données
- `redirect($url)` : Redirige vers une URL
- `jsonResponse($data, $statusCode)` : Retourne une réponse JSON
- `setFlash($type, $message)` : Définit un message flash
- `getFlash($type)` : Récupère un message flash

## Classe View

La classe `View` gère le rendu des vues :

- `render($viewPath, $data)` : Rend une vue avec des données
- `escape($string)` : Échappe une chaîne pour l'affichage HTML
- `url($controller, $action, $params)` : Génère une URL

## Routeur

Le fichier `index.php` agit comme routeur :

- Il analyse les paramètres `c` (controller) et `a` (action)
- Il instancie le contrôleur approprié
- Il appelle la méthode correspondante

Exemple d'URL : `/projet/index.php?c=front&a=events`

## Standards XHTML

Toutes les vues utilisent XHTML 1.0 Transitional :

- DOCTYPE : `<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"...>`
- Toutes les balises doivent être fermées
- Les attributs doivent être en minuscules
- Les valeurs d'attributs doivent être entre guillemets

## Utilisation

### Créer un nouveau contrôleur

```php
<?php
require_once 'config/database.php';
require_once 'core/Controller.php';

class MonControleur extends Controller
{
    public function index()
    {
        // Logique du contrôleur
        $data = ['titre' => 'Mon titre'];
        $this->render('front/ma_vue', $data);
    }
}
?>
```

### Créer une nouvelle vue

```php
<?php include __DIR__ . '/header.php'; ?>

<div class="ma-page">
    <h1><?php echo htmlspecialchars($titre); ?></h1>
</div>

</body>
</html>
```

