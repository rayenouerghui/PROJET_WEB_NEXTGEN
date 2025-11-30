// Validation du formulaire de connexion
document.getElementById('loginForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Reset errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    document.querySelectorAll('input').forEach(el => el.classList.remove('error-field'));
    
    // Email validation
    const email = document.getElementById('login_email');
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
    const password = document.getElementById('login_password');
    if (password.value === '') {
        document.getElementById('password_error').textContent = 'Mot de passe requis';
        password.classList.add('error-field');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});
