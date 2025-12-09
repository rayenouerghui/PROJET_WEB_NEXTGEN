/**
 * Validation JavaScript pour les formulaires de traitement
 * Remplace les validations HTML5
 */

(function() {
    'use strict';

    // Fonction pour afficher les erreurs
    function showError(input, message) {
        const existingError = input.parentElement.querySelector('.error-message');
        if (existingError) {
            existingError.remove();
        }

        input.classList.add('error');
        input.style.borderColor = '#dc2626';

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

    // Fonction pour valider le contenu du traitement
    function validateContenu(contenuTextarea) {
        const value = contenuTextarea.value.trim();
        
        if (!value) {
            showError(contenuTextarea, 'Le contenu du traitement est obligatoire');
            return false;
        }
        
        if (value.length < 10) {
            showError(contenuTextarea, 'Le contenu doit contenir au moins 10 caractères');
            return false;
        }
        
        if (value.length > 5000) {
            showError(contenuTextarea, 'Le contenu ne doit pas dépasser 5000 caractères');
            return false;
        }
        
        clearError(contenuTextarea);
        return true;
    }

    // Fonction pour valider le statut
    function validateStatut(statutSelect) {
        if (!statutSelect.value || statutSelect.value.trim() === '') {
            showError(statutSelect, 'Veuillez sélectionner un statut');
            return false;
        }
        clearError(statutSelect);
        return true;
    }

    // Validation en temps réel
    function setupRealTimeValidation() {
        const traitementForm = document.getElementById('traitementForm');
        if (!traitementForm) return;

        const contenuTextarea = traitementForm.querySelector('textarea[name="contenu"]');
        const statutSelect = traitementForm.querySelector('select[name="statut"]');

        // Validation du contenu
        if (contenuTextarea) {
            let contenuTimeout;
            contenuTextarea.addEventListener('input', function() {
                clearTimeout(contenuTimeout);
                contenuTimeout = setTimeout(() => {
                    validateContenu(this);
                }, 500);
            });
            contenuTextarea.addEventListener('blur', function() {
                validateContenu(this);
            });
        }

        // Validation du statut
        if (statutSelect) {
            statutSelect.addEventListener('change', function() {
                validateStatut(this);
            });
        }
    }

    // Validation complète du formulaire de traitement
    function validateTraitementForm(e) {
        e.preventDefault();
        
        const form = document.getElementById('traitementForm');
        if (!form) return;

        let isValid = true;

        const contenuTextarea = form.querySelector('textarea[name="contenu"]');
        const statutSelect = form.querySelector('select[name="statut"]');

        if (contenuTextarea && !validateContenu(contenuTextarea)) {
            isValid = false;
        }

        if (statutSelect && !validateStatut(statutSelect)) {
            isValid = false;
        }

        if (isValid) {
            form.submit();
        } else {
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
        const form = document.getElementById('traitementForm');
        if (form) {
            // Supprimer l'attribut required HTML5
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                field.removeAttribute('required');
            });

            setupRealTimeValidation();
            form.addEventListener('submit', validateTraitementForm);
        }
    });

    // Styles pour les erreurs
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
    `;
    document.head.appendChild(style);

})();

