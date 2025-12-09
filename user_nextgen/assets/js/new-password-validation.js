// Validation du formulaire nouveau mot de passe
document.getElementById('newPasswordForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Récupérer les champs
    const password = document.getElementById('new_password');
    const passwordConfirm = document.getElementById('new_password_confirm');
    const passwordError = document.getElementById('password_error');
    const passwordConfirmError = document.getElementById('password_confirm_error');
    
    // Réinitialiser les erreurs
    passwordError.textContent = '';
    passwordConfirmError.textContent = '';
    password.classList.remove('error');
    passwordConfirm.classList.remove('error');
    
    // Validation mot de passe
    const passwordValue = password.value;
    if (passwordValue === '') {
        passwordError.textContent = 'Mot de passe requis';
        password.classList.add('error');
        isValid = false;
    } else if (passwordValue.length < 6) {
        passwordError.textContent = 'Mot de passe minimum 6 caractères';
        password.classList.add('error');
        isValid = false;
    }
    
    // Validation confirmation
    const passwordConfirmValue = passwordConfirm.value;
    if (passwordConfirmValue === '') {
        passwordConfirmError.textContent = 'Confirmation requise';
        passwordConfirm.classList.add('error');
        isValid = false;
    } else if (passwordValue !== passwordConfirmValue) {
        passwordConfirmError.textContent = 'Les mots de passe ne correspondent pas';
        passwordConfirm.classList.add('error');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});

// Validation en temps réel
document.getElementById('new_password').addEventListener('blur', function() {
    const passwordError = document.getElementById('password_error');
    const passwordValue = this.value;
    
    passwordError.textContent = '';
    this.classList.remove('error');
    
    if (passwordValue === '') {
        passwordError.textContent = 'Mot de passe requis';
        this.classList.add('error');
    } else if (passwordValue.length < 6) {
        passwordError.textContent = 'Mot de passe minimum 6 caractères';
        this.classList.add('error');
    }
});

document.getElementById('new_password_confirm').addEventListener('blur', function() {
    const password = document.getElementById('new_password');
    const passwordConfirmError = document.getElementById('password_confirm_error');
    const passwordConfirmValue = this.value;
    
    passwordConfirmError.textContent = '';
    this.classList.remove('error');
    
    if (passwordConfirmValue === '') {
        passwordConfirmError.textContent = 'Confirmation requise';
        this.classList.add('error');
    } else if (password.value !== passwordConfirmValue) {
        passwordConfirmError.textContent = 'Les mots de passe ne correspondent pas';
        this.classList.add('error');
    }
});
