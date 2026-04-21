/**
 * EDU FORUM: ONE QUESTION (DETAILS) JAVASCRIPT
 * --------------------------------------------
 * This file makes the individual question page work.
 * It handles the pop-up box for editing, sending reports to moderators, and accepting answers.
 */

document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. POP-UP EDIT BOX
    // ==========================================
    const editModal = document.getElementById('editModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const editForm = document.getElementById('editForm');
    const editContent = document.getElementById('editContent');
    const editTitleGroup = document.getElementById('editTitleGroup');
    const editTitle = document.getElementById('editTitle');

    // Make this function available everywhere so clicking an "Edit" button on the webpage can open the pop-up box.
    window.openEditModal = function(type, id, content, title = '') {
        // Save the ID and type (question or answer) in hidden text boxes so the server knows what we are editing.
        document.getElementById('editId').value = id;
        document.getElementById('editType').value = type;
        
        // Put the old text into the large text box so the user can change it.
        editContent.value = content;
        
        // Questions have titles, but answers do not. Show or hide the title box depending on what is being edited.
        if (type === 'question') {
            editTitleGroup.style.display = 'block';
            editTitle.value = title;
        } else {
            editTitleGroup.style.display = 'none';
            editTitle.value = '';
        }
        
        // If there is a box asking for a "reason for editing", clear it out to start fresh.
        const editReason = document.getElementById('editReason');
        if (editReason) editReason.value = '';

        // Make the pop-up box visible on the screen.
        editModal.style.display = 'flex';
    };

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', () => {
            editModal.style.display = 'none';
        });
    }

    // Close the pop-up box if the user clicks the dark background outside of it.
    window.addEventListener('click', (e) => {
        if (e.target === editModal) {
            editModal.style.display = 'none';
        }
    });

    // ==========================================
    // 2. REPORTING SYSTEM
    // ==========================================
    window.handleReport = async function(event, type, id) {
        event.preventDefault(); // Stop the page from reloading when the user clicks the report button.

        // 1. Ask the user for details
        const categoryInput = prompt('Enter Report Category (e.g., Spam, Harassment, Inaccurate):', 'Spam');
        if (categoryInput === null) return; // Stop if the user clicked Cancel

        const reasonInput = prompt('Please enter the detailed reason for this report (minimum 5 characters):');
        if (reasonInput === null) return;

        const reason = reasonInput.trim();
        if (reason.length < 5) {
            alert('Please provide a clearer reason (at least 5 characters).');
            return;
        }

        // 2. Send the report details to the server in the background
        try {
            const response = await fetch('/dashboard/forum/report', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ 
                    type: type, 
                    id: id, 
                    reason: reason, 
                    category: categoryInput 
                })
            });

            const data = await response.json();

            // 3. Check what the server says
            if (data.success) {
                alert('✅ Thank you. The report has been submitted to moderators.');
                // Make the report button look faded out and unclickable so they don't report it twice.
                event.target.style.opacity = '0.5';
                event.target.style.pointerEvents = 'none';
                event.target.innerText = 'Reported';
            } else {
                alert('❌ Failed to submit report: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Report submission error:', error);
            alert('❌ Network error occurred while submitting the report.');
        }
    };
});
