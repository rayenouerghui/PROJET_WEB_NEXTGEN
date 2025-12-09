#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Vérification d'intégrité du système d'IA
"""

import os
import json
import sys

def check_installation():
    """Vérifier que tous les fichiers sont en place"""
    
    print("🔍 Vérification d'intégrité du système d'IA\n")
    
    base_path = os.path.dirname(os.path.abspath(__file__))
    
    # Vérifier les dossiers
    required_dirs = [
        'ai_module',
        'ai_module/models',
        'ai_module/data'
    ]
    
    print("📁 Vérification des dossiers:")
    for dir_path in required_dirs:
        full_path = os.path.join(base_path, dir_path)
        if os.path.isdir(full_path):
            print(f"  ✅ {dir_path}")
        else:
            print(f"  ❌ {dir_path} - MANQUANT!")
            return False
    
    print()
    
    # Vérifier les fichiers Python
    python_files = {
        'ai_module/analyse_reclamation.py': 'Script principal',
        'ai_module/models/naive_bayes.py': 'Classifieur Naive Bayes',
        'ai_module/models/markov_model.py': 'Modèle de Markov',
        'ai_module/models/word2vec_simple.py': 'Word2Vec simplifié',
        'ai_module/models/__init__.py': '__init__ du module',
        'ai_module/quick_test.py': 'Test rapide',
        'ai_module/test_ai.py': 'Suite de tests',
    }
    
    print("🐍 Vérification des fichiers Python:")
    for file_path, description in python_files.items():
        full_path = os.path.join(base_path, file_path)
        if os.path.isfile(full_path):
            size = os.path.getsize(full_path)
            print(f"  ✅ {file_path} ({size} bytes)")
        else:
            print(f"  ❌ {file_path} - MANQUANT!")
            return False
    
    print()
    
    # Vérifier les fichiers de données
    data_files = {
        'ai_module/data/badwords_list.json': 'Liste des mots inappropriés',
        'ai_module/data/reclamations_samples.json': 'Exemples d\'entraînement',
        'ai_module/data/word_embeddings.json': 'Embeddings de mots',
    }
    
    print("📊 Vérification des fichiers de données:")
    for file_path, description in data_files.items():
        full_path = os.path.join(base_path, file_path)
        if os.path.isfile(full_path):
            try:
                with open(full_path, 'r', encoding='utf-8') as f:
                    data = json.load(f)
                    print(f"  ✅ {file_path} (valide)")
            except json.JSONDecodeError:
                print(f"  ❌ {file_path} - JSON INVALIDE!")
                return False
        else:
            print(f"  ❌ {file_path} - MANQUANT!")
            return False
    
    print()
    
    # Vérifier les fichiers de configuration
    config_files = {
        'ai_module/config.txt': 'Fichier de configuration',
        'ai_module/README.md': 'Documentation principale',
        'ai_module/GUIDE_AVANCE.md': 'Guide avancé',
        'setup_ai.sh': 'Script installation Linux/Mac',
        'setup_ai.bat': 'Script installation Windows',
        'INDEX_DOCUMENTATION.md': 'Index de documentation',
        'IMPLEMENTATION_SUMMARY.md': 'Résumé implémentation',
        'PHP_API.md': 'API PHP',
        'DEMARRAGE_RAPIDE.md': 'Démarrage rapide',
    }
    
    print("📄 Vérification des fichiers de configuration:")
    for file_path, description in config_files.items():
        full_path = os.path.join(base_path, file_path)
        if os.path.isfile(full_path):
            size = os.path.getsize(full_path)
            print(f"  ✅ {file_path} ({size} bytes)")
        else:
            print(f"  ❌ {file_path} - MANQUANT!")
            return False
    
    print()
    
    # Vérifier la syntaxe Python
    print("🔍 Vérification de la syntaxe Python:")
    import py_compile
    
    for file_path in python_files.keys():
        if 'models/__init__' not in file_path:  # Skip __init__
            full_path = os.path.join(base_path, file_path)
            try:
                py_compile.compile(full_path, doraise=True)
                print(f"  ✅ {file_path}")
            except py_compile.PyCompileError as e:
                print(f"  ❌ {file_path} - ERREUR SYNTAXE!")
                print(f"     {e}")
                return False
    
    print()
    
    # Statistiques
    print("📈 Statistiques:")
    py_count = len(python_files)
    data_count = len(data_files)
    config_count = len(config_files)
    total = py_count + data_count + config_count
    
    print(f"  - Fichiers Python: {py_count}")
    print(f"  - Fichiers de données: {data_count}")
    print(f"  - Fichiers de configuration: {config_count}")
    print(f"  - Total: {total} fichiers")
    
    print()
    
    return True

if __name__ == '__main__':
    try:
        if check_installation():
            print("=" * 50)
            print("✅ VÉRIFICATION RÉUSSIE!")
            print("=" * 50)
            print("\nLe système d'IA est correctement installé.")
            print("Vous pouvez maintenant utiliser:")
            print("  - python3 ai_module/quick_test.py")
            print("  - python3 ai_module/test_ai.py")
            print("  - Le formulaire de réclamation Web")
            sys.exit(0)
        else:
            print("\n" + "=" * 50)
            print("❌ VÉRIFICATION ÉCHOUÉE!")
            print("=" * 50)
            print("\nCertains fichiers manquent ou sont corrompus.")
            print("Réinstallez le système d'IA.")
            sys.exit(1)
    except Exception as e:
        print(f"\n❌ Erreur: {e}")
        sys.exit(1)
