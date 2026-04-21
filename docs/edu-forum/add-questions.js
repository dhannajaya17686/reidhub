/**
 * EDU FORUM: ADD QUESTION JAVASCRIPT
 * ----------------------------------
 * This file controls the "Ask a Question" page.
 * It counts how many letters are in the title and lets users add/remove topic tags.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Find the parts of the page we need
    // Get the form, text boxes, and other elements from the webpage so we can use them.
    const questionForm = document.getElementById('add-question-form');
    const titleInput = document.getElementById('question-title');
    const titleCount = document.getElementById('title-char-count');
    const tagsInput = document.getElementById('question-tags-input');
    const hiddenTagsInput = document.getElementById('hidden-tags');
    const tagsContainer = document.getElementById('tags-container');

    // This empty list will hold the tags the user types in.
    let tagsArray = [];

    // 2. Count the letters in the title
    // Show the user how many letters they have typed in the title box.
    if (titleInput && titleCount) {
        titleInput.addEventListener('input', (e) => {
            const currentLength = e.target.value.length;
            const maxLength = e.target.getAttribute('maxlength') || 100;
            titleCount.textContent = `${currentLength} / ${maxLength}`;
            
            // Make the text red if they are 10 letters or less away from the maximum limit
            if (currentLength > maxLength - 10) {
                titleCount.style.color = '#dc2626'; // Red
            } else {
                titleCount.style.color = 'var(--text-muted)'; // Default
            }
        });
    }

    // 3. Handle the Tags
    // When the user types a tag and presses 'Enter' or comma, turn it into a visual badge.
    if (tagsInput) {
        tagsInput.addEventListener('keydown', (e) => {
            // Check if the user pressed the 'Enter' key or the comma ',' key
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault(); // Stop the page from reloading or submitting the form early
                
                // Get the typed text, remove extra spaces at the ends, and make it all lowercase
                const rawTag = tagsInput.value.trim().toLowerCase();
                // Take out any commas the user might have typed
                const cleanTag = rawTag.replace(/,/g, '');

                // If the tag is not empty, is not already in the list, and we have less than 5 tags total:
                if (cleanTag && !tagsArray.includes(cleanTag) && tagsArray.length < 5) {
                    tagsArray.push(cleanTag); // Add the new tag to our list
                    renderTags(); // Update the screen to show the new tag
                    tagsInput.value = ''; // Clear the text box so they can type another tag
                }
            }
        });
    }

    // Helper function: This updates the webpage to show the tags and prepares them to be sent to the server.
    function renderTags() {
        // Empty the area where tags are shown before redrawing them
        tagsContainer.innerHTML = '';
        
        // Combine all tags with commas and put them in a hidden box. This is what actually gets sent to the server.
        hiddenTagsInput.value = tagsArray.join(',');

        // Go through each tag in our list and create a visual badge for it
        tagsArray.forEach((tag, index) => {
            const tagChip = document.createElement('span');
            tagChip.className = 'tag-chip';
            tagChip.innerHTML = `
                ${tag} 
                <button type="button" class="remove-tag-btn" data-index="${index}">&times;</button>
            `;
            tagsContainer.appendChild(tagChip);
        });

        // Make the 'x' buttons on the tags actually work so users can delete tags
        document.querySelectorAll('.remove-tag-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const idxToRemove = e.target.getAttribute('data-index'); // Find out which tag number was clicked
                tagsArray.splice(idxToRemove, 1); // Delete that tag from our list
                renderTags(); // Redraw the tags on the screen to show it's gone
            });
        });
    }

    // 4. Check everything before sending
    // You can add code here to check if the title is long enough before letting the form submit.
});
