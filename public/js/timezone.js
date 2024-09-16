// JavaScript pour détecter le fuseau horaire et l'envoyer au serveur
document.addEventListener('DOMContentLoaded', (event) => {
    const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
    fetch('/set-timezone', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ timezone: timezone }),
    });
});
