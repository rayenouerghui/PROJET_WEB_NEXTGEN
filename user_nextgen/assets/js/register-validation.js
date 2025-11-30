// Validation du formulaire d'inscription
document.getElementById('registerForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Reset errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    document.querySelectorAll('input').forEach(el => el.classList.remove('error-field'));
    
    // Name validation
    const name = document.getElementById('reg_name');
    if (name.value.trim() === '') {
        document.getElementById('name_error').textContent = 'Nom requis';
        name.classList.add('error-field');
        isValid = false;
    }
    
    // Prenom validation
    const prenom = document.getElementById('reg_prenom');
    if (prenom.value.trim() === '') {
        document.getElementById('prenom_error').textContent = 'Prénom requis';
        prenom.classList.add('error-field');
        isValid = false;
    }
    
    // Email validation
    const email = document.getElementById('reg_email');
    const emailValue = email.value.trim();
    if (emailValue === '') {
        document.getElementById('email_error').textContent = 'Email requis';
        email.classList.add('error-field');
        isValid = false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
        document.getElementById('email_error').textContent = 'Email invalide';
        email.classList.add('error-field');
        isValid = false;
    }
    
    // Password validation
    const password = document.getElementById('reg_password');
    if (password.value === '') {
        document.getElementById('password_error').textContent = 'Mot de passe requis';
        password.classList.add('error-field');
        isValid = false;
    } else if (password.value.length < 6) {
        document.getElementById('password_error').textContent = 'Mot de passe minimum 6 caractères';
        password.classList.add('error-field');
        isValid = false;
    }
    
    // Password confirmation validation
    const passwordConfirm = document.getElementById('reg_password_confirm');
    if (passwordConfirm.value === '') {
        document.getElementById('password_confirm_error').textContent = 'Confirmation requise';
        passwordConfirm.classList.add('error-field');
        isValid = false;
    } else if (password.value !== passwordConfirm.value) {
        document.getElementById('password_confirm_error').textContent = 'Les mots de passe ne correspondent pas';
        passwordConfirm.classList.add('error-field');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});
