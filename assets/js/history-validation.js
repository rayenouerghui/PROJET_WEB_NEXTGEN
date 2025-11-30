// Validation du formulaire d'historique (create et edit)
document.getElementById('historyForm').addEventListener('submit', function(e) {
    let isValid = true;
    
    // Reset errors
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
    document.querySelectorAll('input, textarea').forEach(el => el.classList.remove('error-field'));
    
    // Action validation
    const action = document.getElementById('hist_action');
    if (action.value.trim() === '') {
        document.getElementById('action_error').textContent = 'Action requise';
        action.classList.add('error-field');
        isValid = false;
    }
    
    if (!isValid) {
        e.preventDefault();
    }
});
