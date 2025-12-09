#!/bin/bash
# Script d'installation et de configuration du système d'IA
# Usage: ./setup_ai.sh

echo "=================================================="
echo "🚀 Installation du Système d'IA Nextgen"
echo "=================================================="
echo ""

# Vérifier que Python est installé
echo "📦 Vérification de Python..."
if command -v python3 &> /dev/null; then
    PYTHON_VERSION=$(python3 --version)
    echo "✅ Python3 trouvé: $PYTHON_VERSION"
elif command -v python &> /dev/null; then
    PYTHON_VERSION=$(python --version)
    echo "✅ Python trouvé: $PYTHON_VERSION"
else
    echo "❌ Python n'est pas installé!"
    echo "   Veuillez installer Python 3.7 ou supérieur"
    echo "   https://www.python.org/downloads/"
    exit 1
fi

echo ""

# Vérifier la structure des répertoires
echo "📁 Vérification de la structure..."
if [ -d "ai_module" ]; then
    echo "✅ Dossier ai_module trouvé"
else
    echo "❌ Dossier ai_module non trouvé!"
    exit 1
fi

if [ -d "ai_module/models" ]; then
    echo "✅ Dossier ai_module/models trouvé"
else
    echo "❌ Dossier ai_module/models non trouvé!"
    exit 1
fi

if [ -d "ai_module/data" ]; then
    echo "✅ Dossier ai_module/data trouvé"
else
    echo "❌ Dossier ai_module/data non trouvé!"
    exit 1
fi

echo ""

# Vérifier les fichiers Python
echo "🔍 Vérification des fichiers Python..."
required_files=(
    "ai_module/analyse_reclamation.py"
    "ai_module/models/naive_bayes.py"
    "ai_module/models/markov_model.py"
    "ai_module/models/word2vec_simple.py"
)

for file in "${required_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file"
    else
        echo "❌ $file manquant!"
        exit 1
    fi
done

echo ""

# Vérifier les fichiers de données
echo "📊 Vérification des fichiers de données..."
data_files=(
    "ai_module/data/badwords_list.json"
    "ai_module/data/reclamations_samples.json"
    "ai_module/data/word_embeddings.json"
)

for file in "${data_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file"
    else
        echo "❌ $file manquant!"
        exit 1
    fi
done

echo ""

# Test rapide
echo "🧪 Test rapide du système..."
if [ -f "ai_module/quick_test.py" ]; then
    python3 ai_module/quick_test.py
    if [ $? -eq 0 ]; then
        echo "✅ Tests passés avec succès!"
    else
        echo "⚠️  Les tests ont échoué, mais l'installation est complète"
    fi
else
    echo "⚠️  Fichier de test rapide non trouvé"
fi

echo ""
echo "=================================================="
echo "✅ Installation complète!"
echo "=================================================="
echo ""
echo "📚 Documentations disponibles:"
echo "   - ai_module/README.md (Documentation principale)"
echo "   - ai_module/GUIDE_AVANCE.md (Guide avancé)"
echo "   - IMPLEMENTATION_SUMMARY.md (Résumé de l'implémentation)"
echo ""
echo "🧪 Pour tester le système:"
echo "   python3 ai_module/quick_test.py"
echo "   python3 ai_module/test_ai.py"
echo ""
echo "🔧 Pour analyser un message:"
echo "   python3 ai_module/analyse_reclamation.py 'Votre message'"
echo ""
echo "✨ Le système est prêt à utiliser!"
