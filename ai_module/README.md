# 🧠 Système d'IA Local pour Filtrage de Réclamations

## 📋 Vue d'ensemble

Ce système d'intelligence artificielle **100% local (offline)** filtre automatiquement les messages de réclamation pour :

✅ Détecter les messages valides et compréhensibles
❌ Rejeter les messages sans sens ou incohérents
❌ Filtrer les insultes et propos impolis (français, dialecte tunisien, arabe)
❌ Bloquer les répétitions absurdes
❌ Empêcher les spams
❌ Demander une réécriture pour les messages peu clairs

## 🏗️ Architecture du système

Le système repose sur **3 modèles IA complémentaires**, tous exécutés localement :

### 1️⃣ Classifieur Naive Bayes
- **Rôle** : Classifie les messages en catégories
- **Catégories** :
  - `valid` : Réclamation valide
  - `empty` : Message vide ou sans sens
  - `repetitive` : Message avec répétitions
  - `short` : Message trop court
- **Détecte** : Les mots inappropriés (liste personnalisable)
- **Poids** : 50% du score final

### 2️⃣ Modèle de Markov
- **Rôle** : Vérifie la structure naturelle de la phrase
- **Détecte** :
  - Répétitions excessives de caractères (hhhh, aaaa)
  - Séquences non naturelles
  - Incohérences grammaticales
- **Utilise** : Chaînes de Markov pour analyser les transitions mot-à-mot
- **Poids** : 30% du score final

### 3️⃣ Word2Vec Simplifié
- **Rôle** : Analyse sémantique et cohérence contextuelle
- **Détecte** :
  - La pertinence du message par rapport au contexte "réclamation"
  - La présence sémantique d'insultes
  - La cohérence des mots utilisés
- **Utilise** : Embeddings simples et similarité cosinus
- **Poids** : 20% du score final

## 📊 Fusion des modèles

Le score final est calculé ainsi :

```
Score Final = (Naive Bayes × 50%) + (Markov × 30%) + (Word2Vec × 20%)
```

**Décisions** :
- **Score ≥ 0.70** : ✅ Message accepté
- **0.40 ≤ Score < 0.70** : ⚠️ Demander une réécriture
- **Score < 0.40** : ❌ Message rejeté

## 📁 Structure des fichiers

```
ai_module/
├── analyse_reclamation.py      # Script principal (point d'entrée Python)
├── models/
│   ├── __init__.py
│   ├── naive_bayes.py          # Classifieur Naive Bayes
│   ├── markov_model.py         # Modèle de Markov
│   └── word2vec_simple.py      # Word2Vec simplifié
└── data/
    ├── badwords_list.json      # Liste des mots inappropriés
    ├── reclamations_samples.json # Exemples d'entraînement
    └── word_embeddings.json    # Vecteurs de mots
```

## 🔧 Installation et utilisation

### Prérequis
- Python 3.7+ installé et accessible en ligne de commande
- aucune dépendance externe (100% bibliothèque standard Python)

### Vérification de Python
```bash
python3 --version
# ou
python --version
```

### Intégration PHP
Le système est automatiquement intégré dans `ReclamationController.php`.

**Utilisation** :
```php
$reclamationController = new ReclamationController();

// Analyser un message avec l'IA
$analysis = $reclamationController->analyzeMessageWithAI($message);

// Créer une réclamation (l'IA est appelée automatiquement)
$result = $reclamationController->create($reclamation);
```

**Réponse de l'API** :
```json
{
  "valid": true,
  "reason": "Message valide et approprié",
  "score": 0.856,
  "details": {
    "bayes_score": 0.75,
    "bayes_class": "valid",
    "has_badwords": false,
    "markov_score": 0.89,
    "markov_naturalness": 0.89,
    "word2vec_score": 0.82,
    "semantic_coherence": 0.85,
    "insult_level": 0.0,
    "context_relevance": 0.88,
    "text_length": 145,
    "word_count": 28
  }
}
```

## 🧪 Test du système

### Test manuel via PHP
```php
$controller = new ReclamationController();

// Exemple 1: Message valide
$result = $controller->analyzeMessageWithAI(
    "J'ai commandé un jeu mais j'ai reçu un produit différent. "
);
var_dump($result); // valid: true

// Exemple 2: Message avec insulte
$result = $controller->analyzeMessageWithAI(
    "Vous êtes des idiots, j'ai pas reçu ma commande!"
);
var_dump($result); // valid: false

// Exemple 3: Message sans sens
$result = $controller->analyzeMessageWithAI("hhhhhh aaaa xxxx");
var_dump($result); // valid: false

// Exemple 4: Message peu clair
$result = $controller->analyzeMessageWithAI("problème");
var_dump($result); // valid: false (trop court)
```

### Test via ligne de commande
```bash
python3 ai_module/analyse_reclamation.py "Le jeu n'a pas été livré"
```

## 🎯 Exemples de résultats

### ✅ Message accepté
```
Message: "Le jeu n'a pas été livré dans les délais convenus. 
          Commande #12345 du 01/12/2024."

Score: 0.89 (89%)
Raison: "Message valide et approprié"
```

### ❌ Message rejeté (insulte)
```
Message: "Vous êtes des connards! Je veux mon argent!"

Score: 0.0 (0%)
Raison: "Message contenant des paroles impolis ou offensantes"
```

### ❌ Message rejeté (non-sens)
```
Message: "hhhh kkkk llll xxxx"

Score: 0.05 (5%)
Raison: "Message non compréhensible: Répétition excessive de caractères détectée"
```

### ⚠️ Message demandant réécriture
```
Message: "Bug"

Score: 0.45 (45%)
Raison: "Message peu clair. Veuillez reformuler avec plus de détails."
```

## 🔒 Sécurité et confidentialité

✅ **100% local** → Aucune donnée envoyée à internet
✅ **Pas d'API externe** → Aucune dépendance cloud
✅ **Respect des données privées** → Tout reste sur le serveur
✅ **Pas d'entraînement en ligne** → Aucune synchronisation des données

## 📚 Personalisation

### Ajouter des mots inappropriés
Éditer `ai_module/data/badwords_list.json` :
```json
{
  "insultes_francais": [
    "con", "idiot", "nouveau_mot", ...
  ]
}
```

### Ajouter des exemples d'entraînement
Éditer `ai_module/data/reclamations_samples.json` :
```json
{
  "reclamations_valides": [
    "Message 1...",
    "Message 2...",
    "Vos nouveaux messages..."
  ]
}
```

### Ajuster les embeddings de mots
Éditer `ai_module/data/word_embeddings.json` :
```json
{
  "context_words": {
    "votre_mot": 0.85,
    ...
  }
}
```

## 🚀 Fallback automatique

Si Python n'est pas disponible, le système bascule automatiquement sur une vérification simple en PHP :
- Vérification de mots inappropriés (liste simple)
- Vérification de longueur minimale
- Détection basique de messages vides

**Note** : Le fallback est moins sophistiqué mais maintient la sécurité basique.

## 📊 Performances

- **Temps de traitement** : ~100-500ms par message
- **Mémoire** : ~20-30MB
- **CPU** : Minimal (modèles légers)
- **Pas d'I/O réseau** : Aucun délai de latence

## 🐛 Dépannage

### Python non trouvé
```
Le système bascule automatiquement sur le fallback PHP
Vérifiez: python3 --version
```

### Module JSON invalide
Vérifiez la syntaxe JSON des fichiers dans `ai_module/data/`

### Messages incorrectement rejetés
- Ajoutez des exemples à `reclamations_samples.json`
- Réentraînez les modèles en réappelant le script

## 📞 Support

Pour toute question ou amélioration, consultez les fichiers du module :
- Documentation du code dans chaque fichier
- Logs détaillés disponibles dans les détails d'analyse
- Tests disponibles via la ligne de commande

---

**Version** : 1.0
**Date de création** : Décembre 2024
**Statut** : Production
