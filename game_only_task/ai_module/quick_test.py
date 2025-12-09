#!/usr/bin/env python3
# -*- coding: utf-8 -*-

"""
Script simple pour tester rapidement le système d'IA
"""

import sys
import os
import json

# Ajouter le répertoire au path
script_dir = os.path.dirname(os.path.abspath(__file__))
sys.path.insert(0, os.path.join(script_dir, 'models'))

try:
    from analyse_reclamation import ReclamationAnalyzer
    
    print("✅ Import réussi!")
    print()
    
    # Initialiser l'analyseur
    analyzer = ReclamationAnalyzer()
    print("✅ Analyseur initialisé!")
    print()
    
    # Test 1: Message valide
    print("Test 1: Message valide")
    result = analyzer.analyze("Le jeu n'a pas été livré dans les délais convenus.")
    print(f"Score: {result['score']} - Valide: {result['valid']}")
    print()
    
    # Test 2: Message avec insulte
    print("Test 2: Message avec insulte")
    result = analyzer.analyze("Vous êtes des idiots!")
    print(f"Score: {result['score']} - Valide: {result['valid']}")
    print()
    
    # Test 3: Message sans sens
    print("Test 3: Message sans sens")
    result = analyzer.analyze("hhhhhhhh aaaaa")
    print(f"Score: {result['score']} - Valide: {result['valid']}")
    print()
    
    # Test 4: Message trop court
    print("Test 4: Message trop court")
    result = analyzer.analyze("ok")
    print(f"Score: {result['score']} - Valide: {result['valid']}")
    print()
    
    print("✅ Tous les tests sont passés avec succès!")
    
except ImportError as e:
    print(f"❌ Erreur d'import: {e}")
    sys.exit(1)
except Exception as e:
    print(f"❌ Erreur: {e}")
    import traceback
    traceback.print_exc()
    sys.exit(1)
