/**
 * Forum Interaction System
 * Handles Voting, Bookmarking, and Reporting with Optimistic UI.
 * Integrated with new backend logic.
 */

class ForumInteractions {
    constructor() {
        this.init();
    }

    init() {
        // Global listener for clicks (Event Delegation)
        document.addEventListener('click', (e) => {
            
            // 1. VOTE BUTTON CLICK
            // Matches .vote-button (Main Question) OR .answer-vote-btn (Answers)
            const voteBtn = e.target.closest('.vote-button, .answer-vote-btn');
            if (voteBtn) {
                e.preventDefault();
                this.handleVote(voteBtn);
                return;
            }

            // 2. BOOKMARK CLICK
            const bookmarkBtn = e.target.closest('.bookmark-btn');
            if (bookmarkBtn) {
                e.preventDefault();
                this.handleBookmark(bookmarkBtn);
                return;
            }

            // 3. REPORT CLICK
            const reportBtn = e.target.closest('.report-button');
            if (reportBtn) {
                e.preventDefault();
                this.handleReport(reportBtn);
                return;
            }
        });
    }

    // ==========================================================
    // VOTING LOGIC
    // ==========================================================
    async handleVote(button) {
        // Identify if this is a Question or Answer vote
        const isMainQuestion = button.classList.contains('vote-button');
        const type = isMainQuestion ? 'question' : 'answer';
        
        // 1. Get ID
        let id = button.dataset.id;

        // If missing and it's a main question, try getting it from the URL
        if (!id && isMainQuestion) {
            const urlParams = new URLSearchParams(window.location.search);
            id = urlParams.get('id'); 
        }

        if (!id) {
            console.error("Vote Error: No ID found on button or URL.");
            return;
        }

        // 2. Identify the Counter Element (select by explicit classes)
        let countEl = null;
        if (isMainQuestion) {
            countEl = button.querySelector('.vote-count-span, .vote-text, .stat-number');
        } else {
            const wrapper = button.closest('.answer-vote');
            if (wrapper) countEl = wrapper.querySelector('.answer-vote-count');
        }

        // 3. Optimistic UI Update (Instant Feedback)
        const wasVoted = button.classList.contains('is-voted');

        // Keep a copy of previous count so we can revert precisely on error
        const previousCount = countEl ? (parseInt(countEl.innerText) || 0) : 0;
        const optimisticCount = countEl ? (wasVoted ? Math.max(0, previousCount - 1) : previousCount + 1) : null;

        // Toggle Visuals (just toggle class; CSS controls appearance)
        this.toggleVoteVisuals(button, !wasVoted, isMainQuestion);

        // Apply optimistic count immediately
        if (countEl && optimisticCount !== null) countEl.innerText = optimisticCount;

        // 4. Send Request
        try {
            const response = await fetch('/dashboard/forum/vote', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type, id })
            });

            const data = await response.json();
            
            if (data.status === 'success') {
                // Synchronize with server count if provided
                if (countEl && data.new_count !== undefined && data.new_count !== null) {
                    countEl.innerText = data.new_count;
                }

                // Ensure button state matches server action
                const isAdded = (data.action === 'added' || data.action === 'updated');
                // Set final visuals according to server
                this.toggleVoteVisuals(button, isAdded, isMainQuestion);
            } else {
                throw new Error(data.message || 'Vote failed');
            }

        } catch (error) {
            console.error('Vote failed:', error);
            
            // Revert changes on error: restore previous visuals and exact count
            this.toggleVoteVisuals(button, wasVoted, isMainQuestion);
            if (countEl) {
                countEl.innerText = previousCount;
            }
            
            if (error.message === 'Please login to vote') {
                alert("Please log in to vote.");
                window.location.href = '/login';
            } else {
                this.showToast(error.message || 'Error voting', 'error');
            }
        }
    }

    // Helper to toggle colors/classes
    toggleVoteVisuals(button, isActive, isMainQuestion) {
        if (isActive) {
            button.classList.add('is-voted');
            button.style.color = '#0466C8'; // Active Blue
            if (isMainQuestion) button.style.background = '#e0f2fe';
        } else {
            button.classList.remove('is-voted');
            button.style.color = ''; // Reset
            if (isMainQuestion) button.style.background = '';
        }
    }

    // ==========================================================
    // BOOKMARK LOGIC
    // ==========================================================
    async handleBookmark(button) {
        const id = button.dataset.id;
        if (!id) return;

        // 1. Optimistic UI (Toggle Color)
        const isActive = button.classList.toggle('active');
        
        // Manual color force if CSS isn't set up
        button.style.color = isActive ? '#EAB308' : ''; // Gold color
        button.style.fill = isActive ? 'currentColor' : 'none';

        try {
            const response = await fetch('/dashboard/forum/bookmark', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id })
            });

            const data = await response.json();

            if (data.status === 'success') {
                this.showToast(data.action === 'added' ? 'Question saved!' : 'Bookmark removed', 'success');
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            // Revert on error
            button.classList.toggle('active');
            button.style.color = '';
            
            if (error.message.includes('login')) {
                window.location.href = '/login';
            } else {
                this.showToast('Failed to bookmark', 'error');
            }
        }
    }

    // ==========================================================
    // REPORT LOGIC
    // ==========================================================
    async handleReport(button) {
        const reason = prompt("Why are you reporting this content?");
        if (!reason) return;

        const originalText = button.textContent;
        button.textContent = "Reporting...";
        button.disabled = true;

        try {
            let id = button.dataset.id;
            let type = button.dataset.type || 'question';

            if (!id) {
                const urlParams = new URLSearchParams(window.location.search);
                id = urlParams.get('id');
            }

            const response = await fetch('/dashboard/forum/report', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type, id, reason })
            });

            const data = await response.json();

            if (data.status === 'success') {
                button.textContent = "Reported";
                button.style.color = "var(--danger-color)";
                this.showToast("Report submitted.", "success");
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            button.textContent = originalText;
            button.disabled = false;
            this.showToast("Failed to report.", "error");
        }
    }

    // Helper: Toast Notification
    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.textContent = message;
        const bg = type === 'error' ? '#DC2626' : '#10B981';
        
        toast.style.cssText = `
            position: fixed; bottom: 24px; right: 24px;
            background: ${bg}; color: white; padding: 12px 24px; 
            border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
            z-index: 9999; font-weight: 500; opacity: 0; transform: translateY(10px);
            transition: all 0.3s ease;
        `;
        
        document.body.appendChild(toast);

        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    new ForumInteractions();
});