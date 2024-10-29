// Fonction pour obtenir la position géographique de l'utilisateur
function getLocation() {
    if (navigator.geolocation) {
        console.log("Géolocalisation supportée par le navigateur.");
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

// Fonction appelée en cas de succès de la géolocalisation
function showPosition(position) {
    var latitude = position.coords.latitude;
    var longitude = position.coords.longitude;

    console.log("Latitude: " + latitude + ", Longitude: " + longitude);
    // Met à jour les champs cachés du formulaire avec les coordonnées
    document.getElementById('latitude').value = latitude;
    document.getElementById('longitude').value = longitude;

    // Vérifie les valeurs avant de soumettre le formulaire
    console.log("Valeurs avant soumission - Latitude: " + document.getElementById('latitude').value + ", Longitude: " + document.getElementById('longitude').value);

    // Soumet le formulaire après avoir défini les coordonnées
    document.getElementById('userForm').submit();
}

// Fonction appelée en cas d'échec de la géolocalisation
function proceedWithoutLocation(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            console.error("L'utilisateur a refusé la demande de géolocalisation.");
            break;
        case error.POSITION_UNAVAILABLE:
            console.error("Les informations de localisation sont indisponibles.");
            break;
        case error.TIMEOUT:
            console.error("La demande de géolocalisation a expiré.");
            break;
        case error.UNKNOWN_ERROR:
            console.error("Une erreur inconnue est survenue.");
            break;
    }
    // Soumettre le formulaire sans les coordonnées
    document.getElementById('userForm').submit();
}
