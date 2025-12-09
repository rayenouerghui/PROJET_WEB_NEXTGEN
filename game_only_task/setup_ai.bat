@echo off
REM Script d'installation et de configuration du système d'IA (Windows)
REM Usage: setup_ai.bat

echo ==================================================
echo.
echo     INSTALLATION DU SYSTEME D'IA NEXTGEN
echo.
echo ==================================================
echo.

REM Verifier que Python est installe
echo Verification de Python...
python --version >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    for /f "tokens=*" %%i in ('python --version') do set PYTHON_VERSION=%%i
    echo ✓ Python trouvé: %PYTHON_VERSION%
) else (
    python3 --version >nul 2>&1
    if %ERRORLEVEL% EQU 0 (
        for /f "tokens=*" %%i in ('python3 --version') do set PYTHON_VERSION=%%i
        echo ✓ Python3 trouvé: %PYTHON_VERSION%
    ) else (
        echo × Python n'est pas installe!
        echo Veuillez installer Python 3.7 ou superieur
        echo https://www.python.org/downloads/
        pause
        exit /b 1
    )
)

echo.

REM Verifier la structure des repertoires
echo Verification de la structure...
if exist "ai_module" (
    echo ✓ Dossier ai_module trouve
) else (
    echo × Dossier ai_module non trouve!
    pause
    exit /b 1
)

if exist "ai_module\models" (
    echo ✓ Dossier ai_module\models trouve
) else (
    echo × Dossier ai_module\models non trouve!
    pause
    exit /b 1
)

if exist "ai_module\data" (
    echo ✓ Dossier ai_module\data trouve
) else (
    echo × Dossier ai_module\data non trouve!
    pause
    exit /b 1
)

echo.

REM Verifier les fichiers Python
echo Verification des fichiers Python...
setlocal enabledelayedexpansion
set "files[0]=ai_module\analyse_reclamation.py"
set "files[1]=ai_module\models\naive_bayes.py"
set "files[2]=ai_module\models\markov_model.py"
set "files[3]=ai_module\models\word2vec_simple.py"

for /l %%i in (0,1,3) do (
    if exist "!files[%%i]!" (
        echo ✓ !files[%%i]!
    ) else (
        echo × !files[%%i]! manquant!
        pause
        exit /b 1
    )
)

echo.

REM Verifier les fichiers de donnees
echo Verification des fichiers de donnees...
set "data[0]=ai_module\data\badwords_list.json"
set "data[1]=ai_module\data\reclamations_samples.json"
set "data[2]=ai_module\data\word_embeddings.json"

for /l %%i in (0,1,2) do (
    if exist "!data[%%i]!" (
        echo ✓ !data[%%i]!
    ) else (
        echo × !data[%%i]! manquant!
        pause
        exit /b 1
    )
)

echo.

REM Test rapide
echo Test rapide du systeme...
if exist "ai_module\quick_test.py" (
    python ai_module\quick_test.py >nul 2>&1
    if %ERRORLEVEL% EQU 0 (
        echo ✓ Tests passes avec succes!
    ) else (
        echo ! Les tests ont echoue, mais l'installation est complete
    )
) else (
    echo ! Fichier de test rapide non trouve
)

echo.
echo ==================================================
echo ✓ INSTALLATION COMPLETE!
echo ==================================================
echo.
echo Documentations disponibles:
echo    - ai_module\README.md (Documentation principale)
echo    - ai_module\GUIDE_AVANCE.md (Guide avance)
echo    - IMPLEMENTATION_SUMMARY.md (Resume de l'implementation)
echo.
echo Pour tester le systeme:
echo    python ai_module\quick_test.py
echo    python ai_module\test_ai.py
echo.
echo Pour analyser un message:
echo    python ai_module\analyse_reclamation.py "Votre message"
echo.
echo ✓ Le systeme est pret a utiliser!
echo.
pause
