document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.section');

    sections.forEach(section => {
        section.querySelector('.header').addEventListener('click', function() {
            section.classList.toggle('active');
        });
    });
});
