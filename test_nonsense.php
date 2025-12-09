<?php

function isNonsenseMessage($message) {
    $text_lower = strtolower($message);
    
    // Compter les consonnes et voyelles
    $consonants = 0;
    $vowels = 0;
    $other = 0;
    
    for ($i = 0; $i < strlen($text_lower); $i++) {
        $char = $text_lower[$i];
        if (preg_match('/[bcdfghjklmnpqrstvwxyz]/', $char)) {
            $consonants++;
        } elseif (preg_match('/[aeiouyàâäéèêëîïôöùûüœæ]/', $char)) {
            $vowels++;
        } else {
            $other++;
        }
    }
    
    $total = $consonants + $vowels;
    
    // Si plus de 60% de consonnes (anormal), c'est du bruit
    if ($total > 5 && ($consonants / $total) > 0.60) {
        return true;
    }
    
    // Vérifier les répétitions excessives de caractères
    if (preg_match('/(.)\1{3,}/', $text_lower)) {
        return true;
    }
    
    // Vérifier les caractères spéciaux en excès (tirets, underscores, etc.)
    $special_count = $other;
    if ($total > 0 && ($special_count / ($total + $special_count)) > 0.2) {
        return true;
    }
    
    // Vérifier si c'est une suite de syllabes sans sens
    $words = preg_split('/\s+/', trim($text_lower), -1, PREG_SPLIT_NO_EMPTY);
    if (count($words) >= 1) {
        // Analyser chaque mot
        foreach ($words as $word) {
            $word_consonants = preg_match_all('/[bcdfghjklmnpqrstvwxyz]/', $word);
            $word_vowels = preg_match_all('/[aeiouyàâäéèêëîïôöùûüœæ]/', $word);
            $word_total = $word_consonants + $word_vowels;
            
            // Si un mot a plus de 70% consonnes ET plus de 3 caractères = suspect
            if ($word_total > 3 && ($word_consonants / $word_total) > 0.70) {
                return true;
            }
        }
    }
    
    return false;
}

// Test avec le message du formulaire
$message = "vultficëftig-ê_igmigyugviy_g_";

$text_lower = strtolower($message);
$consonants = 0;
$vowels = 0;
$other = 0;

for ($i = 0; $i < strlen($text_lower); $i++) {
    $char = $text_lower[$i];
    if (preg_match('/[bcdfghjklmnpqrstvwxyz]/', $char)) {
        $consonants++;
    } elseif (preg_match('/[aeiouyàâäéèêëîïôöùûüœæ]/', $char)) {
        $vowels++;
    } else {
        $other++;
    }
}

$total = $consonants + $vowels;
$ratio = $total > 0 ? ($consonants / $total) : 0;

echo "Message: " . $message . "\n";
echo "Consonants: " . $consonants . "\n";
echo "Vowels: " . $vowels . "\n";
echo "Other: " . $other . "\n";
echo "Total: " . $total . "\n";
echo "Consonant Ratio: " . round($ratio * 100, 2) . "%\n";
echo "Is Nonsense (>65%): " . ($ratio > 0.65 ? "YES - REJECTED" : "NO - ACCEPTED") . "\n\n";

echo "Function Result: " . (isNonsenseMessage($message) ? "REJECTED" : "ACCEPTED") . "\n";
?>
