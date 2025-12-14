<?php
// Fichier : app/Controllers/AISummaryController.php

class AISummaryController {
    private $geminiApiKey;
    private $modelName;

    public function __construct() {
        // OBTENIR VOTRE CLE API : https://aistudio.google.com/app/apikey
        $this->geminiApiKey = "AIzaSyAbTgcGElLmBnVFamGZxPHvMoKCJUB2RLQ";
        $this->modelName = "gemini-1.5-flash"; // ✅ Changé en 1.5-flash (plus stable)
    }

    /**
     * Génère un résumé IA avec Gemini
     */
    public function generateSummary($articleText) {
        $text = $this->preprocessText($articleText);

        try {
            // 1. Essayer Gemini API
            $summary = $this->callGeminiAPI($text);

            if ($summary) {
                return [
                    'success' => true,
                    'summary' => $summary,
                    'source' => 'gemini'
                ];
            }

            // 2. Fallback : algorithme local
            return [
                'success' => true,
                'summary' => $this->localSummarizer($text),
                'source' => 'local'
            ];

        } catch (Exception $e) {
            // 3. En cas d'erreur, utiliser le fallback
            error_log("Erreur AI Summary: " . $e->getMessage());
            return [
                'success' => true,
                'summary' => $this->localSummarizer($text),
                'source' => 'fallback',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Appel à l'API Google Gemini
     */
    private function callGeminiAPI($text) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->modelName}:generateContent?key={$this->geminiApiKey}";

        // Préparer les données
        $data = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => "INSTRUTIONS: Analyse cet article en détail et crée un résumé COMPLÈTEMENT REFORMULÉ. Ne copie JAMAIS les phrases originales. Reformule CHAQUE idée avec tes propres mots. Le résumé doit être LONG (5-7 phrases), DÉTAILLÉ et PROFOND.\n\nVoici le style de résumé attendu:\n'Le gaming dépasse largement le simple divertissement en offrant immersion, défis intellectuels et liens sociaux. Il stimule créativité et réflexion à travers des expériences variées. L'industrie vidéoludique est un moteur d'innovation technologique et artistique influençant d'autres domaines. Les esports et les études scientifiques révèlent son impact culturel et ses bienfaits cognitifs. Finalement, cette pratique universelle repose sur le plaisir de l'exploration et la diversité des expériences.'\n\nArticle à résumer:\n{$text}\n\nCrée un résumé REFORMULÉ du même style (5-7 phrases, détaillé, aucun copier-coller):"
                        ]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.8, // Très créatif pour reformulation
                'topP' => 0.9,
                'topK' => 60,
                'maxOutputTokens' => 350 // Plus long pour détail
            ]
        ];

        // Configuration de la requête
        $options = [
            'http' => [
                'header' => [
                    "Content-Type: application/json"
                ],
                'method' => 'POST',
                'content' => json_encode($data),
                'timeout' => 30,
                'ignore_errors' => true
            ]
        ];

        $context = stream_context_create($options);

        // Exécuter la requête
        $response = @file_get_contents($url, false, $context);

        if ($response === FALSE) {
            $error = error_get_last();
            throw new Exception("Erreur connexion: " . ($error['message'] ?? 'Unknown error'));
        }

        // Décoder la réponse
        $result = json_decode($response, true);

        // Vérifier les erreurs
        if (isset($result['error'])) {
            $errorMsg = $result['error']['message'] ?? 'Unknown error';
            throw new Exception("Gemini Error: " . $errorMsg);
        }

        // Extraire le résumé
        if (isset($result['candidates']) && is_array($result['candidates']) && count($result['candidates']) > 0) {
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $summary = $result['candidates'][0]['content']['parts'][0]['text'];
                return $this->cleanSummary($summary);
            }
        }

        throw new Exception("Format de réponse invalide");
    }

    /**
     * Pré-traiter le texte pour l'API
     */
    private function preprocessText($text) {
        // 1. Supprimer le HTML
        $text = strip_tags($text);

        // 2. Limiter la longueur (max 3000 caractères pour Gemini)
        if (strlen($text) > 3000) {
            $text = substr($text, 0, 3000);

            // Essayer de couper à la fin d'une phrase
            $lastPeriod = strrpos($text, '.');
            if ($lastPeriod > 1500) {
                $text = substr($text, 0, $lastPeriod + 1);
            }
        }

        // 3. Nettoyer les espaces multiples
        $text = preg_replace('/\s+/', ' ', $text);

        // 4. Encoder les caractères spéciaux
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        return trim($text);
    }

    /**
     * Nettoyer le résumé retourné
     */
    private function cleanSummary($summary) {
        // Supprimer les espaces inutiles
        $summary = trim($summary);

        // Capitaliser la première lettre
        $summary = ucfirst($summary);

        // S'assurer qu'il y a un point à la fin
        if (substr($summary, -1) !== '.' && substr($summary, -1) !== '!' && substr($summary, -1) !== '?') {
            $summary .= '.';
        }

        return $summary;
    }

    /**
     * Fallback : résumé local intelligent
     */
    private function localSummarizer($text, $sentences = 3) {
        // Découper en phrases
        $sentencesArray = preg_split('/(?<=[.?!])\s+(?=[A-ZÀÂÄÇÉÈÊËÏÎÔÖÙÛÜÆŒ])/u', $text);

        // Filtrer les phrases trop courtes
        $sentencesArray = array_filter($sentencesArray, function($sentence) {
            return strlen(trim($sentence)) > 20;
        });

        // Réindexer l'array
        $sentencesArray = array_values($sentencesArray);

        // Prendre les premières phrases
        $selected = array_slice($sentencesArray, 0, $sentences);

        // Combiner
        $summary = implode(' ', $selected);

        // Si vide, prendre un extrait
        if (empty($summary)) {
            $summary = substr($text, 0, 250) . '...';
        }

        return $summary;
    }

    /**
     * Vérifier la disponibilité de l'API
     */
    public function checkAPIStatus() {
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->modelName}?key={$this->geminiApiKey}";

            $options = [
                'http' => [
                    'method' => 'GET',
                    'timeout' => 5,
                    'ignore_errors' => true
                ]
            ];

            $context = stream_context_create($options);
            $response = @file_get_contents($url, false, $context);

            if ($response !== FALSE) {
                $data = json_decode($response, true);
                if (isset($data['name'])) {
                    return [
                        'available' => true,
                        'model' => $this->modelName
                    ];
                }
            }
        } catch (Exception $e) {
            // Ignorer les erreurs
        }

        return ['available' => false];
    }
}
?>