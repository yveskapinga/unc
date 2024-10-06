/* document.getElementById('userForm').addEventListener('submit', function(event) {
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
} */
//FROM BING A PARTIR DU TRAVAIL

// Ajoute un écouteur d'événement pour intercepter la soumission du formulaire
document.getElementById('userForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Empêche la soumission initiale du formulaire
    if (validateForm()) {
        getLocation(); // Appelle la fonction pour obtenir la position géographique
    }
});

// Fonction pour obtenir la position géographique de l'utilisateur
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, proceedWithoutLocation, {
            enableHighAccuracy: true, // Demande une haute précision
            timeout: 5000, // Temps d'attente maximum de 5 secondes
            maximumAge: 0 // Ne pas utiliser de cache
        });
    } else {
        console.error("La géolocalisation n'est pas supportée par ce navigateur.");
        proceedWithoutLocation(); // Procède sans la localisation si non supportée
    }
}

// Fonction appelée en cas de succès de la géolocalisation
function showPosition(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    // Met à jour les champs cachés du formulaire avec les coordonnées
    document.getElementById('latitude').value = latitude;
    document.getElementById('longitude').value = longitude;

    // Soumet le formulaire après avoir défini les coordonnées
    document.getElementById('userForm').submit();
}

// Fonction appelée en cas d'échec de la géolocalisation
function proceedWithoutLocation() {
    // Soumettre le formulaire sans les coordonnées
    document.getElementById('userForm').submit();
}

// Ajoute des écouteurs d'événements pour la validation des champs du formulaire
document.querySelectorAll('#userForm input').forEach(function(input) {
    input.addEventListener('blur', validateField); // Valide le champ lorsqu'il perd le focus
    input.addEventListener('input', clearError); // Efface les erreurs lorsqu'il y a une entrée
});

// Fonction pour valider le formulaire entier
function validateForm() {
    var isValid = true;
    document.querySelectorAll('#userForm input').forEach(function(input) {
        if (!validateField({ target: input })) {
            isValid = false;
        }
    });
    return isValid;
}

// Fonction pour valider les champs du formulaire
function validateField(event) {
    var field = event.target;
    var value = field.value.trim();
    var name = field.name;

    // Réinitialise les messages d'erreur
    clearError({ target: field });

    // Validation spécifique pour le nom d'utilisateur
    if (name.includes('username')) {
        if (value === '') {
            showError(field, 'Veuillez saisir un nom d\'utilisateur.');
            return false;
        } else if (/\s/.test(value) || /[A-Z]/.test(value)) {
            showError(field, 'Le nom d\'utilisateur ne doit pas contenir d\'espaces ou de lettres majuscules.');
            return false;
        }
    // Validation spécifique pour l'email
    } else if (name.includes('email')) {
        if (!validateEmail(value)) {
            showError(field, 'Veuillez saisir une adresse email valide.');
            return false;
        }
    // Validation spécifique pour le premier champ de mot de passe
    } else if (name.includes('plainPassword') && name.includes('first')) {
        if (!validatePassword(value)) {
            showError(field, 'Le mot de passe doit contenir au moins 8 caractères, une lettre majuscule, un chiffre et un caractère spécial.');
            return false;
        }
    // Validation spécifique pour le second champ de mot de passe
    } else if (name.includes('plainPassword') && name.includes('second')) {
        var passwordFirst = document.querySelector('[name="registrationForm[plainPassword][first]"]').value;
        if (value !== passwordFirst) {
            showError(field, 'Les mots de passe ne correspondent pas.');
            return false;
        }
    }

    return true;
}

// Fonction pour effacer les messages d'erreur
function clearError(event) {
    var field = event.target;
    var error = field.parentNode.querySelector('.error-message');
    if (error) {
        error.remove();
    }
}

// Fonction pour afficher les messages d'erreur
function showError(element, message) {
    var error = document.createElement('div');
    error.className = 'error-message';
    error.textContent = message;
    element.parentNode.appendChild(error);
}

// Fonction pour valider les adresses email
function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Fonction pour valider les mots de passe
function validatePassword(password) {
    var hasUpperCase = /[A-Z]/.test(password);
    var hasNumber = /[0-9]/.test(password);
    var hasSpecialChar = /[!@#$%^&*(),.?":{}|<>_\-+=~`[\]{}|\\;:'",<.>/?]/.test(password);
    var isValidLength = password.length >= 8;

    return hasUpperCase && hasNumber && hasSpecialChar && isValidLength;
}

// Explications des principales parties du code :
// Soumission du formulaire :
// Intercepte la soumission du formulaire pour valider tous les champs avant d’obtenir la position géographique.
// Géolocalisation :
// Utilise l’API de géolocalisation pour obtenir la position de l’utilisateur et met à jour les champs cachés du formulaire avec les coordonnées.
// Validation des champs :
// Ajoute des écouteurs d’événements pour valider les champs du formulaire lorsqu’ils perdent le focus ou lorsqu’il y a une entrée.
// Valide le nom d’utilisateur, l’email et les mots de passe selon les critères spécifiés.
// Validation du formulaire entier :
// Valide tous les champs du formulaire avant de permettre la soumission.
// Affichage et effacement des erreurs :
// Affiche des messages d’erreur spécifiques sous les champs invalides et les efface lorsqu’il y a une nouvelle entrée.
// Validation des mots de passe :
// Vérifie que le mot de passe contient au moins une lettre majuscule, un chiffre, un caractère spécial et a une longueur minimale de 8 caractères.
// Ajoute une vérification pour s’assurer que les deux champs de mot de passe contiennent des valeurs identiques.