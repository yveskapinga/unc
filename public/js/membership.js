document.addEventListener('DOMContentLoaded', function() {
    const levelField = document.getElementById('level');
    const fonctionField = document.getElementById('fonction');

    levelField.addEventListener('change', function() {
        const selectedLevel = levelField.value;
        const availableFonctions = fonctions[selectedLevel] || [];

        // Vider les options actuelles
        fonctionField.innerHTML = '';

        if (availableFonctions.length > 0) {
            // Activer le champ fonction
            fonctionField.removeAttribute('disabled');

            // Ajouter les nouvelles options
            availableFonctions.forEach(function(fonction) {
                const option = document.createElement('option');
                option.value = fonction;
                option.textContent = fonction;
                fonctionField.appendChild(option);
            });
        } else {
            // Désactiver le champ fonction si aucune fonction n'est disponible
            fonctionField.setAttribute('disabled', 'disabled');
        }
    });

    // Déclencher le changement pour initialiser les fonctions
    levelField.dispatchEvent(new Event('change'));
});
