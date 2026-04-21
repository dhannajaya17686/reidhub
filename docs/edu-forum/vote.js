

document.addEventListener('DOMContentLoaded', () => {

    // Make this function available everywhere so clicking a vote button on the webpage works.
    window.toggleVote = async function(targetType, targetId, voteValue, buttonElement) {
        
        // 1. Find the buttons and the number on the screen
        // Find the box holding the clicked button so we can easily find the other parts (like the count and the opposite button).
        const voteContainer = buttonElement.closest('.vote-container');
        const countElement = voteContainer.querySelector('.vote-count');
        const upBtn = voteContainer.querySelector('.upvote-btn');
        const downBtn = voteContainer.querySelector('.downvote-btn');

        // Remember the current number in case something goes wrong and we need to put it back.
        let currentCount = parseInt(countElement.innerText);
        const previousState = {
            count: currentCount,
            upActive: upBtn.classList.contains('active'),
            downActive: downBtn.classList.contains('active')
        };

        // 2. Send the vote to the server in the background
        try {
            const response = await fetch('/dashboard/forum/vote', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    target_type: targetType,
                    target_id: targetId,
                    vote_value: voteValue // This will be either 'up' or 'down'
                })
            });

            // Stop if the user is not logged in or if there is a server error.
            if (!response.ok) {
                if (response.status === 401) alert("You must be logged in to vote.");
                return;
            }

            const data = await response.json();

            if (data.success) {
                // 3. Update the webpage based on what the server says
                // The server will tell us if the vote was added, changed, or removed.
                
                // Change the vote number on the screen
                countElement.innerText = data.new_count;

                // Remove the highlighted colors from both buttons first
                upBtn.classList.remove('active');
                downBtn.classList.remove('active');

                // If the vote was added or changed, highlight the correct button
                if (data.action === 'added' || data.action === 'updated') {
                    if (voteValue === 'up') {
                        upBtn.classList.add('active');
                    } else {
                        downBtn.classList.add('active');
                    }
                }

            } else {
                console.warn('Voting failed:', data.message);
            }
        } catch (error) {
            console.error('Fetch error during voting:', error);
            alert("A network error occurred. Please try again.");
        }
    };
});
