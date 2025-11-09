#!/bin/bash
# Script pour créer toutes les branches du projet NextGen
# Usage: ./SETUP_BRANCHES.sh

echo "🌿 Création des branches pour NextGen..."

# Se placer sur main
git checkout main

# Créer les branches pour chaque module
echo "📦 Création de la branche: feature/users-management"
git checkout -b feature/users-management
git push -u origin feature/users-management

echo "📦 Création de la branche: feature/products-management"
git checkout main
git checkout -b feature/products-management
git push -u origin feature/products-management

echo "📦 Création de la branche: feature/orders-management"
git checkout main
git checkout -b feature/orders-management
git push -u origin feature/orders-management

echo "📦 Création de la branche: feature/donations-management"
git checkout main
git checkout -b feature/donations-management
git push -u origin feature/donations-management

echo "📦 Création de la branche: feature/partners-management"
git checkout main
git checkout -b feature/partners-management
git push -u origin feature/partners-management

echo "📦 Création de la branche: feature/returns-management"
git checkout main
git checkout -b feature/returns-management
git push -u origin feature/returns-management

# Revenir sur main
git checkout main

echo "✅ Toutes les branches ont été créées !"
echo ""
echo "📋 Branches disponibles:"
git branch -a

echo ""
echo "👥 Assignation des branches:"
echo "  - feature/users-management → Ahlem Zouari"
echo "  - feature/products-management → Boulares DhiaEddine"
echo "  - feature/orders-management → Ouerghi Rayen"
echo "  - feature/donations-management → Ayoub Bouzidi"
echo "  - feature/partners-management → Sridi Mariem"
echo "  - feature/returns-management → Dhorbani Louay"

