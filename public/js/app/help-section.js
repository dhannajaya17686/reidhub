// Help & Feedback JavaScript Utilities

/**
 * Validate form input
 */
function validateFormInput(input, minLength = 1, maxLength = 5000) {
    const value = input.value.trim();
    
    if (value.length < minLength) {
        return {
            valid: false,
            message: `Input must be at least ${minLength} characters`
        };
    }
    
    if (value.length > maxLength) {
        return {
            valid: false,
            message: `Input cannot exceed ${maxLength} characters`
        };
    }
    
    return { valid: true };
}

/**
 * Display error message
 */
function displayError(message, container = null) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'alert alert-error';
    errorDiv.textContent = message;
    
    if (container) {
        container.insertBefore(errorDiv, container.firstChild);
    } else {
        document.body.insertBefore(errorDiv, document.body.firstChild);
    }
    
    // Auto-remove after 5 seconds
    setTimeout(() => errorDiv.remove(), 5000);
}

/**
 * Display success message
 */
function displaySuccess(message, container = null) {
    const successDiv = document.createElement('div');
    successDiv.className = 'alert alert-success';
    successDiv.textContent = message;
    
    if (container) {
        container.insertBefore(successDiv, container.firstChild);
    } else {
        document.body.insertBefore(successDiv, document.body.firstChild);
    }
    
    // Auto-remove after 5 seconds
    setTimeout(() => successDiv.remove(), 5000);
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

/**
 * Format date in readable format
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    };
    return date.toLocaleDateString('en-US', options);
}


/**
 * Fetch wrapper with error handling
 */
async function apiFetch(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('API Fetch Error:', error);
        throw error;
    }
}

/**
 * Initialize character counter for textarea
 */
function initializeCharacterCounter(textareaSelector, counterSelector, maxLength = 5000) {
    const textarea = document.querySelector(textareaSelector);
    const counter = document.querySelector(counterSelector);
    
    if (!textarea || !counter) return;
    
    textarea.addEventListener('input', function() {
        const count = this.value.length;
        const percentage = (count / maxLength) * 100;
        
        counter.textContent = `${count} / ${maxLength} characters`;
        
        // Change color if approaching limit
        if (percentage > 80) {
            counter.style.color = '#faad14';
        } else if (percentage > 95) {
            counter.style.color = '#f5222d';
        } else {
            counter.style.color = '#8c8c8c';
        }
    });
}

/**
 * Set button loading state
 */
function setButtonLoading(button, isLoading = true) {
    const originalText = button.innerHTML;
    
    if (isLoading) {
        button.disabled = true;
        button.innerHTML = '<span class="loading-spinner"></span> Loading...';
        button.dataset.originalText = originalText;
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || originalText;
    }
}

/**
 * Get URL parameters
 */
function getUrlParams() {
    const params = new URLSearchParams(window.location.search);
    const result = {};
    
    for (let [key, value] of params) {
        result[key] = value;
    }
    
    return result;
}

/**
 * Format category text
 */
function formatCategory(category) {
    return category
        .replace(/_/g, ' ')
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
}

/**
 * Format status text
 */
function formatStatus(status) {
    const statusMap = {
        'pending': 'Pending',
        'replied': 'Replied',
        'resolved': 'Resolved',
        'closed': 'Closed'
    };
    return statusMap[status] || status;
}

/**
 * Truncate text with ellipsis
 */
function truncateText(text, maxLength = 100) {
    if (text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    }
    return text;
}

/**
 * Debounce function
 */
function debounce(func, delay = 300) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Show confirmation dialog
 */
function showConfirmation(message) {
    return new Promise((resolve) => {
        if (confirm(message)) {
            resolve(true);
        } else {
            resolve(false);
        }
    });
}

/**
 * Local storage helper
 */
const StorageHelper = {
    set: (key, value) => {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (e) {
            console.error('Storage error:', e);
            return false;
        }
    },
    
    get: (key) => {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : null;
        } catch (e) {
            console.error('Storage error:', e);
            return null;
        }
    },
    
    remove: (key) => {
        try {
            localStorage.removeItem(key);
            return true;
        } catch (e) {
            console.error('Storage error:', e);
            return false;
        }
    }
};

// Export utilities for use in views
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        validateFormInput,
        displayError,
        displaySuccess,
        escapeHtml,
        formatDate,
        apiFetch,
        initializeCharacterCounter,
        setButtonLoading,
        getUrlParams,
        formatCategory,
        formatStatus,
        truncateText,
        debounce,
        showConfirmation,
        StorageHelper
    };
}
