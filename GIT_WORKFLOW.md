# Guide Git - Workflow pour l'Équipe NextGen

## 📚 Table des Matières
1. [Initialisation du Projet sur GitHub](#1-initialisation-du-projet-sur-github)
2. [Création des Branches](#2-création-des-branches)
3. [Workflow pour Chaque Membre](#3-workflow-pour-chaque-membre)
4. [Fusion des Branches](#4-fusion-des-branches)
5. [Résolution de Conflits](#5-résolution-de-conflits)

---

## 1. Initialisation du Projet sur GitHub

### Étape 1 : Créer le Repository sur GitHub
1. Aller sur [GitHub.com](https://github.com)
2. Cliquer sur "New repository"
3. Nom : `nextgen` (ou votre nom de projet)
4. Description : "Plateforme de vente de jeux vidéo solidaire"
5. Visibilité : **Private** (recommandé pour un projet d'équipe)
6. **NE PAS** cocher "Initialize with README" (vous avez déjà un projet)
7. Cliquer sur "Create repository"

### Étape 2 : Initialiser Git Localement
```bash
# Dans le dossier de votre projet (C:\xampp\htdocs\projet)
cd C:\xampp\htdocs\projet

# Initialiser Git
git init

# Ajouter tous les fichiers
git add .

# Premier commit
git commit -m "Initial commit - Structure du projet NextGen"

# Ajouter le remote GitHub (remplacez par votre URL)
git remote add origin https://github.com/votre-username/nextgen.git

# Pousser vers GitHub
git branch -M main
git push -u origin main
```

---

## 2. Création des Branches

### Créer toutes les branches pour les modules

```bash
# Se placer sur main
git checkout main

# Créer les branches pour chaque module
git checkout -b feature/users-management
git checkout -b feature/products-management
git checkout -b feature/orders-management
git checkout -b feature/donations-management
git checkout -b feature/partners-management
git checkout -b feature/returns-management

# Pousser toutes les branches vers GitHub
git push -u origin feature/users-management
git push -u origin feature/products-management
git push -u origin feature/orders-management
git push -u origin feature/donations-management
git push -u origin feature/partners-management
git push -u origin feature/returns-management

# Revenir sur main
git checkout main
```

### Assignation des Branches

| Branche | Responsable | Fichiers à Modifier |
|---------|------------|---------------------|
| `feature/users-management` | **Ahlem Zouari** | `frontoffice/login.html`, `frontoffice/account.html`, `backoffice/users.html` |
| `feature/products-management` | **Boulares DhiaEddine** | `frontoffice/catalog.html`, `backoffice/games.html` |
| `feature/orders-management` | **Ouerghi Rayen** | `frontoffice/cart.html`, `frontoffice/checkout.html`, `backoffice/orders.html` |
| `feature/donations-management` | **Ayoub Bouzidi** | `frontoffice/donations.html`, `backoffice/donations.html` |
| `feature/partners-management` | **Sridi Mariem** | `backoffice/partners.html`, `frontoffice/donations.html` (section partenaires) |
| `feature/returns-management` | **Dhorbani Louay** | `frontoffice/returns.html`, `backoffice/returns.html` |

---

## 3. Workflow pour Chaque Membre

### Pour chaque développeur :

#### A. Première fois - Cloner le projet
```bash
# Cloner le repository
git clone https://github.com/votre-username/nextgen.git
cd nextgen

# Voir toutes les branches disponibles
git branch -a

# Se placer sur votre branche
git checkout feature/votre-module
```

#### B. Chaque jour - Travailler sur votre module
```bash
# 1. Récupérer les dernières modifications de main
git checkout main
git pull origin main

# 2. Mettre à jour votre branche avec main
git checkout feature/votre-module
git merge main  # ou git rebase main

# 3. Travailler sur vos fichiers...

# 4. Voir les changements
git status
git diff

# 5. Ajouter vos modifications
git add .

# 6. Commiter avec un message clair
git commit -m "Ajout fonctionnalité X pour le module Y"

# 7. Pousser vers GitHub
git push origin feature/votre-module
```

#### C. Exemple concret pour Ahlem (Gestion Utilisateurs)
```bash
# Se placer sur sa branche
git checkout feature/users-management

# Modifier les fichiers
# - frontoffice/login.html → login.php
# - frontoffice/account.html → account.php
# - backoffice/users.html → users.php

# Commiter
git add frontoffice/login.php frontoffice/account.php backoffice/users.php
git commit -m "Implémentation PHP pour la gestion des utilisateurs"

# Pousser
git push origin feature/users-management
```

---

## 4. Fusion des Branches

### Option A : Via Pull Request (Recommandé)

1. **Sur GitHub** :
   - Aller sur votre repository
   - Cliquer sur "Pull requests"
   - Cliquer sur "New pull request"
   - Sélectionner votre branche (`feature/votre-module`) → `main`
   - Ajouter une description
   - Demander une review (optionnel)
   - Cliquer sur "Create pull request"

2. **Fusionner** :
   - Un autre membre peut reviewer
   - Cliquer sur "Merge pull request"
   - Confirmer la fusion

### Option B : Via ligne de commande (si vous êtes admin)

```bash
# Se placer sur main
git checkout main

# Mettre à jour main
git pull origin main

# Fusionner la branche
git merge feature/users-management

# Résoudre les conflits si nécessaire (voir section 5)

# Pousser vers GitHub
git push origin main
```

---

## 5. Résolution de Conflits

### Si vous avez des conflits lors d'un merge :

```bash
# Git vous indiquera les fichiers en conflit
# Ouvrir les fichiers et chercher les marqueurs :
<<<<<<< HEAD
Votre code actuel
=======
Code de la branche fusionnée
>>>>>>> feature/autre-branche

# Modifier pour garder le bon code
# Supprimer les marqueurs <<<<<<<, =======, >>>>>>>

# Après résolution :
git add fichier-resolu.php
git commit -m "Résolution conflit dans fichier-resolu.php"
```

---

## 6. Commandes Utiles

### Voir l'historique
```bash
git log --oneline --graph --all
```

### Voir les différences
```bash
git diff main..feature/votre-module
```

### Annuler des modifications non commitées
```bash
git checkout -- fichier.php
```

### Voir sur quelle branche vous êtes
```bash
git branch
```

### Créer une nouvelle branche depuis main
```bash
git checkout main
git pull origin main
git checkout -b feature/nouvelle-fonctionnalite
```

---

## 7. Bonnes Pratiques

✅ **À FAIRE** :
- Commiter souvent avec des messages clairs
- Toujours pull avant de push
- Travailler sur votre branche, jamais sur main
- Créer des Pull Requests pour fusionner
- Tester avant de push

❌ **À ÉVITER** :
- Commiter directement sur main
- Pousser du code non testé
- Ignorer les conflits
- Messages de commit vagues ("fix", "update")

---

## 8. Structure Recommandée pour les Fichiers PHP

Quand vous convertirez vos HTML en PHP, organisez ainsi :

```
projet/
├── frontoffice/
│   ├── users/              # Module Utilisateurs (Ahlem)
│   │   ├── login.php
│   │   └── account.php
│   ├── products/           # Module Produits (Boulares)
│   │   └── catalog.php
│   ├── orders/             # Module Achats (Rayen)
│   │   ├── cart.php
│   │   └── checkout.php
│   └── ...
├── backoffice/
│   ├── users/              # Module Utilisateurs (Ahlem)
│   │   └── users.php
│   └── ...
└── includes/               # Fichiers partagés
    ├── config.php
    ├── database.php
    └── functions.php
```

---

## 🆘 Besoin d'Aide ?

Si vous avez des problèmes :
1. Vérifier `git status`
2. Lire les messages d'erreur Git
3. Chercher sur [GitHub Docs](https://docs.github.com)
4. Créer une issue sur GitHub

---

**Bon développement ! 🚀**

