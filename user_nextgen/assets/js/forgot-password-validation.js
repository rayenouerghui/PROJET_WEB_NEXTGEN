// Validation du formulaire mot de passe oublié
document.getElementById('forgotPasswordForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Récupérer les champs
    const identifier = document.getElementById('forgot_identifier');
    const identifierError = document.getElementById('identifier_error');
    
    // Réinitialiser les erreurs
    identifierError.textContent = '';
    identifier.classList.remove('error');
    
    // Validation identifiant (email ou téléphone)
    const identifierValue = identifier.value.trim();
    if (identifierValue === '') {
        identifierError.textContent = 'Email ou téléphone requis';
        identifier.classList.add('error');
        isValid = false;
    } else if (!validateIdentifier(identifierValue)) {
        identifierError.textContent = 'Email ou téléphone invalide';
        identifier.classList.add('error');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});

// Fonction de validation email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Fonction de validation téléphone (format international)
function validatePhone(phone) {
    // Accepte les formats: +212612345678, 0612345678, 212612345678
    const re = /^(\+?\d{1,3})?[\s.-]?\(?\d{1,4}\)?[\s.-]?\d{1,4}[\s.-]?\d{1,9}$/;
    return re.test(phone);
}

// Fonction de validation identifiant (email OU téléphone)
function validateIdentifier(identifier) {
    return validateEmail(identifier) || validatePhone(identifier);
}

// Validation en temps réel
document.getElementById('forgot_identifier').addEventListener('blur', function() {
    const identifierError = document.getElementById('identifier_error');
    const identifierValue = this.value.trim();
    
    identifierError.textContent = '';
    this.classList.remove('error');
    
    if (identifierValue === '') {
        identifierError.textContent = 'Email ou téléphone requis';
        this.classList.add('error');
    } else if (!validateIdentifier(identifierValue)) {
        identifierError.textContent = 'Email ou téléphone invalide';
        this.classList.add('error');
    }
});

// Changer le placeholder selon la méthode choisie
document.getElementById('send_method').addEventListener('change', function() {
    const identifier = document.getElementById('forgot_identifier');
    const placeholders = {
        'email': 'votre@email.com',
        'sms': '+212612345678',
        'both': 'email@example.com ou +212612345678'
    };
    identifier.placeholder = placeholders[this.value] || placeholders['both'];
});
