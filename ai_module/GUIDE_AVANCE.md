# 📚 Guide d'utilisation avancée du système d'IA

## Installation et dépannage

### Vérification de l'installation

1. **Vérifier que Python 3 est installé** :
```bash
python3 --version
# ou sur Windows
python --version
```

2. **Vérifier la structure des fichiers** :
```
ai_module/
├── analyse_reclamation.py
├── models/
│   ├── naive_bayes.py
│   ├── markov_model.py
│   └── word2vec_simple.py
└── data/
    ├── badwords_list.json
    ├── reclamations_samples.json
    └── word_embeddings.json
```

3. **Tester le module Python directement** :
```bash
cd ai_module
python3 test_ai.py
```

### Problèmes courants

**Problème** : "Python not found"
- **Solution** : Ajouter Python au PATH système ou utiliser le chemin absolu

**Problème** : "JSON decode error"
- **Solution** : Vérifier la syntaxe JSON dans les fichiers data/

**Problème** : "Permission denied"
- **Solution** : Sur Linux/Mac, rendre le fichier exécutable : `chmod +x ai_module/analyse_reclamation.py`

---

## Cas d'usage avancés

### 1. Entraînement personnalisé

Pour améliorer la détection sur votre domaine spécifique :

#### Étape 1 : Ajouter des exemples
Éditer `ai_module/data/reclamations_samples.json` :

```json
{
  "reclamations_valides": [
    "Je n'ai pas reçu ma commande depuis 2 semaines",
    "Le jeu n'est pas compatible avec mon système",
    "La facture est incorrecte, j'ai été facturisé deux fois",
    "VOS_PROPRES_EXEMPLES_ICI"
  ]
}
```

#### Étape 2 : Ajouter des mots contextuels
Éditer `ai_module/data/word_embeddings.json` :

```json
{
  "context_words": {
    "votre_mot": 0.95,
    "autre_mot": 0.8
  }
}
```

#### Étape 3 : Tester
```bash
python3 ai_module/analyse_reclamation.py "Votre message de test"
```

### 2. Intégration personnalisée en PHP

```php
<?php
require_once 'controller/ReclamationController.php';

$controller = new ReclamationController();

// Analyser un message
$analysis = $controller->analyzeMessageWithAI("Mon message");

// Accéder aux résultats
if ($analysis['valid'] === true) {
    echo "Message accepté avec score: " . $analysis['score'];
} elseif ($analysis['valid'] === null) {
    echo "Réécriture demandée: " . $analysis['reason'];
} else {
    echo "Message rejeté: " . $analysis['reason'];
}

// Accéder aux détails
foreach ($analysis['details'] as $key => $value) {
    echo "$key: $value\n";
}
?>
```

### 3. Statistiques et monitoring

Ajouter du logging pour suivre les messages rejetés :

```php
<?php
// Dans ReclamationController.php, ajouter :

private function logAnalysis($message, $result) {
    $log_file = __DIR__ . '/../logs/ai_analysis.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = json_encode([
        'timestamp' => $timestamp,
        'message' => substr($message, 0, 100),
        'valid' => $result['valid'],
        'score' => $result['score'],
        'reason' => $result['reason']
    ]);
    
    error_log($log_entry . "\n", 3, $log_file);
}
?>
```

### 4. Seuils personnalisés

Pour modifier les seuils de décision, éditer `analyse_reclamation.py` :

```python
# Lignes ~140-150
if final_score >= 0.70:  # Changer ce seuil
    decision = True
elif final_score < 0.40:  # Changer ce seuil
    decision = False
else:
    decision = None
```

### 5. Ajouter une catégorie personnalisée

Pour classifier les messages en catégories supplémentaires :

```python
# Dans naive_bayes.py, ajouter une nouvelle catégorie :

categories = {
    'valid': self.samples.get('reclamations_valides', []),
    'urgent': self.samples.get('reclamations_urgentes', []),  # NOUVELLE
    'empty': self.samples.get('messages_vides_sans_sens', []),
    # ...
}
```

Puis ajouter les exemples dans `reclamations_samples.json` :

```json
{
  "reclamations_urgentes": [
    "Mon compte a été hacké d'urgence!",
    "J'ai un problème critique avec ma commande"
  ]
}
```

---

## API complète

### ReclamationController::analyzeMessageWithAI()

```php
public function analyzeMessageWithAI($message): array
```

**Paramètres** :
- `$message` (string) : Le message à analyser

**Retour** :
```php
[
    'valid' => bool|null,  // true=accepté, false=rejeté, null=réécriture
    'reason' => string,    // Raison de la décision
    'score' => float,      // Score entre 0.0 et 1.0
    'details' => [         // Détails d'analyse
        'bayes_score' => float,
        'bayes_class' => string,
        'markov_score' => float,
        'word2vec_score' => float,
        'insult_level' => float,
        'context_relevance' => float,
        // ...
    ]
]
```

### analyzeMessagePHP() (Fallback)

Utilisé automatiquement si Python n'est pas disponible. Version simplifiée avec vérification basique.

---

## Performance et optimisation

### Temps de réponse

- **Avec Python** : 100-500ms par message
- **Fallback PHP** : <10ms par message

### Optimisation

1. **Cache les résultats** : Pour les messages identiques
2. **Batch processing** : Analyser plusieurs messages en parallèle
3. **Lazy loading** : Charger les modèles une seule fois

### Exemple de cache :

```php
<?php
private $analysisCache = [];

public function analyzeMessageWithAI($message) {
    $hash = md5($message);
    
    // Vérifier le cache
    if (isset($this->analysisCache[$hash])) {
        return $this->analysisCache[$hash];
    }
    
    // Analyser
    $result = $this->callPythonAnalyzer($message);
    
    // Cacher
    $this->analysisCache[$hash] = $result;
    
    return $result;
}
?>
```

---

## Métriques et statistiques

### Collecter les statistiques

```python
# Dans test_ai.py ou analyse_reclamation.py
class AnalysisStatistics:
    def __init__(self):
        self.total_messages = 0
        self.accepted = 0
        self.rejected = 0
        self.rewrite_needed = 0
        self.average_score = 0.0
    
    def update(self, result):
        self.total_messages += 1
        if result['valid'] is True:
            self.accepted += 1
        elif result['valid'] is False:
            self.rejected += 1
        else:
            self.rewrite_needed += 1
        self.average_score = (
            self.average_score * (self.total_messages - 1) + 
            result['score']
        ) / self.total_messages
    
    def report(self):
        return {
            'total': self.total_messages,
            'accepted': self.accepted,
            'rejected': self.rejected,
            'rewrite_needed': self.rewrite_needed,
            'acceptance_rate': self.accepted / self.total_messages if self.total_messages > 0 else 0,
            'average_score': self.average_score
        }
```

---

## Maintenance

### Mise à jour des badwords

```bash
# Ajouter de nouveaux mots inappropriés
# Éditer ai_module/data/badwords_list.json
```

### Réentraînement du modèle

```python
# Les modèles se réentraînent automatiquement
# à chaque démarrage du ReclamationAnalyzer

# Pour forcer un réentraînement :
analyzer = ReclamationAnalyzer()
# Puis recréer l'instance
```

### Vérification de santé

```php
<?php
// Créer un endpoint de santé
public function healthCheck() {
    $analysis = $this->analyzeMessageWithAI("Test message");
    return [
        'status' => $analysis['valid'] !== null ? 'healthy' : 'degraded',
        'ai_available' => $this->aiAnalyzerAvailable,
        'python_path' => $this->pythonPath
    ];
}
?>
```

---

## Questions fréquentes

**Q: Pourquoi mon message valide est-il rejeté?**
A: Ajoutez des exemples similaires à `reclamations_samples.json` et testez avec `test_ai.py`.

**Q: Comment puis-je tester le système offline?**
A: Exécutez `python3 ai_module/test_ai.py` sans besoin de serveur web.

**Q: Y a-t-il des risques de sécurité?**
A: Non, le système est 100% local et n'envoie aucune donnée à internet.

**Q: Puis-je désactiver la validation IA?**
A: Oui, dans `ReclamationController::__construct()`, définissez `$this->aiAnalyzerAvailable = false`.

**Q: Comment ajouter une langue?**
A: Ajoutez les badwords dans `badwords_list.json` et les exemples dans `reclamations_samples.json`.

---

## Contribution

Pour améliorer le système :

1. Testez avec `test_ai.py`
2. Ajoutez vos exemples aux datasets
3. Créez un rapport de tout cas d'erreur
4. Suggérez des améliorations

---

**Version** : 1.0
**Dernière mise à jour** : Décembre 2024
