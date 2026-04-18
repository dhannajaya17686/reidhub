/**
 * All Questions Page Controller
 * -----------------------------
 * Handles tabs, search interaction, bookmarks, and toast notifications.
 */
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Initialize Tab Keyboard Navigation (Accessibility only)
    initTabAccessibility();

    // 2. Initialize Bookmark Buttons
    initBookmarks();

    // 3. Check for Success/Error Messages (Toast)
    checkURLMessages();

    // 4. Initialize Search "Clear" functionality (Optional UX)
    initSearchUX();
});

/**
 * 1. Tab Accessibility
 * Handles Arrow Key navigation. We do NOT need to handle 'click' 
 * because the <a> tags will reload the page via PHP.
 */
function initTabAccessibility() {
    const tabContainer = document.querySelector('.content-tabs');
    if (!tabContainer) return;

    const tabs = tabContainer.querySelectorAll('.tab-link');

    tabContainer.addEventListener('keydown', (e) => {
        const { key } = e;
        if (key !== 'ArrowRight' && key !== 'ArrowLeft') return;

        const currentTab = document.activeElement;
        const currentIndex = Array.from(tabs).indexOf(currentTab);
        if (currentIndex === -1) return;

        e.preventDefault();
        const direction = key === 'ArrowRight' ? 1 : -1;
        const nextIndex = (currentIndex + direction + tabs.length) % tabs.length;
        
        tabs[nextIndex].focus();
    });
}

/**
 * 2. Bookmark System
 * Handles AJAX requests to bookmark/unbookmark questions.
 */
function initBookmarks() {
    const bookmarkBtns = document.querySelectorAll('.bookmark-btn');

    bookmarkBtns.forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            e.stopPropagation(); // Prevent clicking the card link

            const id = btn.dataset.id;
            
            // Optimistic UI Update (Instant feedback)
            btn.classList.toggle('active');
            
            // Animate
            btn.style.transform = 'scale(1.2)';
            setTimeout(() => btn.style.transform = 'scale(1)', 200);

            try {
                const response = await fetch('/dashboard/forum/bookmark', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showToast(data.action === 'added' ? 'Question saved' : 'Question removed');
                } else {
                    // Revert if error
                    btn.classList.toggle('active');
                    if (data.message === 'Please login to bookmark') {
                        window.location.href = '/login';
                    }
                }
            } catch (error) {
                console.error('Bookmark error:', error);
                btn.classList.toggle('active'); // Revert
            }
        });
    });
}

/**
 * 3. URL Message Handler (Toasts)
 * Reads ?success=created or ?error=failed from URL
 */
function checkURLMessages() {
    const params = new URLSearchParams(window.location.search);
    
    if (params.has('success')) {
        const type = params.get('success');
        let msg = 'Action successful';
        
        if (type === 'created') msg = 'Question posted successfully!';
        if (type === 'deleted') msg = 'Question deleted.';
        
        showToast(msg, 'success');
        
        // Clean URL
        cleanURL();
    }
    
    if (params.has('error')) {
        showToast('Something went wrong. Please try again.', 'error');
        cleanURL();
    }
}

function cleanURL() {
    const newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + window.location.search.replace(/[\?&](success|error)=[^&]+/, '');
    window.history.replaceState({}, document.title, newUrl);
}

/**
 * 4. Search UX
 * Adds a visual cue when searching
 */
function initSearchUX() {
    const searchInput = document.querySelector('input[name="search"]');
    if(searchInput && searchInput.value.length > 0) {
        searchInput.classList.add('has-value');
    }
}

/**
 * Utility: Show Toast
 */
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `interaction-toast ${type}`;
    toast.textContent = message;
    
    // Style matches your CSS variables
    Object.assign(toast.style, {
        position: 'fixed',
        bottom: '20px',
        right: '20px',
        background: type === 'success' ? '#10B981' : '#EF4444',
        color: 'white',
        padding: '12px 24px',
        borderRadius: '8px',
        boxShadow: '0 4px 6px rgba(0,0,0,0.1)',
        zIndex: '9999',
        opacity: '0',
        transform: 'translateY(10px)',
        transition: 'all 0.3s ease',
        fontWeight: '500'
    });

    document.body.appendChild(toast);

    // Animate In
    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });

    // Remove after 3s
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
