document.addEventListener('DOMContentLoaded', () => {
    const aboutHeading = document.querySelector('.about-us h2');
    const text = "What is ReidHub?"; // The text to type
    let charIndex = 0;

    // Function to type one letter at a time
    function typeLetter() {
        if (charIndex < text.length) {
            aboutHeading.textContent += text[charIndex];
            charIndex++;
            setTimeout(typeLetter, 100); // Adjust typing speed (100ms per letter)
        }
    }

    // Scroll detection to trigger the typing effect
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                typeLetter(); // Start typing when the section is visible
                observer.unobserve(aboutHeading); // Stop observing after animation starts
            }
        });
    }, { threshold: 0.5 }); // Trigger when 50% of the section is visible

    observer.observe(aboutHeading);
});