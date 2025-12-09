# 🔌 API PHP du système d'IA

## Guide d'intégration pour développeurs PHP

### Classes et méthodes disponibles

#### ReclamationController

Classe principale pour gérer les réclamations avec validation IA.

##### Méthodes publiques

```php
class ReclamationController {
    
    /**
     * Analyser un message avec le système d'IA
     * 
     * @param string $message Le message à analyser
     * @return array Résultat de l'analyse
     *   - valid (bool|null): true=accepté, false=rejeté, null=réécriture
     *   - reason (string): Raison de la décision
     *   - score (float): Score entre 0.0 et 1.0
     *   - details (array): Détails d'analyse complets
     *   - ai_analysis (array): Résultat complet du système d'IA
     */
    public function analyzeMessageWithAI($message);
    
    /**
     * Créer une nouvelle réclamation
     * 
     * @param Reclamation $reclamation L'objet réclamation
     * @return array Résultat de l'opération
     *   - success (bool): Opération réussie
     *   - message (string): Message de résultat
     *   - id (int|null): ID de la réclamation créée
     *   - ai_score (float|null): Score IA si disponible
     *   - ai_analysis (array|null): Analyse IA complète si rejectée
     *   - needs_rewrite (bool|null): True si réécriture demandée
     */
    public function create($reclamation);
    
    /**
     * Lire toutes les réclamations
     * 
     * @return array Liste de toutes les réclamations avec jointures
     */
    public function readAll();
    
    /**
     * Lire une réclamation par ID
     * 
     * @param int $id ID de la réclamation
     * @return array|object Détails de la réclamation
     */
    public function readById($id);
    
    /**
     * Lire les réclamations d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return array Liste des réclamations de l'utilisateur
     */
    public function readByUserId($userId);
    
    /**
     * Mettre à jour une réclamation
     * 
     * @param Reclamation $reclamation L'objet réclamation mise à jour
     * @return array Résultat de l'opération
     *   - success (bool): Opération réussie
     *   - message (string): Message de résultat
     */
    public function update($reclamation);
    
    /**
     * Supprimer une réclamation
     * 
     * @param int $id ID de la réclamation
     * @return array Résultat de l'opération
     *   - success (bool): Opération réussie
     *   - message (string): Message de résultat
     */
    public function delete($id);
    
    /**
     * Mettre à jour le statut
     * 
     * @param int $id ID de la réclamation
     * @param string $nouveauStatut Nouveau statut
     * @return array Résultat de l'opération
     */
    public function updateStatut($id, $nouveauStatut);
    
    /**
     * Lire les réclamations par statut
     * 
     * @param string $statut Statut à rechercher
     * @return array Liste des réclamations avec ce statut
     */
    public function readByStatut($statut);
}
```

### Exemples d'utilisation

#### Exemple 1 : Analyser un message seul

```php
<?php
require_once 'controller/ReclamationController.php';

$controller = new ReclamationController();

// Analyser un message
$analysis = $controller->analyzeMessageWithAI(
    "Le jeu n'a pas été livré dans les délais"
);

// Vérifier le résultat
if ($analysis['valid'] === true) {
    echo "Message accepté avec score: " . round($analysis['score'] * 100) . "%";
} elseif ($analysis['valid'] === false) {
    echo "Message rejeté: " . $analysis['reason'];
} else {
    echo "Réécriture demandée: " . $analysis['reason'];
}

// Accéder aux détails
echo "Score Naive Bayes: " . $analysis['details']['bayes_score'];
echo "Score Markov: " . $analysis['details']['markov_score'];
echo "Score Word2Vec: " . $analysis['details']['word2vec_score'];
?>
```

#### Exemple 2 : Créer une réclamation avec validation IA

```php
<?php
require_once 'controller/ReclamationController.php';
require_once 'models/Reclamation.php';

$controller = new ReclamationController();

// Créer une réclamation
$reclamation = new Reclamation();
$reclamation->setIdUser(1)
           ->setIdJeu(5)
           ->setType('Retour')
           ->setDescription("Le jeu est défectueux")
           ->setDateReclamation(date('Y-m-d H:i:s'))
           ->setStatut('En attente');

// Créer (l'IA valide automatiquement)
$result = $controller->create($reclamation);

if ($result['success']) {
    echo "Réclamation créée avec ID: " . $result['id'];
    if (!empty($result['ai_score'])) {
        echo " (Score IA: " . round($result['ai_score'] * 100) . "%)";
    }
} else {
    // L'IA a rejeté le message
    if (!empty($result['ai_analysis'])) {
        $analysis = $result['ai_analysis'];
        echo "Erreur: " . $result['message'];
        echo "\nScore IA: " . round($analysis['score'] * 100) . "%";
    }
    
    // Si réécriture demandée
    if (!empty($result['needs_rewrite'])) {
        echo "Veuillez reformuler votre message.";
    }
}
?>
```

#### Exemple 3 : Gestion avancée des réponses

```php
<?php
require_once 'controller/ReclamationController.php';
require_once 'models/Reclamation.php';

$controller = new ReclamationController();

$reclamation = new Reclamation();
$reclamation->setIdUser($_SESSION['user']['id'])
           ->setType($_POST['type'])
           ->setDescription($_POST['description'])
           ->setDateReclamation(date('Y-m-d H:i:s'))
           ->setStatut('En attente');

$result = $controller->create($reclamation);

if ($result['success']) {
    // Succès
    $_SESSION['success'] = "Votre réclamation a été envoyée avec succès!";
    $score = round($result['ai_score'] * 100);
    $_SESSION['ai_score'] = $score;
    
} elseif (!empty($result['needs_rewrite'])) {
    // Réécriture demandée
    $_SESSION['warning'] = $result['message'];
    $_SESSION['ai_analysis'] = $result['ai_analysis'];
    
} else {
    // Rejet
    $_SESSION['error'] = $result['message'];
    
    if (!empty($result['ai_analysis'])) {
        $analysis = $result['ai_analysis'];
        
        // Stocker les détails pour le frontend
        $_SESSION['ai_details'] = [
            'score' => round($analysis['score'] * 100),
            'reason' => $analysis['reason'],
            'bayes_score' => $analysis['details']['bayes_score'],
            'markov_score' => $analysis['details']['markov_score'],
            'word2vec_score' => $analysis['details']['word2vec_score']
        ];
    }
}

// Redirection appropriée
header('Location: reclamation.php');
exit;
?>
```

#### Exemple 4 : Afficher les détails d'analyse

```php
<?php
$analysis = $controller->analyzeMessageWithAI($message);

echo "📊 Analyse détaillée:\n";
echo "Score global: " . round($analysis['score'] * 100) . "%\n";
echo "\nDétails par modèle:\n";

$details = $analysis['details'];
echo "- Naive Bayes: " . round($details['bayes_score'] * 100) . "% ";
echo "({$details['bayes_class']})\n";

echo "- Markov: " . round($details['markov_score'] * 100) . "% ";
echo "(naturalité: " . round($details['markov_naturalness'] * 100) . "%)\n";

echo "- Word2Vec: " . round($details['word2vec_score'] * 100) . "%\n";

echo "\nIndicateurs:\n";
if ($details['has_badwords']) {
    echo "⚠️  Mots inappropriés détectés\n";
}
echo "- Niveau d'insulte: " . round($details['insult_level'] * 100) . "%\n";
echo "- Pertinence contextuelle: " . round($details['context_relevance'] * 100) . "%\n";
echo "- Cohérence sémantique: " . round($details['semantic_coherence'] * 100) . "%\n";

echo "\nTexte:\n";
echo "- Longueur: {$details['text_length']} caractères\n";
echo "- Nombre de mots: {$details['word_count']}\n";
?>
```

#### Exemple 5 : Implémenter un callback personnalisé

```php
<?php
class ReclamationHandler {
    private $controller;
    
    public function __construct() {
        $this->controller = new ReclamationController();
    }
    
    public function handleSubmission($data) {
        $message = $data['description'];
        
        // Analyser
        $analysis = $this->controller->analyzeMessageWithAI($message);
        
        // Appeler des callbacks selon le résultat
        if ($analysis['valid'] === true) {
            return $this->onMessageAccepted($analysis);
        } elseif ($analysis['valid'] === false) {
            return $this->onMessageRejected($analysis);
        } else {
            return $this->onMessageRewriteNeeded($analysis);
        }
    }
    
    private function onMessageAccepted($analysis) {
        // Créer la réclamation
        // Logger le succès
        // Envoyer un email
        return ['status' => 'created'];
    }
    
    private function onMessageRejected($analysis) {
        // Logger le rejet
        // Retourner le message d'erreur
        return [
            'status' => 'rejected',
            'message' => $analysis['reason'],
            'score' => $analysis['score']
        ];
    }
    
    private function onMessageRewriteNeeded($analysis) {
        // Demander une réécriture
        // Suggérer des améliorations
        return [
            'status' => 'rewrite_needed',
            'message' => $analysis['reason'],
            'suggestions' => $this->generateSuggestions($analysis)
        ];
    }
    
    private function generateSuggestions($analysis) {
        $suggestions = [];
        
        if ($analysis['details']['bayes_score'] < 0.5) {
            $suggestions[] = "Le message n'est pas assez clair";
        }
        
        if ($analysis['details']['insult_level'] > 0) {
            $suggestions[] = "Évitez le langage offensant";
        }
        
        if ($analysis['details']['word_count'] < 5) {
            $suggestions[] = "Fournissez plus de détails";
        }
        
        return $suggestions;
    }
}

// Utilisation
$handler = new ReclamationHandler();
$response = $handler->handleSubmission($_POST);
?>
```

### Format des réponses

#### Réponse d'analyse réussie

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
    "markov_nonsense": false,
    "word2vec_score": 0.82,
    "semantic_coherence": 0.85,
    "insult_level": 0.0,
    "context_relevance": 0.88,
    "text_length": 145,
    "word_count": 28
  }
}
```

#### Réponse de rejet

```json
{
  "valid": false,
  "reason": "Message contenant des paroles impolis ou offensantes",
  "score": 0.0,
  "details": {
    "has_badwords": true,
    "insult_level": 0.95,
    "text_length": 50,
    "word_count": 8
  }
}
```

#### Réponse de réécriture demandée

```json
{
  "valid": null,
  "reason": "Message peu clair. Veuillez reformuler avec plus de détails.",
  "score": 0.52,
  "details": {
    "bayes_score": 0.45,
    "word_count": 3,
    "text_length": 15
  }
}
```

### Points d'intégration dans votre code

1. **Formulaire de création** : `reclamation.php`
2. **Contrôleur de création** : `ReclamationController::create()`
3. **Modèle de données** : `Reclamation.php` (aucune modification nécessaire)
4. **Base de données** : Aucune modification nécessaire

### Erreurs possibles et gestion

```php
<?php
try {
    $analysis = $controller->analyzeMessageWithAI($message);
    
    // Vérifier les erreurs
    if (empty($analysis)) {
        throw new Exception("Réponse vide du système d'IA");
    }
    
    if (!isset($analysis['valid'])) {
        throw new Exception("Format de réponse invalide");
    }
    
    // Traiter normalement
    
} catch (Exception $e) {
    // Logger l'erreur
    error_log("Erreur IA: " . $e->getMessage());
    
    // Utiliser le fallback PHP
    $analysis = $controller->analyzeMessagePHP($message);
}
?>
```

### Configuration et optimisation

```php
<?php
// Pour désactiver temporairement l'IA
$controller->aiAnalyzerAvailable = false;
// Basculera automatiquement au fallback PHP

// Pour forcer Python3 au lieu de python
$controller->pythonPath = 'python3';

// Pour ignorer les logs
// Éditer directement dans le code ou la config
?>
```

---

**Version** : 1.0
**Dernière mise à jour** : Décembre 2024
**Compatibilité** : PHP 7.4+
