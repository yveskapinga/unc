document.getElementById('userForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche la soumission du formulaire initiale
    getLocation(); // Appelle la fonction pour obtenir la position 
});

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, proceedWithoutLocation, {
            enableHighAccuracy: true,
            timeout: 5000,
            maximumAge: 0
        });
    } else {
        console.error("La géolocalisation n'est pas supportée par ce navigateur.");
        proceedWithoutLocation();
    }
}

function showPosition(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    // Mettre à jour les champs cachés du formulaire
    document.getElementById('latitude').value = latitude;
    document.getElementById('longitude').value = longitude;

    // Soumettre le formulaire après avoir défini les coordonnées
    document.getElementById('userForm').submit();
}

function proceedWithoutLocation() {
    // Soumettre le formulaire sans les coordonnées
    document.getElementById('userForm').submit();
}

// Validation côté client
document.querySelectorAll('#userForm input').forEach(function(input) {
    input.addEventListener('blur', validateField);
    input.addEventListener('input', clearError);
});

function validateField(event) {
    var field = event.target;
    var value = field.value.trim();
    var name = field.name;

    // Réinitialiser les messages d'erreur
    clearError({ target: field });

    if (name.includes('username') && value === '') {
        showError(field, 'Veuillez saisir un nom d\'utilisateur.');
    } else if (name.includes('email') && !validateEmail(value)) {
        showError(field, 'Veuillez saisir une adresse email valide.');
    } else if (name.includes('plainPassword') && name.includes('first') && value === '') {
        showError(field, 'Veuillez saisir un mot de passe.');
    } else if (name.includes('plainPassword') && name.includes('second') && value !== document.querySelector('[name="registrationForm[plainPassword][first]"]').value) {
        showError(field, 'Les mots de passe ne correspondent pas.');
    }
}

function clearError(event) {
    var field = event.target;
    var error = field.parentNode.querySelector('.error-message');
    if (error) {
        error.remove();
    }
}

function showError(element, message) {
    var error = document.createElement('div');
    error.className = 'error-message';
    error.textContent = message;
    element.parentNode.appendChild(error);
}

function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
