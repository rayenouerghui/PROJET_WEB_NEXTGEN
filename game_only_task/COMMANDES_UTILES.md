# 🛠️ Commandes utiles et référence rapide

## ⚡ Commandes essentielles

### Vérification de l'installation
```bash
python3 ai_module/verify_installation.py
```
Vérifie que tous les fichiers sont présents et valides.

### Test rapide (< 2 secondes)
```bash
python3 ai_module/quick_test.py
```
Lance 4 tests rapides du système.

### Suite complète de tests (5-10 secondes)
```bash
python3 ai_module/test_ai.py
```
Lance 10+ cas de test pour validation complète.

### Analyser un message unique
```bash
python3 ai_module/analyse_reclamation.py "Votre message ici"
```
Analyse un message et retourne un JSON avec résultats.

### Vérifier la version de Python
```bash
python3 --version
```
Doit être Python 3.7 ou supérieur.

---

## 📂 Navigation rapide

### Accéder au dossier IA
```bash
cd ai_module
```

### Lister tous les fichiers
```bash
ls -la ai_module/
```

### Afficher la documentation principale
```bash
cat ai_module/README.md
```

### Afficher le démarrage rapide
```bash
cat DEMARRAGE_RAPIDE.md
```

---

## 🧪 Cas de test manuels

### Test 1: Message valide
```bash
python3 ai_module/analyse_reclamation.py "Le jeu n'a pas été livré dans les délais convenus. Commande #12345 du 01/12/2024."
```
Résultat attendu: Score 0.8+, valid: true

### Test 2: Message avec insulte
```bash
python3 ai_module/analyse_reclamation.py "Vous êtes des idiots! Je veux mon argent!"
```
Résultat attendu: Score 0.0, valid: false, "Paroles impolis"

### Test 3: Message sans sens
```bash
python3 ai_module/analyse_reclamation.py "hhhhhh aaaaa xxxxx"
```
Résultat attendu: Score < 0.1, valid: false, "Charabia"

### Test 4: Message trop court
```bash
python3 ai_module/analyse_reclamation.py "ok"
```
Résultat attendu: Score 0.1, valid: false, "Trop court"

### Test 5: Message peu clair
```bash
python3 ai_module/analyse_reclamation.py "bug"
```
Résultat attendu: Score 0.4-0.6, valid: null, "Réécriture"

---

## 🔍 Débogage

### Afficher les détails complets d'un message
```bash
python3 ai_module/analyse_reclamation.py "Votre message" | python3 -m json.tool
```
Affiche le JSON formaté avec tous les détails.

### Vérifier si Python est dans le PATH
```bash
which python3
# ou
where python3
```

### Vérifier les dépendances
```bash
python3 -c "import json; import math; import re; print('OK')"
```
Si "OK" s'affiche, toutes les dépendances sont OK.

---

## 📝 Fichiers à éditer pour personnaliser

### Ajouter des mots inappropriés
```bash
vim ai_module/data/badwords_list.json
```

### Ajouter des exemples d'entraînement
```bash
vim ai_module/data/reclamations_samples.json
```

### Modifier les embeddings de mots
```bash
vim ai_module/data/word_embeddings.json
```

### Changer les seuils de décision
```bash
vim ai_module/analyse_reclamation.py
# Lignes ~140-150
```

---

## 📊 Visualiser les résultats

### Analyser avec output JSON formaté
```bash
python3 ai_module/analyse_reclamation.py "Message" | python3 -m json.tool
```

### Compter les mots inappropriés
```bash
python3 -c "import json; d = json.load(open('ai_module/data/badwords_list.json')); print(f\"Total: {sum(len(v) for v in d.values())}\")"
```

### Compter les exemples d'entraînement
```bash
python3 -c "import json; d = json.load(open('ai_module/data/reclamations_samples.json')); print(f\"Total: {sum(len(v) for v in d.values())}\")"
```

---

## 🚀 Déploiement

### Installation Windows
```batch
setup_ai.bat
```

### Installation Linux/Mac
```bash
bash setup_ai.sh
```

### Installation manuelle
```bash
# 1. Vérifier Python
python3 --version

# 2. Tester
python3 ai_module/quick_test.py

# 3. Vérifier
python3 ai_module/verify_installation.py
```

---

## 📚 Commandes de documentation

### Lire le START_HERE
```bash
cat START_HERE.md
```

### Lire le guide complet
```bash
cat ai_module/README.md
```

### Lire le guide avancé
```bash
cat ai_module/GUIDE_AVANCE.md
```

### Lire l'API PHP
```bash
cat PHP_API.md
```

### Afficher l'index documentation
```bash
cat INDEX_DOCUMENTATION.md
```

---

## 🔧 Maintenance

### Réinitialiser les modèles
```bash
# Supprimer les fichiers (optionnel, ils se régénèrent)
# Les modèles se réentraînent au démarrage suivant
python3 ai_module/quick_test.py
```

### Vérifier la santé du système
```bash
python3 ai_module/verify_installation.py
```

### Voir tous les tests disponibles
```bash
ls ai_module/test*.py ai_module/*_test.py 2>/dev/null
```

---

## 💾 Sauvegarde et restauration

### Sauvegarder les configurations
```bash
cp -r ai_module/data/ ai_module/data.backup/
cp ai_module/config.txt ai_module/config.backup.txt
```

### Restaurer les configurations
```bash
rm -r ai_module/data/
cp -r ai_module/data.backup/ ai_module/data/
```

---

## 📊 Statistiques et monitoring

### Compter les lignes de code Python
```bash
find ai_module -name "*.py" | xargs wc -l | tail -1
```

### Compter les lignes de documentation
```bash
find . -name "*.md" | xargs wc -l | tail -1
```

### Taille totale du module IA
```bash
du -sh ai_module/
```

### Lister tous les fichiers créés
```bash
ls -lhR ai_module/
```

---

## 🐛 Troubleshooting

### Python non trouvé
```bash
# Windows
python --version

# Linux/Mac
python3 --version

# Si absent, installer depuis https://www.python.org/downloads/
```

### JSON invalide
```bash
# Vérifier la syntaxe
python3 -m json.tool ai_module/data/badwords_list.json
python3 -m json.tool ai_module/data/reclamations_samples.json
python3 -m json.tool ai_module/data/word_embeddings.json
```

### Erreur d'import
```bash
# Vérifier que tous les fichiers sont présents
ls ai_module/models/
ls ai_module/data/
```

### Permission denied (Linux/Mac)
```bash
chmod +x ai_module/analyse_reclamation.py
chmod +x ai_module/test_ai.py
chmod +x ai_module/quick_test.py
```

---

## 🔗 Fichiers connexes

### ReclamationController.php
```bash
cat controller/ReclamationController.php | grep "analyzeMessage"
```
Voir les méthodes d'intégration.

### reclamation.php
```bash
grep -n "ai_" view/frontoffice/reclamation.php
```
Voir les modifications UI.

---

## ✨ Commandes utiles supplémentaires

### Reformater du code Python
```bash
python3 -m py_compile ai_module/analyse_reclamation.py
```

### Vérifier la syntaxe Python sans exécuter
```bash
python3 -m py_compile ai_module/models/*.py
```

### Afficher les versions des modules (si installés)
```bash
python3 -c "import sys; print(sys.version)"
```

---

## 📞 Support

Pour toute question sur les commandes:
```bash
# Lire la documentation
cat INDEX_DOCUMENTATION.md

# Ou regarder les fichiers
ls -la
ls ai_module/
```

---

**Version 1.0 - Décembre 2024**
