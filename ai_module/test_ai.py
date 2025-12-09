#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Script de test pour le système d'IA de filtrage de réclamations
"""

import sys
import os
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'models'))

from analyse_reclamation import ReclamationAnalyzer

def test_system():
    """Tester le système avec différents cas"""
    
    analyzer = ReclamationAnalyzer()
    
    test_cases = [
        # (message, nom_du_test)
        ("Le jeu n'a pas été livré dans les délais convenus. Commande #12345", "Message valide"),
        ("J'ai reçu un produit différent de celui commandé", "Message valide 2"),
        ("hhhhhh", "Message non-sens (répétition)"),
        ("asdfghjkl", "Message non-sens (charabia)"),
        ("Vous êtes des idiots!", "Message avec insulte"),
        ("ok", "Message trop court"),
        ("", "Message vide"),
        ("Le jeu s'est arrêté de fonctionner après une semaine. Le service client est mauvais.", "Message avec critique"),
        ("VIAGRA CASINO GRATUIT", "Message SPAM"),
        ("Je veux un remboursement pour mon jeu défectueux", "Réclamation standard"),
    ]
    
    print("=" * 80)
    print("🧪 TEST DU SYSTÈME D'IA DE FILTRAGE DE RÉCLAMATIONS")
    print("=" * 80)
    print()
    
    results_summary = {
        'valid': 0,
        'invalid': 0,
        'rewrite': 0
    }
    
    for message, test_name in test_cases:
        print(f"📋 Test: {test_name}")
        print(f"   Message: '{message}'")
        
        result = analyzer.analyze(message)
        
        if result['valid'] is True:
            print(f"   ✅ VALIDE")
            results_summary['valid'] += 1
        elif result['valid'] is False:
            print(f"   ❌ REJETÉ")
            results_summary['invalid'] += 1
        else:
            print(f"   ⚠️  DEMANDE RÉÉCRITURE")
            results_summary['rewrite'] += 1
        
        print(f"   Score: {result['score']} ({result['score']*100:.1f}%)")
        print(f"   Raison: {result['reason']}")
        
        # Afficher les détails
        details = result['details']
        print(f"   Details:")
        print(f"     - Bayes: {details['bayes_score']} ({details['bayes_class']})")
        print(f"     - Markov: {details['markov_score']} (naturalité: {details['markov_naturalness']})")
        print(f"     - Word2Vec: {details['word2vec_score']}")
        if details['has_badwords']:
            print(f"     - ⚠️  Mots inappropriés détectés!")
        
        print()
    
    print("=" * 80)
    print("📊 RÉSUMÉ")
    print("=" * 80)
    print(f"✅ Messages valides: {results_summary['valid']}")
    print(f"❌ Messages rejetés: {results_summary['invalid']}")
    print(f"⚠️  Messages demandant réécriture: {results_summary['rewrite']}")
    print(f"Total: {sum(results_summary.values())} tests")
    print()

if __name__ == '__main__':
    try:
        test_system()
        print("✅ Tous les tests sont terminés avec succès!")
    except Exception as e:
        print(f"❌ Erreur: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)
