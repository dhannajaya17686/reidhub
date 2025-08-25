document.addEventListener('DOMContentLoaded', () => {
    const header = document.querySelector('.header');

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) { // Change 50 to the desired scroll threshold
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
});