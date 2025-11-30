// Validation du formulaire de modification de profil
document.getElementById('profileForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Reset errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    document.querySelectorAll('input').forEach(el => el.classList.remove('error-field'));
    
    // Nom validation
    const nom = document.getElementById('edit_nom');
    if (nom.value.trim() === '') {
        document.getElementById('nom_error').textContent = 'Nom requis';
        nom.classList.add('error-field');
        isValid = false;
    }
    
    // Email validation
    const email = document.getElementById('edit_email');
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
    
    if (!isValid) {
        e.preventDefault();
    }
});
