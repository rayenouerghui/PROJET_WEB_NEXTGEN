function validateNumber(input, min, max) {
    const value = parseInt(input.value);
    if (isNaN(value)) {
        return { valid: false, message: 'Veuillez entrer un nombre valide' };
    }
    if (min !== undefined && value < min) {
        return { valid: false, message: 'La valeur doit être supérieure ou égale à ' + min };
    }
    if (max !== undefined && value > max) {
        return { valid: false, message: 'La valeur doit être inférieure ou égale à ' + max };
    }
    return { valid: true };
}

function validateRequired(input) {
    if (!input.value || input.value.trim() === '') {
        return { valid: false, message: 'Ce champ est obligatoire' };
    }
    return { valid: true };
}

function validateEmail(input) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!input.value || input.value.trim() === '') {
        return { valid: false, message: 'Ce champ est obligatoire' };
    }
    if (!emailRegex.test(input.value)) {
        return { valid: false, message: 'Veuillez entrer une adresse email valide' };
    }
    return { valid: true };
}

function showError(input, message) {
    const formGroup = input.closest('.form-group');
    if (!formGroup) return;
    
    let errorDiv = formGroup.querySelector('.error-message');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        formGroup.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    input.style.borderColor = '#ef4444';
}

function clearError(input) {
    const formGroup = input.closest('.form-group');
    if (!formGroup) return;
    
    const errorDiv = formGroup.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
    input.style.borderColor = '';
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[data-validate], select[data-validate], textarea[data-validate]');
    
    inputs.forEach(input => {
        clearError(input);
        const validateType = input.getAttribute('data-validate');
        let result = { valid: true };
        
        if (validateType.includes('required')) {
            result = validateRequired(input);
        }
        
        if (result.valid && validateType.includes('number')) {
            const min = input.getAttribute('data-min') ? parseInt(input.getAttribute('data-min')) : undefined;
            const max = input.getAttribute('data-max') ? parseInt(input.getAttribute('data-max')) : undefined;
            result = validateNumber(input, min, max);
        }
        
        if (result.valid && validateType.includes('email')) {
            result = validateEmail(input);
        }
        
        if (!result.valid) {
            showError(input, result.message);
            isValid = false;
        }
    });
    
    return isValid;
}

document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(form)) {
                e.preventDefault();
                return false;
            }
        });
        
        const inputs = form.querySelectorAll('input[data-validate], select[data-validate]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                const validateType = input.getAttribute('data-validate');
                let result = { valid: true };
                
                if (validateType.includes('required')) {
                    result = validateRequired(input);
                }
                
                if (result.valid && validateType.includes('number')) {
                    const min = input.getAttribute('data-min') ? parseInt(input.getAttribute('data-min')) : undefined;
                    const max = input.getAttribute('data-max') ? parseInt(input.getAttribute('data-max')) : undefined;
                    result = validateNumber(input, min, max);
                }
                
                if (result.valid && validateType.includes('email')) {
                    result = validateEmail(input);
                }
                
                if (!result.valid) {
                    showError(input, result.message);
                } else {
                    clearError(input);
                }
            });
        });
    });
});


