/**
 * Validation JavaScript pour les formulaires de réclamation
 * Remplace les validations HTML5
 */

(function() {
    'use strict';

    const BAD_WORDS = [
        'con', 'idiot', 'imbecile', 'imbécile', 'merde', 'pute', 'salope',
        'encule', 'enculé', 'batard', 'bâtard', 'fuck', 'shit', 'asshole'
    ];

    function sanitizeText(text) {
        if (typeof text !== 'string') {
            return '';
        }

        let sanitized = text.toLowerCase();

        if (typeof sanitized.normalize === 'function') {
            sanitized = sanitized.normalize('NFD');
        }

        return sanitized
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s]/g, ' ');
    }

    function containsBadWords(text) {
        const words = sanitizeText(text).split(/\s+/).filter(Boolean);
        return words.some(word => BAD_WORDS.includes(word));
    }

    // Fonction pour afficher les erreurs
    function showError(input, message) {
        // Supprimer l'erreur précédente
        const existingError = input.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        // Ajouter la classe d'erreur
        input.classList.add('error');
        input.style.borderColor = '#dc2626';

        // Créer le message d'erreur
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.color = '#dc2626';
        errorDiv.style.fontSize = '0.875rem';
        errorDiv.style.marginTop = '0.25rem';
        errorDiv.textContent = message;

        input.parentElement.appendChild(errorDiv);
    }

    // Fonction pour supprimer les erreurs
    function clearError(input) {
        input.classList.remove('error');
        input.style.borderColor = '';
        const errorDiv = input.parentElement.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    // Fonction pour valider le type de réclamation
    function validateType(typeSelect) {
        if (!typeSelect.value || typeSelect.value.trim() === '') {
            showError(typeSelect, 'Veuillez sélectionner un type de réclamation');
            return false;
        }
        clearError(typeSelect);
        return true;
    }

    // Fonction pour valider la description
    function validateDescription(descriptionTextarea) {
        const value = descriptionTextarea.value.trim();
        
        if (!value) {
            showError(descriptionTextarea, 'La description est obligatoire');
            return false;
        }
        
        if (value.length < 10) {
            showError(descriptionTextarea, 'La description doit contenir au moins 10 caractères');
            return false;
        }
        
        if (value.length > 5000) {
            showError(descriptionTextarea, 'La description ne doit pas dépasser 5000 caractères');
            return false;
        }

        if (containsBadWords(value)) {
            showError(descriptionTextarea, 'Merci d\'utiliser un langage approprié.');
            return false;
        }
        
        clearError(descriptionTextarea);
        return true;
    }

    // Fonction pour valider le produit concerné (si spécifié manuellement)
    function validateProduitConcerne(produitInput) {
        const value = produitInput.value.trim();
        
        // Si un jeu est sélectionné, le produit manuel n'est pas nécessaire
        const jeuSelect = document.getElementById('id_jeu');
        if (jeuSelect && jeuSelect.value) {
            clearError(produitInput);
            return true;
        }
        
        // Si aucun jeu n'est sélectionné et qu'un produit est saisi
        if (value) {
            if (value.length < 3) {
                showError(produitInput, 'Le nom du produit doit contenir au moins 3 caractères');
                return false;
            }
            if (value.length > 255) {
                showError(produitInput, 'Le nom du produit ne doit pas dépasser 255 caractères');
                return false;
            }
        }
        
        clearError(produitInput);
        return true;
    }

    // Validation en temps réel
    function setupRealTimeValidation() {
        const form = document.getElementById('reclamationForm');
        if (!form) return;

        const typeSelect = document.getElementById('type');
        const descriptionTextarea = document.getElementById('description');
        const produitInput = document.getElementById('produit_concerne');
        const jeuSelect = document.getElementById('id_jeu');

        // Validation du type
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                validateType(this);
            });
            typeSelect.addEventListener('blur', function() {
                validateType(this);
            });
        }

        // Validation de la description
        if (descriptionTextarea) {
            let descriptionTimeout;
            descriptionTextarea.addEventListener('input', function() {
                clearTimeout(descriptionTimeout);
                descriptionTimeout = setTimeout(() => {
                    validateDescription(this);
                }, 500);
            });
            descriptionTextarea.addEventListener('blur', function() {
                validateDescription(this);
            });
        }

        // Validation du produit concerné
        if (produitInput) {
            produitInput.addEventListener('blur', function() {
                validateProduitConcerne(this);
            });
        }

        // Si un jeu est sélectionné, effacer l'erreur du produit manuel
        if (jeuSelect && produitInput) {
            jeuSelect.addEventListener('change', function() {
                if (this.value) {
                    clearError(produitInput);
                }
            });
        }
    }

    // Validation complète du formulaire
    function validateForm(e) {
        e.preventDefault();
        
        const form = document.getElementById('reclamationForm');
        if (!form) return;

        let isValid = true;

        // Valider tous les champs
        const typeSelect = document.getElementById('type');
        const descriptionTextarea = document.getElementById('description');
        const produitInput = document.getElementById('produit_concerne');

        if (!validateType(typeSelect)) {
            isValid = false;
        }

        if (!validateDescription(descriptionTextarea)) {
            isValid = false;
        }

        if (produitInput && !validateProduitConcerne(produitInput)) {
            isValid = false;
        }

        // Si le formulaire est valide, le soumettre
        if (isValid) {
            form.submit();
        } else {
            // Faire défiler jusqu'au premier champ en erreur
            const firstError = form.querySelector('.error');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstError.focus();
            }
        }

        return false;
    }

    // Initialisation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('reclamationForm');
        if (form) {
            // Supprimer l'attribut required HTML5
            form.removeAttribute('novalidate');
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                field.removeAttribute('required');
            });

            // Configuration de la validation en temps réel
            setupRealTimeValidation();

            // Validation à la soumission
            form.addEventListener('submit', validateForm);
        }
    });

    // Ajouter les styles pour les erreurs
    const style = document.createElement('style');
    style.textContent = `
        .error {
            border-color: #dc2626 !important;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1) !important;
        }
        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }
        .form-control.error:focus,
        .form-select.error:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
        }
    `;
    document.head.appendChild(style);

})();

