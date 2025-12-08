// Validation du formulaire de vérification du code
document.getElementById('verifyCodeForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Récupérer les champs
    const code = document.getElementById('verify_code');
    const codeError = document.getElementById('code_error');
    
    // Réinitialiser les erreurs
    codeError.textContent = '';
    code.classList.remove('error');
    
    // Validation code
    const codeValue = code.value.trim();
    if (codeValue === '') {
        codeError.textContent = 'Code requis';
        code.classList.add('error');
        isValid = false;
    } else if (codeValue.length !== 6) {
        codeError.textContent = 'Le code doit contenir 6 chiffres';
        code.classList.add('error');
        isValid = false;
    } else if (!/^\d{6}$/.test(codeValue)) {
        codeError.textContent = 'Le code doit contenir uniquement des chiffres';
        code.classList.add('error');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});

// Validation en temps réel
document.getElementById('verify_code').addEventListener('input', function() {
    // Autoriser uniquement les chiffres
    this.value = this.value.replace(/\D/g, '');
    
    const codeError = document.getElementById('code_error');
    const codeValue = this.value.trim();
    
    codeError.textContent = '';
    this.classList.remove('error');
    
    if (codeValue.length > 0 && codeValue.length < 6) {
        codeError.textContent = 'Le code doit contenir 6 chiffres';
        this.classList.add('error');
    }
});

// Auto-focus et auto-submit quand 6 chiffres sont entrés
document.getElementById('verify_code').addEventListener('input', function() {
    if (this.value.length === 6) {
        // Optionnel : soumettre automatiquement
        // document.getElementById('verifyCodeForm').submit();
    }
});
