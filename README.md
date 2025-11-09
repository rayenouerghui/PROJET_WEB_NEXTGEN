# NextGen - Plateforme de Vente de Jeux Vidéo Solidaire

Plateforme de vente de jeux vidéo où chaque achat contribue à soutenir la Maison des Orphelins.

## 🎯 Modules et Responsables

| Module | Responsable | Branche Git |
|--------|------------|-------------|
| 👥 Gestion des Utilisateurs | Ahlem Zouari | `feature/users-management` |
| 🎮 Gestion des Produits (Jeux) | Boulares DhiaEddine | `feature/products-management` |
| 🛒 Gestion des Achats | Ouerghi Rayen | `feature/orders-management` |
| 💝 Gestion des Dons | Ayoub Bouzidi | `feature/donations-management` |
| 🤝 Gestion des Partenaires | Sridi Mariem | `feature/partners-management` |
| ↩️ Retours et Réclamations | Dhorbani Louay | `feature/returns-management` |

## 📁 Structure du Projet

```
projet/
├── frontoffice/          # Interface utilisateur
│   ├── css/
│   ├── js/              # (vide - sera géré en PHP)
│   └── *.html           # Pages HTML (à convertir en .php)
├── backoffice/          # Interface administrateur
│   ├── css/
│   ├── js/              # (vide - sera géré en PHP)
│   └── *.html           # Pages HTML (à convertir en .php)
└── assets/              # Ressources partagées
    ├── css/
    └── images/
```

## 🚀 Installation

1. Cloner le repository :
```bash
git clone https://github.com/votre-username/nextgen.git
cd nextgen
```

2. Démarrer XAMPP (Apache)

3. Accéder au site :
- Frontoffice : `http://localhost/nextgen/frontoffice/`
- Backoffice : `http://localhost/nextgen/backoffice/`

## 🌿 Workflow Git

### Pour chaque membre de l'équipe :

1. **Récupérer la dernière version** :
```bash
git checkout main
git pull origin main
```

2. **Créer/Se placer sur votre branche** :
```bash
git checkout -b feature/votre-module
# ou si la branche existe déjà :
git checkout feature/votre-module
git pull origin feature/votre-module
```

3. **Travailler sur votre module**

4. **Commiter vos changements** :
```bash
git add .
git commit -m "Description de vos modifications"
git push origin feature/votre-module
```

5. **Créer une Pull Request sur GitHub** pour fusionner dans `main`

## 📝 Conventions de Nommage

- **Branches** : `feature/nom-module` (ex: `feature/users-management`)
- **Commits** : Messages clairs en français (ex: "Ajout authentification utilisateur")
- **Fichiers PHP** : `nom_module.php` (ex: `users.php`, `products.php`)

## 🎨 Charte Graphique

- **Couleur Primaire** : Bleu (#2563eb)
- **Couleur Secondaire** : Rouge (#dc2626)
- **Couleur Accent** : Orange (#ea580c)
- **Typographie** : Montserrat (titres), Roboto (texte)

## 📋 Technologies

- Frontend : HTML5, CSS3
- Backend : PHP (à implémenter)
- Base de données : MySQL (à configurer)
- Serveur : XAMPP

## 👥 Équipe

- **Ahlem Zouari** - Gestion des Utilisateurs
- **Boulares DhiaEddine** - Gestion des Produits
- **Ouerghi Rayen** - Gestion des Achats
- **Ayoub Bouzidi** - Gestion des Dons
- **Sridi Mariem** - Gestion des Partenaires
- **Dhorbani Louay** - Retours et Réclamations

## 📞 Contact

Pour toute question, créer une issue sur GitHub.
