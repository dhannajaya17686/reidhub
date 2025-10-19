/**
 * Archived Items Management
 * Minimal functionality extending active items behavior
 */

class ArchivedItemsManager {
  constructor() {
    this.init();
  }

  init() {
    this.setupEventListeners();
    this.checkItemsCount();
  }

  // Setup basic event listeners
  setupEventListeners() {
    // Handle button click animations
    document.addEventListener('click', (e) => {
      if (e.target.matches('.btn')) {
        e.target.classList.add('clicked');
        setTimeout(() => {
          e.target.classList.remove('clicked');
        }, 200);
      }
    });
  }

  // Check if there are items to show empty state
  checkItemsCount() {
    const itemsGrid = document.getElementById('items-grid');
    const emptyState = document.getElementById('empty-state');
    const items = itemsGrid.querySelectorAll('.item-card');

    if (items.length === 0) {
      itemsGrid.style.display = 'none';
      emptyState.style.display = 'block';
    } else {
      itemsGrid.style.display = 'grid';
      emptyState.style.display = 'none';
    }
  }

  // Show loading overlay
  showLoading() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
      loadingOverlay.style.display = 'flex';
    }
  }

  // Hide loading overlay
  hideLoading() {
    const loadingOverlay = document.getElementById('loading-overlay');
    if (loadingOverlay) {
      loadingOverlay.style.display = 'none';
    }
  }

  // Show success message
  showMessage(message, type = 'success') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `${type}-message slide-up`;
    messageDiv.innerHTML = `
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
        <path d="M5 12l5 5L20 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      ${message}
    `;

    const main = document.querySelector('.active-items-main');
    main.insertBefore(messageDiv, main.firstChild);

    // Remove message after 3 seconds
    setTimeout(() => {
      messageDiv.classList.add('fade-out');
      setTimeout(() => {
        messageDiv.remove();
      }, 300);
    }, 3000);
  }

  // Remove item from grid
  removeItemFromGrid(itemId) {
    const itemCard = document.querySelector(`[data-item-id="${itemId}"]`);
    if (itemCard) {
      itemCard.classList.add('fade-out');
      setTimeout(() => {
        itemCard.remove();
        this.checkItemsCount();
      }, 300);
    }
  }
}

// Global functions for button actions
function viewItem(itemId) {
  window.location.href = `/marketplace/seller/items/${itemId}/view`;
}

function editItem(itemId) {
  window.location.href = `/marketplace/seller/items/${itemId}/edit`;
}

// Main difference: unarchive instead of archive
function unarchiveItem(itemId) {
  if (confirm('Are you sure you want to unarchive this item?')) {
    const manager = window.archivedItemsManager;
    manager.showLoading();

    // Simulate API call
    fetch(`/api/marketplace/seller/items/${itemId}/unarchive`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        manager.showMessage('Item unarchived successfully!');
        manager.removeItemFromGrid(itemId);
      } else {
        manager.showMessage('Failed to unarchive item', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      manager.showMessage('An error occurred', 'error');
    })
    .finally(() => {
      manager.hideLoading();
    });
  }
}

// Initialize when DOM loads
document.addEventListener('DOMContentLoaded', () => {
  window.archivedItemsManager = new ArchivedItemsManager();
});